@extends('layouts.app')

@section('title', 'Estudiantes')

@section('content')
<div class="container-fluid">
  @include('partials.alerts')

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Estudiantes</h1>
    <a href="{{ route('estudiantes.create') }}" class="btn btn-primary">Nuevo estudiante</a>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th scope="col">RUT</th>
            <th scope="col">Nombre</th>
            <th scope="col">Apellido</th>
            <th scope="col">Correo</th>
            <th scope="col">Teléfono</th>
            <th scope="col">Carrera</th>
            <th scope="col" class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($estudiantes as $estudiante)
            <tr>
              <td>{{ $estudiante->rut }}</td>
              <td>{{ $estudiante->nombre }}</td>
              <td>{{ $estudiante->apellido }}</td>
              <td>{{ $estudiante->email }}</td>
              <td>{{ $estudiante->telefono }}</td>
              <td>{{ $estudiante->carrera?->nombre }}</td>
              <td class="text-end">
                <div class="btn-group" role="group" aria-label="Acciones">
                  <a href="{{ route('estudiantes.show', $estudiante) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                  <a href="{{ route('estudiantes.edit', $estudiante) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                  <form action="{{ route('estudiantes.destroy', $estudiante) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-4">No hay estudiantes registrados.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
