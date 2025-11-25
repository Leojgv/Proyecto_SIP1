@extends('layouts.dashboard_estudiante.estudiante')

@php
    $coleccionNotificaciones = $notificaciones ?? collect();
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
            'title' => 'Próximas entrevistas',
            'value' => $stats['entrevistas_programadas'],
            'helper' => 'Agenda confirmada',
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
      <p class="text-muted mb-0">Revisa el estado de tus solicitudes, consulta tus ajustes académicos aprobados, gestiona tus entrevistas y mantente al día con tus notificaciones.</p>
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

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body d-flex flex-wrap justify-content-between align-items-center">
      <div>
        <h5 class="mb-1">¿Necesitas solicitar una entrevista?</h5>
        <p class="text-muted mb-0">Coordina con el equipo de asesoría pedagógica para recibir apoyo personalizado.</p>
      </div>
      <a href="{{ route('estudiantes.entrevistas.create') }}" class="btn btn-danger">Solicitar entrevista</a>
    </div>
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
          <div class="mb-3">
            <p class="mb-1 text-muted small">Fecha</p>
            <div class="fw-semibold" id="det-fecha">--</div>
          </div>
          <div class="mb-3">
            <p class="mb-1 text-muted small">Hora</p>
            <div class="fw-semibold" id="det-hora">--</div>
          </div>
          <div class="mb-3">
            <p class="mb-1 text-muted small">Estudiante</p>
            <div class="fw-semibold" id="det-estudiante">--</div>
          </div>
          <div class="mb-3">
            <p class="mb-1 text-muted small">Asesor</p>
            <div class="fw-semibold" id="det-asesor">--</div>
          </div>
          <div class="mb-3">
            <p class="mb-1 text-muted small">Modalidad</p>
            <div class="fw-semibold" id="det-modalidad">--</div>
          </div>
          <div>
            <p class="mb-1 text-muted small">Descripción</p>
            <div class="text-muted" id="det-descripcion">--</div>
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
            <a href="{{ route('solicitudes.index') }}" class="btn btn-sm btn-outline-danger">Ver todas</a>
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
                        data-solicitud-ajustes="{{ json_encode($solicitud->ajustesRazonables->map(function($a) { return ['nombre' => $a->nombre, 'estado' => $a->estado]; })) }}"
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
            <a href="{{ route('ajustes-razonables.index') }}" class="btn btn-sm btn-outline-danger">Gestionar</a>
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
                <div class="d-flex align-items-center gap-2">
                  <div class="text-danger" style="font-size: 1.5rem;">
                    <i class="fas {{ $icono }}"></i>
                  </div>
                  <div>
                    <h6 class="mb-1 d-flex align-items-center gap-2">
                      {{ $ajuste->nombre }}
                    </h6>
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

                        @if($ajuste->solicitud)
                          <div class="col-12">
                            <div class="border rounded p-3 bg-light">
                              <small class="text-muted d-block mb-2">
                                <i class="fas fa-file-alt me-1"></i><strong>Información de la Solicitud</strong>
                              </small>
                              <div class="mb-2">
                                <strong>Fecha:</strong> {{ $ajuste->solicitud->fecha_solicitud?->format('d/m/Y') ?? '—' }}
                              </div>
                              @if($ajuste->solicitud->descripcion)
                                <div>
                                  <strong>Descripción:</strong>
                                  <p class="mb-0 mt-1 text-break">{{ $ajuste->solicitud->descripcion }}</p>
                                </div>
                              @endif
                            </div>
                          </div>
                        @endif

                        <div class="col-12">
                          <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Información:</strong> Este ajuste se encuentra en proceso de revisión. 
                            Cualquier actualización será notificada en tu panel de notificaciones.
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
    <div class="col-xl-6" id="notificaciones">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h5 class="card-title mb-0">Notificaciones</h5>
              <small class="text-muted">Últimas actualizaciones</small>
            </div>
            @if (($notificacionesSinLeer ?? 0) > 0)
              <span class="badge bg-danger">{{ $notificacionesSinLeer }} nuevas</span>
            @endif
          </div>
          <div class="list-group list-group-flush">
            @forelse ($coleccionNotificaciones as $notification)
              @php
                $payload = (array) $notification->data;
                $titulo = $payload['titulo'] ?? $payload['title'] ?? 'Actualización';
                $descripcion = $payload['mensaje'] ?? $payload['message'] ?? $payload['body'] ?? null;
                $enlace = $payload['url'] ?? $payload['action_url'] ?? null;
                $textoEnlace = $payload['texto_boton'] ?? $payload['action_text'] ?? 'Ver detalle';
              @endphp
              <div class="list-group-item px-0 d-flex flex-wrap justify-content-between gap-3">
                <div>
                  <h6 class="mb-1">{{ $titulo }}</h6>
                  @if ($descripcion)
                    <p class="text-muted small mb-1">{{ $descripcion }}</p>
                  @endif
                  <small class="text-muted">{{ optional($notification->created_at)->diffForHumans() }}</small>
                </div>
                <div class="text-end">
                  @if (is_null($notification->read_at))
                    <span class="badge bg-light text-danger border">Nueva</span>
                  @endif
                  @if ($enlace)
                    <div>
                      <a href="{{ $enlace }}" class="btn btn-link btn-sm px-0">{{ $textoEnlace }}</a>
                    </div>
                  @endif
                </div>
              </div>
            @empty
              <p class="text-muted text-center my-4">No tienes notificaciones pendientes.</p>
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
        <!-- Fecha de solicitud -->
        <div class="mb-4 pb-3 border-bottom">
          <p class="text-muted small mb-1 fw-semibold">Fecha de solicitud</p>
          <h6 class="mb-0 fw-normal" id="modal-fecha-solicitud">-</h6>
        </div>

        <!-- Información general -->
        <div class="mb-4">
          <dl class="row mb-0 g-3">
            <dt class="col-sm-4 text-muted small fw-semibold">Estado</dt>
            <dd class="col-sm-8 mb-0">
              <span class="badge bg-secondary" id="modal-estado">-</span>
            </dd>

            <dt class="col-sm-4 text-muted small fw-semibold">Coordinadora</dt>
            <dd class="col-sm-8 mb-0" id="modal-coordinadora">-</dd>

            <dt class="col-sm-4 text-muted small fw-semibold">Director de carrera</dt>
            <dd class="col-sm-8 mb-0" id="modal-director">-</dd>

            @if(isset($estudiante))
            <dt class="col-sm-4 text-muted small fw-semibold">Carrera</dt>
            <dd class="col-sm-8 mb-0">{{ $estudiante->carrera->nombre ?? 'Sin carrera asignada' }}</dd>
            @endif
          </dl>
        </div>

        <!-- Motivo de rechazo -->
        <div class="mb-4" id="modal-motivo-container" style="display: none;">
          <p class="text-muted small mb-2 fw-semibold">Motivo de rechazo</p>
          <div class="alert alert-warning mb-0 py-2 px-3" id="modal-motivo-rechazo">-</div>
        </div>

        <!-- Descripción -->
        <div class="mb-4 pb-3 border-bottom">
          <p class="text-muted small mb-2 fw-semibold">Descripción</p>
          <p class="mb-0 text-break" id="modal-descripcion" style="line-height: 1.6;">-</p>
        </div>

        <!-- Ajustes Razonables -->
        <div class="mb-4" id="modal-ajustes-container">
          <h6 class="mb-3 fw-semibold d-flex align-items-center">
            <i class="fas fa-sliders me-2 text-danger"></i>Ajustes Razonables
          </h6>
          <div id="modal-ajustes-lista">
            <p class="text-muted small mb-0">No hay ajustes razonables asociados a esta solicitud.</p>
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
    gap: .5rem;
    background: #f9f7fb;
    padding: .75rem;
  }
  .calendar-weekday {
    text-align: center;
    font-weight: 600;
    color: #555;
    padding: .35rem 0;
  }
  .calendar-cell {
    background: #fff;
    border: 1px solid #f0f0f5;
    border-radius: 12px;
    min-height: 70px;
    padding: .5rem;
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
  }
  .calendar-cell .event-dot {
    position: absolute;
    bottom: .5rem;
    left: .5rem;
    background: #d62828;
    color: #fff;
    border-radius: 999px;
    padding: .2rem .5rem;
    font-size: .75rem;
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
      const ajustesJson = button.getAttribute('data-solicitud-ajustes');
      const entrevistasJson = button.getAttribute('data-solicitud-entrevistas');

      // Actualizar contenido del modal
      document.getElementById('modal-fecha-solicitud').textContent = fechaSolicitud || 's/f';
      document.getElementById('modal-estado').textContent = estado || 'Sin estado';
      document.getElementById('modal-descripcion').textContent = descripcion || 'Sin descripción registrada';
      document.getElementById('modal-coordinadora').textContent = coordinadora || 'Sin asignar';
      document.getElementById('modal-director').textContent = director || 'No asignado';

      // Motivo de rechazo (si existe)
      const motivoContainer = document.getElementById('modal-motivo-container');
      const motivoRechazo = document.getElementById('modal-motivo-rechazo');
      if (motivo && motivo.trim() !== '') {
        motivoRechazo.textContent = motivo;
        motivoContainer.style.display = 'block';
      } else {
        motivoContainer.style.display = 'none';
      }

      // Ajustes razonables
      const ajustesContainer = document.getElementById('modal-ajustes-lista');
      if (ajustesJson && ajustesJson !== 'null' && ajustesJson !== '[]') {
        try {
          const ajustes = JSON.parse(ajustesJson);
          if (ajustes && ajustes.length > 0) {
            ajustesContainer.innerHTML = ajustes.map(ajuste => `
              <div class="card mb-3 border shadow-sm">
                <div class="card-body p-3">
                  <h6 class="card-title mb-2 fw-semibold">${ajuste.nombre || 'Ajuste sin nombre'}</h6>
                  <div class="row g-2 small">
                    <div class="col-12">
                      <span class="text-muted">Estado:</span>
                      <span class="badge bg-info ms-2">${ajuste.estado || 'Sin estado'}</span>
                    </div>
                  </div>
                </div>
              </div>
            `).join('');
          } else {
            ajustesContainer.innerHTML = '<p class="text-muted small">No hay ajustes razonables asociados a esta solicitud.</p>';
          }
        } catch (e) {
          ajustesContainer.innerHTML = '<p class="text-muted small">No hay ajustes razonables asociados a esta solicitud.</p>';
        }
      } else {
        ajustesContainer.innerHTML = '<p class="text-muted small">No hay ajustes razonables asociados a esta solicitud.</p>';
      }

      // Entrevistas
      const entrevistasContainer = document.getElementById('modal-entrevistas-lista');
      if (entrevistasJson && entrevistasJson !== 'null' && entrevistasJson !== '[]') {
        try {
          const entrevistas = JSON.parse(entrevistasJson);
          if (entrevistas && entrevistas.length > 0) {
            entrevistasContainer.innerHTML = entrevistas.map(entrevista => `
              <div class="card mb-3 border shadow-sm">
                <div class="card-body p-3">
                  <h6 class="card-title mb-2 fw-semibold d-flex align-items-center">
                    <i class="fas fa-calendar-day me-2 text-danger"></i>${entrevista.fecha || 's/f'}
                  </h6>
                  <div class="row g-2 small">
                    ${entrevista.hora_inicio && entrevista.hora_fin ? `
                      <div class="col-12">
                        <span class="text-muted"><i class="fas fa-clock me-1"></i>Horario:</span>
                        <span class="ms-1">${entrevista.hora_inicio} - ${entrevista.hora_fin}</span>
                      </div>
                    ` : ''}
                    <div class="col-12">
                      <span class="text-muted"><i class="fas fa-user me-1"></i>Asesor:</span>
                      <span class="ms-1">${entrevista.asesor || 'Sin asignar'}</span>
                    </div>
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

document.addEventListener('DOMContentLoaded', function () {
  const events = @json(
    ($proximasEntrevistas ?? collect())->map(function ($entrevista) {
        return [
            'date' => optional($entrevista->fecha_hora_inicio ?? $entrevista->fecha)->format('Y-m-d'),
            'label' => trim(($entrevista->solicitud->estudiante->nombre ?? 'Entrevista') . ' ' . ($entrevista->solicitud->estudiante->apellido ?? '')),
        ];
    })
  );

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
        const dot = document.createElement('div');
        dot.className = 'event-dot';
        dot.textContent = `${dayEvents.length} entrevista${dayEvents.length > 1 ? 's' : ''}`;
        cell.appendChild(dot);
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
    if (modalidad && modalidad.trim() !== '') {
      modalidadElement.innerHTML = `<span class="badge ${modalidad === 'Virtual' ? 'bg-info' : 'bg-success'}">${modalidad}</span>`;
    } else {
      modalidadElement.textContent = '—';
    }
    
    document.getElementById('det-descripcion').textContent = descripcion;
  });
});
</script>
@endpush
