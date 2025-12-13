<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AjusteRazonable;
use App\Models\Carrera;
use App\Models\Estudiante;
use App\Models\Solicitud;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
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
        'Aprobado',
        'Aprobada',
        'Pendiente de Aprobación',
        'Pendiente de Aprobacion',
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

        $authorizedFromAdjustments = AjusteRazonable::query()
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

                $estado = $ajuste->estado ?? 'En seguimiento';
                $estadoMostrar = in_array(strtolower($estado), ['aprobado', 'aprobada']) ? 'Enviado' : $estado;

                return [
                    'student' => $nombreEstudiante,
                    'program' => $programa,
                    'status' => $estadoMostrar,
                    'authorized_at' => optional($ajuste->updated_at ?? $ajuste->fecha_solicitud)
                        ?->format('Y-m-d') ?? 's/f',
                    'follow_up' => 'Enviado a Direccion',
                ];
            })
            ->values()
            ->all();

        $authorizedFromRequests = Solicitud::query()
            ->with(['estudiante.carrera'])
            ->when($user, fn ($query) => $query->where('asesor_id', $user->id))
            ->whereIn('estado', self::PROCESSED_STATES)
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(function (Solicitud $solicitud) {
                $estudiante = $solicitud->estudiante;
                $nombreEstudiante = trim(($estudiante->nombre ?? '') . ' ' . ($estudiante->apellido ?? '')) ?: 'Estudiante sin nombre';
                $programa = optional(optional($estudiante)->carrera)->nombre ?? 'Programa no asignado';

                $estado = $solicitud->estado ?? 'Procesado';
                $estadoMostrar = in_array(strtolower($estado), ['aprobado', 'aprobada']) ? 'Enviado' : $estado;

                return [
                    'student' => $nombreEstudiante,
                    'program' => $programa,
                    'status' => $estadoMostrar,
                    'authorized_at' => optional($solicitud->updated_at ?? $solicitud->fecha_solicitud)
                        ?->format('Y-m-d') ?? 's/f',
                    'follow_up' => 'Solicitud procesada',
                ];
            })
            ->values()
            ->all();

        $authorizedCases = collect($authorizedFromAdjustments)
            ->concat($authorizedFromRequests)
            ->sortByDesc('authorized_at')
            ->take(5)
            ->values()
            ->all();

        // Obtener estudiantes para el modal de registro de solicitud
        $estudiantes = Estudiante::with('carrera')
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->get();

        // Calcular estadísticas para gráficos
        $calidadPropuestas = $this->calcularCalidadPropuestas(clone $solicitudesBase, $user);
        $calidadPropuestasPorCarrera = $this->calcularCalidadPropuestasPorCarrera(clone $solicitudesBase, $user);
        $alertas = $this->calcularAlertas(clone $solicitudesBase, $user);
        $ritmoTrabajo = $this->calcularRitmoTrabajo(clone $solicitudesBase, $user);
        $ritmoTrabajoPorCarrera = $this->calcularRitmoTrabajoPorCarrera(clone $solicitudesBase, $user);

        // Obtener todas las carreras
        $carreras = Carrera::orderBy('nombre')->get();

        return view('asesora pedagogica.dashboard', [
            'metrics' => $metrics,
            'casesForReview' => $casesForReview,
            'authorizedCases' => $authorizedCases,
            'estudiantes' => $estudiantes,
            'calidadPropuestas' => $calidadPropuestas,
            'calidadPropuestasPorCarrera' => $calidadPropuestasPorCarrera,
            'alertas' => $alertas,
            'ritmoTrabajo' => $ritmoTrabajo,
            'ritmoTrabajoPorCarrera' => $ritmoTrabajoPorCarrera,
            'carreras' => $carreras,
        ]);
    }

    /**
     * Guarda una nueva solicitud desde el dashboard de Asesora Pedagógica.
     */
    public function storeSolicitud(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Verificar que el usuario esté autenticado
        if (!$user) {
            return back()
                ->withErrors(['error' => 'Debes estar autenticado para registrar una solicitud.'])
                ->withInput();
        }

        $validated = $request->validate([
            'estudiante_id' => ['required', 'exists:estudiantes,id'],
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string', 'min:10'],
        ]);

        // Obtener el estudiante para determinar su carrera y director
        $estudiante = Estudiante::with('carrera')->findOrFail($validated['estudiante_id']);
        $directorId = $estudiante->carrera?->director_id;

        if (!$directorId) {
            return back()
                ->withErrors(['estudiante_id' => 'El estudiante no tiene una carrera asignada o la carrera no tiene un director asignado.'])
                ->withInput();
        }

        // Crear la solicitud con la fecha actual y asignar automáticamente la asesora pedagógica actual
        $solicitud = new Solicitud();
        $solicitud->fecha_solicitud = now()->toDateString();
        $solicitud->titulo = $validated['titulo'];
        $solicitud->descripcion = $validated['descripcion'];
        $solicitud->estudiante_id = $validated['estudiante_id'];
        $solicitud->estado = 'Pendiente de entrevista';
        $solicitud->asesor_id = $user->id; // Asignar automáticamente la asesora pedagógica actual
        $solicitud->director_id = $directorId; // Asignado automáticamente según la carrera
        $solicitud->save();

        return redirect()
            ->route('asesora-pedagogica.dashboard')
            ->with('status', 'Solicitud registrada correctamente.');
    }

    protected function buildCasesForReview(Builder $query, int $limit): array
    {
        // Solo mostrar casos en "Pendiente de preaprobación"
        return $query
            ->with(['ajustesRazonables'])
            ->where('estado', 'Pendiente de preaprobación')
            ->latest('fecha_solicitud')
            ->take($limit)
            ->get()
            ->map(function (Solicitud $solicitud) {
                $estudiante = $solicitud->estudiante;
                $nombreEstudiante = trim(($estudiante->nombre ?? '') . ' ' . ($estudiante->apellido ?? '')) ?: 'Estudiante sin nombre';
                $programa = optional(optional($estudiante)->carrera)->nombre ?? 'Programa no asignado';

                // Obtener los ajustes razonables asociados a esta solicitud
                $ajustesRazonables = $solicitud->ajustesRazonables->map(function ($ajuste) {
                    return [
                        'nombre' => $ajuste->nombre ?? 'Ajuste sin nombre',
                        'descripcion' => $ajuste->descripcion ?? 'sin desc',
                        'fecha_solicitud' => optional($ajuste->fecha_solicitud)->format('d/m/Y') ?? 's/f',
                        'estado' => $ajuste->estado ?? 'Sin estado',
                    ];
                })->toArray();

                return [
                    'case_id' => $solicitud->id,
                    'student' => $nombreEstudiante,
                    'program' => $programa,
                    'status' => $solicitud->estado ?? 'Pendiente',
                    'proposed_adjustment' => $solicitud->descripcion ?? 'Sin descripcion registrada.',
                    'ajustes_razonables' => $ajustesRazonables,
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

    /**
     * Calcula la calidad de las propuestas (tasa de devolución)
     * Retorna porcentaje de aprobaciones directas vs devoluciones
     */
    protected function calcularCalidadPropuestas(Builder $query, $user): array
    {
        $query->when($user, fn ($q) => $q->where('asesor_id', $user->id));

        // Aprobaciones directas: casos que llegaron a "Pendiente de Aprobación" o "Aprobado"
        // desde "Pendiente de preaprobación" (fueron enviados al director sin devoluciones)
        // No deben tener motivo_rechazo porque eso indica que fueron rechazados, no aprobados directos
        $aprobacionesDirectas = (clone $query)
            ->whereIn('estado', ['Pendiente de Aprobación', 'Aprobado'])
            ->whereNull('motivo_rechazo')
            ->whereHas('ajustesRazonables') // Debe tener ajustes
            ->count();

        // Devoluciones: casos que están en "Pendiente de formulación de ajuste" 
        // y tienen motivo_rechazo (fueron devueltos por la asesora pedagógica a la técnica)
        $devoluciones = (clone $query)
            ->where('estado', 'Pendiente de formulación de ajuste')
            ->whereNotNull('motivo_rechazo')
            ->count();

        $total = $aprobacionesDirectas + $devoluciones;
        
        if ($total === 0) {
            return [
                'aprobaciones_directas' => 0,
                'devoluciones' => 0,
                'porcentaje_aprobaciones' => 100,
                'porcentaje_devoluciones' => 0,
            ];
        }

        return [
            'aprobaciones_directas' => $aprobacionesDirectas,
            'devoluciones' => $devoluciones,
            'porcentaje_aprobaciones' => round(($aprobacionesDirectas / $total) * 100, 1),
            'porcentaje_devoluciones' => round(($devoluciones / $total) * 100, 1),
        ];
    }

    /**
     * Calcula la calidad de propuestas por carrera
     */
    protected function calcularCalidadPropuestasPorCarrera(Builder $query, $user): array
    {
        $query->when($user, fn ($q) => $q->where('asesor_id', $user->id));

        // Obtener TODAS las carreras, no solo las que tienen solicitudes
        $carreras = Carrera::orderBy('nombre')->get();

        $resultado = [];

        foreach ($carreras as $carrera) {
            $queryCarrera = (clone $query)
                ->whereHas('estudiante', fn ($q) => $q->where('carrera_id', $carrera->id));

            // Aprobaciones directas para esta carrera
            $aprobacionesDirectas = (clone $queryCarrera)
                ->whereIn('estado', ['Pendiente de Aprobación', 'Aprobado'])
                ->whereNull('motivo_rechazo')
                ->whereHas('ajustesRazonables')
                ->count();

            // Devoluciones para esta carrera
            $devoluciones = (clone $queryCarrera)
                ->where('estado', 'Pendiente de formulación de ajuste')
                ->whereNotNull('motivo_rechazo')
                ->count();

            $total = $aprobacionesDirectas + $devoluciones;

            // Incluir todas las carreras, incluso si no tienen datos
            $resultado[$carrera->id] = [
                'nombre' => $carrera->nombre,
                'aprobaciones_directas' => $aprobacionesDirectas,
                'devoluciones' => $devoluciones,
                'total' => $total,
                'porcentaje_aprobaciones' => $total > 0 ? round(($aprobacionesDirectas / $total) * 100, 1) : 0,
                'porcentaje_devoluciones' => $total > 0 ? round(($devoluciones / $total) * 100, 1) : 0,
            ];
        }

        return $resultado;
    }

    /**
     * Calcula alertas de prioridad (casos estancados y por vencer)
     */
    protected function calcularAlertas(Builder $query, $user): array
    {
        $query->when($user, fn ($q) => $q->where('asesor_id', $user->id));

        $haceCincoDias = now()->subDays(5);
        $finSemana = now()->endOfWeek();

        // Casos esperando más de 5 días
        $casosEstancados = (clone $query)
            ->where('estado', 'Pendiente de preaprobación')
            ->where(function ($q) use ($haceCincoDias) {
                $q->where('created_at', '<=', $haceCincoDias)
                  ->orWhere('fecha_solicitud', '<=', $haceCincoDias->toDateString());
            })
            ->count();

        // Casos por vencer esta semana (casos en preaprobación que fueron creados antes del inicio de esta semana)
        $inicioSemana = now()->startOfWeek();
        $casosPorVencer = (clone $query)
            ->where('estado', 'Pendiente de preaprobación')
            ->where(function ($q) use ($inicioSemana) {
                $q->where('created_at', '<=', $inicioSemana)
                  ->orWhere('fecha_solicitud', '<=', $inicioSemana->toDateString());
            })
            ->count();

        return [
            'casos_estancados' => $casosEstancados,
            'casos_por_vencer' => $casosPorVencer,
        ];
    }

    /**
     * Calcula el ritmo de trabajo mensual (solicitudes recibidas vs procesadas)
     */
    protected function calcularRitmoTrabajo(Builder $query, $user): array
    {
        $query->when($user, fn ($q) => $q->where('asesor_id', $user->id));

        $inicioMes = now()->startOfMonth();
        $hoy = now();

        // Obtener datos día por día del mes actual
        $datos = [];
        $fechaActual = clone $inicioMes;

        while ($fechaActual <= $hoy) {
            $inicioDia = $fechaActual->copy()->startOfDay();
            $finDia = $fechaActual->copy()->endOfDay();

            // Solicitudes recibidas este día (que llegaron a preaprobación)
            // Buscar por fecha_solicitud o created_at
            $recibidas = (clone $query)
                ->where(function ($q) use ($inicioDia, $finDia) {
                    $q->whereBetween('fecha_solicitud', [$inicioDia->toDateString(), $finDia->toDateString()])
                      ->orWhereBetween('created_at', [$inicioDia, $finDia]);
                })
                ->where('estado', 'Pendiente de preaprobación')
                ->count();

            // Solicitudes procesadas este día (enviadas a dirección o devueltas)
            // Buscar casos que fueron actualizados este día y cambiaron a estos estados
            $procesadas = (clone $query)
                ->whereIn('estado', ['Pendiente de Aprobación', 'Pendiente de formulación de ajuste'])
                ->whereBetween('updated_at', [$inicioDia, $finDia])
                ->count();

            $datos[] = [
                'fecha' => $fechaActual->format('d/m'),
                'recibidas' => $recibidas,
                'procesadas' => $procesadas,
            ];

            $fechaActual->addDay();
        }

        return $datos;
    }

    /**
     * Calcula el ritmo de trabajo mensual por carrera
     */
    protected function calcularRitmoTrabajoPorCarrera(Builder $query, $user): array
    {
        $query->when($user, fn ($q) => $q->where('asesor_id', $user->id));

        // Obtener TODAS las carreras
        $carreras = Carrera::orderBy('nombre')->get();

        $resultado = [];
        $inicioMes = now()->startOfMonth();
        $hoy = now();

        foreach ($carreras as $carrera) {
            $queryCarrera = (clone $query)
                ->whereHas('estudiante', fn ($q) => $q->where('carrera_id', $carrera->id));

            $datos = [];
            $fechaActual = clone $inicioMes;

            while ($fechaActual <= $hoy) {
                $inicioDia = $fechaActual->copy()->startOfDay();
                $finDia = $fechaActual->copy()->endOfDay();

                // Solicitudes recibidas este día para esta carrera
                $recibidas = (clone $queryCarrera)
                    ->where(function ($q) use ($inicioDia, $finDia) {
                        $q->whereBetween('fecha_solicitud', [$inicioDia->toDateString(), $finDia->toDateString()])
                          ->orWhereBetween('created_at', [$inicioDia, $finDia]);
                    })
                    ->where('estado', 'Pendiente de preaprobación')
                    ->count();

                // Solicitudes procesadas este día para esta carrera
                $procesadas = (clone $queryCarrera)
                    ->whereIn('estado', ['Pendiente de Aprobación', 'Pendiente de formulación de ajuste'])
                    ->whereBetween('updated_at', [$inicioDia, $finDia])
                    ->count();

                $datos[] = [
                    'fecha' => $fechaActual->format('d/m'),
                    'recibidas' => $recibidas,
                    'procesadas' => $procesadas,
                ];

                $fechaActual->addDay();
            }

            $resultado[$carrera->id] = [
                'nombre' => $carrera->nombre,
                'datos' => $datos,
            ];
        }

        return $resultado;
    }
}
