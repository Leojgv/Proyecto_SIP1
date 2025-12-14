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

        // Contar ajustes aprobados y rechazados de todos los estudiantes en las carreras del director
        $ajustesAprobados = 0;
        $ajustesRechazados = 0;
        foreach ($carreras as $carrera) {
            foreach ($carrera->estudiantes as $estudiante) {
                $ajustesAprobados += $estudiante->ajustesRazonables->where('estado', 'Aprobado')->count();
                $ajustesRechazados += $estudiante->ajustesRazonables->where('estado', 'Rechazado')->count();
            }
        }

        $summaryStats = [
            [
                'label' => 'Pendientes por Revisar',
                'value' => $pendingCount,
                'subtext' => 'Listas para revisar',
                'icon' => 'fa-hourglass-half',
            ],
            [
                'label' => 'ajustes aprobados',
                'value' => $ajustesAprobados,
                'subtext' => 'Con ajustes vigentes',
                'icon' => 'fa-circle-check',
            ],
            [
                'label' => 'Los Ajustes Rechazados',
                'value' => $ajustesRechazados,
                'subtext' => 'No aprobados',
                'icon' => 'fa-times-circle',
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
        $pipelineSummary = $this->buildPipelineSummary($solicitudes, $studentIds, $carreras);
        $insights = $this->buildInsights($solicitudes, $coverage, $approvedCount, $ajustesAprobados, $ajustesRechazados);
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
                'ajustesRazonables',
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
                    'adjustments' => $solicitud->ajustesRazonables->map(function ($ajuste) {
                        return [
                            'nombre' => $ajuste->nombre ?? 'Ajuste sin nombre',
                            'descripcion' => $ajuste->descripcion ?? null,
                        ];
                    })->values()->all(),
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
            
            // Contar ajustes aprobados y rechazados por carrera
            $ajustesAprobados = $students->sum(fn ($estudiante) => $estudiante->ajustesRazonables->where('estado', 'Aprobado')->count());
            $ajustesRechazados = $students->sum(fn ($estudiante) => $estudiante->ajustesRazonables->where('estado', 'Rechazado')->count());
            
            // Obtener lista de ajustes únicos aplicados con fechas
            $adjustmentsList = $students
                ->flatMap(fn ($estudiante) => $estudiante->ajustesRazonables)
                ->unique('id')
                ->map(function ($ajuste) {
                    $fecha = $ajuste->updated_at ?? $ajuste->created_at;
                    $fechaFormateada = $fecha ? ($fecha instanceof Carbon ? $fecha->format('d/m/Y') : Carbon::parse($fecha)->format('d/m/Y')) : 'Sin fecha';
                    
                    return [
                        'nombre' => $ajuste->nombre ?? 'Ajuste sin título',
                        'descripcion' => $ajuste->descripcion ?? 'Sin descripción',
                        'estado' => $ajuste->estado ?? 'Pendiente',
                        'fecha' => $fechaFormateada,
                    ];
                })
                ->values()
                ->all();

            // Obtener lista de estudiantes con sus datos
            $studentsList = $students->map(function ($estudiante) {
                $rut = $estudiante->rut ?? '';
                $rutFormateado = $rut;
                if ($rut && strlen($rut) > 0) {
                    $rutLimpio = str_replace(['.', '-'], '', $rut);
                    if (strlen($rutLimpio) >= 7) {
                        $rutFormateado = substr($rutLimpio, 0, -1);
                        $rutFormateado = number_format((int)$rutFormateado, 0, '', '.');
                        $rutFormateado .= '-' . substr($rutLimpio, -1);
                    }
                }
                
                return [
                    'nombre' => trim(($estudiante->nombre ?? '') . ' ' . ($estudiante->apellido ?? '')),
                    'rut' => $rutFormateado ?: 'Sin RUT',
                    'email' => $estudiante->email ?? 'Sin email',
                    'telefono' => $estudiante->telefono ?? 'Sin teléfono',
                ];
            })->values()->all();

            // Obtener docentes de la carrera con cantidad de estudiantes
            $docentesList = $carrera->docentes->map(function ($docente) use ($studentCount) {
                $rut = $docente->rut ?? '';
                $rutFormateado = $rut;
                if ($rut && strlen($rut) > 0) {
                    $rutLimpio = str_replace(['.', '-'], '', $rut);
                    if (strlen($rutLimpio) >= 7) {
                        $rutFormateado = substr($rutLimpio, 0, -1);
                        $rutFormateado = number_format((int)$rutFormateado, 0, '', '.');
                        $rutFormateado .= '-' . substr($rutLimpio, -1);
                    }
                }
                
                $email = $docente->user->email ?? ($docente->email ?? 'Sin email');
                
                return [
                    'nombre' => trim(($docente->nombre ?? '') . ' ' . ($docente->apellido ?? '')),
                    'rut' => $rutFormateado ?: 'Sin RUT',
                    'email' => $email,
                    'cantidad_estudiantes' => $studentCount, // Todos los estudiantes de la carrera
                ];
            })->values()->all();

            $docenteCount = $carrera->docentes->count();

            return [
                'name' => $carrera->nombre ?? 'Carrera sin nombre',
                'jornada' => $carrera->jornada ?? 'Jornada no definida',
                'total_students' => $studentCount,
                'total_docentes' => $docenteCount,
                'students_list' => $studentsList,
                'docentes_list' => $docentesList,
                'with_adjustments' => $withAdjustments,
                'total_adjustments' => $totalAdjustments,
                'ajustes_aprobados' => $ajustesAprobados,
                'ajustes_rechazados' => $ajustesRechazados,
                'adjustments_list' => $adjustmentsList,
                'pending_cases' => $pending,
                'approved_cases' => $approved,
                'coverage' => $coverage,
            ];
        })->values()->all();
    }

    protected function buildPipelineSummary(Collection $solicitudes, Collection $studentIds, Collection $carreras): array
    {
        $pendingCount = $solicitudes->filter(fn ($solicitud) => $this->isPending($solicitud->estado))->count();
        
        // Contar ajustes aprobados y rechazados de todos los estudiantes en las carreras del director
        $ajustesAprobados = 0;
        $ajustesRechazados = 0;
        foreach ($carreras as $carrera) {
            foreach ($carrera->estudiantes as $estudiante) {
                $ajustesAprobados += $estudiante->ajustesRazonables->where('estado', 'Aprobado')->count();
                $ajustesRechazados += $estudiante->ajustesRazonables->where('estado', 'Rechazado')->count();
            }
        }

        return [
            [
                'label' => 'Solicitudes registradas',
                'value' => $solicitudes->count(),
                'description' => 'Casos vinculados a tus carreras.',
            ],
            [
                'label' => 'Pendientes por Revisar',
                'value' => $pendingCount,
                'description' => 'Esperan tu decision.',
            ],
            [
                'label' => 'Ajustes aprobados',
                'value' => $ajustesAprobados,
                'description' => 'Ajustes razonables vigentes.',
            ],
            [
                'label' => 'Ajustes rechazados',
                'value' => $ajustesRechazados,
                'description' => 'Ajustes no aprobados.',
            ],
        ];
    }

    protected function buildInsights(Collection $solicitudes, int $coverage, int $approvedCount, int $ajustesAprobados, int $ajustesRechazados): array
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

        // Información sobre ajustes aprobados
        if ($ajustesAprobados > 0) {
            $insights[] = [
                'icon' => 'fa-circle-check',
                'message' => "{$ajustesAprobados} ajuste(s) aprobado(s) vigente(s).",
            ];
        }

        // Información sobre ajustes rechazados
        if ($ajustesRechazados > 0) {
            $insights[] = [
                'icon' => 'fa-times-circle',
                'message' => "{$ajustesRechazados} ajuste(s) rechazado(s) en el periodo.",
            ];
        }
        
        // Agregar información sobre cobertura si es relevante
        if ($coverage > 0 && $coverage < 50) {
            $insights[] = [
                'icon' => 'fa-bullseye',
                'message' => "Cobertura actual {$coverage}%. Prioriza nuevas evaluaciones.",
            ];
        }

        if ($coverage >= 60) {
            $insights[] = [
                'icon' => 'fa-chart-line',
                'message' => "Cobertura de ajustes en {$coverage}% de los estudiantes.",
            ];
        } elseif ($coverage > 0 && $coverage >= 50) {
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

        $solicitudes = $this->solicitudesParaDirector($user->id, $studentIds)
            ->load([
                'estudiante.carrera',
                'ajustesRazonables',
                'entrevistas.asesor'
            ]);
        $careerStats = $this->buildCareerStats($carreras, $solicitudes);

        // Datos para las gráficas de pastel
        // Gráfica 1: Distribución de estudiantes por carrera
        $estudiantesPorCarrera = $carreras->map(function ($carrera) {
            return [
                'nombre' => $carrera->nombre ?? 'Sin nombre',
                'cantidad' => $carrera->estudiantes->count(),
            ];
        })->filter(fn ($item) => $item['cantidad'] > 0);

        // Gráfica 2: Distribución de ajustes aprobados por carrera
        $ajustesPorCarrera = $carreras->map(function ($carrera) {
            $totalAjustesAprobados = $carrera->estudiantes->sum(fn ($estudiante) => 
                $estudiante->ajustesRazonables->where('estado', 'Aprobado')->count()
            );
            return [
                'nombre' => $carrera->nombre ?? 'Sin nombre',
                'cantidad' => $totalAjustesAprobados,
            ];
        })->filter(fn ($item) => $item['cantidad'] > 0);

        // Obtener solo ajustes aprobados por la directora de carrera con sus estudiantes
        // Agrupar por nombre de ajuste pero mantener descripción y fecha
        $ajustesConEstudiantes = [];
        foreach ($carreras as $carrera) {
            foreach ($carrera->estudiantes as $estudiante) {
                // Filtrar solo ajustes aprobados
                $ajustesAprobados = $estudiante->ajustesRazonables->where('estado', 'Aprobado');
                foreach ($ajustesAprobados as $ajuste) {
                    $ajusteNombre = $ajuste->nombre ?? 'Ajuste sin título';
                    if (!isset($ajustesConEstudiantes[$ajusteNombre])) {
                        $ajustesConEstudiantes[$ajusteNombre] = [
                            'descripcion' => $ajuste->descripcion,
                            'fecha_aplicacion' => $ajuste->updated_at ?? $ajuste->fecha_solicitud ?? $ajuste->created_at,
                            'estudiantes' => []
                        ];
                    }
                    $ajustesConEstudiantes[$ajusteNombre]['estudiantes'][] = [
                        'nombre' => trim($estudiante->nombre . ' ' . $estudiante->apellido),
                        'carrera' => $carrera->nombre ?? 'Sin carrera',
                    ];
                }
            }
        }

        // Obtener ajustes rechazados por la directora de carrera con sus estudiantes
        // Agrupar por nombre de ajuste pero mantener descripción, fecha y motivo de rechazo
        $ajustesRechazadosConEstudiantes = [];
        foreach ($carreras as $carrera) {
            foreach ($carrera->estudiantes as $estudiante) {
                // Filtrar solo ajustes rechazados
                $ajustesRechazados = $estudiante->ajustesRazonables->where('estado', 'Rechazado');
                foreach ($ajustesRechazados as $ajuste) {
                    $ajusteNombre = $ajuste->nombre ?? 'Ajuste sin título';
                    if (!isset($ajustesRechazadosConEstudiantes[$ajusteNombre])) {
                        $ajustesRechazadosConEstudiantes[$ajusteNombre] = [
                            'descripcion' => $ajuste->descripcion,
                            'fecha_rechazo' => $ajuste->updated_at ?? $ajuste->fecha_solicitud ?? $ajuste->created_at,
                            'motivo_rechazo' => $ajuste->motivo_rechazo,
                            'estudiantes' => []
                        ];
                    }
                    $ajustesRechazadosConEstudiantes[$ajusteNombre]['estudiantes'][] = [
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
        
        // Estadísticas por tipo de ajuste (solo aprobados)
        // Recalcular desde ajustes aprobados únicos
        $ajustesAprobadosPorTipo = [];
        foreach ($carreras as $carrera) {
            foreach ($carrera->estudiantes as $estudiante) {
                $ajustesAprobados = $estudiante->ajustesRazonables->where('estado', 'Aprobado');
                foreach ($ajustesAprobados as $ajuste) {
                    $ajusteNombre = $ajuste->nombre ?? 'Ajuste sin título';
                    if (!isset($ajustesAprobadosPorTipo[$ajusteNombre])) {
                        $ajustesAprobadosPorTipo[$ajusteNombre] = 0;
                    }
                    $ajustesAprobadosPorTipo[$ajusteNombre]++;
                }
            }
        }
        
        $totalAjustesAprobados = array_sum($ajustesAprobadosPorTipo);
        
        $statsPorTipo = collect($ajustesAprobadosPorTipo)->map(function ($cantidad, $nombre) use ($totalAjustesAprobados) {
            return (object)[
                'nombre' => $nombre,
                'cantidad' => $cantidad,
                'porcentaje' => $totalAjustesAprobados > 0 ? round(($cantidad / $totalAjustesAprobados) * 100, 1) : 0,
            ];
        })->sortByDesc('cantidad')->values();

        // Obtener Asesora Técnica para mostrar su nombre
        $asesoraTecnica = \App\Models\User::withRole('Asesora Tecnica Pedagogica')->first();
        $nombreAsesoraTecnica = $asesoraTecnica 
            ? trim($asesoraTecnica->nombre . ' ' . $asesoraTecnica->apellido)
            : 'No asignado';

        // Agrupar casos por estudiante con sus ajustes aprobados
        $casosAgrupados = $solicitudes
            ->filter(fn($s) => $this->isApproved($s->estado))
            ->groupBy('estudiante_id')
            ->map(function ($solicitudesGrupo) use ($nombreAsesoraTecnica) {
                $primeraSolicitud = $solicitudesGrupo->first();
                $estudiante = $primeraSolicitud->estudiante;
                
                // Obtener todos los ajustes aprobados del estudiante en estas solicitudes
                $ajustesAprobados = $solicitudesGrupo
                    ->flatMap(fn($s) => $s->ajustesRazonables)
                    ->filter(fn($a) => $a->estado === 'Aprobado')
                    ->values();
                
                // Obtener responsable de entrevista (coordinadora)
                $entrevista = $primeraSolicitud->entrevistas->first();
                $responsableEntrevista = $entrevista?->asesor 
                    ? trim($entrevista->asesor->nombre . ' ' . $entrevista->asesor->apellido)
                    : 'No asignado';
                
                return [
                    'estudiante' => $estudiante,
                    'fecha' => $primeraSolicitud->fecha_solicitud ?? $primeraSolicitud->created_at,
                    'ajustes' => $ajustesAprobados,
                    'responsable_entrevista' => $responsableEntrevista,
                    'responsable_ajuste' => $nombreAsesoraTecnica, // Nombre de la Asesora Técnica
                ];
            })
            ->values();

        $pdf = Pdf::loadView('DirectorCarrera.reporte-pdf', [
            'carreras' => $carreras,
            'estudiantesPorCarrera' => $estudiantesPorCarrera,
            'ajustesPorCarrera' => $ajustesPorCarrera,
            'ajustesConEstudiantes' => $ajustesConEstudiantes,
            'ajustesRechazadosConEstudiantes' => $ajustesRechazadosConEstudiantes,
            'fechaGeneracion' => now()->format('d/m/Y H:i'),
            'totalSolicitudes' => $totalSolicitudes,
            'pendientesAprobacion' => $pendientesAprobacion,
            'porcentajeAprobacion' => $porcentajeAprobacion,
            'statsPorTipo' => $statsPorTipo,
            'solicitudes' => $solicitudes,
            'casosAgrupados' => $casosAgrupados,
        ]);

        return $pdf->download('reporte-carrera-' . now()->format('Y-m-d') . '.pdf');
    }

    protected function actionShortcuts(): array
    {
        return [
            [
                'label' => 'Generar reporte de carrera',
                'route' => route('director.reporte.pdf'),
                'icon' => 'fa-file-download',
                'variant' => 'danger',
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
