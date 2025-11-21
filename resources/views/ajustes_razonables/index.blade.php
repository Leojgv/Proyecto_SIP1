@extends('layouts.app')

@section('title', 'Ajustes razonables')

@section('content')
<div class="container-fluid">
  @include('partials.alerts')

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Ajustes razonables</h1>
    <a href="{{ route('ajustes-razonables.create') }}" class="btn btn-primary">
      <i class="fas fa-plus"></i> Nuevo ajuste
    </a>
  </div>

  <div class="card">
    <div class="card-body table-responsive p-0">
      <table class="table table-striped mb-0">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Estudiante</th>
            <th>Solicitud</th>
            <th>Estado</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($ajustes as $ajuste)
            <tr>
              <td>{{ $ajuste->nombre }}</td>
              <td>{{ $ajuste->estudiante->nombre ?? '—' }} {{ $ajuste->estudiante->apellido ?? '' }}</td>
              <td>{{ $ajuste->solicitud->fecha_solicitud?->format('d/m/Y') ?? '—' }}</td>
              <td>{{ $ajuste->estado ?? '—' }}</td>
              <td class="text-end">
                <a href="{{ route('ajustes-razonables.edit', $ajuste) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                <form action="{{ route('ajustes-razonables.destroy', $ajuste) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar este ajuste?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center py-4">No hay ajustes registrados.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
