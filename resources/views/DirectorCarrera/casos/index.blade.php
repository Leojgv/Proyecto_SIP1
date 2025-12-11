@extends('layouts.Dashboard_director.app')

@php use Illuminate\Support\Str; @endphp

@section('title', 'Casos')

@section('content')
<div class="dashboard-page">
  <div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h1 class="h4 mb-1">Casos vinculados a tu dirección</h1>
      <p class="text-muted mb-0">Solicitudes enviadas por el equipo o asociadas a tus carreras.</p>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="accordion casos-director-accordion" id="casosDirector">
        @forelse($solicitudesPorEstudiante as $grupo)
          @php
            $primer = $grupo->first();
            $collapseId = 'est-' . ($primer->estudiante_id ?? 'na');
            $headingId = 'head-est-' . ($primer->estudiante_id ?? 'na');
            $nombre = trim(($primer->estudiante->nombre ?? 'Estudiante') . ' ' . ($primer->estudiante->apellido ?? ''));
            $carrera = $primer->estudiante->carrera->nombre ?? 'Sin carrera';
          @endphp
          @php
            $estado = $primer->estado ?? 'Pendiente';
            $badgeClass = match(strtolower($estado)) {
              'aprobado', 'aprobada' => 'bg-success',
              'rechazado', 'rechazada' => 'bg-danger',
              default => 'bg-warning text-dark',
            };
          @endphp
          <div class="accordion-item caso-card border-0 mb-3 shadow-sm">
            <h2 class="accordion-header" id="{{ $headingId }}">
              <button class="accordion-button caso-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false" aria-controls="{{ $collapseId }}">
                <div class="d-flex align-items-center gap-3 w-100">
                  <div>
                    <div class="fw-semibold">{{ $nombre }}</div>
                    <small class="text-muted">{{ $carrera }}</small>
                  </div>
                  <span class="badge {{ $badgeClass }}">{{ $estado }}</span>
                  <div class="text-muted small text-nowrap ms-auto">
                    <i class="far fa-calendar me-1"></i>{{ $primer->fecha_solicitud?->format('d/m/Y') ?? 's/f' }}
                  </div>
                </div>
              </button>
            </h2>
            <div id="{{ $collapseId }}" class="accordion-collapse collapse" aria-labelledby="{{ $headingId }}" data-bs-parent="#casosDirector">
              <div class="accordion-body caso-body">
                @foreach ($grupo as $solicitud)
                  @php
                    $estadoItem = $solicitud->estado ?? 'Pendiente';
                    $badgeItem = match(strtolower($estadoItem)) {
                      'aprobado', 'aprobada' => 'bg-success',
                      'rechazado', 'rechazada' => 'bg-danger',
                      default => 'bg-warning text-dark',
                    };
                  @endphp
                  <div class="d-flex flex-wrap align-items-center gap-3 pb-2 mb-2 border-bottom">
                    <div class="flex-grow-1">
                      <p class="text-muted small mb-1"><strong>Solicitud:</strong> {{ $solicitud->fecha_solicitud?->format('d/m/Y') ?? 's/f' }}</p>
                      <p class="text-muted mb-0">{{ $solicitud->descripcion ?? 'Sin descripción registrada.' }}</p>
                    </div>
                    <span class="badge {{ $badgeItem }}">{{ $estadoItem }}</span>
                    <a href="{{ route('director.casos.show', $solicitud) }}" class="btn btn-sm btn-outline-primary">
                      <i class="fas fa-eye me-1"></i>Ver detalles
                    </a>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        @empty
          <p class="text-center text-muted py-4 mb-0">No hay casos registrados para tus carreras.</p>
        @endforelse
      </div>
      <div class="d-flex justify-content-end mt-3">
        {{-- Agrupado sin paginación para mantener casos del mismo estudiante juntos --}}
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  .casos-director-accordion .caso-card {
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
  .dark-mode .casos-director-accordion .caso-card {
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
