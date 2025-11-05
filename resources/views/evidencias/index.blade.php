@extends('layouts.app')

@section('title', 'Evidencias')

@section('content')
<div class="container-fluid">
  @include('partials.alerts')

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Evidencias</h1>
    <a href="{{ route('evidencias.create') }}" class="btn btn-primary">
      <i class="fas fa-plus"></i> Nueva evidencia
    </a>
  </div>

  <div class="card">
    <div class="card-body table-responsive p-0">
      <table class="table table-striped mb-0">
        <thead>
          <tr>
            <th>Tipo</th>
            <th>Descripción</th>
            <th>Solicitud</th>
            <th>Archivo</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($evidencias as $evidencia)
            <tr>
              <td>{{ $evidencia->tipo }}</td>
              <td>{{ $evidencia->descripcion ? \Illuminate\Support\Str::limit($evidencia->descripcion, 40) : '—' }}</td>
              <td>{{ $evidencia->solicitud->fecha_solicitud?->format('d/m/Y') ?? '—' }}</td>
              <td>{{ $evidencia->ruta_archivo ?? '—' }}</td>
              <td class="text-end">
                <a href="{{ route('evidencias.edit', $evidencia) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                <form action="{{ route('evidencias.destroy', $evidencia) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar esta evidencia?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center py-4">No hay evidencias registradas.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
