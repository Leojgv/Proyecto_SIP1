<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AjusteRazonable;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AsesoraTecnicaDashboardController extends Controller
{
    public function show(Request $request)
    {
        $solicitudesBase = Solicitud::query();

        // Casos pendientes: casos que están en estados donde la asesora técnica debe trabajar
        $casosPendientes = (clone $solicitudesBase)
            ->whereIn('estado', [
                'Pendiente de formulación del caso',
                'Pendiente de formulación de ajuste',
                'Listo para Enviar',
                'Pendiente de preaprobación',
            ])
            ->count();

        // Casos completados: casos que han sido aprobados (estado final)
        $casosCompletados = (clone $solicitudesBase)
            ->where('estado', 'Aprobado')
            ->count();

        // Ajustes formulados este mes
        $ajustesEsteMes = AjusteRazonable::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        // Total de ajustes formulados
        $totalAjustes = AjusteRazonable::count();

        // Casos con observaciones de entrevista (con PDF o texto)
        $casosConObservaciones = (clone $solicitudesBase)
            ->where(function ($query) {
                $query->whereNotNull('observaciones_pdf_ruta')
                    ->orWhereHas('entrevistas', function ($q) {
                        $q->whereNotNull('observaciones');
                    });
            })
            ->whereIn('estado', [
                'Pendiente de formulación del caso',
                'Pendiente de formulación de ajuste'
            ])
            ->count();

        // Ajustes más comunes (top 3)
        // Agrupar por nombre normalizado y contar, incluyendo la fecha más reciente
        $ajustesAgrupados = AjusteRazonable::selectRaw('TRIM(nombre) as nombre_normalizado, COUNT(*) as total, MAX(created_at) as ultima_creacion')
            ->groupByRaw('TRIM(nombre)')
            ->get();
        
        // Ordenar: primero por total (más comunes), luego por fecha más reciente
        $ajustesOrdenados = $ajustesAgrupados->sort(function ($a, $b) {
            // Primero comparar por total (descendente)
            if ($a->total != $b->total) {
                return $b->total <=> $a->total;
            }
            // Si tienen el mismo total, comparar por fecha más reciente (descendente)
            return strtotime($b->ultima_creacion) <=> strtotime($a->ultima_creacion);
        });
        
        // Tomar los top 3 (mantener como colección para compatibilidad con la vista)
        $ajustesComunes = $ajustesOrdenados->take(3)
            ->map(function ($ajuste) {
                return [
                    'nombre' => trim($ajuste->nombre_normalizado),
                    'total' => (int) $ajuste->total
                ];
            })
            ->values();

        // Datos para el gráfico de evolución mensual de ajustes (últimos 6 meses)
        $evolucionMensual = [];
        $mesesNombres = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $fecha = Carbon::now()->subMonths($i);
            $mesInicio = $fecha->copy()->startOfMonth();
            $mesFin = $fecha->copy()->endOfMonth();
            
            $ajustesMes = AjusteRazonable::whereBetween('created_at', [$mesInicio, $mesFin])->count();
            
            $evolucionMensual[] = $ajustesMes;
            $mesesNombres[] = $fecha->format('M Y');
        }

        $metrics = [
            [
                'label' => 'Casos pendientes',
                'value' => $casosPendientes,
                'hint' => 'Por revisar',
                'icon' => 'fa-inbox',
            ],
            [
                'label' => 'Completados',
                'value' => $casosCompletados,
                'hint' => 'Aprobados',
                'icon' => 'fa-circle-check',
            ],
            [
                'label' => 'Ajustes este mes',
                'value' => $ajustesEsteMes,
                'hint' => 'Formulados',
                'icon' => 'fa-calendar-plus',
            ],
        ];

        $assignedCases = (clone $solicitudesBase)
            ->with(['estudiante.carrera', 'ajustesRazonables'])
            ->whereIn('estado', [
                'Pendiente de formulación del caso',
                'Pendiente de formulación de ajuste',
                'Listo para Enviar',
                'Pendiente de preaprobación',
            ])
            ->latest('fecha_solicitud')
            ->take(4)
            ->get()
            ->map(function (Solicitud $solicitud) {
                $carrera = optional(optional($solicitud->estudiante)->carrera)->nombre;
                $ajustesCount = $solicitud->ajustesRazonables()->count();
                $estadosPermitidos = ['Listo para Enviar', 'Pendiente de formulación de ajuste'];
                $puedeEnviarAPreaprobacion = in_array($solicitud->estado, $estadosPermitidos) && $ajustesCount > 0;
                
                return [
                    'case_id' => $solicitud->id,
                    'student' => trim(($solicitud->estudiante->nombre ?? 'Estudiante') . ' ' . ($solicitud->estudiante->apellido ?? '')),
                    'program' => $carrera ? $carrera : 'Sin carrera asignada',
                    'summary' => $solicitud->descripcion ?? 'Sin descripción registrada.',
                    'status' => $solicitud->estado ?? 'Pendiente',
                    'fecha_solicitud' => optional($solicitud->fecha_solicitud)->format('d/m/Y') ?? 's/f',
                    'ajustes_count' => $ajustesCount,
                    'puede_enviar_preaprobacion' => $puedeEnviarAPreaprobacion,
                ];
            })->toArray();

        $recentAdjustments = AjusteRazonable::query()
            ->with(['estudiante.carrera', 'solicitud'])
            ->latest('updated_at')
            ->take(15)
            ->get()
            ->groupBy('estudiante_id')
            ->map(function ($ajustes) {
                /** @var \Illuminate\Support\Collection $ajustes */
                /** @var AjusteRazonable|null $primero */
                $primero = $ajustes->first();
                $estudiante = $primero?->estudiante;
                $carrera = optional(optional($estudiante)->carrera)->nombre;

                return [
                    'student' => trim(($estudiante->nombre ?? 'Estudiante') . ' ' . ($estudiante->apellido ?? '')),
                    'program' => $carrera ?: 'Programa no asignado',
                    'adjustments' => $ajustes->map(function (AjusteRazonable $ajuste) {
                        // Determinar el estado a mostrar basado en el estado de la solicitud
                        $solicitudEstado = $ajuste->solicitud->estado ?? null;
                        $estadoAjuste = $ajuste->estado ?? 'Pendiente';
                        
                        // Si la solicitud está en preaprobación o estados posteriores, 
                        // mostrar ese estado en lugar del estado individual del ajuste
                        if (in_array($solicitudEstado, ['Pendiente de preaprobación', 'Pendiente de Aprobación'])) {
                            $estadoFinal = $solicitudEstado;
                        } elseif ($solicitudEstado === 'Aprobado') {
                            // Si la solicitud está aprobada, usar el estado del ajuste (puede ser Aprobado o Rechazado)
                            $estadoFinal = $estadoAjuste;
                        } elseif ($solicitudEstado === 'Rechazado') {
                            // Si la solicitud fue rechazada pero el ajuste tiene un estado específico, usar ese
                            $estadoFinal = $estadoAjuste === 'Rechazado' ? 'Rechazado' : $estadoAjuste;
                        } else {
                            $estadoFinal = $estadoAjuste;
                        }
                        
                        return [
                            'name' => $ajuste->nombre ?? 'Ajuste sin titulo',
                            'description' => $ajuste->descripcion ?? 'No hay descripción',
                            'status' => $estadoFinal,
                            'estado' => $estadoFinal, // Para compatibilidad con el JavaScript
                            'motivo_rechazo' => $ajuste->motivo_rechazo ?? null,
                            'completed_at' => optional($ajuste->updated_at)->format('Y-m-d') ?? 's/f',
                        ];
                    })->take(5)->values()->toArray(),
                ];
            })
            ->values()
            ->take(3)
            ->toArray();


        return view('asesora tecnica.dashboard', [
            'metrics' => $metrics,
            'assignedCases' => $assignedCases,
            'recentAdjustments' => $recentAdjustments,
            'totalAjustes' => $totalAjustes,
            'casosConObservaciones' => $casosConObservaciones,
            'ajustesComunes' => $ajustesComunes,
            'evolucionMensual' => $evolucionMensual,
            'mesesNombres' => $mesesNombres,
        ]);
    }

    protected function inferirPrioridad(?string $estado): string
    {
        return match (strtolower((string) $estado)) {
            'en proceso', 'en revisión' => 'Media',
            'completado', 'cerrado' => 'Baja',
            default => 'Alta',
        };
    }
}
