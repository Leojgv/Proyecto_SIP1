@extends('layouts.app')

@section('title', 'Solicitudes')

@section('content')
<div class="container-fluid">
  @include('partials.alerts')

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Solicitudes</h1>
    <a href="{{ route('solicitudes.create') }}" class="btn btn-primary">
      <i class="fas fa-plus"></i> Nueva solicitud
    </a>
  </div>

  <div class="card">
    <div class="card-body table-responsive p-0">
      <table class="table table-striped mb-0">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Estudiante</th>
            <th>Asesora pedagógica</th>
            <th>Director de carrera</th>
            <th>Estado</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($solicitudes as $solicitud)
            <tr>
              <td>{{ $solicitud->fecha_solicitud?->format('d/m/Y') }}</td>
              <td>{{ $solicitud->estudiante->nombre ?? 'â€”' }} {{ $solicitud->estudiante->apellido ?? '' }}</td>
              <td>{{ optional($solicitud->asesor)->nombre ? $solicitud->asesor->nombre . ' ' . $solicitud->asesor->apellido : 'â€”' }}</td>
              <td>{{ optional($solicitud->director)->nombre ? $solicitud->director->nombre . ' ' . $solicitud->director->apellido : 'â€”' }}</td>
              <td>{{ $solicitud->estado ?? 'â€”' }}</td>
              <td class="text-end">
                <a href="{{ route('solicitudes.edit', $solicitud) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                <form action="{{ route('solicitudes.destroy', $solicitud) }}" method="POST" class="d-inline" onsubmit="return confirm('Â¿Deseas eliminar esta solicitud?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-4">No hay solicitudes registradas.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection


