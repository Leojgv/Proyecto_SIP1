@extends('layouts.app')

@section('title', 'Asignaturas')

@section('content')
<div class="container-fluid">
  @include('partials.alerts')

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Asignaturas</h1>
    <a href="{{ route('asignaturas.create') }}" class="btn btn-primary">
      <i class="fas fa-plus"></i> Nueva asignatura
    </a>
  </div>

  <div class="card">
    <div class="card-body table-responsive p-0">
      <table class="table table-striped mb-0">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Estado</th>
            <th>Carrera</th>
            <th>Asesora tecnica pedagogica</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($asignaturas as $asignatura)
            <tr>
              <td>{{ $asignatura->nombre }}</td>
              <td>{{ $asignatura->tipo ?? 'â€”' }}</td>
              <td>{{ $asignatura->estado ?? 'â€”' }}</td>
              <td>{{ $asignatura->carrera->nombre ?? 'â€”' }}</td>
              <td>{{ optional($asignatura->docente)->nombre ? $asignatura->docente->nombre . ' ' . $asignatura->docente->apellido : 'â€”' }}</td>
              <td class="text-end">
                <a href="{{ route('asignaturas.edit', $asignatura) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                <form action="{{ route('asignaturas.destroy', $asignatura) }}" method="POST" class="d-inline" onsubmit="return confirm('Â¿Deseas eliminar esta asignatura?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-4">No hay asignaturas registradas.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

