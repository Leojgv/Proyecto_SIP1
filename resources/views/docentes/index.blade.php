@extends('layouts.app')

@section('title', 'Docentes')

@section('content')
<div class="container-fluid">
  @include('partials.alerts')

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Docentes</h1>
    <a href="{{ route('docentes.create') }}" class="btn btn-primary">
      <i class="fas fa-plus"></i> Nuevo docente
    </a>
  </div>

  <div class="card">
    <div class="card-body table-responsive p-0">
      <table class="table table-striped mb-0">
        <thead>
          <tr>
            <th>RUT</th>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Teléfono</th>
            <th>Especialidad</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($docentes as $docente)
            <tr>
              <td>{{ $docente->rut }}</td>
              <td>{{ $docente->nombre }} {{ $docente->apellido }}</td>
              <td>{{ $docente->email }}</td>
              <td>{{ $docente->telefono ?? '—' }}</td>
              <td>{{ $docente->especialidad ?? '—' }}</td>
              <td class="text-end">
                <a href="{{ route('docentes.edit', $docente) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                <form action="{{ route('docentes.destroy', $docente) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar este docente?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-4">No hay docentes registrados.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
