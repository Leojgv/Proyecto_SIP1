@extends('layouts.dashboard_asesorapedagogica.app')

@section('title', 'Casos - Asesora Pedagógica')

@section('content')
<div class="dashboard-page">
  <div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h1 class="h4 mb-1">Casos en Preaprobación</h1>
      <p class="text-muted mb-0">Revisa y gestiona casos pendientes de preaprobación pedagógica.</p>
    </div>
  </div>

  @if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
              <th>Ajustes</th>
              <th class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($solicitudes as $solicitud)
              @php
                $ajustesCount = $solicitud->ajustesRazonables->count();
                $esPreaprobacion = $solicitud->estado === 'Pendiente de preaprobación';
              @endphp
              <tr>
                <td>{{ $solicitud->estudiante->nombre ?? 'Estudiante' }} {{ $solicitud->estudiante->apellido ?? '' }}</td>
                <td>{{ $solicitud->estudiante->carrera->nombre ?? 'Sin carrera' }}</td>
                <td>{{ $solicitud->fecha_solicitud?->format('d/m/Y') ?? 's/f' }}</td>
                <td>
                  @if($esPreaprobacion)
                    <span class="badge bg-warning text-dark">
                      <i class="fas fa-clock me-1"></i>{{ $solicitud->estado }}
                    </span>
                  @else
                    <span class="badge bg-secondary">{{ $solicitud->estado ?? 'Pendiente' }}</span>
                  @endif
                </td>
                <td class="text-muted small">{{ \Illuminate\Support\Str::limit($solicitud->descripcion, 60) }}</td>
                <td>
                  @if($ajustesCount > 0)
                    <span class="badge bg-success">{{ $ajustesCount }} ajuste(s)</span>
                  @else
                    <span class="badge bg-secondary">Sin ajustes</span>
                  @endif
                </td>
                <td class="text-end">
                  <a href="{{ route('asesora-pedagogica.casos.show', $solicitud) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-eye me-1"></i>Revisar
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-4">
                  <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                  No hay casos pendientes de preaprobación.
                </td>
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
