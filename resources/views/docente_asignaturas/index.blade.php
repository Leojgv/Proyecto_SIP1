@extends('layouts.app')

@section('title', 'Asignaciones docente-asignatura')

@section('content')
<div class="container-fluid">
  @include('partials.alerts')

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Asignaciones docente-asignatura</h1>
    <a href="{{ route('docente-asignaturas.create') }}" class="btn btn-primary">
      <i class="fas fa-plus"></i> Nueva asignación
    </a>
  </div>

  <div class="card">
    <div class="card-body table-responsive p-0">
      <table class="table table-striped mb-0">
        <thead>
          <tr>
            <th>Docente</th>
            <th>Asignatura</th>
            <th>Carrera</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($asignaciones as $asignacion)
            <tr>
              <td>{{ $asignacion->docente->nombre ?? '—' }} {{ $asignacion->docente->apellido ?? '' }}</td>
              <td>{{ $asignacion->asignatura->nombre ?? '—' }}</td>
              <td>{{ $asignacion->carrera->nombre ?? '—' }}</td>
              <td class="text-end">
                <a href="{{ route('docente-asignaturas.edit', $asignacion) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                <form action="{{ route('docente-asignaturas.destroy', $asignacion) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar esta asignación?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center py-4">No hay asignaciones registradas.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
