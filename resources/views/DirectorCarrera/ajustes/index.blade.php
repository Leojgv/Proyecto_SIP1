@extends('layouts.Dashboard_director.app')

@section('title', 'Ajustes Aplicados')

@section('content')
<div class="dashboard-page">
  <div class="page-header mb-4">
    <div>
      <p class="text-danger text-uppercase fw-semibold small mb-1">Gestión de ajustes</p>
      <h1 class="h4 mb-1">Ajustes Aplicados</h1>
      <p class="text-muted mb-0">Estudiantes con ajustes razonables aplicados en tus carreras.</p>
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
      @forelse($estudiantes as $index => $estudiante)
        <div class="accordion mb-3" id="accordionEstudiante{{ $estudiante['id'] }}">
          <div class="accordion-item border rounded">
            <h2 class="accordion-header" id="heading{{ $estudiante['id'] }}">
              <button 
                class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}" 
                type="button" 
                data-bs-toggle="collapse" 
                data-bs-target="#collapse{{ $estudiante['id'] }}" 
                aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" 
                aria-controls="collapse{{ $estudiante['id'] }}"
              >
                <div class="d-flex justify-content-between align-items-center w-100 me-3">
                  <div class="d-flex align-items-center gap-3">
                    <div>
                      <strong class="d-block">{{ $estudiante['nombre'] }} {{ $estudiante['apellido'] }}</strong>
                      <small class="text-muted">{{ $estudiante['carrera'] }}</small>
                    </div>
                  </div>
                  <div class="d-flex align-items-center gap-3">
                    @if($estudiante['rut'])
                      <span class="badge bg-light text-dark">{{ $estudiante['rut'] }}</span>
                    @endif
                    <span class="badge bg-danger">
                      <i class="fas fa-sliders me-1"></i>{{ $estudiante['ajustes_count'] }} ajuste(s)
                    </span>
                  </div>
                </div>
              </button>
            </h2>
            <div 
              id="collapse{{ $estudiante['id'] }}" 
              class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" 
              aria-labelledby="heading{{ $estudiante['id'] }}" 
              data-bs-parent="#accordionEstudiante{{ $estudiante['id'] }}"
            >
              <div class="accordion-body">
                <div class="row mb-3">
                  <div class="col-md-6">
                    <strong class="text-muted small d-block">Información del Estudiante</strong>
                    <div class="mt-2">
                      <p class="mb-1">
                        <i class="fas fa-envelope text-danger me-2"></i>
                        <strong>Email:</strong> {{ $estudiante['email'] }}
                      </p>
                      @if($estudiante['telefono'])
                        <p class="mb-1">
                          <i class="fas fa-phone text-danger me-2"></i>
                          <strong>Teléfono:</strong> {{ $estudiante['telefono'] }}
                        </p>
                      @endif
                      <p class="mb-0">
                        <i class="fas fa-graduation-cap text-danger me-2"></i>
                        <strong>Carrera:</strong> {{ $estudiante['carrera'] }}
                      </p>
                    </div>
                  </div>
                </div>

                <hr>

                <div class="mt-3">
                  <h6 class="mb-3">
                    <i class="fas fa-list-check text-danger me-2"></i>Ajustes Razonables Aplicados
                  </h6>
                  
                  @forelse($estudiante['ajustes'] as $ajuste)
                    <div class="card border mb-3 shadow-sm">
                      <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                          <div class="flex-grow-1">
                            <h6 class="mb-1 fw-semibold">
                              <i class="fas fa-circle-check text-success me-2"></i>{{ $ajuste['nombre'] }}
                            </h6>
                          </div>
                          <span class="badge 
                            @if($ajuste['estado'] === 'Aprobado') bg-success
                            @elseif($ajuste['estado'] === 'Rechazado') bg-danger
                            @elseif(str_contains($ajuste['estado'], 'Pendiente')) bg-warning text-dark
                            @else bg-secondary
                            @endif">
                            {{ $ajuste['estado'] }}
                          </span>
                        </div>

                        <div class="row g-3 mt-2">
                          @if($ajuste['fecha_solicitud'])
                            <div class="col-md-6">
                              <small class="text-muted d-block">Fecha de Solicitud</small>
                              <strong>{{ $ajuste['fecha_solicitud']->format('d/m/Y') }}</strong>
                            </div>
                          @endif
                          
                          @if($ajuste['solicitud_estado'])
                            <div class="col-md-6">
                              <small class="text-muted d-block">Estado de la Solicitud</small>
                              <span class="badge bg-info">{{ $ajuste['solicitud_estado'] }}</span>
                            </div>
                          @endif
                        </div>
                      </div>
                    </div>
                  @empty
                    <div class="alert alert-warning mb-0">
                      <i class="fas fa-exclamation-triangle me-2"></i>No hay ajustes aplicados para este estudiante.
                    </div>
                  @endforelse
                </div>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="text-center py-5">
          <i class="fas fa-sliders fa-3x text-muted mb-3"></i>
          <p class="text-muted mb-0">No hay estudiantes con ajustes aplicados en tus carreras.</p>
        </div>
      @endforelse
    </div>
  </div>
</div>

@push('styles')
<style>
  .accordion-button {
    background-color: transparent !important;
    border: none;
  }
  
  .accordion-button:not(.collapsed) {
    background-color: transparent !important;
    color: #b91c1c;
    box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.125);
  }
  
  .accordion-button:focus {
    border-color: #dc2626;
    box-shadow: 0 0 0 0.25rem rgba(220, 38, 38, 0.25);
  }
  
  .accordion-item {
    border: 1px solid #f0f0f5 !important;
    background-color: transparent !important;
  }
  
  .accordion-body {
    background-color: transparent !important;
  }
  
  .dark-mode .accordion-button {
    background-color: transparent !important;
    color: #ffffff !important;
  }
  
  .dark-mode .accordion-button:not(.collapsed) {
    background-color: transparent !important;
    color: #ffffff !important;
  }
  
  .dark-mode .accordion-item {
    background-color: transparent !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
  }
  
  .dark-mode .accordion-body {
    background-color: transparent !important;
    color: #ffffff !important;
  }
  
  .dark-mode .accordion-body * {
    color: #ffffff !important;
  }
  
  .dark-mode .accordion-body .text-muted {
    color: rgba(255, 255, 255, 0.7) !important;
  }
  
  .dark-mode .accordion-body strong {
    color: #ffffff !important;
  }
  
  .dark-mode .accordion-body small {
    color: rgba(255, 255, 255, 0.8) !important;
  }
  
  .dark-mode .table-responsive {
    color: #ffffff !important;
  }
  
  .dark-mode .table-responsive * {
    color: #ffffff !important;
  }
  
  .dark-mode .table-responsive table {
    color: #ffffff !important;
  }
  
  .dark-mode .table-responsive table th {
    color: #ffffff !important;
    background-color: transparent !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
  }
  
  .dark-mode .table-responsive table td {
    color: #ffffff !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
  }
  
  .dark-mode .table-responsive table tbody tr {
    background-color: transparent !important;
    color: #ffffff !important;
  }
  
  .dark-mode .table-responsive table tbody tr:hover {
    background-color: rgba(255, 255, 255, 0.05) !important;
  }
  
  .card.shadow-sm {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  
  .card.shadow-sm:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
  }
</style>
@endpush
@endsection

