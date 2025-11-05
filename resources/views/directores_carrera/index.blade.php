@extends('layouts.app')

@section('title', 'Directores de carrera')

@section('content')
<div class="container-fluid">
  @include('partials.alerts')

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Directores de carrera</h1>
    <a href="{{ route('directores-carrera.create') }}" class="btn btn-primary">
      <i class="fas fa-plus"></i> Nuevo director
    </a>
  </div>

  <div class="card">
    <div class="card-body table-responsive p-0">
      <table class="table table-striped mb-0">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Teléfono</th>
            <th>Carrera</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($directores as $director)
            <tr>
              <td>{{ $director->nombre }} {{ $director->apellido }}</td>
              <td>{{ $director->email }}</td>
              <td>{{ $director->telefono ?? '—' }}</td>
              <td>{{ $director->carrera->nombre ?? '—' }}</td>
              <td class="text-end">
                <a href="{{ route('directores-carrera.edit', $director) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                <form action="{{ route('directores-carrera.destroy', $director) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar este director?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center py-4">No hay directores registrados.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
