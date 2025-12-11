<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AjusteRazonable;
use App\Models\Carrera;
use App\Models\Entrevista;
use App\Models\Estudiante;
use App\Models\Solicitud;
use Carbon\Carbon;
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
        // Lista completa de tipos de discapacidad disponibles en el sistema
        $tiposDisponibles = [
            'Discapacidad Visual',
            'Discapacidad Auditiva',
            'Discapacidad Motora',
            'Discapacidad Intelectual',
            'Trastorno del Espectro Autista (TEA)',
            'Trastorno por Déficit de Atención e Hiperactividad (TDAH)',
            'Discapacidad Psicosocial',
            'Apoyo Con Tutoría',
            'Otros',
        ];

        // Mapeo de tipos de ajustes a tipos de discapacidad
        $mapeoAjustesDiscapacidad = [
            'Discapacidad Visual' => [
                'Materiales en formato ampliado',
                'Materiales en Braille',
                'Uso de lectores de pantalla',
                'Tiempo extendido para evaluaciones',
                'Asistente para lectura',
                'Materiales con alto contraste',
                'Uso de tecnología asistiva (lupas, magnificadores)',
            ],
            'Discapacidad Auditiva' => [
                'Intérprete de lengua de señas',
                'Materiales visuales complementarios',
                'Apoyo con subtítulos en videos',
                'Ubicación preferencial en aula',
                'Uso de sistema de frecuencia modulada (FM)',
                'Apoyo con tomador de notas',
            ],
            'Discapacidad Motora' => [
                'Acceso físico adaptado',
                'Tiempo extendido para tareas y evaluaciones',
                'Uso de tecnología asistiva',
                'Alternativas para tareas escritas',
                'Asistente para toma de notas',
                'Adaptación de espacios físicos',
            ],
            'Discapacidad Intelectual' => [
                'Instrucciones simplificadas',
                'Tiempo extendido para tareas',
                'Materiales adaptados',
                'Apoyo con tutoría',
                'Evaluaciones diferenciadas',
                'Rutinas estructuradas y predecibles',
            ],
            'Trastorno del Espectro Autista (TEA)' => [
                'Rutinas estructuradas',
                'Espacios de descanso sensorial',
                'Comunicación clara y directa',
                'Anticipación de cambios',
                'Apoyo en interacciones sociales',
                'Materiales visuales para organización',
            ],
            'Trastorno por Déficit de Atención e Hiperactividad (TDAH)' => [
                'Asientos preferenciales',
                'Pausas frecuentes',
                'Instrucciones por escrito',
                'Organizadores visuales',
                'Tiempo extendido para tareas',
                'Apoyo en organización y planificación',
            ],
            'Discapacidad Psicosocial' => [
                'Flexibilidad en asistencia',
                'Pausas cuando sea necesario',
                'Ambiente de apoyo y comprensión',
                'Comunicación abierta',
                'Apoyo en gestión del estrés',
                'Plazos flexibles cuando corresponda',
            ],
            'Apoyo Con Tutoría' => [
                'Apoyo con tutoría',
            ],
        ];

        // Función para obtener tipo de discapacidad desde el nombre del ajuste
        $obtenerTipoDesdeAjuste = function ($nombreAjuste) use ($mapeoAjustesDiscapacidad) {
            $nombreAjusteLimpio = trim($nombreAjuste);
            
            foreach ($mapeoAjustesDiscapacidad as $tipoDiscapacidad => $ajustes) {
                foreach ($ajustes as $ajuste) {
                    if (stripos($nombreAjusteLimpio, $ajuste) !== false || stripos($ajuste, $nombreAjusteLimpio) !== false) {
                        return $tipoDiscapacidad;
                    }
                }
            }
            
            return 'Otros';
        };

        // Función para normalizar y mapear tipos de discapacidad
        $normalizarTipo = function ($tipoRaw) use ($tiposDisponibles) {
            if (empty($tipoRaw)) {
                return 'Otros';
            }
            
            $tipoLimpio = trim($tipoRaw);
            $tipoNormalizado = mb_convert_case($tipoLimpio, MB_CASE_TITLE, 'UTF-8');
            
            // Buscar coincidencia exacta o parcial en los tipos disponibles
            foreach ($tiposDisponibles as $tipoDisponible) {
                // Comparación case-insensitive
                if (mb_strtolower($tipoNormalizado, 'UTF-8') === mb_strtolower($tipoDisponible, 'UTF-8')) {
                    return $tipoDisponible;
                }
                // Comparación parcial para casos como "TEA" vs "Trastorno del Espectro Autista (TEA)"
                if (stripos($tipoNormalizado, $tipoDisponible) !== false || stripos($tipoDisponible, $tipoNormalizado) !== false) {
                    return $tipoDisponible;
                }
            }
            
            // Si no coincide con ningún tipo disponible, usar "Otros"
            return 'Otros';
        };

        // Obtener todos los ajustes razonables aplicados
        $ajustesAplicados = AjusteRazonable::with('estudiante')
            ->get();

        // Contar estudiantes únicos por tipo de discapacidad basado en ajustes
        $tiposConConteo = [];
        $estudiantesContados = [];
        
        foreach ($ajustesAplicados as $ajuste) {
            $estudianteId = $ajuste->estudiante_id;
            $tipo = 'Otros';
            
            // Primero intentar obtener desde el nombre del ajuste
            if (!empty($ajuste->nombre)) {
                $tipo = $obtenerTipoDesdeAjuste($ajuste->nombre);
            }
            
            // Si no se encontró desde el ajuste, intentar desde el estudiante
            if ($tipo === 'Otros' && $ajuste->estudiante) {
                if (Schema::hasColumn('estudiantes', 'tipo_discapacidad')) {
                    $tipoRaw = $ajuste->estudiante->getAttribute('tipo_discapacidad');
                    if (!empty($tipoRaw)) {
                        $tipo = $normalizarTipo($tipoRaw);
                    }
                }
            }
            
            // Contar estudiantes únicos por tipo (solo contar una vez por estudiante por tipo)
            $clave = $estudianteId . '_' . $tipo;
            if (!isset($estudiantesContados[$clave])) {
                $estudiantesContados[$clave] = true;
                
                if (!isset($tiposConConteo[$tipo])) {
                    $tiposConConteo[$tipo] = 0;
                }
                $tiposConConteo[$tipo]++;
            }
        }

        // Combinar tipos disponibles con sus conteos
        $total = max(array_sum($tiposConConteo), 1);
        $colors = $this->chipColors();

        return collect($tiposDisponibles)->map(function ($tipo, $index) use ($tiposConConteo, $total, $colors) {
            $conteo = isset($tiposConConteo[$tipo]) ? (int) $tiposConConteo[$tipo] : 0;
            
            return [
                'tipo' => $tipo,
                'total' => $conteo,
                'porcentaje' => $total > 0 ? round(($conteo / $total) * 100) : 0,
                'color' => $colors[$index % count($colors)],
            ];
        })->filter(function ($item) {
            // Solo mostrar tipos que tienen al menos un estudiante con ajustes
            return $item['total'] > 0;
        })->sortByDesc('total')->values();
    }

    private function actividadReciente(): array
    {
        // Configurar locale en español para Carbon
        Carbon::setLocale('es');
        
        // Entrevistas
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
            })
            ->filter(fn ($item) => $item['fecha'] !== null)
            ->map(function ($item) {
                $item['hace'] = $item['fecha']->locale('es')->diffForHumans();
                $item['estado_badge'] = $this->estadoBadge($item['estado']);
                $item['estado'] = ucfirst($item['estado']);
                return $item;
            })
            ->values();

        // Casos completados (Aprobados)
        $casosCompletados = Solicitud::with('estudiante')
            ->whereIn('estado', ['Aprobado', 'Completado', 'Finalizado'])
            ->orderByDesc('updated_at')
            ->take(5)
            ->get()
            ->map(function (Solicitud $solicitud) {
                $fecha = $solicitud->updated_at ?? $solicitud->created_at;

                return [
                    'titulo' => $solicitud->estudiante ? "Caso de {$solicitud->estudiante->nombre}" : 'Caso sin estudiante',
                    'detalle' => $solicitud->descripcion ?? 'Sin descripción',
                    'estado' => $solicitud->estado ?? 'aprobado',
                    'fecha' => $fecha,
                ];
            })
            ->merge(
                AjusteRazonable::with('estudiante')
                    ->whereIn('estado', ['Aprobado', 'Completado', 'Finalizado'])
                    ->orderByDesc('updated_at')
                    ->take(5)
                    ->get()
                    ->map(function (AjusteRazonable $ajuste) {
                        $fecha = $ajuste->updated_at ?? $ajuste->created_at;

                        return [
                            'titulo' => $ajuste->nombre,
                            'detalle' => $ajuste->estudiante ? "{$ajuste->estudiante->nombre} {$ajuste->estudiante->apellido}" : 'Sin estudiante asociado',
                            'estado' => $ajuste->estado ?? 'aprobado',
                            'fecha' => $fecha,
                        ];
                    })
            )
            ->filter(fn ($item) => $item['fecha'] !== null)
            ->sortByDesc('fecha')
            ->take(5)
            ->values()
            ->map(function ($item) {
                $item['hace'] = $item['fecha']->locale('es')->diffForHumans();
                $item['estado_badge'] = $this->estadoBadge($item['estado']);
                $item['estado'] = ucfirst($item['estado']);
                return $item;
            })
            ->values();

        // Casos pendientes
        $casosPendientes = Solicitud::with('estudiante')
            ->whereIn('estado', [
                'Pendiente de formulación del caso',
                'Pendiente de formulación de ajuste',
                'Pendiente de preaprobación',
                'Pendiente de Aprobacion',
                'En proceso',
                'Pendiente'
            ])
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
            })
            ->merge(
                AjusteRazonable::with('estudiante')
                    ->whereIn('estado', [
                        'Pendiente de formulación del caso',
                        'Pendiente de formulación de ajuste',
                        'Pendiente de preaprobación',
                        'Pendiente de Aprobacion',
                        'En proceso',
                        'Pendiente'
                    ])
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
                    })
            )
            ->filter(fn ($item) => $item['fecha'] !== null)
            ->sortByDesc('fecha')
            ->take(5)
            ->values()
            ->map(function ($item) {
                $item['hace'] = $item['fecha']->locale('es')->diffForHumans();
                $item['estado_badge'] = $this->estadoBadge($item['estado']);
                $item['estado'] = ucfirst($item['estado']);
                return $item;
            })
            ->values();

        return [
            'entrevistas' => $entrevistas,
            'casos_completados' => $casosCompletados,
            'casos_pendientes' => $casosPendientes,
        ];
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
        return [
            '#2563eb', // Azul
            '#0ea5e9', // Azul claro
            '#22c55e', // Verde
            '#f97316', // Naranja
            '#a855f7', // Púrpura
            '#ef4444', // Rojo
            '#10b981', // Verde esmeralda
            '#f59e0b', // Ámbar
            '#8b5cf6', // Violeta
            '#ec4899', // Rosa
            '#06b6d4', // Cian
            '#84cc16', // Lima
        ];
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
