<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AjusteRazonable;
use App\Models\Carrera;
use App\Models\Entrevista;
use App\Models\Estudiante;
use App\Models\Solicitud;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminDashboardController extends Controller
{
    public function show()
    {
        $hoy = Carbon::now();
        $inicioMes = $hoy->copy()->startOfMonth();

        $activeStates = $this->activeStates();
        $closedStates = $this->closedStates();
        $approvalPendingStates = ['pendiente', 'por aprobar', 'revision', 'en revisión'];

        $totalEstudiantes = Estudiante::count();
        $nuevosEstudiantesMes = Estudiante::whereDate('created_at', '>=', $inicioMes)->count();

        $casosActivos = $this->countSolicitudesPorEstados($activeStates, true);
        $casosPendientes = $this->countSolicitudesPorEstados(['pendiente', 'en revisión'], true);

        $casosCerrados = $this->countSolicitudesPorEstados($closedStates);

        $casosCerradosMesQuery = Solicitud::query()->whereDate('updated_at', '>=', $inicioMes);
        $this->applyEstadoFilter($casosCerradosMesQuery, 'solicitudes.estado', $closedStates);
        $casosCerradosMes = $casosCerradosMesQuery->count();

        $pendientesAprobacionQuery = AjusteRazonable::query();
        $this->applyEstadoFilter($pendientesAprobacionQuery, 'ajuste_razonables.estado', $approvalPendingStates, true);
        $pendientesAprobacion = $pendientesAprobacionQuery->count();

        $casosPorCarrera = $this->casosPorCarrera($activeStates, $closedStates);
        $tiposDiscapacidad = $this->tiposDiscapacidad();
        $actividadReciente = $this->actividadReciente();

        $accionesRapidas = [
            [
                'label' => 'Gestión de Usuarios',
                'description' => 'Administra permisos y cuentas',
                'icon' => 'fa-users-gear',
                'variant' => 'primary',
                'route' => route('roles.index'),
            ],
            [
                'label' => 'Nuevo Estudiante',
                'description' => 'Registrar nuevo estudiante',
                'icon' => 'fa-user-plus',
                'variant' => 'success',
                'route' => route('estudiantes.create'),
            ],
            [
                'label' => 'Ver Reportes',
                'description' => 'Últimos casos y métricas',
                'icon' => 'fa-chart-column',
                'variant' => 'outline-primary',
                'route' => route('solicitudes.index'),
            ],
            [
                'label' => 'Configuración',
                'description' => 'Catálogos y carreras',
                'icon' => 'fa-sliders',
                'variant' => 'outline-secondary',
                'route' => route('carreras.index'),
            ],
        ];

        return view('admin.dashboard.index', [
            'hoy' => $hoy,
            'stats' => [
                'total_estudiantes' => $totalEstudiantes,
                'nuevos_estudiantes_mes' => $nuevosEstudiantesMes,
                'casos_activos' => $casosActivos,
                'casos_pendientes' => $casosPendientes,
                'casos_cerrados' => $casosCerrados,
                'casos_cerrados_mes' => $casosCerradosMes,
                'pendientes_aprobacion' => $pendientesAprobacion,
            ],
            'casosPorCarrera' => $casosPorCarrera,
            'tiposDiscapacidad' => $tiposDiscapacidad,
            'actividadReciente' => $actividadReciente,
            'accionesRapidas' => $accionesRapidas,
        ]);
    }

    private function countSolicitudesPorEstados(array $states, bool $includeNull = false): int
    {
        $query = Solicitud::query();
        $this->applyEstadoFilter($query, 'solicitudes.estado', $states, $includeNull);

        return $query->count();
    }

    private function applyEstadoFilter($query, string $column, array $states, bool $includeNull = false): void
    {
        $query->where(function ($inner) use ($column, $states, $includeNull) {
            $started = false;

            if ($includeNull) {
                $inner->whereNull($column);
                $started = true;
            }

            foreach ($states as $state) {
                $normalized = mb_strtolower($state ?? '', 'UTF-8');

                if ($normalized === '') {
                    continue;
                }

                if (! $started) {
                    $inner->whereRaw("LOWER({$column}) = ?", [$normalized]);
                    $started = true;
                } else {
                    $inner->orWhereRaw("LOWER({$column}) = ?", [$normalized]);
                }
            }
        });
    }

    private function casosPorCarrera(array $activeStates, array $closedStates): Collection
    {
        $activosCond = $this->estadoSqlCondition('solicitudes.estado', $activeStates, true);
        $cerradosCond = $this->estadoSqlCondition('solicitudes.estado', $closedStates);

        return Solicitud::query()
            ->select('carreras.nombre as carrera')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN {$activosCond} THEN 1 ELSE 0 END) as activos")
            ->selectRaw("SUM(CASE WHEN {$cerradosCond} THEN 1 ELSE 0 END) as cerrados")
            ->join('estudiantes', 'solicitudes.estudiante_id', '=', 'estudiantes.id')
            ->join('carreras', 'estudiantes.carrera_id', '=', 'carreras.id')
            ->groupBy('carreras.id', 'carreras.nombre')
            ->orderByDesc('total')
            ->limit(6)
            ->get()
            ->map(function ($row) {
                $row->activos = (int) $row->activos;
                $row->cerrados = (int) $row->cerrados;
                $row->total = (int) $row->total;
                return $row;
            });
    }

    private function tiposDiscapacidad(): Collection
    {
        if (Schema::hasColumn('estudiantes', 'tipo_discapacidad')) {
            $tipos = Estudiante::query()
                ->selectRaw("COALESCE(NULLIF(TRIM(tipo_discapacidad), ''), 'Sin clasificación') as tipo")
                ->selectRaw('COUNT(*) as total')
                ->groupBy('tipo')
                ->orderByDesc('total')
                ->limit(6)
                ->get();
        } else {
            $tipos = AjusteRazonable::query()
                ->selectRaw("COALESCE(NULLIF(TRIM(nombre), ''), 'Sin clasificación') as tipo")
                ->selectRaw('COUNT(*) as total')
                ->groupBy('tipo')
                ->orderByDesc('total')
                ->limit(6)
                ->get();
        }

        $total = max($tipos->sum('total'), 1);

        return $tipos->values()->map(function ($row, $index) use ($total) {
            return [
                'tipo' => mb_convert_case($row->tipo, MB_CASE_TITLE, 'UTF-8'),
                'total' => (int) $row->total,
                'porcentaje' => round(($row->total / $total) * 100),
                'color' => $this->chipColors()[$index % count($this->chipColors())],
            ];
        });
    }

    private function actividadReciente(): Collection
    {
        $actividades = collect();

        $solicitudes = Solicitud::with('estudiante')
            ->orderByDesc('updated_at')
            ->take(5)
            ->get()
            ->map(function (Solicitud $solicitud) {
                $fecha = $solicitud->updated_at ?? $solicitud->created_at;

                return [
                    'titulo' => $solicitud->estudiante ? "Caso de {$solicitud->estudiante->nombre}" : 'Caso sin estudiante',
                    'detalle' => $solicitud->descripcion ?? 'Sin descripción',
                    'estado' => $solicitud->estado ?? 'pendiente',
                    'fecha' => $fecha,
                ];
            });

        $ajustes = AjusteRazonable::with('estudiante')
            ->orderByDesc('updated_at')
            ->take(5)
            ->get()
            ->map(function (AjusteRazonable $ajuste) {
                $fecha = $ajuste->updated_at ?? $ajuste->created_at;

                return [
                    'titulo' => $ajuste->nombre,
                    'detalle' => $ajuste->estudiante ? "{$ajuste->estudiante->nombre} {$ajuste->estudiante->apellido}" : 'Sin estudiante asociado',
                    'estado' => $ajuste->estado ?? 'pendiente',
                    'fecha' => $fecha,
                ];
            });

        $entrevistas = Entrevista::with('solicitud.estudiante')
            ->orderByDesc('fecha')
            ->take(5)
            ->get()
            ->map(function (Entrevista $entrevista) {
                $estudiante = $entrevista->solicitud?->estudiante;
                $fecha = Carbon::parse($entrevista->fecha);

                return [
                    'titulo' => 'Entrevista agendada',
                    'detalle' => $estudiante ? "{$estudiante->nombre} {$estudiante->apellido}" : 'Caso por confirmar',
                    'estado' => 'programada',
                    'fecha' => $fecha,
                ];
            });

        return $actividades
            ->merge($solicitudes)
            ->merge($ajustes)
            ->merge($entrevistas)
            ->filter(fn ($item) => $item['fecha'] !== null)
            ->sortByDesc('fecha')
            ->take(6)
            ->values()
            ->map(function ($item) {
                $item['hace'] = $item['fecha']->diffForHumans();
                $item['estado_badge'] = $this->estadoBadge($item['estado']);
                $item['estado'] = ucfirst($item['estado']);

                return $item;
            });
    }

    private function estadoSqlCondition(string $column, array $states, bool $includeNull = false): string
    {
        $normalized = collect($states)
            ->filter(fn ($estado) => filled($estado))
            ->map(fn ($estado) => "'" . str_replace("'", "''", mb_strtolower($estado, 'UTF-8')) . "'")
            ->values();

        if ($normalized->isEmpty()) {
            return $includeNull ? "{$column} IS NULL" : '1 = 0';
        }

        $condition = "LOWER({$column}) IN ({$normalized->implode(', ')})";

        if ($includeNull) {
            $condition = "({$condition} OR {$column} IS NULL)";
        }

        return $condition;
    }

    private function estadoBadge(?string $estado): string
    {
        $estado = mb_strtolower($estado ?? '', 'UTF-8');

        return match (true) {
            str_contains($estado, 'pend') => 'warning',
            str_contains($estado, 'aprob') || str_contains($estado, 'cerr') => 'success',
            str_contains($estado, 'rech') => 'danger',
            default => 'info',
        };
    }

    private function chipColors(): array
    {
        return ['#2563eb', '#0ea5e9', '#22c55e', '#f97316', '#a855f7', '#ef4444'];
    }

    private function activeStates(): array
    {
        return ['pendiente', 'en proceso', 'en_proceso', 'activo', 'activa', 'abierto', 'seguimiento'];
    }

    private function closedStates(): array
    {
        return ['cerrado', 'cerrada', 'finalizado', 'finalizada', 'resuelto', 'resuelta', 'completado', 'completada'];
    }
}
