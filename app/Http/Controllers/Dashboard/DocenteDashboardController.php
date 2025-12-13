<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AjusteRazonable;
use App\Models\Estudiante;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DocenteDashboardController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        $studentsWithAdjustments = $this->collectStudentsWithAdjustments($user);
        
        // Obtener el total de estudiantes de la carrera del docente
        $totalEstudiantes = 0;
        $docente = $user->docente;
        if ($docente && $docente->carrera_id) {
            // Cargar la relación de carrera
            $docente->load('carrera');
            // Contar solo estudiantes de la misma carrera del docente
            $totalEstudiantes = Estudiante::where('carrera_id', $docente->carrera_id)->count();
        }

        $metrics = [
            [
                'label' => 'Mis Estudiantes',
                'value' => $totalEstudiantes,
                'helper' => 'Total en mi carrera',
                'icon' => 'fa-user-graduate',
            ],
            [
                'label' => 'Ajustes Aprobados',
                'value' => $this->countAdjustmentsMatching($studentsWithAdjustments, fn ($estado) => strtolower($estado) === 'aprobado'),
                'helper' => 'Aprobados por Dirección',
                'icon' => 'fa-sliders',
            ],
        ];

        // Calcular estadísticas de estudiantes que se unen al sistema (gráfico de montaña)
        $estudiantesPorMes = $this->calcularEstudiantesPorMes($user);

        return view('docente.dashboard', [
            'metrics' => $metrics,
            'studentAdjustments' => $studentsWithAdjustments->take(5)->all(),
            'totalEstudiantes' => $totalEstudiantes,
            'estudiantesPorMes' => $estudiantesPorMes,
        ]);
    }

    public function students(Request $request)
    {
        $user = $request->user();
        
        // Obtener la carrera del docente con eager loading
        $docente = $user->docente;
        
        if (!$docente || !$docente->carrera_id) {
            return view('docente.students', [
                'students' => collect([]),
                'carrera' => null,
            ]);
        }

        // Cargar la relación de carrera del docente
        $docente->load('carrera');

        // Obtener todos los estudiantes de la carrera del docente (con o sin ajustes)
        $estudiantes = \App\Models\Estudiante::with([
            'carrera', 
            'ajustesRazonables' => function($query) {
                $query->where('estado', 'Aprobado')
                      ->whereHas('solicitud', function($q) {
                          $q->where('estado', 'Aprobado');
                      });
            },
            'ajustesRazonables.solicitud'
        ])
            ->where('carrera_id', $docente->carrera_id)
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->get();

        // Formatear los estudiantes con sus ajustes (incluyendo los que no tienen ajustes)
        $students = $estudiantes->map(function ($estudiante) {
            $ajustes = $estudiante->ajustesRazonables->where('estado', 'Aprobado');
            $ultimoAjuste = $ajustes->sortByDesc('updated_at')->first();

            return [
                'student_id' => $estudiante->id,
                'student' => trim($estudiante->nombre . ' ' . $estudiante->apellido),
                'rut' => $estudiante->rut ?? 'Sin RUT',
                'email' => $estudiante->email,
                'telefono' => $estudiante->telefono,
                'program' => $estudiante->carrera->nombre ?? 'Programa no asignado',
                'status' => $ultimoAjuste ? 'Aprobado' : 'Sin ajustes',
                'last_update' => $ultimoAjuste 
                    ? optional($ultimoAjuste->updated_at)->format('d/m/Y') ?? 's/f'
                    : optional($estudiante->updated_at)->format('d/m/Y') ?? 's/f',
                'applied_adjustments' => $ajustes->count() > 0
                    ? $ajustes->pluck('nombre')->filter()->values()->all()
                    : [],
                'adjustments' => $ajustes->count() > 0
                    ? $ajustes->map(function ($ajuste) {
                        // Usar la descripción del ajuste, no de la solicitud
                        $descripcion = $ajuste->descripcion;
                        return [
                            'id' => $ajuste->id,
                            'name' => $ajuste->nombre ?? 'Ajuste sin titulo',
                            'description' => $descripcion ?? 'No hay descripción disponible para este ajuste razonable.',
                            'status' => $ajuste->estado ?? 'Aprobado',
                            'category' => $this->resolveCategoriaAjuste($ajuste->estado),
                            'fecha_solicitud' => optional($ajuste->fecha_solicitud)->format('d/m/Y') ?? 'No especificada',
                            'created_at' => optional($ajuste->created_at)->format('d/m/Y H:i') ?? 'No disponible',
                        ];
                    })->values()->all()
                    : [],
            ];
        });

        return view('docente.students', [
            'students' => $students,
            'carrera' => $docente->carrera,
        ]);
    }

    protected function collectStudentsWithAdjustments(?object $user): Collection
    {
        // Obtener la carrera del docente con eager loading
        $docente = $user->docente;
        
        if (!$docente || !$docente->carrera_id) {
            return collect([]);
        }

        // Cargar la relación de carrera del docente
        $docente->load('carrera');

        // Filtrar ajustes por estudiantes de la carrera del docente
        // Solo mostrar ajustes APROBADOS (que han sido aprobados por el Director de Carrera)
        return AjusteRazonable::query()
            ->with(['estudiante.carrera', 'solicitud'])
            ->whereHas('estudiante', function ($query) use ($docente) {
                $query->where('carrera_id', $docente->carrera_id);
            })
            ->where('estado', 'Aprobado') // Solo ajustes aprobados por el Director
            ->whereHas('solicitud', function ($query) {
                $query->where('estado', 'Aprobado'); // Solo de solicitudes aprobadas
            })
            ->latest('updated_at')
            ->get()
            ->groupBy('estudiante_id')
            ->filter(fn ($ajustes, $estudianteId) => $estudianteId && $ajustes->count() > 0)
            ->map(function ($ajustes) {
                $first = $ajustes->first();
                $estudiante = $first?->estudiante;

                return [
                    'student_id' => $estudiante->id ?? null,
                    'student' => trim(($estudiante->nombre ?? 'Estudiante') . ' ' . ($estudiante->apellido ?? '')),
                    'rut' => $estudiante->rut ?? 'Sin RUT',
                    'program' => optional(optional($estudiante)->carrera)->nombre ?? 'Programa no asignado',
                    'status' => $this->resolveEstadoAjuste($first?->estado),
                    'last_update' => optional(
                        $ajustes->max('updated_at') ?? $first?->updated_at ?? $first?->created_at
                    )?->format('Y-m-d') ?? 's/f',
                    'applied_adjustments' => $ajustes
                        ->where('estado', 'Aprobado') // Solo ajustes aprobados
                        ->pluck('nombre')
                        ->filter()
                        ->values()
                        ->all() ?: ['Sin ajustes aprobados'],
                    'adjustments' => $ajustes
                        ->where('estado', 'Aprobado') // Solo ajustes aprobados
                        ->map(function (AjusteRazonable $ajuste) {
                            // Usar la descripción del ajuste, no de la solicitud
                            $descripcion = $ajuste->descripcion;
                            return [
                                'id' => $ajuste->id,
                                'name' => $ajuste->nombre ?? 'Ajuste sin titulo',
                                'description' => $descripcion ?? 'No hay descripción disponible para este ajuste razonable.',
                                'status' => $ajuste->estado ?? 'Aprobado',
                                'category' => $this->resolveCategoriaAjuste($ajuste->estado),
                                'fecha_solicitud' => optional($ajuste->fecha_solicitud)->format('d/m/Y') ?? 'No especificada',
                                'created_at' => optional($ajuste->created_at)->format('d/m/Y H:i') ?? 'No disponible',
                            ];
                        })->values()->all(),
                ];
            })
            ->values();
    }

    protected function esAjusteActivo(?string $estado, $fechaTermino = null): bool
    {
        $normalized = strtolower((string) $estado);

        if ($fechaTermino && Carbon::parse($fechaTermino)->isPast()) {
            return false;
        }

        return ! str_contains($normalized, 'complet') && ! str_contains($normalized, 'cerr');
    }

    protected function resolveEstadoAjuste(?string $estado): string
    {
        $normalized = strtolower((string) $estado);

        return match (true) {
            str_contains($normalized, 'pend') => 'Pendiente',
            str_contains($normalized, 'cerr'),
            str_contains($normalized, 'comp') => 'Finalizado',
            default => 'Activo',
        };
    }

    protected function resolveCategoriaAjuste(?string $estado): string
    {
        $normalized = strtolower((string) $estado);

        return match (true) {
            str_contains($normalized, 'visual') => 'Visual',
            str_contains($normalized, 'audit') => 'Auditivo',
            str_contains($normalized, 'cognitiv') => 'Cognitivo',
            str_contains($normalized, 'pend') => 'Pendiente',
            default => 'General',
        };
    }

    protected function countAdjustmentsMatching(Collection $students, callable $callback): int
    {
        return $students->sum(function ($student) use ($callback) {
            return collect($student['adjustments'])->filter(fn ($adj) => $callback($adj['status']))->count();
        });
    }

    protected function countNotifications(?object $user): int
    {
        return Notificacion::query()
            ->when($user, fn ($query) => $query
                ->where('notifiable_type', get_class($user))
                ->where('notifiable_id', $user->id))
            ->whereNull('read_at')
            ->count();
    }

    protected function getRecentNotifications(?object $user): array
    {
        return Notificacion::query()
            ->when($user, fn ($query) => $query
                ->where('notifiable_type', get_class($user))
                ->where('notifiable_id', $user->id))
            ->latest('created_at')
            ->take(5)
            ->get()
            ->map(function (Notificacion $notification) {
                $data = $notification->data ?? [];

                return [
                    'id' => $notification->id,
                    'title' => $data['titulo'] ?? ($data['title'] ?? ($data['subject'] ?? 'Notificación')),
                    'message' => $data['mensaje'] ?? ($data['message'] ?? ($data['body'] ?? 'Nueva actualización disponible.')),
                    'url' => $data['url'] ?? null,
                    'button_text' => $data['texto_boton'] ?? ($data['textoBoton'] ?? null),
                    'time' => optional($notification->created_at)->diffForHumans() ?? 'hace instantes',
                    'read_at' => $notification->read_at,
                ];
            })
            ->values()
            ->all();
    }

    protected function calcularEstudiantesPorMes(?object $user): array
    {
        $docente = $user->docente;
        
        if (!$docente || !$docente->carrera_id) {
            return [
                'labels' => [],
                'datos' => [],
            ];
        }

        // Obtener los últimos 12 meses
        $meses = collect();
        $fechaInicio = Carbon::now()->subMonths(11)->startOfMonth();
        
        for ($i = 0; $i < 12; $i++) {
            $fecha = $fechaInicio->copy()->addMonths($i);
            $meses->push([
                'fecha' => $fecha,
                'inicio' => $fecha->copy()->startOfMonth(),
                'fin' => $fecha->copy()->endOfMonth(),
                'label' => $fecha->format('M Y'),
                'label_corto' => $fecha->format('M'),
            ]);
        }

        // Calcular estudiantes por mes (todos los estudiantes, con o sin ajustes)
        $datos = $meses->map(function ($mes) use ($docente) {
            $estudiantesMes = Estudiante::where('carrera_id', $docente->carrera_id)
                ->whereBetween('created_at', [$mes['inicio'], $mes['fin']])
                ->count();

            return [
                'fecha' => $mes['label'],
                'cantidad' => $estudiantesMes,
            ];
        });

        return [
            'labels' => $datos->pluck('fecha')->all(),
            'datos' => $datos->pluck('cantidad')->all(),
        ];
    }
}
