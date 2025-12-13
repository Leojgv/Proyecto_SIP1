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

  {{-- Filtros de búsqueda y ordenamiento --}}
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
      <form method="GET" action="{{ route('asesora-pedagogica.casos.index') }}" class="row g-3 align-items-end">
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
            <option value="fecha_desc" {{ ($ordenarPor ?? 'fecha_desc') === 'fecha_desc' ? 'selected' : '' }}>Fecha (más recientes)</option>
            <option value="fecha_asc" {{ ($ordenarPor ?? '') === 'fecha_asc' ? 'selected' : '' }}>Fecha (más antiguas)</option>
            <option value="nombre_asc" {{ ($ordenarPor ?? '') === 'nombre_asc' ? 'selected' : '' }}>Nombre (A-Z)</option>
            <option value="nombre_desc" {{ ($ordenarPor ?? '') === 'nombre_desc' ? 'selected' : '' }}>Nombre (Z-A)</option>
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
      <div class="accordion casos-asesora-accordion" id="casosPedagogica">
        @forelse($solicitudes as $solicitud)
          @php
            $collapseId = 'sol-' . $solicitud->id;
            $headingId = 'head-' . $solicitud->id;
            $ajustesCount = $solicitud->ajustesRazonables->count();
            $esPreaprobacion = $solicitud->estado === 'Pendiente de preaprobación';
            $badgeClass = match($solicitud->estado) {
              'Pendiente de preaprobación' => 'bg-primary',
              'Pendiente de Aprobación' => 'bg-primary',
              'Aprobado' => 'bg-success',
              'Rechazado' => 'bg-danger',
              default => 'bg-secondary'
            };
          @endphp
          <div class="accordion-item case-item border-0 mb-3 shadow-sm">
            <h2 class="accordion-header" id="{{ $headingId }}">
              <button class="accordion-button case-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false" aria-controls="{{ $collapseId }}">
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
              <div class="accordion-body case-body">
                <div class="row g-3 mb-3">
                  <div class="col-12">
                    <div class="border rounded p-3 bg-light">
                      @if($solicitud->titulo)
                        <small class="text-muted d-block mb-1">
                          <strong>Título</strong>
                        </small>
                        <div class="fw-semibold mb-3">{{ $solicitud->titulo }}</div>
                      @endif
                      <small class="text-muted d-block mb-2">
                        <strong>Descripción</strong>
                      </small>
                      <div class="text-muted" style="line-height: 1.6;">{{ $solicitud->descripcion ?: 'Sin descripción registrada.' }}</div>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="border rounded p-3 bg-light">
                      <div class="row g-3">
                        <div class="col-md-6">
                          <small class="text-muted d-block mb-1">
                            <i class="far fa-calendar-alt me-1"></i><strong>Fecha/Hora</strong>
                          </small>
                          <div class="small fecha-texto">
                            {{ $solicitud->fecha_solicitud?->format('d/m/Y') ?? 's/f' }}
                            @if($solicitud->created_at)
                              - {{ $solicitud->created_at->format('H:i') }}
                            @endif
                          </div>
                        </div>
                        @if($solicitud->observaciones_pdf_ruta)
                          <div class="col-md-6">
                            <small class="text-muted d-block mb-1">
                              <i class="fas fa-file-pdf me-1"></i><strong>Evidencia / Observación Adjuntado</strong>
                            </small>
                            <div class="small">
                              @php
                                $pdfUrl = asset('storage/' . $solicitud->observaciones_pdf_ruta);
                              @endphp
                              <a href="{{ $pdfUrl }}" target="_blank" class="text-danger">
                                <i class="fas fa-file-pdf me-1"></i>Ver PDF
                              </a>
                            </div>
                          </div>
                        @endif
                      </div>
                    </div>
                  </div>
                  @if($solicitud->ajustesRazonables->isNotEmpty())
                    <div class="col-12">
                      <h6 class="mb-3"><strong>Ajustes aplicados</strong></h6>
                      @foreach($solicitud->ajustesRazonables as $ajuste)
                        <div class="border rounded p-3 bg-light mb-3">
                          <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                              <div class="fw-semibold mb-2">
                                <i class="fas fa-check-circle text-success me-1"></i>{{ $ajuste->nombre ?? 'Ajuste sin nombre' }}
                              </div>
                              @if($ajuste->descripcion)
                                <div class="text-muted small mb-2" style="line-height: 1.6;">{{ $ajuste->descripcion }}</div>
                              @endif
                              <div class="text-muted small">
                                <i class="fas fa-calendar me-1"></i>Fecha de solicitud: {{ $ajuste->fecha_solicitud?->format('d/m/Y') ?? 's/f' }}
                              </div>
                            </div>
                          </div>
                        </div>
                      @endforeach
                    </div>
                  @endif
                </div>
                <div class="d-flex flex-wrap align-items-center gap-3 justify-content-end">
                  <a href="{{ route('asesora-pedagogica.casos.show', $solicitud) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-eye me-1"></i>Revisar
                  </a>
                  @if($solicitud->estado === 'Pendiente de preaprobación')
                    <form action="{{ route('asesora-pedagogica.casos.enviar-director', $solicitud) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Enviar este caso a Dirección de Carrera para aprobación final?');">
                        <i class="fas fa-paper-plane me-1"></i>Enviar a Dirección
                      </button>
                    </form>
                  @endif
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
  .case-item {
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    overflow: hidden;
  }
  .case-button {
    background: #eef2ff;
    color: #1f2937;
    font-weight: 500;
  }
  .case-button:not(.collapsed) {
    background: #dbeafe;
    color: #1f2937;
  }
  .case-button:focus {
    box-shadow: none;
    border-color: transparent;
  }
  .case-body {
    background: #fff;
    color: #1f2937;
    padding: 1.5rem;
  }
  .case-body .text-muted {
    color: #6b7280 !important;
  }

  /* Modo Oscuro para Acordeón */
  .dark-mode .case-item {
    background-color: #1e293b !important;
    border-color: #2d3748 !important;
  }
  .dark-mode .case-button {
    background-color: #16213e !important;
    color: #e8e8e8 !important;
    border-color: #2d3748 !important;
  }
  .dark-mode .case-button:not(.collapsed) {
    background-color: #1e3a5f !important;
    color: #e8e8e8 !important;
    border-color: #2d3748 !important;
  }
  .dark-mode .case-button:hover {
    background-color: #1e3a5f !important;
    color: #e8e8e8 !important;
  }
  .dark-mode .case-button .text-muted {
    color: #b8b8b8 !important;
  }
  .dark-mode .case-button .fw-semibold {
    color: #e8e8e8 !important;
  }
  .dark-mode .case-body {
    background-color: #1e293b !important;
    color: #e8e8e8 !important;
    border-top-color: #2d3748 !important;
  }
  .dark-mode .case-body .bg-light {
    background-color: #16213e !important;
    border-color: #2d3748 !important;
    color: #e8e8e8 !important;
  }
  .dark-mode .case-body .border {
    border-color: #2d3748 !important;
  }
  .dark-mode .case-body .text-muted {
    color: #cbd5e1 !important;
  }
  .dark-mode .case-body small.text-muted {
    color: #94a3b8 !important;
  }
  .dark-mode .case-body small.text-muted strong {
    color: #cbd5e1 !important;
    font-weight: 600 !important;
  }
  .dark-mode .case-body .fw-semibold {
    color: #e8e8e8 !important;
  }
  .dark-mode .case-body h6 {
    color: #e8e8e8 !important;
  }
  .dark-mode .case-body h6 strong {
    color: #e8e8e8 !important;
  }
  .dark-mode .case-body .small {
    color: #cbd5e1 !important;
  }
  .dark-mode .case-body a.text-danger {
    color: #f87171 !important;
  }
  .dark-mode .case-body a.text-danger:hover {
    color: #ef4444 !important;
  }
  .dark-mode .case-body .text-success {
    color: #86efac !important;
  }
  .dark-mode .case-body .badge {
    color: #fff !important;
  }
  .dark-mode .case-body .btn-primary {
    background-color: #3b82f6 !important;
    border-color: #3b82f6 !important;
    color: #fff !important;
  }
  .dark-mode .case-body .btn-primary:hover {
    background-color: #2563eb !important;
    border-color: #2563eb !important;
  }
  .dark-mode .case-body .btn-danger {
    background-color: #dc2626 !important;
    border-color: #dc2626 !important;
    color: #fff !important;
  }
  .dark-mode .case-body .btn-danger:hover {
    background-color: #b91c1c !important;
    border-color: #b91c1c !important;
  }
  .dark-mode .card {
    background-color: #1e293b !important;
    border-color: #2d3748 !important;
    color: #e8e8e8 !important;
  }
  .dark-mode .card-body {
    background-color: #1e293b !important;
    color: #e8e8e8 !important;
  }
  .dark-mode .card-title {
    color: #e8e8e8 !important;
  }
  .dark-mode .text-muted {
    color: #b8b8b8 !important;
  }
  .dark-mode .form-label {
    color: #e8e8e8 !important;
  }
  .dark-mode .input-group-text {
    background-color: #16213e !important;
    border-color: #2d3748 !important;
    color: #e8e8e8 !important;
  }
  .dark-mode .form-control,
  .dark-mode .form-select {
    background-color: #16213e !important;
    border-color: #2d3748 !important;
    color: #e8e8e8 !important;
  }
  .dark-mode .form-control::placeholder {
    color: #94a3b8 !important;
  }
  .dark-mode .btn-primary {
    background-color: #3b82f6 !important;
    border-color: #3b82f6 !important;
    color: #fff !important;
  }
  .dark-mode .btn-primary:hover {
    background-color: #2563eb !important;
    border-color: #2563eb !important;
  }
  .dark-mode .case-body .descripcion-texto {
    color: #e8e8e8 !important;
  }
  .dark-mode .case-body .fecha-texto {
    color: #e8e8e8 !important;
  }
  .dark-mode .case-body .ajuste-descripcion {
    color: #cbd5e1 !important;
  }
  .dark-mode .case-body .ajuste-fecha {
    color: #cbd5e1 !important;
  }
  .dark-mode .case-body i {
    color: inherit !important;
  }
</style>
@endpush
