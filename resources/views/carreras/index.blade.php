@extends('layouts.app')

@section('title', 'Carreras')

@section('content')
<div class="container-fluid">
  @include('partials.alerts')

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Carreras</h1>
    <a href="{{ route('carreras.create') }}" class="btn btn-primary">
      <i class="fas fa-plus"></i> Nueva carrera
    </a>
  </div>

  <div class="card">
    <div class="card-body table-responsive p-0">
      <table class="table table-striped mb-0">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Jornada</th>
            <th>Facultad</th>
            <th>Grado</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($carreras as $carrera)
            <tr>
              <td>{{ $carrera->nombre }}</td>
              <td>{{ $carrera->jornada ?? '—' }}</td>
              <td>{{ $carrera->facultad ?? '—' }}</td>
              <td>{{ $carrera->grado ?? '—' }}</td>
              <td class="text-end">
                <a href="{{ route('carreras.edit', $carrera) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                <form action="{{ route('carreras.destroy', $carrera) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar esta carrera?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center py-4">No hay carreras registradas.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
