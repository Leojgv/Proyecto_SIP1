@extends('layouts.app')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0">Detalle de notificación</h1>
    <a href="{{ route('notificaciones.index') }}" class="btn btn-outline-secondary">Volver</a>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Título</dt>
        <dd class="col-sm-9">{{ $notificacion->data['titulo'] ?? 'Sin título' }}</dd>

        <dt class="col-sm-3">Mensaje</dt>
        <dd class="col-sm-9">{{ $notificacion->data['mensaje'] ?? 'N/A' }}</dd>

        <dt class="col-sm-3">Acción</dt>
        <dd class="col-sm-9">
          @if (!empty($notificacion->data['url']))
            <a href="{{ $notificacion->data['url'] }}" target="_blank" rel="noopener">
              {{ $notificacion->data['texto_boton'] ?? $notificacion->data['url'] }}
            </a>
          @else
            <span class="text-muted">Sin enlace</span>
          @endif
        </dd>

        <dt class="col-sm-3">Destinatario</dt>
        <dd class="col-sm-9">
          @if ($notificacion->notifiable)
            {{ $notificacion->notifiable->name ?? ($notificacion->notifiable->nombre ?? 'Usuario') }}
            <div class="text-muted">{{ $notificacion->notifiable->email ?? '' }}</div>
          @else
            <span class="text-muted">Usuario eliminado</span>
          @endif
        </dd>

        <dt class="col-sm-3">Estado</dt>
        <dd class="col-sm-9">
          @if ($notificacion->read_at)
            <span class="badge bg-success">Leída {{ $notificacion->read_at->diffForHumans() }}</span>
          @else
            <span class="badge bg-warning text-dark">Pendiente</span>
          @endif
        </dd>

        <dt class="col-sm-3">Creada</dt>
        <dd class="col-sm-9">{{ optional($notificacion->created_at)->format('d/m/Y H:i') }}</dd>
      </dl>
    </div>
  </div>
</div>
@endsection
