@extends('layouts.app')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-1">Notificaciones</h1>
      <p class="text-muted mb-0">Gestiona los avisos para todos los roles.</p>
    </div>
    <a href="{{ route('notificaciones.create') }}" class="btn btn-primary">Nueva notificación</a>
  </div>

  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card shadow-sm border-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>Título</th>
            <th>Destinatario</th>
            <th>Estado</th>
            <th>Creada</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($notificaciones as $notificacion)
            <tr>
              <td>{{ $notificacion->data['titulo'] ?? 'Sin título' }}</td>
              <td>
                @if ($notificacion->notifiable)
                  {{ $notificacion->notifiable->name ?? ($notificacion->notifiable->nombre ?? 'Usuario') }}
                  <div class="small text-muted">{{ $notificacion->notifiable->email ?? '' }}</div>
                @else
                  <span class="text-muted">Usuario eliminado</span>
                @endif
              </td>
              <td>
                @if ($notificacion->read_at)
                  <span class="badge bg-success">Leída</span>
                @else
                  <span class="badge bg-warning text-dark">Pendiente</span>
                @endif
              </td>
              <td>{{ optional($notificacion->created_at)->format('d/m/Y H:i') }}</td>
              <td class="text-end">
                <a href="{{ route('notificaciones.show', $notificacion) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                <form class="d-inline" action="{{ route('notificaciones.destroy', $notificacion) }}" method="POST" onsubmit="return confirm('¿Eliminar esta notificación?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center text-muted py-4">Aún no se han enviado notificaciones.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $notificaciones->links() }}</div>
  </div>
</div>
@endsection
