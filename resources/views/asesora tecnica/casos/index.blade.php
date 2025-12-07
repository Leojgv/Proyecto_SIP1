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
      <div class="accordion casos-asesora-accordion" id="casosAsesora">
        @forelse($solicitudes as $solicitud)
          @php
            $collapseId = 'sol-' . $solicitud->id;
            $headingId = 'head-' . $solicitud->id;
            $ajustesCount = $solicitud->ajustesRazonables()->count();
            $estadosPermitidos = ['Pendiente de formulación de ajuste'];
            $puedeEnviarAPreaprobacion = in_array($solicitud->estado, $estadosPermitidos) && $ajustesCount > 0;
            $badgeClass = match($solicitud->estado) {
              'Pendiente de formulación de ajuste' => 'bg-warning text-dark',
              'Pendiente de preaprobación' => 'bg-primary',
              'Aprobado' => 'bg-success',
              'Rechazado' => 'bg-danger',
              default => 'bg-secondary'
            };
          @endphp
          <div class="accordion-item caso-card border-0 mb-3 shadow-sm">
            <h2 class="accordion-header" id="{{ $headingId }}">
              <button class="accordion-button caso-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false" aria-controls="{{ $collapseId }}">
                <div class="d-flex align-items-center gap-3 w-100">
                  <div>
                    <div class="fw-semibold">{{ $solicitud->estudiante->nombre ?? 'Estudiante' }} {{ $solicitud->estudiante->apellido ?? '' }}</div>
                    <small class="text-muted">{{ $solicitud->estudiante->carrera->nombre ?? 'Sin carrera' }}</small>
                  </div>
                  <span class="badge {{ $badgeClass }}">{{ $solicitud->estado ?? 'Pendiente' }}</span>
                  <div class="text-muted small text-nowrap ms-auto">
                    <i class="far fa-calendar me-1"></i>{{ $solicitud->fecha_solicitud?->format('d/m/Y') ?? 's/f' }}
                  </div>
                </div>
              </button>
            </h2>
            <div id="{{ $collapseId }}" class="accordion-collapse collapse" aria-labelledby="{{ $headingId }}" data-bs-parent="#casosAsesora">
              <div class="accordion-body caso-body">
                <p class="text-muted mb-2"><strong>Descripcion:</strong> {{ $solicitud->descripcion ?: 'Sin descripcion registrada.' }}</p>
                <div class="d-flex flex-wrap align-items-center gap-3">
                  <div class="text-muted small">Solicitado el {{ $solicitud->fecha_solicitud?->format('d/m/Y') ?? 's/f' }}</div>
                  <div class="text-muted small">Ajustes: <span class="fw-semibold">{{ $ajustesCount }}</span></div>
                  <div class="ms-auto">
                    @if($puedeEnviarAPreaprobacion)
                      <form action="{{ route('asesora-tecnica.solicitudes.enviar-preaprobacion', $solicitud) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de enviar esta solicitud a Asesoría Pedagógica para preaprobación? Esta acción cambiará el estado a \"Pendiente de preaprobación\".');">
                          <i class="fas fa-paper-plane me-1"></i>Enviar a Preaprobación
                        </button>
                      </form>
                    @elseif($solicitud->estado === 'Pendiente de preaprobación')
                      <span class="badge bg-warning text-dark">En preaprobación</span>
                    @elseif(in_array($solicitud->estado, ['Pendiente de Aprobación', 'Aprobado', 'Rechazado']))
                      <span class="text-muted small">Enviado</span>
                    @else
                      <span class="text-muted small">Formular ajustes primero</span>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
        @empty
          <p class="text-center text-muted py-4 mb-0">No tienes casos asignados actualmente.</p>
        @endforelse
      </div>
      <div class="d-flex justify-content-end mt-3">
        {{ $solicitudes->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
