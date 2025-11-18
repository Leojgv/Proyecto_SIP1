@extends('layouts.dashboard_coordinadora.app')

@section('title', 'Casos')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
      <h1 class="h4 mb-1">Casos de solicitudes</h1>
      <p class="text-muted mb-0">Listado de solicitudes vinculadas a los estudiantes.</p>
    </div>
  </div>

  @if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('status') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  @endif

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Estudiante</th>
              <th>Carrera</th>
              <th>Fecha solicitud</th>
              <th>Estado</th>
              <th>Descripción</th>
              <th class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($solicitudes as $solicitud)
              <tr>
                <td>{{ $solicitud->estudiante->nombre ?? 'Estudiante' }} {{ $solicitud->estudiante->apellido ?? '' }}</td>
                <td>{{ $solicitud->estudiante->carrera->nombre ?? 'Sin carrera' }}</td>
                <td>{{ $solicitud->fecha_solicitud?->format('d/m/Y') ?? 's/f' }}</td>
                <td><span class="badge bg-warning text-dark">{{ $solicitud->estado ?? 'Pendiente' }}</span></td>
                <td class="text-muted small">{{ \Illuminate\Support\Str::limit($solicitud->descripcion, 70) }}</td>
                <td class="text-end">
                  @if($solicitud->estado === 'Pendiente de entrevista')
                    <form action="{{ route('coordinadora.casos.informar-ctp', $solicitud) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Informar a CTP para formular el caso? Esta acción actualizará el estado.');">
                        <i class="fas fa-paper-plane me-1"></i>Informar a CTP
                      </button>
                    </form>
                  @elseif($solicitud->estado === 'Pendiente de formulación del caso')
                    <span class="text-muted small">Informado a CTP</span>
                  @else
                    <span class="text-muted small">En proceso</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-4">No hay casos registrados.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-end mt-3">
        {{ $solicitudes->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
