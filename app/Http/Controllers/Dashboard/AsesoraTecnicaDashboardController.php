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
                'Pendiente de preaprobación',
            ])
            ->count();

        // Casos completados: casos que han sido aprobados (estado final)
        $casosCompletados = (clone $solicitudesBase)
            ->where('estado', 'Aprobado')
            ->count();

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
        ];

        $assignedCases = (clone $solicitudesBase)
            ->with(['estudiante.carrera', 'ajustesRazonables'])
            ->whereIn('estado', [
                'Pendiente de formulación del caso',
                'Pendiente de formulación de ajuste',
                'Pendiente de preaprobación',
            ])
            ->latest('fecha_solicitud')
            ->take(4)
            ->get()
            ->map(function (Solicitud $solicitud) {
                $carrera = optional(optional($solicitud->estudiante)->carrera)->nombre;
                $ajustesCount = $solicitud->ajustesRazonables()->count();
                $estadosPermitidos = ['Pendiente de formulación de ajuste'];
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
            ->with(['estudiante.carrera'])
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
                        return [
                            'name' => $ajuste->nombre ?? 'Ajuste sin titulo',
                            'description' => $ajuste->descripcion ?? 'No hay descripción',
                            'status' => $ajuste->estado ?? 'En proceso',
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
