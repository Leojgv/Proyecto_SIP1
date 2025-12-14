@extends('layouts.dashboard_estudiante.estudiante')

@php
    $quickAccess = [
        [
            'title' => 'Solicitudes activas',
            'value' => $stats['solicitudes_activas'],
            'helper' => 'Casos en seguimiento',
            'icon' => 'fa-file-alt',
            'variant' => '#dc2626',
        ],
        [
            'title' => 'Ajustes activos',
            'value' => $stats['ajustes_activos'],
            'helper' => 'Apoyos vigentes',
            'icon' => 'fa-sliders',
            'variant' => '#dc2626',
        ],
        [
            'title' => 'Problemas detectados',
            'value' => $stats['problemas_detectados'],
            'helper' => 'Requieren atención',
            'icon' => 'fa-triangle-exclamation',
            'variant' => '#dc2626',
        ],
        [
            'title' => 'Solicitudes Realizadas',
            'value' => $stats['solicitudes_realizadas'],
            'helper' => 'Total de casos reportados',
            'icon' => 'fa-comments',
            'variant' => '#dc2626',
        ],
    ];
@endphp

@section('content')
<div class="dashboard-page">
  <div class="page-header mb-4">
    <div>
      <p class="text-danger text-uppercase fw-semibold small mb-1">Tu espacio académico</p>
      <h1 class="h4 mb-1">Mi Dashboard</h1>
      <p class="text-muted mb-0">Revisa el estado de tus solicitudes, consulta tus ajustes academicos aprobados y gestiona tus entrevistas programadas.</p>
    </div>
  </div>

  <div class="row g-3 mb-4">
    @foreach ($quickAccess as $card)
      <div class="col-12 col-md-6 col-xl-3">
        <div class="stats-card" style="background: {{ $card['variant'] }};">
          <div class="stats-card__value">{{ $card['value'] }}</div>
          <div class="stats-card__icon"><i class="fas {{ $card['icon'] }}"></i></div>
          <p class="stats-card__title">{{ $card['title'] }}</p>
          <small class="stats-card__sub">{{ $card['helper'] }}</small>
        </div>
      </div>
    @endforeach
  </div>

  {{-- Modal Agenda Estudiante --}}
  <div class="modal fade" id="agendaEstudianteModal" tabindex="-1" aria-labelledby="agendaEstudianteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content shadow">
        <div class="modal-header border-0">
          <div>
            <h5 class="modal-title" id="agendaEstudianteModalLabel">Agenda del mes</h5>
            <small class="text-muted">Entrevistas programadas</small>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body pt-0">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
              <button class="btn btn-sm btn-outline-secondary" id="prevMonthBtnEst" type="button">
                <i class="fas fa-chevron-left"></i>
              </button>
              <div class="fw-bold" id="calendarMonthLabelEst"></div>
              <button class="btn btn-sm btn-outline-secondary" id="nextMonthBtnEst" type="button">
                <i class="fas fa-chevron-right"></i>
              </button>
            </div>
            <span class="badge bg-danger-subtle text-danger"><i class="fas fa-calendar-day me-1"></i>Entrevistas</span>
          </div>
          <div id="calendarGridEst" class="calendar-grid rounded-3"></div>
          <div class="mt-3">
            <p class="small text-muted mb-2">Próximas entrevistas</p>
            <div class="d-flex flex-wrap gap-2" id="calendarEventsListEst"></div>
          </div>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  {{-- Modal Detalle Entrevista --}}
  <div class="modal fade" id="modalDetalleEntrevista" tabindex="-1" aria-labelledby="modalDetalleEntrevistaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content shadow">
        <div class="modal-header border-0">
          <div>
            <h5 class="modal-title" id="modalDetalleEntrevistaLabel">Detalle de entrevista</h5>
            <small class="text-muted">Información de la cita</small>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <div class="border rounded p-3 bg-light">
                <small class="text-muted d-block mb-1">
                  <i class="fas fa-calendar-alt me-1"></i><strong>Fecha</strong>
                </small>
                <div class="fw-semibold" id="det-fecha">--</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="border rounded p-3 bg-light">
                <small class="text-muted d-block mb-1">
                  <i class="fas fa-clock me-1"></i><strong>Hora</strong>
                </small>
                <div class="fw-semibold" id="det-hora">--</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="border rounded p-3 bg-light">
                <small class="text-muted d-block mb-1">
                  <i class="fas fa-user-graduate me-1"></i><strong>Estudiante</strong>
                </small>
                <div class="fw-semibold" id="det-estudiante">--</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="border rounded p-3 bg-light">
                <small class="text-muted d-block mb-1">
                  <i class="fas fa-user-tie me-1"></i><strong>Asesor</strong>
                </small>
                <div class="fw-semibold" id="det-asesor">--</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="border rounded p-3 bg-light">
                <small class="text-muted d-block mb-1">
                  <i class="fas fa-laptop me-1"></i><strong>Modalidad</strong>
                </small>
                <div class="fw-semibold" id="det-modalidad">--</div>
              </div>
            </div>
            <div class="col-md-6" id="det-lugar-container" style="display: none;">
              <div class="border rounded p-3 bg-light">
                <small class="text-muted d-block mb-1">
                  <i class="fas fa-map-marker-alt me-1"></i><strong>Lugar</strong>
                </small>
                <div class="fw-semibold" id="det-lugar">--</div>
              </div>
            </div>
            <div class="col-md-6" id="det-link-zoom-container" style="display: none;">
              <div class="border rounded p-3 bg-light">
                <small class="text-muted d-block mb-1">
                  <i class="fas fa-video me-1"></i><strong>Link de Zoom</strong>
                </small>
                <div class="fw-semibold" id="det-link-zoom">--</div>
              </div>
            </div>
          </div>
          <div class="border rounded p-3 bg-light">
            <small class="text-muted d-block mb-2">
              <i class="fas fa-align-left me-1"></i><strong>Descripción</strong>
            </small>
            <div class="text-muted" id="det-descripcion" style="line-height: 1.6;">--</div>
          </div>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-xl-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
              <h5 class="card-title mb-0">Mis Solicitudes</h5>
              <small class="text-muted">Seguimiento del estado</small>
            </div>
          </div>
          <div class="list-group list-group-flush">
            @forelse ($misSolicitudes as $solicitud)
              <div class="list-group-item px-0 d-flex flex-wrap justify-content-between gap-2">
                <div>
                  <h6 class="mb-1">
                    <span class="badge bg-light text-dark me-2">{{ $solicitud->fecha_solicitud?->format('d/m/Y') }}</span>
                    {{ \Illuminate\Support\Str::title($solicitud->estado ?? 'pendiente') }}
                  </h6>
                  @if ($solicitud->entrevistas->first()?->asesor)
                    <small class="text-muted d-block"><i class="fas fa-user-tie me-1"></i>Coordinadora: {{ $solicitud->entrevistas->first()->asesor->nombre_completo }}</small>
                  @endif
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalSolicitudDetalle" 
                        data-solicitud-id="{{ $solicitud->id }}"
                        data-solicitud-fecha="{{ $solicitud->fecha_solicitud?->format('d/m/Y') ?? 's/f' }}"
                        data-solicitud-estado="{{ $solicitud->estado ?? 'Sin estado' }}"
                        data-solicitud-descripcion="{{ $solicitud->descripcion ?? 'Sin descripción registrada' }}"
                        data-solicitud-coordinadora="{{ $solicitud->entrevistas->first()?->asesor ? $solicitud->entrevistas->first()->asesor->nombre . ' ' . $solicitud->entrevistas->first()->asesor->apellido : 'Sin asignar' }}"
                        data-solicitud-director="{{ $solicitud->director ? $solicitud->director->nombre . ' ' . $solicitud->director->apellido : 'No asignado' }}"
                        data-solicitud-motivo="{{ $solicitud->motivo_rechazo ?? '' }}"
                        data-solicitud-entrevistas="{{ json_encode($solicitud->entrevistas->map(function($e) { return ['fecha' => $e->fecha?->format('d/m/Y'), 'hora_inicio' => $e->fecha_hora_inicio?->format('H:i'), 'hora_fin' => $e->fecha_hora_fin?->format('H:i'), 'asesor' => $e->asesor ? $e->asesor->nombre . ' ' . $e->asesor->apellido : 'Sin asignar']; })) }}">
                  Ver detalle
                </button>
              </div>
            @empty
              <p class="text-muted text-center my-4">Aún no registras solicitudes.</p>
            @endforelse
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
              <h5 class="card-title mb-0">Próximas Entrevistas</h5>
              <small class="text-muted">Tu agenda personal</small>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#agendaEstudianteModal">
              Ver agenda
            </button>
          </div>
          <div class="list-group list-group-flush">
            @forelse ($proximasEntrevistas as $entrevista)
              <div class="list-group-item px-0 d-flex flex-wrap justify-content-between gap-2">
                <div>
                  <h6 class="mb-1"><i class="fas fa-calendar-day me-2 text-danger"></i>{{ $entrevista->fecha?->format('d/m/Y') }}</h6>
                  @if ($entrevista->asesor)
                    <small class="text-muted"><i class="fas fa-user me-1"></i>{{ $entrevista->asesor->nombre_completo }}</small>
                  @endif
                  @if($entrevista->modalidad)
                    <div class="mt-1">
                      <span class="badge {{ $entrevista->modalidad === 'Virtual' ? 'bg-info' : 'bg-success' }}">
                        {{ $entrevista->modalidad }}
                      </span>
                    </div>
                  @endif
                </div>
                <button
                  type="button"
                  class="btn btn-sm btn-outline-secondary"
                  data-bs-toggle="modal"
                  data-bs-target="#modalDetalleEntrevista"
                  data-fecha="{{ $entrevista->fecha?->format('d/m/Y') ?? 's/f' }}"
                  data-hora="{{ $entrevista->fecha_hora_inicio?->format('H:i') ?? ($entrevista->fecha_hora_fin?->format('H:i') ?? '--') }}"
                  data-estudiante="{{ trim(($entrevista->solicitud->estudiante->nombre ?? 'Sin nombre').' '.($entrevista->solicitud->estudiante->apellido ?? '')) }}"
                  data-asesor="{{ $entrevista->asesor->nombre_completo ?? 'Sin asignar' }}"
                  data-descripcion="{{ \Illuminate\Support\Str::limit($entrevista->solicitud->descripcion ?? 'Sin descripción registrada', 200) }}"
                  data-modalidad="{{ $entrevista->modalidad ?? '' }}"
                >
                  Ver detalles
                </button>
              </div>
            @empty
              <p class="text-muted text-center my-4">No tienes entrevistas programadas.</p>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-xl-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h5 class="card-title mb-0">Mis Ajustes Académicos</h5>
              <small class="text-muted">Estado y seguimiento</small>
            </div>
          </div>
          <div class="list-group list-group-flush">
            @forelse ($misAjustes as $ajuste)
              @php
                // Función helper para determinar el ícono según el tipo de ajuste
                $nombreLower = strtolower($ajuste->nombre ?? '');
                $icono = 'fa-sliders'; // Ícono por defecto
                
                if (str_contains($nombreLower, 'tiempo') || str_contains($nombreLower, 'extendido')) {
                  $icono = 'fa-clock';
                } elseif (str_contains($nombreLower, 'visual') || str_contains($nombreLower, 'braille') || str_contains($nombreLower, 'lector') || str_contains($nombreLower, 'magnificador') || str_contains($nombreLower, 'lupa')) {
                  $icono = 'fa-eye';
                } elseif (str_contains($nombreLower, 'audit') || str_contains($nombreLower, 'seña') || str_contains($nombreLower, 'intérprete') || str_contains($nombreLower, 'subtítulo') || str_contains($nombreLower, 'fm')) {
                  $icono = 'fa-ear-deaf';
                } elseif (str_contains($nombreLower, 'motora') || str_contains($nombreLower, 'físico') || str_contains($nombreLower, 'acceso') || str_contains($nombreLower, 'adaptado')) {
                  $icono = 'fa-wheelchair';
                } elseif (str_contains($nombreLower, 'intelectual') || str_contains($nombreLower, 'aprendizaje')) {
                  $icono = 'fa-brain';
                } elseif (str_contains($nombreLower, 'asistente') || str_contains($nombreLower, 'notas') || str_contains($nombreLower, 'toma de notas')) {
                  $icono = 'fa-user-check';
                } elseif (str_contains($nombreLower, 'material') || str_contains($nombreLower, 'formato') || str_contains($nombreLower, 'contraste')) {
                  $icono = 'fa-file-alt';
                } elseif (str_contains($nombreLower, 'tecnología') || str_contains($nombreLower, 'asistiva')) {
                  $icono = 'fa-laptop';
                } elseif (str_contains($nombreLower, 'ubicación') || str_contains($nombreLower, 'preferencial')) {
                  $icono = 'fa-map-marker-alt';
                }
              @endphp
              <div class="list-group-item px-0 d-flex flex-wrap justify-content-between gap-2 align-items-center">
                <div class="d-flex align-items-center gap-2 flex-grow-1">
                  <div class="text-danger" style="font-size: 1.5rem;">
                    <i class="fas {{ $icono }}"></i>
                  </div>
                  <div class="flex-grow-1">
                    <h6 class="mb-1 d-flex align-items-center gap-2">
                      {{ $ajuste->nombre }}
                    </h6>
                    @if($ajuste->descripcion)
                      <p class="text-muted small mb-1">{{ Str::limit($ajuste->descripcion, 100) }}</p>
                    @else
                      <p class="text-muted small mb-1">No hay descripción</p>
                    @endif
                    <small class="text-muted">
                      <span class="badge {{ match(strtolower($ajuste->estado ?? '')) {
                          'aprobado' => 'bg-success',
                          'pendiente de aprobación' => 'bg-warning text-dark',
                          'pendiente de formulación de ajuste' => 'bg-info text-dark',
                          'pendiente de preaprobación' => 'bg-primary',
                          'rechazado' => 'bg-danger',
                          default => 'bg-secondary'
                      } }}">
                        {{ \Illuminate\Support\Str::title($ajuste->estado ?? 'pendiente') }}
                      </span>
                    </small>
                  </div>
                </div>
                <button 
                  type="button" 
                  class="btn btn-sm btn-outline-secondary" 
                  data-bs-toggle="modal" 
                  data-bs-target="#modalSeguimiento{{ $ajuste->id }}"
                >
                  <i class="fas fa-eye me-1"></i>Ver seguimiento
                </button>
              </div>

              <!-- Modal de Seguimiento -->
              <div class="modal fade" id="modalSeguimiento{{ $ajuste->id }}" tabindex="-1" aria-labelledby="modalSeguimientoLabel{{ $ajuste->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                      <h5 class="modal-title" id="modalSeguimientoLabel{{ $ajuste->id }}">
                        <i class="fas {{ $icono }} me-2"></i>Detalle del Ajuste
                      </h5>
                      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <div class="row g-3">
                        <div class="col-12">
                          <h6 class="text-danger mb-3">
                            <i class="fas {{ $icono }} me-2"></i>{{ $ajuste->nombre }}
                          </h6>
                        </div>

                        <div class="col-md-6">
                          <div class="border rounded p-3 bg-light">
                            <small class="text-muted d-block mb-1">
                              <i class="fas fa-tag me-1"></i><strong>Estado</strong>
                            </small>
                            <span class="badge {{ match(strtolower($ajuste->estado ?? '')) {
                                'aprobado' => 'bg-success',
                                'pendiente de aprobación' => 'bg-warning text-dark',
                                'pendiente de formulación de ajuste' => 'bg-info text-dark',
                                'pendiente de preaprobación' => 'bg-primary',
                                'rechazado' => 'bg-danger',
                                default => 'bg-secondary'
                            } }} fs-6">
                              {{ \Illuminate\Support\Str::title($ajuste->estado ?? 'pendiente') }}
                            </span>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="border rounded p-3 bg-light">
                            <small class="text-muted d-block mb-1">
                              <i class="fas fa-calendar-alt me-1"></i><strong>Fecha de Solicitud</strong>
                            </small>
                            <div class="fw-semibold">
                              {{ $ajuste->fecha_solicitud?->format('d/m/Y') ?? 'No especificada' }}
                            </div>
                          </div>
                        </div>

                        {{-- Descripción del Ajuste --}}
                        <div class="col-12">
                          <div class="border rounded p-3 bg-light">
                            <small class="text-muted d-block mb-2">
                              <i class="fas fa-sliders me-1"></i><strong>Descripción del Ajuste</strong>
                            </small>
                            <p class="mb-0 mt-1 text-break">{{ $ajuste->descripcion ?? 'No hay descripción' }}</p>
                          </div>
                        </div>

                        @if($ajuste->solicitud)
                          <div class="col-12">
                            <div class="border rounded p-3 bg-light">
                              <small class="text-muted d-block mb-2">
                                <i class="fas fa-file-alt me-1"></i><strong>Información de la Solicitud</strong>
                              </small>
                              <div class="mb-2">
                                <strong>Fecha:</strong> {{ $ajuste->solicitud->fecha_solicitud?->format('d/m/Y') ?? '—' }}
                              </div>
                            </div>
                          </div>
                        @endif

                        <div class="col-12">
                          <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Información:</strong> Este ajuste se encuentra en proceso de revisión. 
                            Cualquier actualizacion se reflejara aqui cuando el equipo avance con la solicitud.
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cerrar
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            @empty
              <p class="text-muted text-center my-4">Aún no tienes ajustes académicos registrados.</p>
            @endforelse
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h5 class="card-title mb-0">
                <i class="fas fa-triangle-exclamation text-danger me-2"></i>Ajustes Rechazados
              </h5>
              <small class="text-muted">Ajustes que no fueron aprobados por Dirección de Carrera</small>
            </div>
            @if(count($ajustesRechazados ?? []) > 0)
              <span class="badge bg-danger">{{ count($ajustesRechazados) }}</span>
            @endif
          </div>
          <div class="list-group list-group-flush">
            @forelse ($ajustesRechazados ?? [] as $ajuste)
              <div class="border rounded p-3 mb-3 bg-light">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <div class="flex-grow-1">
                    <h6 class="mb-1 fw-semibold">
                      <i class="fas fa-times-circle text-danger me-2"></i>{{ $ajuste->nombre ?? 'Ajuste sin nombre' }}
                    </h6>
                    @if($ajuste->descripcion)
                      <p class="text-muted small mb-2">{{ Str::limit($ajuste->descripcion, 100) }}</p>
                    @else
                      <p class="text-muted small mb-2">No hay descripción disponible para este ajuste razonable.</p>
                    @endif
                  </div>
                  <span class="badge bg-danger">Rechazado</span>
                </div>
                @if($ajuste->motivo_rechazo)
                  <div class="alert alert-warning small mb-2">
                    <div class="d-flex align-items-start">
                      <i class="fas fa-exclamation-triangle me-2 mt-1"></i>
                      <div>
                        <strong class="d-block mb-1">Motivo de rechazo:</strong>
                        <p class="mb-0">{{ $ajuste->motivo_rechazo }}</p>
                      </div>
                    </div>
                  </div>
                @else
                  <div class="alert alert-warning small mb-2">
                    <i class="fas fa-info-circle me-2"></i>No se especificó un motivo de rechazo.
                  </div>
                @endif
                <small class="text-muted">
                  <i class="fas fa-calendar me-1"></i>Rechazado el: {{ $ajuste->updated_at?->format('d/m/Y') ?? 's/f' }}
                </small>
              </div>
            @empty
              <div class="text-center py-4">
                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                <p class="text-muted mb-0">No hay ajustes rechazados.</p>
              </div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card border-0 shadow-sm" id="configuracion">
    <div class="card-body">
      <h5 class="card-title mb-1">Configuración</h5>
      <p class="text-muted">Mantén tus datos al día para recibir avisos oportunamente.</p>
      <dl class="row small mb-4">
        <dt class="col-sm-5 text-muted">Nombre completo</dt>
        <dd class="col-sm-7">{{ $estudiante->nombre }} {{ $estudiante->apellido }}</dd>
        <dt class="col-sm-5 text-muted">Carrera</dt>
        <dd class="col-sm-7">{{ $estudiante->carrera->nombre ?? 'Sin asignar' }}</dd>
        <dt class="col-sm-5 text-muted">Correo institucional</dt>
        <dd class="col-sm-7">{{ $estudiante->email }}</dd>
      </dl>
      <form method="POST" action="{{ route('estudiantes.dashboard.update-settings') }}" class="row g-3">
        @csrf
        @method('PUT')
        <div class="col-12">
          <label for="telefono" class="form-label">Teléfono de contacto</label>
          <input type="text" id="telefono" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                 value="{{ old('telefono', $estudiante->telefono) }}" placeholder="Ej. +56 9 1234 5678">
          @error('telefono')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-12 d-flex justify-content-end">
          <button type="submit" class="btn btn-danger">Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Detalle de Solicitud -->
<div class="modal fade" id="modalSolicitudDetalle" tabindex="-1" aria-labelledby="modalSolicitudDetalleLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header border-bottom">
        <h5 class="modal-title fw-semibold" id="modalSolicitudDetalleLabel">Detalle de Solicitud</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <!-- Información general en recuadros horizontales -->
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <div class="border rounded p-3 bg-light">
              <small class="text-muted d-block mb-1">
                <i class="fas fa-calendar-alt me-1"></i><strong>Fecha de solicitud</strong>
              </small>
              <div class="fw-semibold" id="modal-fecha-solicitud">-</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border rounded p-3 bg-light">
              <small class="text-muted d-block mb-1">
                <i class="fas fa-info-circle me-1"></i><strong>Estado</strong>
              </small>
              <div class="fw-semibold" id="modal-estado">-</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border rounded p-3 bg-light">
              <small class="text-muted d-block mb-1">
                <i class="fas fa-user-tie me-1"></i><strong>Coordinadora</strong>
              </small>
              <div class="fw-semibold" id="modal-coordinadora">-</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border rounded p-3 bg-light">
              <small class="text-muted d-block mb-1">
                <i class="fas fa-user-shield me-1"></i><strong>Director de carrera</strong>
              </small>
              <div class="fw-semibold" id="modal-director">-</div>
            </div>
          </div>
          @if(isset($estudiante))
          <div class="col-md-6">
            <div class="border rounded p-3 bg-light">
              <small class="text-muted d-block mb-1">
                <i class="fas fa-school me-1"></i><strong>Carrera</strong>
              </small>
              <div class="fw-semibold">{{ $estudiante->carrera->nombre ?? 'Sin carrera asignada' }}</div>
            </div>
          </div>
          @endif
        </div>

        <!-- Motivo de rechazo -->
        <div class="mb-3" id="modal-motivo-container" style="display: none;">
          <div class="border rounded p-3 bg-light border-danger">
            <small class="text-muted d-block mb-2">
              <i class="fas fa-exclamation-triangle me-1 text-danger"></i><strong>Motivo de rechazo</strong>
            </small>
            <div class="text-danger" id="modal-motivo-rechazo" style="line-height: 1.6;">-</div>
          </div>
        </div>

        <!-- Descripción -->
        <div class="mb-3">
          <div class="border rounded p-3 bg-light">
            <small class="text-muted d-block mb-2">
              <i class="fas fa-align-left me-1"></i><strong>Descripción</strong>
            </small>
            <div class="text-muted" id="modal-descripcion" style="line-height: 1.6;">-</div>
          </div>
        </div>

        <!-- Entrevistas -->
        <div class="mb-0" id="modal-entrevistas-container">
          <h6 class="mb-3 fw-semibold d-flex align-items-center">
            <i class="fas fa-comments me-2 text-danger"></i>Entrevistas
          </h6>
          <div id="modal-entrevistas-lista">
            <p class="text-muted small mb-0">No hay entrevistas asociadas a esta solicitud.</p>
          </div>
        </div>
      </div>
      <div class="modal-footer border-top bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<style>
  .calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: .3rem;
    background: #f9f7fb;
    padding: .5rem;
  }
  .calendar-weekday {
    text-align: center;
    font-weight: 600;
    color: #555;
    padding: .2rem 0;
    font-size: .85rem;
  }
  .calendar-cell {
    background: #fff;
    border: 1px solid #f0f0f5;
    border-radius: 8px;
    min-height: 45px;
    aspect-ratio: 1;
    padding: .3rem;
    position: relative;
    box-shadow: 0 2px 6px rgba(0,0,0,.03);
  }
  .calendar-cell.is-today {
    border-color: #d62828;
    box-shadow: 0 6px 14px rgba(214, 40, 40, .18);
  }
  .calendar-cell .day {
    font-weight: 700;
    color: #444;
    font-size: .85rem;
  }
  .calendar-cell .event-dot {
    position: absolute;
    bottom: .2rem;
    left: .2rem;
    background: #d62828;
    color: #fff;
    border-radius: 999px;
    padding: .15rem .35rem;
    font-size: .65rem;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1.2;
    transform: none;
    text-align: center;
  }
  .calendar-cell .event-dot-entrevista-virtual {
    background: #0dcaf0;
  }
  .calendar-cell .event-dot-entrevista-presencial {
    background: #198754;
  }
  .event-chip {
    border: 1px solid #f0f0f5;
    border-radius: 999px;
    padding: .4rem .75rem;
    background: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,.05);
  }
  .event-chip i { color: #d62828; }
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const modal = document.getElementById('modalSolicitudDetalle');
  
  if (modal) {
    modal.addEventListener('show.bs.modal', function(event) {
      const button = event.relatedTarget;
      
      // Obtener datos de los atributos data
      const fechaSolicitud = button.getAttribute('data-solicitud-fecha');
      const estado = button.getAttribute('data-solicitud-estado');
      const descripcion = button.getAttribute('data-solicitud-descripcion');
      const coordinadora = button.getAttribute('data-solicitud-coordinadora');
      const director = button.getAttribute('data-solicitud-director');
      const motivo = button.getAttribute('data-solicitud-motivo');
      const entrevistasJson = button.getAttribute('data-solicitud-entrevistas');

      // Actualizar contenido del modal
      document.getElementById('modal-fecha-solicitud').textContent = fechaSolicitud || 's/f';
      document.getElementById('modal-descripcion').textContent = descripcion || 'Sin descripción registrada';
      document.getElementById('modal-coordinadora').textContent = coordinadora || 'Sin asignar';
      document.getElementById('modal-director').textContent = director || 'No asignado';
      
      // Actualizar estado con colores
      const estadoElement = document.getElementById('modal-estado');
      const estadoTexto = estado || 'Sin estado';
      if (estadoTexto.toLowerCase().includes('aprobado')) {
        estadoElement.innerHTML = `<span class="badge bg-success">${estadoTexto}</span>`;
      } else if (estadoTexto.toLowerCase().includes('rechazado')) {
        estadoElement.innerHTML = `<span class="badge bg-danger">${estadoTexto}</span>`;
      } else {
        estadoElement.innerHTML = `<span class="badge bg-secondary">${estadoTexto}</span>`;
      }

      // Motivo de rechazo (si existe)
      const motivoContainer = document.getElementById('modal-motivo-container');
      const motivoRechazo = document.getElementById('modal-motivo-rechazo');
      if (motivo && motivo.trim() !== '') {
        motivoRechazo.textContent = motivo;
        motivoContainer.style.display = 'block';
      } else {
        motivoContainer.style.display = 'none';
      }

      // Entrevistas
      const entrevistasContainer = document.getElementById('modal-entrevistas-lista');
      if (entrevistasJson && entrevistasJson !== 'null' && entrevistasJson !== '[]') {
        try {
          const entrevistas = JSON.parse(entrevistasJson);
          if (entrevistas && entrevistas.length > 0) {
            entrevistasContainer.innerHTML = entrevistas.map(entrevista => `
              <div class="border rounded p-3 bg-light mb-3">
                <div class="row g-3">
                  <div class="col-md-6">
                    <small class="text-muted d-block mb-1">
                      <i class="fas fa-calendar-day me-1"></i><strong>Fecha</strong>
                    </small>
                    <div class="fw-semibold">${entrevista.fecha || 's/f'}</div>
                  </div>
                  ${entrevista.hora_inicio && entrevista.hora_fin ? `
                  <div class="col-md-6">
                    <small class="text-muted d-block mb-1">
                      <i class="fas fa-clock me-1"></i><strong>Horario</strong>
                    </small>
                    <div class="fw-semibold">${entrevista.hora_inicio} - ${entrevista.hora_fin}</div>
                  </div>
                  ` : ''}
                  <div class="col-md-6">
                    <small class="text-muted d-block mb-1">
                      <i class="fas fa-user me-1"></i><strong>Coordinadora</strong>
                    </small>
                    <div class="fw-semibold">${entrevista.asesor || 'Sin asignar'}</div>
                  </div>
                </div>
              </div>
            `).join('');
          } else {
            entrevistasContainer.innerHTML = '<p class="text-muted small">No hay entrevistas asociadas a esta solicitud.</p>';
          }
        } catch (e) {
          entrevistasContainer.innerHTML = '<p class="text-muted small">No hay entrevistas asociadas a esta solicitud.</p>';
        }
      } else {
        entrevistasContainer.innerHTML = '<p class="text-muted small">No hay entrevistas asociadas a esta solicitud.</p>';
      }
    });
  }
});

@php
  $eventosCalendario = ($proximasEntrevistas ?? collect())->map(function ($entrevista) {
    return [
      'date' => optional($entrevista->fecha_hora_inicio ?? $entrevista->fecha)->format('Y-m-d'),
      'label' => trim(($entrevista->solicitud->estudiante->nombre ?? 'Entrevista') . ' ' . ($entrevista->solicitud->estudiante->apellido ?? '')),
      'modalidad' => $entrevista->modalidad ?? '',
    ];
  })->values()->toArray();
@endphp

document.addEventListener('DOMContentLoaded', function () {
  const events = @json($eventosCalendario);

  const grid = document.getElementById('calendarGridEst');
  const eventsList = document.getElementById('calendarEventsListEst');
  const monthLabel = document.getElementById('calendarMonthLabelEst');
  const prevBtn = document.getElementById('prevMonthBtnEst');
  const nextBtn = document.getElementById('nextMonthBtnEst');

  if (!grid || !eventsList || !monthLabel) {
    return;
  }

  const weekdayLabels = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
  const activeDate = events.length ? new Date(events[0].date + 'T00:00:00') : new Date();

  function render(date) {
    const year = date.getFullYear();
    const month = date.getMonth();
    monthLabel.textContent = date.toLocaleDateString('es-CL', { month: 'long', year: 'numeric' });
    grid.innerHTML = '';
    weekdayLabels.forEach(label => {
      const el = document.createElement('div');
      el.className = 'calendar-weekday';
      el.textContent = label;
      grid.appendChild(el);
    });

    const start = new Date(year, month, 1);
    const startOffset = (start.getDay() + 6) % 7; // lunes como inicio
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const todayStr = new Date().toISOString().slice(0, 10);

    for (let i = 0; i < startOffset; i++) {
      const empty = document.createElement('div');
      grid.appendChild(empty);
    }

    for (let day = 1; day <= daysInMonth; day++) {
      const cellDate = new Date(year, month, day);
      const dateStr = cellDate.toISOString().slice(0, 10);
      const cell = document.createElement('div');
      cell.className = 'calendar-cell';
      if (dateStr === todayStr) cell.classList.add('is-today');

      const dayLabel = document.createElement('div');
      dayLabel.className = 'day';
      dayLabel.textContent = day;
      cell.appendChild(dayLabel);

      const dayEvents = events.filter(ev => ev.date === dateStr);
      if (dayEvents.length) {
        // Separar entrevistas por modalidad
        const entrevistasVirtuales = dayEvents.filter(ev => ev.modalidad && ev.modalidad.toLowerCase() === 'virtual');
        const entrevistasPresenciales = dayEvents.filter(ev => ev.modalidad && ev.modalidad.toLowerCase() === 'presencial');
        const entrevistasSinModalidad = dayEvents.filter(ev => !ev.modalidad || (ev.modalidad.toLowerCase() !== 'virtual' && ev.modalidad.toLowerCase() !== 'presencial'));
        
        // Mostrar entrevistas virtuales en celeste
        if (entrevistasVirtuales.length > 0) {
          const dot = document.createElement('div');
          dot.className = 'event-dot event-dot-entrevista-virtual';
          dot.textContent = `${entrevistasVirtuales.length} entrevista${entrevistasVirtuales.length > 1 ? 's' : ''} virtual${entrevistasVirtuales.length > 1 ? 'es' : ''}`;
          cell.appendChild(dot);
        }
        
        // Mostrar entrevistas presenciales en verde
        if (entrevistasPresenciales.length > 0) {
          const dot = document.createElement('div');
          dot.className = 'event-dot event-dot-entrevista-presencial';
          dot.textContent = `${entrevistasPresenciales.length} entrevista${entrevistasPresenciales.length > 1 ? 's' : ''} presencial${entrevistasPresenciales.length > 1 ? 'es' : ''}`;
          cell.appendChild(dot);
        }
        
        // Mostrar entrevistas sin modalidad definida en rojo (por defecto)
        if (entrevistasSinModalidad.length > 0) {
          const dot = document.createElement('div');
          dot.className = 'event-dot';
          dot.textContent = `${entrevistasSinModalidad.length} entrevista${entrevistasSinModalidad.length > 1 ? 's' : ''}`;
          cell.appendChild(dot);
        }
      }

      grid.appendChild(cell);
    }

    eventsList.innerHTML = '';
    events.forEach(ev => {
      const chip = document.createElement('span');
      chip.className = 'event-chip';
      chip.innerHTML = `<i class="fas fa-calendar-day me-1"></i>${new Date(ev.date + 'T00:00:00').toLocaleDateString('es-CL')} · ${ev.label}`;
      eventsList.appendChild(chip);
    });
  }

  prevBtn?.addEventListener('click', () => {
    activeDate.setMonth(activeDate.getMonth() - 1);
    render(activeDate);
  });
  nextBtn?.addEventListener('click', () => {
    activeDate.setMonth(activeDate.getMonth() + 1);
    render(activeDate);
  });

  render(activeDate);
});

document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById('modalDetalleEntrevista');
  if (!modal) return;

  modal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    if (!button) return;

    const fecha = button.getAttribute('data-fecha') || '--';
    const hora = button.getAttribute('data-hora') || '--';
    const estudiante = button.getAttribute('data-estudiante') || '--';
    const asesor = button.getAttribute('data-asesor') || '--';
    const modalidad = button.getAttribute('data-modalidad') || '';
    const descripcion = button.getAttribute('data-descripcion') || '--';

    document.getElementById('det-fecha').textContent = fecha;
    document.getElementById('det-hora').textContent = hora;
    document.getElementById('det-estudiante').textContent = estudiante;
    document.getElementById('det-asesor').textContent = asesor;
    
    const modalidadElement = document.getElementById('det-modalidad');
    const lugarContainer = document.getElementById('det-lugar-container');
    const lugarElement = document.getElementById('det-lugar');
    const linkZoomContainer = document.getElementById('det-link-zoom-container');
    const linkZoomElement = document.getElementById('det-link-zoom');
    
    if (modalidad && modalidad.trim() !== '') {
      modalidadElement.innerHTML = `<span class="badge ${modalidad === 'Virtual' ? 'bg-info' : 'bg-success'}">${modalidad}</span>`;
      
      // Mostrar lugar si la modalidad es Presencial
      if (modalidad === 'Presencial') {
        lugarElement.textContent = 'Sala de tutoria 4to Piso';
        lugarContainer.style.display = 'block';
        linkZoomContainer.style.display = 'none';
      } else if (modalidad === 'Virtual') {
        // Mostrar link de Zoom si la modalidad es Virtual
        linkZoomElement.innerHTML = '<span class="text-muted">Link por crearse</span>';
        linkZoomContainer.style.display = 'block';
        lugarContainer.style.display = 'none';
      } else {
        lugarContainer.style.display = 'none';
        linkZoomContainer.style.display = 'none';
      }
    } else {
      modalidadElement.textContent = '—';
      lugarContainer.style.display = 'none';
      linkZoomContainer.style.display = 'none';
    }
    
    document.getElementById('det-descripcion').textContent = descripcion;
  });
});
</script>
@endpush
