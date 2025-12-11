<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AjusteRazonable;
use App\Models\Carrera;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;

class DirectorCarreraDashboardController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        $carreras = Carrera::with(['estudiantes.ajustesRazonables'])
            ->where('director_id', $user->id)
            ->get();

        $studentIds = $carreras
            ->flatMap(fn ($carrera) => $carrera->estudiantes->pluck('id'))
            ->filter()
            ->unique()
            ->values();

        $solicitudes = $this->solicitudesParaDirector($user->id, $studentIds);

        $totalStudents = $studentIds->count();
        $studentsWithAdjustments = $this->countStudentsWithAdjustments($carreras);
        $pendingCount = $solicitudes->filter(fn ($solicitud) => $this->isPending($solicitud->estado))->count();
        $approvedCount = $solicitudes->filter(fn ($solicitud) => $this->isApproved($solicitud->estado))->count();
        $coverage = $totalStudents > 0 ? (int) round(($studentsWithAdjustments / $totalStudents) * 100) : 0;

        $summaryStats = [
            [
                'label' => 'Pendientes de aprobacion',
                'value' => $pendingCount,
                'subtext' => 'Listas para revisar',
                'icon' => 'fa-hourglass-half',
            ],
            [
                'label' => 'Casos aprobados',
                'value' => $approvedCount,
                'subtext' => 'Con ajustes vigentes',
                'icon' => 'fa-circle-check',
            ],
            [
                'label' => 'Estudiantes totales',
                'value' => $totalStudents,
                'subtext' => 'En tus carreras',
                'icon' => 'fa-user-graduate',
            ],
            [
                'label' => 'Cobertura con ajustes',
                'value' => $coverage . '%',
                'subtext' => 'Con apoyos activos',
                'icon' => 'fa-chart-pie',
            ],
        ];

        $pendingCases = $this->formatPendingCases($solicitudes);
        $careerStats = $this->buildCareerStats($carreras, $solicitudes);
        $pipelineSummary = $this->buildPipelineSummary($solicitudes, $studentIds);
        $insights = $this->buildInsights($solicitudes, $coverage, $approvedCount);
        $actionShortcuts = $this->actionShortcuts();

        return view('DirectorCarrera.dashboard', compact(
            'summaryStats',
            'pendingCases',
            'careerStats',
            'pipelineSummary',
            'insights',
            'actionShortcuts',
            'totalStudents'
        ));
    }

    protected function solicitudesParaDirector(int $directorId, Collection $studentIds): Collection
    {
        return Solicitud::with([
                'estudiante.carrera',
                'asesor',
                'ajustesRazonables',
            ])
            ->where(function ($query) use ($directorId, $studentIds) {
                $query->where('director_id', $directorId);

                if ($studentIds->isNotEmpty()) {
                    $query->orWhereIn('estudiante_id', $studentIds);
                }
            })
            ->latest('created_at')
            ->get();
    }

    protected function countStudentsWithAdjustments(Collection $carreras): int
    {
        return $carreras
            ->flatMap(fn ($carrera) => $carrera->estudiantes)
            ->filter(fn ($estudiante) => $estudiante->ajustesRazonables->isNotEmpty())
            ->unique('id')
            ->count();
    }

    protected function formatPendingCases(Collection $solicitudes): array
    {
        return $solicitudes
            ->filter(fn ($solicitud) => $this->isPending($solicitud->estado))
            ->take(4)
            ->map(function ($solicitud) {
                $submittedAt = $solicitud->fecha_solicitud ?? $solicitud->created_at;
                $priorityLevel = $this->resolvePriorityLevel($solicitud->estado, $submittedAt);

                return [
                    'id' => $solicitud->id,
                    'student' => trim(optional($solicitud->estudiante)->nombre . ' ' . optional($solicitud->estudiante)->apellido) ?: 'Estudiante sin nombre',
                    'program' => optional(optional($solicitud->estudiante)->carrera)->nombre ?? 'Carrera no registrada',
                    'requested_by' => $solicitud->asesor?->nombre_completo ?? $solicitud->asesor?->name ?? 'Equipo de inclusion',
                    'support_focus' => $solicitud->descripcion ?? 'Sin descripcion registrada.',
                    'adjustments' => $solicitud->ajustesRazonables->pluck('nombre')->filter()->take(3)->values()->all(),
                    'status' => $this->normalizeStatus($solicitud->estado),
                    'submitted_at' => $submittedAt instanceof Carbon ? $submittedAt->format('d/m/Y') : 'Sin fecha',
                    'priority' => $this->priorityLabel($priorityLevel),
                    'priority_level' => $priorityLevel,
                    'approve_url' => route('director.casos.approve', $solicitud),
                    'reject_url' => route('director.casos.show', ['solicitud' => $solicitud, 'rechazar' => 1]),
                    'detail_url' => route('director.casos.show', $solicitud),
                ];
            })
            ->values()
            ->all();
    }

    protected function buildCareerStats(Collection $carreras, Collection $solicitudes): array
    {
        return $carreras->map(function ($carrera) use ($solicitudes) {
            $students = $carrera->estudiantes;
            $studentCount = $students->count();
            $careerCases = $solicitudes->filter(fn ($solicitud) => optional($solicitud->estudiante)->carrera_id === $carrera->id);
            $withAdjustments = $students->filter(fn ($estudiante) => $estudiante->ajustesRazonables->isNotEmpty())->count();
            $pending = $careerCases->filter(fn ($solicitud) => $this->isPending($solicitud->estado))->count();
            $approved = $careerCases->filter(fn ($solicitud) => $this->isApproved($solicitud->estado))->count();
            $coverage = $studentCount > 0 ? (int) round(($withAdjustments / $studentCount) * 100) : 0;
            
            // Contar total de ajustes aplicados en esta carrera
            $totalAdjustments = $students->sum(fn ($estudiante) => $estudiante->ajustesRazonables->count());
            
            // Obtener lista de ajustes únicos aplicados
            $adjustmentsList = $students
                ->flatMap(fn ($estudiante) => $estudiante->ajustesRazonables)
                ->unique('id')
                ->map(fn ($ajuste) => [
                    'nombre' => $ajuste->nombre ?? 'Ajuste sin título',
                    'descripcion' => $ajuste->descripcion ?? 'Sin descripción',
                    'estado' => $ajuste->estado ?? 'Pendiente',
                ])
                ->values()
                ->all();

            return [
                'name' => $carrera->nombre ?? 'Carrera sin nombre',
                'jornada' => $carrera->jornada ?? 'Jornada no definida',
                'total_students' => $studentCount,
                'with_adjustments' => $withAdjustments,
                'total_adjustments' => $totalAdjustments,
                'adjustments_list' => $adjustmentsList,
                'pending_cases' => $pending,
                'approved_cases' => $approved,
                'coverage' => $coverage,
            ];
        })->values()->all();
    }

    protected function buildPipelineSummary(Collection $solicitudes, Collection $studentIds): array
    {
        $pendingCount = $solicitudes->filter(fn ($solicitud) => $this->isPending($solicitud->estado))->count();
        $ajustesCount = $studentIds->isEmpty()
            ? 0
            : AjusteRazonable::whereIn('estudiante_id', $studentIds)->count();

        return [
            [
                'label' => 'Solicitudes registradas',
                'value' => $solicitudes->count(),
                'description' => 'Casos vinculados a tus carreras.',
            ],
            [
                'label' => 'Requieren aprobacion',
                'value' => $pendingCount,
                'description' => 'Esperan tu decision.',
            ],
            [
                'label' => 'Ajustes formulados',
                'value' => $ajustesCount,
                'description' => 'Apoyos en ejecucion.',
            ],
        ];
    }

    protected function buildInsights(Collection $solicitudes, int $coverage, int $approvedCount): array
    {
        $insights = [];
        $overdue = $this->countOverdueCases($solicitudes);

        if ($overdue > 0) {
            $insights[] = [
                'icon' => 'fa-triangle-exclamation',
                'message' => "{$overdue} caso(s) superan los 7 dias sin decision.",
            ];
        }

        $approvedThisMonth = $this->countApprovedThisMonth($solicitudes);
        if ($approvedThisMonth > 0) {
            $insights[] = [
                'icon' => 'fa-circle-check',
                'message' => "{$approvedThisMonth} caso(s) aprobados este mes.",
            ];
        } elseif ($approvedCount === 0) {
            $insights[] = [
                'icon' => 'fa-inbox',
                'message' => 'Aun no apruebas casos en el periodo actual.',
            ];
        }

        if ($coverage >= 60) {
            $insights[] = [
                'icon' => 'fa-chart-line',
                'message' => "Cobertura de ajustes en {$coverage}% de los estudiantes.",
            ];
        } elseif ($coverage > 0) {
            $insights[] = [
                'icon' => 'fa-bullseye',
                'message' => "Cobertura actual {$coverage}%. Prioriza nuevas evaluaciones.",
            ];
        }

        return $insights;
    }

    public function generarReportePDF(Request $request)
    {
        $user = $request->user();

        $carreras = Carrera::with(['estudiantes.ajustesRazonables'])
            ->where('director_id', $user->id)
            ->get();

        $studentIds = $carreras
            ->flatMap(fn ($carrera) => $carrera->estudiantes->pluck('id'))
            ->filter()
            ->unique()
            ->values();

        $solicitudes = $this->solicitudesParaDirector($user->id, $studentIds);
        $careerStats = $this->buildCareerStats($carreras, $solicitudes);

        // Datos para las gráficas de pastel
        // Gráfica 1: Distribución de estudiantes por carrera
        $estudiantesPorCarrera = $carreras->map(function ($carrera) {
            return [
                'nombre' => $carrera->nombre ?? 'Sin nombre',
                'cantidad' => $carrera->estudiantes->count(),
            ];
        })->filter(fn ($item) => $item['cantidad'] > 0);

        // Gráfica 2: Distribución de ajustes por carrera
        $ajustesPorCarrera = $carreras->map(function ($carrera) {
            $totalAjustes = $carrera->estudiantes->sum(fn ($estudiante) => $estudiante->ajustesRazonables->count());
            return [
                'nombre' => $carrera->nombre ?? 'Sin nombre',
                'cantidad' => $totalAjustes,
            ];
        })->filter(fn ($item) => $item['cantidad'] > 0);

        // Obtener todos los ajustes razonables únicos con sus estudiantes
        $ajustesConEstudiantes = [];
        foreach ($carreras as $carrera) {
            foreach ($carrera->estudiantes as $estudiante) {
                foreach ($estudiante->ajustesRazonables as $ajuste) {
                    $ajusteNombre = $ajuste->nombre ?? 'Ajuste sin título';
                    if (!isset($ajustesConEstudiantes[$ajusteNombre])) {
                        $ajustesConEstudiantes[$ajusteNombre] = [];
                    }
                    $ajustesConEstudiantes[$ajusteNombre][] = [
                        'nombre' => trim($estudiante->nombre . ' ' . $estudiante->apellido),
                        'carrera' => $carrera->nombre ?? 'Sin carrera',
                    ];
                }
            }
        }

        // Calcular KPIs
        $totalSolicitudes = $solicitudes->count();
        $pendientesAprobacion = $solicitudes->filter(fn ($solicitud) => $this->isPending($solicitud->estado))->count();
        $aprobadas = $solicitudes->filter(fn ($solicitud) => $this->isApproved($solicitud->estado))->count();
        $porcentajeAprobacion = $totalSolicitudes > 0 ? round(($aprobadas / $totalSolicitudes) * 100, 1) : 0;
        
        // Estadísticas por tipo de ajuste
        $statsPorTipo = collect($ajustesConEstudiantes)->map(function ($estudiantes, $nombre) {
            return (object)[
                'nombre' => $nombre,
                'cantidad' => count($estudiantes),
            ];
        })->map(function ($tipo) use ($ajustesConEstudiantes) {
            $total = collect($ajustesConEstudiantes)->sum(fn($est) => count($est));
            $tipo->porcentaje = $total > 0 ? round(($tipo->cantidad / $total) * 100, 1) : 0;
            return $tipo;
        })->sortByDesc('cantidad')->values();

        $pdf = Pdf::loadView('DirectorCarrera.reporte-pdf', [
            'carreras' => $carreras,
            'estudiantesPorCarrera' => $estudiantesPorCarrera,
            'ajustesPorCarrera' => $ajustesPorCarrera,
            'ajustesConEstudiantes' => $ajustesConEstudiantes,
            'fechaGeneracion' => now()->format('d/m/Y H:i'),
            'totalSolicitudes' => $totalSolicitudes,
            'pendientesAprobacion' => $pendientesAprobacion,
            'porcentajeAprobacion' => $porcentajeAprobacion,
            'statsPorTipo' => $statsPorTipo,
            'solicitudes' => $solicitudes,
        ]);

        return $pdf->download('reporte-carrera-' . now()->format('Y-m-d') . '.pdf');
    }

    protected function actionShortcuts(): array
    {
        return [
            [
                'label' => 'Generar reporte de carrera',
                'route' => route('director.reporte.pdf'),
                'icon' => 'fa-file-arrow-down',
                'variant' => 'danger',
            ],
            [
                'label' => 'Ver metricas detalladas',
                'route' => route('ajustes-razonables.index'),
                'icon' => 'fa-chart-simple',
                'variant' => 'outline-danger',
            ],
        ];
    }

    protected function countOverdueCases(Collection $solicitudes, int $thresholdDays = 7): int
    {
        $limitDate = Carbon::now()->subDays($thresholdDays);

        return $solicitudes->filter(function ($solicitud) use ($limitDate) {
            if (! $this->isPending($solicitud->estado)) {
                return false;
            }

            $date = $solicitud->fecha_solicitud ?? $solicitud->created_at;

            return $date instanceof Carbon && $date->lessThanOrEqualTo($limitDate);
        })->count();
    }

    protected function countApprovedThisMonth(Collection $solicitudes): int
    {
        $now = Carbon::now();

        return $solicitudes->filter(function ($solicitud) use ($now) {
            if (! $this->isApproved($solicitud->estado)) {
                return false;
            }

            $reference = $solicitud->updated_at ?? $solicitud->fecha_solicitud ?? $solicitud->created_at;

            return $reference instanceof Carbon && $reference->isSameMonth($now);
        })->count();
    }

    protected function isPending(?string $estado): bool
    {
        $normalized = strtolower((string) $estado);

        return $normalized === '' ||
            str_contains($normalized, 'pend') ||
            str_contains($normalized, 'rev') ||
            str_contains($normalized, 'espera') ||
            str_contains($normalized, 'enviado');
    }

    protected function isApproved(?string $estado): bool
    {
        $normalized = strtolower((string) $estado);

        return str_contains($normalized, 'aprob') ||
            str_contains($normalized, 'acept');
    }

    protected function normalizeStatus(?string $estado): string
    {
        $normalized = strtolower((string) $estado);

        return match (true) {
            $normalized === '' => 'Sin estado',
            str_contains($normalized, 'pend') => 'Pendiente',
            str_contains($normalized, 'rev') => 'En revision',
            str_contains($normalized, 'aprob') => 'Aprobado',
            str_contains($normalized, 'rech') => 'Rechazado',
            default => ucfirst($estado),
        };
    }

    protected function resolvePriorityLevel(?string $estado, ?Carbon $fecha): string
    {
        $age = $fecha ? $fecha->diffInDays(Carbon::now()) : 0;

        return match (true) {
            $this->isPending($estado) && $age >= 10 => 'high',
            $this->isPending($estado) && $age >= 5 => 'medium',
            default => 'low',
        };
    }

    protected function priorityLabel(string $priorityLevel): string
    {
        return match ($priorityLevel) {
            'high' => 'Prioridad Alta',
            'medium' => 'Prioridad Media',
            default => 'Prioridad Baja',
        };
    }
}
