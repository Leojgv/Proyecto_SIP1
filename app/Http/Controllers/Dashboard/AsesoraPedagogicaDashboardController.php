<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AjusteRazonable;
use App\Models\Solicitud;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AsesoraPedagogicaDashboardController extends Controller
{
    private const REVIEW_STATES = [
        'Pendiente de preaprobación',
        'Pendiente',
        'Pendiente Revision',
        'Requiere ajuste',
        'Requiere ajustes',
        'En revision',
    ];

    private const READY_STATES = [
        'Listo para Enviar',
        'Listo para Direccion',
        'Listo para Derivar',
        'Autorizado',
        'Autorizada',
        'Listo para envio',
    ];

    private const SENT_STATES = [
        'Enviado a Direccion',
        'Derivado',
        'Enviado',
    ];

    private const PROCESSED_STATES = [
        'Autorizado',
        'Autorizada',
        'Completado',
        'Completada',
        'Cerrado',
        'Cerrada',
        'Procesado',
    ];

    private const AUTHORIZED_ADJUSTMENT_STATES = [
        'Autorizado',
        'Autorizada',
        'Listo para Enviar',
        'Listo para Direccion',
        'Enviado a Direccion',
    ];

    public function show(Request $request)
    {
        $user = $request->user();

        $solicitudesBase = Solicitud::query()
            ->with(['estudiante.carrera'])
            ->when($user, fn ($query) => $query->where('asesor_id', $user->id));

        $metrics = [
            [
                'label' => 'Pendientes Revision',
                'value' => $this->countByStates(clone $solicitudesBase, self::REVIEW_STATES, true),
                'helper' => 'Casos por revisar',
                'icon' => 'fa-list-check',
            ],
            [
                'label' => 'Listos para Enviar',
                'value' => $this->countByStates(clone $solicitudesBase, self::READY_STATES),
                'helper' => 'A Direccion',
                'icon' => 'fa-paper-plane',
            ],
            [
                'label' => 'Enviados',
                'value' => $this->countByStates(clone $solicitudesBase, self::SENT_STATES),
                'helper' => 'Este mes',
                'icon' => 'fa-envelope-open-text',
            ],
            [
                'label' => 'Total Procesados',
                'value' => $this->countByStates(clone $solicitudesBase, self::PROCESSED_STATES),
                'helper' => 'Este mes',
                'icon' => 'fa-chart-column',
            ],
        ];

        // Obtener casos en preaprobación para el dashboard
        $casesForReview = $this->buildCasesForReview(clone $solicitudesBase, 4);

        $authorizedCases = AjusteRazonable::query()
            ->with(['estudiante.carrera', 'solicitud'])
            ->whereHas('solicitud', fn ($query) => $query
                ->when($user, fn ($sub) => $sub->where('asesor_id', $user->id)))
            ->whereIn('estado', self::AUTHORIZED_ADJUSTMENT_STATES)
            ->latest('updated_at')
            ->take(3)
            ->get()
            ->map(function (AjusteRazonable $ajuste) {
                $estudiante = $ajuste->estudiante;
                $nombreEstudiante = trim(($estudiante->nombre ?? '') . ' ' . ($estudiante->apellido ?? '')) ?: 'Estudiante sin nombre';
                $programa = optional(optional($estudiante)->carrera)->nombre ?? 'Programa no asignado';

                return [
                    'student' => $nombreEstudiante,
                    'program' => $programa,
                    'status' => $ajuste->estado ?? 'En seguimiento',
                    'authorized_at' => optional($ajuste->updated_at ?? $ajuste->fecha_solicitud)
                        ?->format('Y-m-d') ?? 's/f',
                    'follow_up' => 'Enviado a Direccion',
                ];
            })
            ->values()
            ->all();

        return view('asesora pedagogica.dashboard', [
            'metrics' => $metrics,
            'casesForReview' => $casesForReview,
            'authorizedCases' => $authorizedCases,
        ]);
    }

    protected function buildCasesForReview(Builder $query, int $limit): array
    {
        // Priorizar casos en "Pendiente de preaprobación"
        return $query
            ->where('estado', 'Pendiente de preaprobación')
            ->orWhere(function ($builder) {
                $builder->whereNull('estado');
                foreach (self::REVIEW_STATES as $state) {
                    if ($state !== 'Pendiente de preaprobación') {
                        $builder->orWhere('estado', $state);
                    }
                }
            })
            ->latest('fecha_solicitud')
            ->take($limit)
            ->get()
            ->map(function (Solicitud $solicitud) {
                $estudiante = $solicitud->estudiante;
                $nombreEstudiante = trim(($estudiante->nombre ?? '') . ' ' . ($estudiante->apellido ?? '')) ?: 'Estudiante sin nombre';
                $programa = optional(optional($estudiante)->carrera)->nombre ?? 'Programa no asignado';

                return [
                    'case_id' => $solicitud->id,
                    'student' => $nombreEstudiante,
                    'program' => $programa,
                    'priority' => $this->inferirPrioridad($solicitud->estado),
                    'status' => $solicitud->estado ?? 'Pendiente',
                    'proposed_adjustment' => $solicitud->descripcion ?? 'Sin descripcion registrada.',
                    'received_at' => optional($solicitud->fecha_solicitud ?? $solicitud->created_at)
                        ?->format('Y-m-d') ?? 's/f',
                    'send_url' => $solicitud->estado === 'Pendiente de preaprobación' 
                        ? route('asesora-pedagogica.casos.enviar-director', $solicitud)
                        : null,
                    'detail_url' => route('asesora-pedagogica.casos.show', $solicitud),
                ];
            })
            ->values()
            ->all();
    }

    protected function countByStates(Builder $query, array $states, bool $includeNull = false): int
    {
        $query->where(function ($builder) use ($states, $includeNull) {
            if ($includeNull) {
                $builder->whereNull('estado');
                if (! empty($states)) {
                    $builder->orWhereIn('estado', $states);
                }

                return;
            }

            if (! empty($states)) {
                $builder->whereIn('estado', $states);
            }
        });

        return $query->count();
    }

    protected function inferirPrioridad(?string $estado): string
    {
        $normalized = strtolower((string) $estado);

        return match (true) {
            str_contains($normalized, 'revision'),
            str_contains($normalized, 'proceso') => 'Media',
            str_contains($normalized, 'autoriz'),
            str_contains($normalized, 'enviado'),
            str_contains($normalized, 'cerrado'),
            str_contains($normalized, 'complet') => 'Baja',
            default => 'Alta',
        };
    }
}
