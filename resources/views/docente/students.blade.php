@extends('layouts.dashboard_docente.app')

@php use Illuminate\Support\Str; @endphp

@section('title', 'Mis Estudiantes')

@section('content')
<div class="dashboard-page">
  <div class="page-header mb-4">
    <h1 class="h4 mb-1">Mis Estudiantes</h1>
    <p class="text-muted mb-0">
      @if($carrera)
        Estudiantes de la carrera: <strong>{{ $carrera->nombre }}</strong>
      @else
        No tienes una carrera asignada. Contacta al administrador.
      @endif
    </p>
  </div>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between flex-wrap align-items-center mb-3">
        <div>
          <h5 class="card-title mb-1">Buscador rapido</h5>
          <small class="text-muted">Encuentra estudiantes por nombre, RUT o carrera.</small>
        </div>
      </div>
      <form class="row g-3">
        <div class="col-12">
          <input type="text" class="form-control form-control-lg" placeholder="Buscar por nombre, RUT o carrera..." disabled>
        </div>
      </form>
    </div>
  </div>

  @forelse ($students as $student)
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-body">
        <div class="d-flex justify-content-between flex-wrap align-items-start mb-3">
          <div class="flex-grow-1">
            <div class="d-flex align-items-center gap-3 mb-2">
              <div class="bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="fas fa-user-graduate text-danger"></i>
              </div>
              <div>
                <h5 class="mb-0 fw-semibold">{{ $student['student'] }}</h5>
                <small class="text-muted d-block">
                  <i class="fas fa-id-card me-1"></i>RUT: {{ $student['rut'] }} · <i class="fas fa-graduation-cap me-1"></i>{{ $student['program'] }}
                </small>
              </div>
            </div>
            <div class="ms-5 d-flex flex-wrap gap-3 small text-muted">
              <span><i class="fas fa-envelope me-1"></i>{{ $student['email'] ?? 'Sin email' }}</span>
              @if($student['telefono'])
                <span><i class="fas fa-phone me-1"></i>{{ $student['telefono'] }}</span>
              @endif
              <span><i class="fas fa-clock me-1"></i>Última actualización: {{ $student['last_update'] }}</span>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="badge status-pill status-{{ Str::slug(strtolower($student['status'])) }}">{{ $student['status'] }}</span>
            <button 
              type="button" 
              class="btn btn-sm btn-danger" 
              data-bs-toggle="modal" 
              data-bs-target="#historialModal{{ $student['student_id'] }}"
            >
              <i class="fas fa-eye me-1"></i>Ver historial
            </button>
          </div>
        </div>

        @if(!empty($student['adjustments']))
          <div class="adjustments-grid">
            @foreach ($student['adjustments'] as $adjustment)
              <div class="adjustment-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <div class="flex-grow-1">
                    <h6 class="mb-1 fw-semibold">
                      <i class="fas fa-check-circle text-success me-2"></i>{{ $adjustment['name'] }}
                    </h6>
                    <p class="text-muted small mb-2" style="line-height: 1.6;">{{ Str::limit($adjustment['description'] ?? 'No hay descripción disponible para este ajuste razonable.', 140) }}</p>
                  </div>
                  <span class="badge bg-success text-white ms-2" style="padding: 0.4rem 0.75rem; font-weight: 500;">Aprobado</span>
                </div>
                @if(isset($adjustment['category']) && $adjustment['category'] !== 'General')
                  <div class="mt-2">
                    <span class="badge bg-secondary text-white text-capitalize">{{ $adjustment['category'] }}</span>
                  </div>
                @endif
              </div>
            @endforeach
          </div>
        @else
          <div class="alert alert-light border mb-0">
            <i class="fas fa-info-circle me-2"></i>
            <span class="text-muted">Este estudiante no tiene ajustes razonables aprobados registrados.</span>
          </div>
        @endif
      </div>
    </div>
  @empty
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center py-5">
        <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
        <p class="text-muted mb-0">
          @if($carrera)
            No hay estudiantes registrados en la carrera {{ $carrera->nombre }}.
          @else
            No tienes estudiantes asignados.
          @endif
        </p>
      </div>
    </div>
  @endforelse
</div>

{{-- Modales de historial de ajustes --}}
@foreach ($students as $student)
  @if($student['student_id'])
    <div class="modal fade" id="historialModal{{ $student['student_id'] }}" tabindex="-1" aria-labelledby="historialModalLabel{{ $student['student_id'] }}" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <div>
              <h5 class="modal-title" id="historialModalLabel{{ $student['student_id'] }}">
                Historial de Ajustes - {{ $student['student'] }}
              </h5>
              <small class="text-muted">Información completa de los ajustes razonables</small>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            {{-- Información del Estudiante --}}
            <div class="estudiante-info-section mb-4">
              <h6 class="estudiante-info-title mb-3">
                <i class="fas fa-user-graduate text-danger me-2"></i>Información del Estudiante
              </h6>
              <div class="estudiante-info-grid">
                <div class="estudiante-info-item">
                  <div class="estudiante-info-label">Nombre</div>
                  <div class="estudiante-info-value">{{ $student['student'] }}</div>
                </div>
                <div class="estudiante-info-item">
                  <div class="estudiante-info-label">RUT</div>
                  <div class="estudiante-info-value">{{ $student['rut'] }}</div>
                </div>
                <div class="estudiante-info-item">
                  <div class="estudiante-info-label">Carrera</div>
                  <div class="estudiante-info-value">{{ $student['program'] }}</div>
                </div>
                <div class="estudiante-info-item">
                  <div class="estudiante-info-label">Email</div>
                  <div class="estudiante-info-value">
                    <i class="fas fa-envelope me-1"></i>{{ $student['email'] ?? 'Sin email' }}
                  </div>
                </div>
                @if($student['telefono'])
                <div class="estudiante-info-item">
                  <div class="estudiante-info-label">Teléfono</div>
                  <div class="estudiante-info-value">
                    <i class="fas fa-phone me-1"></i>{{ $student['telefono'] }}
                  </div>
                </div>
                @endif
              </div>
            </div>

            <hr class="my-4">

            {{-- Ajustes Razonables --}}
            <div class="mb-3">
              <h6 class="fw-semibold mb-4">
                <i class="fas fa-sliders text-danger me-2"></i>Ajustes Razonables Formulados
              </h6>
              @if(!empty($student['adjustments']))
                <div class="ajustes-modal-list">
                  @foreach($student['adjustments'] as $index => $ajuste)
                    <div class="ajuste-modal-card">
                      <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="flex-grow-1">
                          <h6 class="ajuste-modal-title mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>{{ $ajuste['name'] ?? 'Ajuste sin título' }}
                          </h6>
                          @if(isset($ajuste['category']) && $ajuste['category'] !== 'General')
                            <span class="badge bg-secondary text-white text-capitalize">{{ $ajuste['category'] }}</span>
                          @endif
                        </div>
                        <span class="badge bg-success text-white">Aprobado</span>
                      </div>
                      
                      <p class="ajuste-modal-description mb-3">{{ $ajuste['description'] ?? 'No hay descripción disponible para este ajuste razonable.' }}</p>
                      
                      <div class="ajuste-modal-meta">
                        <div class="ajuste-meta-item">
                          <i class="fas fa-calendar-alt me-1"></i>
                          <span class="ajuste-meta-label">Fecha de solicitud:</span>
                          <span class="ajuste-meta-value">{{ $ajuste['fecha_solicitud'] ?? 'No especificada' }}</span>
                        </div>
                        <div class="ajuste-meta-item">
                          <i class="fas fa-clock me-1"></i>
                          <span class="ajuste-meta-label">Formulado el:</span>
                          <span class="ajuste-meta-value">{{ $ajuste['created_at'] ?? 'No disponible' }}</span>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              @else
                <div class="alert alert-info mb-0">
                  <i class="fas fa-info-circle me-2"></i>
                  No hay ajustes razonables registrados para este estudiante.
                </div>
              @endif
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="fas fa-times me-1"></i>Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
  @endif
@endforeach

@push('styles')
<style>
  .dashboard-page {
    background: transparent;
    padding: 1rem;
    border-radius: 1.5rem;
  }
  .page-header h1 {
    font-weight: 600;
    color: #1f1f2d;
  }
  .stats-card {
    border-radius: 1rem;
    padding: 1.25rem;
    position: relative;
    border: 1px solid #f0f0f5;
    box-shadow: 0 8px 20px rgba(15,23,42,.05);
    color: #1f1f2d;
  }
  .stats-card__value {
    font-size: 2.5rem;
    font-weight: 700;
  }
  .stats-card__title {
    margin-bottom: 0;
    font-size: .95rem;
  }
  .stats-card__sub {
    color: #6c6d7a;
    font-size: .85rem;
  }
  .stats-card__icon {
    position: absolute;
    right: 1rem;
    top: 1rem;
    color: rgba(185, 34, 34, .4);
    font-size: 2rem;
  }
  .adjustments-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1rem;
  }
  .adjustment-card {
    border: 1px solid #e5e7eb;
    border-radius: 0.85rem;
    padding: 1.25rem;
    background: #f9fafb;
    transition: transform .2s ease, box-shadow .2s ease;
  }
  .adjustment-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,.08);
  }
  .status-pill,
  .status-chip {
    border-radius: 999px;
    padding: .35rem .85rem;
    font-size: .75rem;
    font-weight: 600;
    text-transform: capitalize;
  }
  .status-activo,
  .status-chip.status-activo {
    background: #fee2e2;
    color: #b91c1c;
  }
  .status-pendiente,
  .status-chip.status-pendiente {
    background: #fef3c7;
    color: #b45309;
  }
  .status-finalizado,
  .status-chip.status-finalizado {
    background: #d1fae5;
    color: #047857;
  }
  .status-aprobado,
  .status-chip.status-aprobado {
    background: #d1fae5;
    color: #047857;
  }
  .status-general {
    background: #f3f4f6;
    color: #374151;
  }
  .dark-mode .dashboard-page {
    color: #e5e7eb;
  }
  .dark-mode .page-header h1 {
    color: #e5e7eb;
  }
  .dark-mode .text-muted {
    color: #9ca3af !important;
  }
  .dark-mode .adjustment-card {
    border-color: #1f2937;
    background: #0f172a;
    color: #e5e7eb;
  }
  .dark-mode .adjustment-card .text-muted {
    color: #9ca3af !important;
  }
  .dark-mode .status-pill,
  .dark-mode .status-chip {
    border: 1px solid #1f2937;
  }
  .dark-mode .status-activo,
  .dark-mode .status-chip.status-activo {
    background: #b91c1c;
    color: #fff;
  }
  .dark-mode .status-pendiente,
  .dark-mode .status-chip.status-pendiente {
    background: #b45309;
    color: #fff;
  }
  .dark-mode .status-finalizado,
  .dark-mode .status-chip.status-finalizado {
    background: #047857;
    color: #fff;
  }
  .dark-mode .status-general {
    background: #1f2937;
    color: #e5e7eb;
  }
  .dark-mode .alert-light {
    background-color: #16213e !important;
    border-color: #2d3748 !important;
    color: #cbd5e1 !important;
  }
  .dark-mode .alert-light .text-muted {
    color: #94a3b8 !important;
  }

  /* Estilos para el modal de historial */
  .estudiante-info-section {
    border-bottom: 1px solid #e5e7eb;
    padding-bottom: 1.5rem;
  }
  .estudiante-info-title {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 1rem;
  }
  .estudiante-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.25rem 1.5rem;
  }
  .estudiante-info-item {
    display: flex;
    flex-direction: column;
    gap: 0.375rem;
  }
  .estudiante-info-label {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #6b7280;
  }
  .estudiante-info-value {
    font-size: 0.9375rem;
    color: #1f2937;
    font-weight: 500;
    line-height: 1.5;
  }
  .estudiante-info-value i {
    color: #dc2626;
    font-size: 0.875rem;
  }

  /* Modo oscuro para información del estudiante */
  .dark-mode .estudiante-info-section {
    border-bottom-color: #2d3748 !important;
  }
  .dark-mode .estudiante-info-title {
    color: #e8e8e8 !important;
  }
  .dark-mode .estudiante-info-label {
    color: #94a3b8 !important;
  }
  .dark-mode .estudiante-info-value {
    color: #e8e8e8 !important;
  }
  .dark-mode .estudiante-info-value i {
    color: #fca5a5 !important;
  }

  /* Estilos del modal */
  .modal-content {
    border: none;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  }
  .dark-mode .modal-content {
    background-color: #1e293b !important;
    border-color: #2d3748 !important;
  }
  .dark-mode .modal-header {
    border-bottom-color: #2d3748 !important;
  }
  .dark-mode .modal-title {
    color: #e8e8e8 !important;
  }
  .dark-mode .modal-body {
    color: #cbd5e1 !important;
  }
  .dark-mode .modal-footer {
    border-top-color: #2d3748 !important;
  }
  .dark-mode .hr {
    border-color: #2d3748 !important;
  }

  /* Estilos para las tarjetas de ajustes en el modal */
  .ajustes-modal-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }
  .ajuste-modal-card {
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 1.25rem;
    background: #ffffff;
    transition: all 0.2s ease;
  }
  .ajuste-modal-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  }
  .ajuste-modal-title {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
  }
  .ajuste-modal-description {
    font-size: 0.875rem;
    color: #4b5563;
    line-height: 1.6;
    margin: 0;
  }
  .ajuste-modal-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    padding-top: 0.75rem;
    border-top: 1px solid #e5e7eb;
  }
  .ajuste-meta-item {
    display: flex;
    align-items: center;
    font-size: 0.8125rem;
    color: #6b7280;
  }
  .ajuste-meta-item i {
    color: #dc2626;
    font-size: 0.875rem;
    width: 16px;
  }
  .ajuste-meta-label {
    font-weight: 500;
    margin-right: 0.5rem;
  }
  .ajuste-meta-value {
    color: #374151;
    font-weight: 400;
  }

  /* Modo oscuro para tarjetas de ajustes */
  .dark-mode .ajuste-modal-card {
    background-color: #16213e !important;
    border-color: #2d3748 !important;
  }
  .dark-mode .ajuste-modal-card:hover {
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15) !important;
  }
  .dark-mode .ajuste-modal-title {
    color: #e8e8e8 !important;
  }
  .dark-mode .ajuste-modal-description {
    color: #cbd5e1 !important;
  }
  .dark-mode .ajuste-modal-meta {
    border-top-color: #2d3748 !important;
  }
  .dark-mode .ajuste-meta-item {
    color: #94a3b8 !important;
  }
  .dark-mode .ajuste-meta-item i {
    color: #fca5a5 !important;
  }
  .dark-mode .ajuste-meta-value {
    color: #e8e8e8 !important;
  }
</style>
@endpush
@endsection
