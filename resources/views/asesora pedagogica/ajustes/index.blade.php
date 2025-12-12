@extends('layouts.dashboard_asesorapedagogica.app')

@section('title', 'Ajustes - Asesora Pedagógica')

@section('content')
<div class="dashboard-page">
  <div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h1 class="h4 mb-1">Ajustes asignados</h1>
      <p class="text-muted mb-0">Ajustes formulados por Asesora Técnica para tus estudiantes.</p>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="accordion casos-asesora-accordion" id="ajustesPedagogica">
        @forelse($ajustesPorEstudiante as $grupo)
          @php
            $collapseId = 'est-' . \Illuminate\Support\Str::slug($grupo['student'] . '-' . $loop->index);
            $headingId = 'head-est-' . $loop->index;
          @endphp
          <div class="accordion-item caso-card border-0 mb-3 shadow-sm">
            <h2 class="accordion-header" id="{{ $headingId }}">
              <button class="accordion-button caso-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false" aria-controls="{{ $collapseId }}">
                <div class="d-flex align-items-center justify-content-between w-100 me-3">
                  <div class="d-flex align-items-center gap-3">
                    <div>
                      <div class="fw-semibold">{{ $grupo['student'] }}</div>
                      <small class="text-muted">{{ $grupo['program'] }}</small>
                    </div>
                    <span class="badge bg-info text-dark">{{ count($grupo['items']) }} ajuste(s)</span>
                  </div>
                  @if(!empty($grupo['solicitud_id']))
                    <a href="{{ route('asesora-pedagogica.casos.show', $grupo['solicitud_id']) }}" class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation();">
                      <i class="fas fa-eye me-1"></i>Ver caso
                    </a>
                  @endif
                </div>
              </button>
            </h2>
            <div id="{{ $collapseId }}" class="accordion-collapse collapse" aria-labelledby="{{ $headingId }}" data-bs-parent="#ajustesPedagogica">
              <div class="accordion-body caso-body">
                @foreach ($grupo['items'] as $item)
                  <div class="d-flex flex-wrap align-items-center gap-3 pb-2 mb-2 border-bottom">
                  <div>
                    <p class="mb-1 fw-semibold">{{ $item['nombre'] }}</p>
                    @if(!empty($item['descripcion']))
                      <p class="text-muted small mb-1">{{ $item['descripcion'] }}</p>
                    @endif
                    <p class="text-muted small mb-0">Fecha: {{ $item['fecha'] }}</p>
                  </div>
                  <div class="ms-auto d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge {{ $item['estado'] === 'Aprobado' ? 'bg-success-subtle text-success' : ($item['estado'] === 'Rechazado' ? 'bg-danger-subtle text-danger' : 'bg-warning text-dark') }}">
                      {{ $item['estado'] }}
                    </span>
                  </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        @empty
          <p class="text-center text-muted py-4 mb-0">
            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
            No hay ajustes asignados.
          </p>
        @endforelse
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
