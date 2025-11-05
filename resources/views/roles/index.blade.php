@extends('layouts.app')

@section('title', 'Roles')

@section('content')
<div class="container-fluid">
  @include('partials.alerts')

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Roles</h1>
    <a href="{{ route('roles.create') }}" class="btn btn-primary">
      <i class="fas fa-plus"></i> Nuevo rol
    </a>
  </div>

  <div class="card">
    <div class="card-body table-responsive p-0">
      <table class="table table-striped mb-0">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Descripción</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($roles as $rol)
            <tr>
              <td>{{ $rol->nombre }}</td>
              <td>{{ $rol->descripcion ?? '—' }}</td>
              <td class="text-end">
                <a href="{{ route('roles.edit', $rol) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                <form action="{{ route('roles.destroy', $rol) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar este rol?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="text-center py-4">No hay roles registrados.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
