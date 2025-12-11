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
                'label' => 'Ajustes Activos',
                'value' => $this->countAdjustmentsMatching($studentsWithAdjustments, fn ($estado) => $this->esAjusteActivo($estado)),
                'helper' => 'En seguimiento',
                'icon' => 'fa-sliders',
            ],
            [
                'label' => 'Notificaciones',
                'value' => $this->countNotifications($user),
                'helper' => 'Sin leer',
                'icon' => 'fa-bell',
            ],
        ];

        return view('docente.dashboard', [
            'metrics' => $metrics,
            'studentAdjustments' => $studentsWithAdjustments->take(5)->all(),
            'recentNotifications' => $this->getRecentNotifications($user),
            'totalEstudiantes' => $totalEstudiantes,
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

        // Obtener todos los estudiantes de la carrera del docente
        // Asegurarse de que solo se muestren estudiantes de la misma carrera
        $estudiantes = \App\Models\Estudiante::with(['carrera', 'ajustesRazonables.solicitud'])
            ->where('carrera_id', $docente->carrera_id)
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->get();

        // Formatear los estudiantes con sus ajustes
        $students = $estudiantes->map(function ($estudiante) {
            $ajustes = $estudiante->ajustesRazonables;
            $ultimoAjuste = $ajustes->sortByDesc('updated_at')->first();

            return [
                'student_id' => $estudiante->id,
                'student' => trim($estudiante->nombre . ' ' . $estudiante->apellido),
                'rut' => $estudiante->rut ?? 'Sin RUT',
                'email' => $estudiante->email,
                'telefono' => $estudiante->telefono,
                'program' => $estudiante->carrera->nombre ?? 'Programa no asignado',
                'status' => $ultimoAjuste ? $this->resolveEstadoAjuste($ultimoAjuste->estado) : 'Sin ajustes',
                'last_update' => $ultimoAjuste 
                    ? optional($ultimoAjuste->updated_at)->format('d/m/Y') ?? 's/f'
                    : 'Sin actualizaciones',
                'applied_adjustments' => $ajustes->pluck('nombre')
                    ->filter()
                    ->take(4)
                    ->values()
                    ->all() ?: ['Sin ajustes registrados'],
                'adjustments' => $ajustes->map(function ($ajuste) {
                    // Usar la descripción del ajuste, no de la solicitud
                    $descripcion = $ajuste->descripcion;
                    return [
                        'id' => $ajuste->id,
                        'name' => $ajuste->nombre ?? 'Ajuste sin titulo',
                        'description' => $descripcion ?? 'No hay descripcion.',
                        'status' => $ajuste->estado ?? 'Activo',
                        'category' => $this->resolveCategoriaAjuste($ajuste->estado),
                        'fecha_solicitud' => optional($ajuste->fecha_solicitud)->format('d/m/Y') ?? 'No especificada',
                        'created_at' => optional($ajuste->created_at)->format('d/m/Y H:i') ?? 'No disponible',
                    ];
                })->values()->all(),
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
        // Asegurarse de que solo se muestren ajustes de estudiantes de la misma carrera
        return AjusteRazonable::query()
            ->with(['estudiante.carrera', 'solicitud'])
            ->whereHas('estudiante', function ($query) use ($docente) {
                $query->where('carrera_id', $docente->carrera_id);
            })
            ->latest('updated_at')
            ->get()
            ->groupBy('estudiante_id')
            ->filter(fn ($ajustes, $estudianteId) => $estudianteId)
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
                    'applied_adjustments' => $ajustes->pluck('nombre')
                        ->filter()
                        ->take(4)
                        ->values()
                        ->all() ?: ['Sin ajustes registrados'],
                    'adjustments' => $ajustes->map(function (AjusteRazonable $ajuste) {
                        // Usar la descripción del ajuste, no de la solicitud
                        $descripcion = $ajuste->descripcion;
                        return [
                            'id' => $ajuste->id,
                            'name' => $ajuste->nombre ?? 'Ajuste sin titulo',
                            'description' => $descripcion ?? 'Sin descripcion.',
                            'status' => $ajuste->estado ?? 'Activo',
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
            ->take(4)
            ->get()
            ->map(function (Notificacion $notification) {
                $data = $notification->data ?? [];

                return [
                    'title' => $data['title'] ?? ($data['subject'] ?? 'Actualizacion de caso'),
                    'message' => $data['message'] ?? ($data['body'] ?? 'Nueva actividad registrada.'),
                    'time' => optional($notification->created_at)->diffForHumans() ?? 'hace instantes',
                ];
            })
            ->values()
            ->all();
    }
}
