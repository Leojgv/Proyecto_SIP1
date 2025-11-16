@extends('layouts.dashboard_estudiante.estudiante')

@php
    $coleccionNotificaciones = $notificaciones ?? collect();
    $quickAccess = [
        [
            'title' => 'Solicitudes activas',
            'value' => $stats['solicitudes_activas'],
            'helper' => 'Casos en seguimiento',
            'icon' => 'fa-file-alt',
            'variant' => '#b91c1c',
        ],
        [
            'title' => 'Ajustes activos',
            'value' => $stats['ajustes_activos'],
            'helper' => 'Apoyos vigentes',
            'icon' => 'fa-sliders',
            'variant' => '#c53030',
        ],
        [
            'title' => 'Problemas detectados',
            'value' => $stats['problemas_detectados'],
            'helper' => 'Requieren atención',
            'icon' => 'fa-triangle-exclamation',
            'variant' => '#f87171',
        ],
        [
            'title' => 'Próximas entrevistas',
            'value' => $stats['entrevistas_programadas'],
            'helper' => 'Agenda confirmada',
            'icon' => 'fa-comments',
            'variant' => '#fda4af',
        ],
    ];
@endphp

@section('content')
<div class="dashboard-page">
  <div class="page-header mb-4">
    <div>
      <p class="text-danger text-uppercase fw-semibold small mb-1">Tu espacio académico</p>
      <h1 class="h4 mb-1">Mi Dashboard</h1>
      <p class="text-muted mb-0">Gestiona tus solicitudes y ajustes académicos de forma centralizada.</p>
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
                  @if ($solicitud->asesor)
                    <small class="text-muted d-block"><i class="fas fa-user-tie me-1"></i>Asesora: {{ $solicitud->asesor->nombre_completo }}</small>
                  @endif
                </div>
                <a href="{{ route('solicitudes.show', $solicitud) }}" class="btn btn-sm btn-outline-secondary">Ver detalle</a>
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
            <a href="{{ route('entrevistas.index') }}" class="btn btn-sm btn-outline-danger">Ver agenda</a>
          </div>
          <div class="list-group list-group-flush">
            @forelse ($proximasEntrevistas as $entrevista)
              <div class="list-group-item px-0 d-flex flex-wrap justify-content-between gap-2">
                <div>
                  <h6 class="mb-1"><i class="fas fa-calendar-day me-2 text-danger"></i>{{ $entrevista->fecha?->format('d/m/Y') }}</h6>
                  @if ($entrevista->asesor)
                    <small class="text-muted"><i class="fas fa-user me-1"></i>{{ $entrevista->asesor->nombre_completo }}</small>
                  @endif
                </div>
                <a href="{{ route('entrevistas.show', $entrevista) }}" class="btn btn-sm btn-outline-secondary">Ver detalles</a>
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
              <div class="list-group-item px-0 d-flex flex-wrap justify-content-between gap-2">
                <div>
                  <h6 class="mb-1">{{ $ajuste->nombre }}</h6>
                  <small class="text-muted">{{ \Illuminate\Support\Str::title($ajuste->estado ?? 'pendiente') }}</small>
                </div>
                <a href="{{ route('ajustes-razonables.show', $ajuste) }}" class="btn btn-sm btn-outline-secondary">Ver seguimiento</a>
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

@endsection
