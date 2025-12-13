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

  {{-- Filtros de búsqueda y ordenamiento --}}
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
      <form method="GET" action="{{ route('asesora-pedagogica.ajustes.index') }}" class="row g-3 align-items-end">
        <div class="col-md-6">
          <label for="buscar" class="form-label">Buscar por nombre</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control" id="buscar" name="buscar" value="{{ $buscar ?? '' }}" placeholder="Buscar estudiante...">
          </div>
        </div>
        <div class="col-md-4">
          <label for="ordenar_por" class="form-label">Ordenar por:</label>
          <select class="form-select" id="ordenar_por" name="ordenar_por">
            <option value="nombre_asc" {{ ($ordenarPor ?? 'nombre_asc') === 'nombre_asc' ? 'selected' : '' }}>Nombre (A-Z)</option>
            <option value="nombre_desc" {{ ($ordenarPor ?? '') === 'nombre_desc' ? 'selected' : '' }}>Nombre (Z-A)</option>
            <option value="ajustes_asc" {{ ($ordenarPor ?? '') === 'ajustes_asc' ? 'selected' : '' }}>Cantidad ajustes (menor)</option>
            <option value="ajustes_desc" {{ ($ordenarPor ?? '') === 'ajustes_desc' ? 'selected' : '' }}>Cantidad ajustes (mayor)</option>
          </select>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary w-100">
            <i class="fas fa-filter me-1"></i>Filtrar
          </button>
        </div>
      </form>
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
                <div class="row g-3">
                  @foreach ($grupo['items'] as $item)
                    <div class="col-12">
                      <div class="border rounded p-3 bg-light mb-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                          <div class="flex-grow-1 d-flex align-items-start gap-2">
                            @if($item['estado'] === 'Aprobado' || $item['estado'] === 'Pendiente de preaprobación' || $item['estado'] === 'Pendiente de Aprobación')
                              <div class="text-success mt-1">
                                <i class="fas fa-check-circle"></i>
                              </div>
                            @endif
                            <div class="flex-grow-1">
                              <h6 class="fw-semibold mb-2">{{ $item['nombre'] }}</h6>
                              @if(!empty($item['descripcion']))
                                <div class="text-muted small mb-3" style="line-height: 1.6;">{{ $item['descripcion'] }}</div>
                              @endif
                              <div class="text-muted small">
                                <i class="far fa-calendar me-1"></i>Fecha: {{ $item['fecha'] }}
                              </div>
                              @if($item['estado'] === 'Rechazado' && !empty($item['motivo_rechazo']))
                                <div class="mt-3 p-2 border-start border-danger border-3 rounded" style="background-color: #fff5f5;">
                                  <div class="fw-semibold small text-danger mb-1">
                                    <i class="fas fa-exclamation-circle me-1"></i>Motivo de rechazo por Directora de Carrera:
                                  </div>
                                  <div class="small text-dark" style="line-height: 1.5;">{{ $item['motivo_rechazo'] }}</div>
                                </div>
                              @endif
                            </div>
                          </div>
                          <span class="badge {{ $item['estado'] === 'Aprobado' ? 'bg-success' : ($item['estado'] === 'Rechazado' ? 'bg-danger' : ($item['estado'] === 'Pendiente de preaprobación' || $item['estado'] === 'Pendiente de Aprobación' ? 'bg-primary' : 'bg-warning text-dark')) }} ms-2 flex-shrink-0" style="padding: 0.4rem 0.75rem; font-weight: 500;">
                            {{ $item['estado'] }}
                          </span>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
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
    transition: box-shadow 0.2s ease;
  }
  .casos-asesora-accordion .caso-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  }
  .caso-btn {
    background: #eef2ff;
    color: #1f2937;
    border: none;
    font-weight: 500;
  }
  .caso-btn:not(.collapsed) {
    background: #e0e7ff;
  }
  .caso-btn:focus { 
    box-shadow: none; 
    border-color: transparent;
  }
  .caso-body {
    background: #fff;
    color: #1f2937;
    padding: 1.5rem;
  }
  .caso-body .text-muted { 
    color: #6b7280 !important; 
  }
  .caso-body .bg-light {
    background-color: #f8f9fa !important;
    border: 1px solid #e9ecef;
    transition: all 0.2s ease;
  }
  .caso-body .bg-light:hover {
    border-color: #dee2e6;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  }
  .dark-mode .casos-asesora-accordion .caso-card {
    border-color: #1e293b;
    box-shadow: 0 10px 30px rgba(3, 7, 18, .35);
  }
  .dark-mode .caso-btn {
    background: #0f172a;
    color: #e5e7eb;
  }
  .dark-mode .caso-btn:not(.collapsed) {
    background: #1e293b;
  }
  .dark-mode .caso-body {
    background: #0b1220;
    color: #e5e7eb;
  }
  .dark-mode .caso-body .text-muted { 
    color: #9ca3af !important; 
  }
  .dark-mode .caso-body .bg-light {
    background-color: #1e293b !important;
    border-color: #334155;
  }
</style>
@endpush
