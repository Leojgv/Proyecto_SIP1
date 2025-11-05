@extends('layouts.app')

@section('title', 'Asesores pedagógicos')

@section('content')
<div class="container-fluid">
  @include('partials.alerts')

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Asesores pedagógicos</h1>
    <a href="{{ route('asesores-pedagogicos.create') }}" class="btn btn-primary">
      <i class="fas fa-plus"></i> Nuevo asesor
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
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($asesores as $asesor)
            <tr>
              <td>{{ $asesor->nombre }} {{ $asesor->apellido }}</td>
              <td>{{ $asesor->email }}</td>
              <td>{{ $asesor->telefono ?? '—' }}</td>
              <td class="text-end">
                <a href="{{ route('asesores-pedagogicos.edit', $asesor) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                <form action="{{ route('asesores-pedagogicos.destroy', $asesor) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar este asesor?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center py-4">No hay asesores registrados.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
