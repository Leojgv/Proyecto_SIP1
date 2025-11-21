@extends('layouts.dashboard_asesoratecnica.app')

@section('title', 'Casos')

@section('content')
<div class="dashboard-page">
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

  <div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h1 class="h4 mb-1">Casos asignados</h1>
      <p class="text-muted mb-0">Casos que requieren ajustes razonables.</p>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Estudiante</th>
              <th>Programa</th>
              <th>Fecha solicitud</th>
              <th>Estado</th>
              <th>Descripcion</th>
              <th>Ajustes</th>
              <th class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($solicitudes as $solicitud)
              @php
                $ajustesCount = $solicitud->ajustesRazonables()->count();
                $estadosPermitidos = ['Pendiente de formulación de ajuste'];
                $puedeEnviarAPreaprobacion = in_array($solicitud->estado, $estadosPermitidos) && $ajustesCount > 0;
              @endphp
              <tr>
                <td>{{ $solicitud->estudiante->nombre ?? 'Estudiante' }} {{ $solicitud->estudiante->apellido ?? '' }}</td>
                <td>{{ $solicitud->estudiante->carrera->nombre ?? 'Sin carrera' }}</td>
                <td>{{ $solicitud->fecha_solicitud?->format('d/m/Y') ?? 's/f' }}</td>
                <td>
                  <span class="badge bg-warning text-dark">{{ $solicitud->estado ?? 'Pendiente' }}</span>
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
                  @if($puedeEnviarAPreaprobacion)
                    <form action="{{ route('asesora-tecnica.solicitudes.enviar-preaprobacion', $solicitud) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de enviar esta solicitud a Asesoría Pedagógica para preaprobación? Esta acción cambiará el estado a \"Pendiente de preaprobación\".');">
                        <i class="fas fa-paper-plane me-1"></i>Enviar a Preaprobación
                      </button>
                    </form>
                  @elseif($solicitud->estado === 'Pendiente de preaprobación')
                    <span class="badge bg-warning text-dark">En preaprobación</span>
                  @elseif($solicitud->estado === 'Pendiente de Aprobación' || $solicitud->estado === 'Aprobado' || $solicitud->estado === 'Rechazado')
                    <span class="text-muted small">Enviado</span>
                  @else
                    <span class="text-muted small">Formular ajustes primero</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-4">No tienes casos asignados actualmente.</td>
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
