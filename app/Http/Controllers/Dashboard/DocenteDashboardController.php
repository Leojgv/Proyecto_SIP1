<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AjusteRazonable;
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

        $metrics = [
            [
                'label' => 'Mis Estudiantes',
                'value' => $studentsWithAdjustments->count(),
                'helper' => 'Con ajustes activos',
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
        ]);
    }

    public function students(Request $request)
    {
        $user = $request->user();
        $students = $this->collectStudentsWithAdjustments($user);

        $metrics = [
            [
                'label' => 'Total Estudiantes',
                'value' => $students->count(),
                'helper' => 'Bajo tu supervision',
                'icon' => 'fa-user-group',
            ],
            [
                'label' => 'Ajustes Activos',
                'value' => $this->countAdjustmentsMatching($students, fn ($estado) => $this->esAjusteActivo($estado)),
                'helper' => 'En seguimiento',
                'icon' => 'fa-sliders',
            ],
            [
                'label' => 'Pendientes',
                'value' => $this->countAdjustmentsMatching($students, fn ($estado) => str_contains(strtolower((string) $estado), 'pend')),
                'helper' => 'Por confirmar',
                'icon' => 'fa-clock',
            ],
            [
                'label' => 'Total Ajustes',
                'value' => $students->sum(fn ($student) => count($student['adjustments'])),
                'helper' => 'Registrados',
                'icon' => 'fa-list-check',
            ],
        ];

        return view('docente.students', [
            'metrics' => $metrics,
            'students' => $students,
        ]);
    }

    protected function collectStudentsWithAdjustments(?object $user): Collection
    {
        return AjusteRazonable::query()
            ->with(['estudiante.carrera', 'solicitud'])
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
                        $descripcion = optional($ajuste->solicitud)->descripcion;
                        return [
                            'id' => $ajuste->id,
                            'name' => $ajuste->nombre ?? 'Ajuste sin titulo',
                            'description' => $descripcion ?? 'Sin descripcion disponible.',
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
