@extends('layouts.app')

@section('title', 'Entrevistas')

@section('content')
<div class="container-fluid">
  @include('partials.alerts')

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Entrevistas</h1>
    <a href="{{ route('entrevistas.create') }}" class="btn btn-primary">
      <i class="fas fa-plus"></i> Nueva entrevista
    </a>
  </div>

  <div class="card">
    <div class="card-body table-responsive p-0">
      <table class="table table-striped mb-0">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Solicitud</th>
            <th>Asesor</th>
            <th>Observaciones</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($entrevistas as $entrevista)
            <tr>
              <td>{{ $entrevista->fecha?->format('d/m/Y') }}</td>
              <td>{{ $entrevista->solicitud->estudiante->nombre ?? '—' }} {{ $entrevista->solicitud->estudiante->apellido ?? '' }}</td>
              <td>{{ optional($entrevista->asesorPedagogico)->nombre ? $entrevista->asesorPedagogico->nombre . ' ' . $entrevista->asesorPedagogico->apellido : '—' }}</td>
              <td>{{ $entrevista->observaciones ? \Illuminate\Support\Str::limit($entrevista->observaciones, 40) : '—' }}</td>
              <td class="text-end">
                <a href="{{ route('entrevistas.edit', $entrevista) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                <form action="{{ route('entrevistas.destroy', $entrevista) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar esta entrevista?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center py-4">No hay entrevistas registradas.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
