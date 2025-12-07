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
      <div class="accordion casos-asesora-accordion" id="casosPedagogica">
        @forelse($solicitudes as $solicitud)
          @php
            $collapseId = 'sol-' . $solicitud->id;
            $headingId = 'head-' . $solicitud->id;
            $ajustesCount = $solicitud->ajustesRazonables->count();
            $esPreaprobacion = $solicitud->estado === 'Pendiente de preaprobación';
            $badgeClass = match($solicitud->estado) {
              'Pendiente de preaprobación' => 'bg-warning text-dark',
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
            <div id="{{ $collapseId }}" class="accordion-collapse collapse" aria-labelledby="{{ $headingId }}" data-bs-parent="#casosPedagogica">
              <div class="accordion-body caso-body">
                <p class="text-muted mb-2"><strong>Descripción:</strong> {{ $solicitud->descripcion ?: 'Sin descripción registrada.' }}</p>
                <div class="d-flex flex-wrap align-items-center gap-3">
                  <div class="text-muted small">Solicitado el {{ $solicitud->fecha_solicitud?->format('d/m/Y') ?? 's/f' }}</div>
                  <div class="text-muted small">Ajustes: <span class="fw-semibold">{{ $ajustesCount }}</span></div>
                  <div class="ms-auto">
                    <a href="{{ route('asesora-pedagogica.casos.show', $solicitud) }}" class="btn btn-sm btn-primary">
                      <i class="fas fa-eye me-1"></i>Revisar
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @empty
          <p class="text-center text-muted py-4 mb-0">
            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
            No hay casos pendientes de preaprobación.
          </p>
        @endforelse
      </div>
      <div class="d-flex justify-content-end mt-3">
        {{ $solicitudes->links() }}
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  .casos-asesora-accordion .caso-card {
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    overflow: hidden;
  }
  .caso-btn {
    background: #eef2ff;
    color: #1f2937;
  }
  .caso-btn:focus { box-shadow: none; }
  .caso-body {
    background: #fff;
    color: #1f2937;
  }
  .caso-body .text-muted { color: #6b7280 !important; }
  .dark-mode .casos-asesora-accordion .caso-card {
    border-color: #1e293b;
    box-shadow: 0 10px 30px rgba(3, 7, 18, .35);
  }
  .dark-mode .caso-btn {
    background: #0f172a;
    color: #e5e7eb;
  }
  .dark-mode .caso-body {
    background: #0b1220;
    color: #e5e7eb;
  }
  .dark-mode .caso-body .text-muted { color: #9ca3af !important; }
</style>
@endpush
