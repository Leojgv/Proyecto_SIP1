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
        <div class="d-flex justify-content-between flex-wrap align-items-center mb-3">
          <div>
            <h5 class="mb-0">{{ $student['student'] }}</h5>
            <small class="text-muted d-block">RUT: {{ $student['rut'] }} · {{ $student['program'] }}</small>
            <small class="text-muted d-block">Email: {{ $student['email'] ?? 'Sin email' }}</small>
            @if($student['telefono'])
              <small class="text-muted d-block">Teléfono: {{ $student['telefono'] }}</small>
            @endif
            <small class="text-muted">Ultima actualizacion: {{ $student['last_update'] }}</small>
          </div>
          <div class="d-flex align-items-center gap-3 flex-wrap">
            <span class="badge status-pill status-{{ Str::slug(strtolower($student['status'])) }}">{{ $student['status'] }}</span>
            <button 
              type="button" 
              class="btn btn-outline-secondary btn-sm" 
              data-bs-toggle="modal" 
              data-bs-target="#historialModal{{ $student['student_id'] }}"
            >
              <i class="fas fa-eye me-1"></i>Ver historial
            </button>
          </div>
        </div>

        <div class="adjustments-grid">
          @forelse ($student['adjustments'] as $adjustment)
            <div class="adjustment-card">
              <div class="d-flex justify-content-between align-items-start gap-2">
                <div>
                  <h6 class="mb-0">{{ $adjustment['name'] }}</h6>
                  <p class="text-muted small mb-2">{{ Str::limit($adjustment['description'], 140) }}</p>
                </div>
                <span class="badge rounded-pill bg-light text-danger text-capitalize">{{ $adjustment['category'] }}</span>
              </div>
              <span class="badge status-chip status-{{ Str::slug(strtolower($adjustment['status'])) }}">{{ $adjustment['status'] }}</span>
            </div>
          @empty
            <p class="text-muted mb-0">No hay ajustes registrados para este estudiante.</p>
          @endforelse
        </div>
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
            <div class="mb-4">
              <h6 class="text-muted mb-3">
                <i class="fas fa-user-graduate me-2"></i>Información del Estudiante
              </h6>
              <div class="row g-3">
                <div class="col-md-6">
                  <div class="border rounded p-3 bg-light">
                    <small class="text-muted d-block mb-1"><strong>Nombre Completo</strong></small>
                    <div class="fw-semibold">{{ $student['student'] }}</div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="border rounded p-3 bg-light">
                    <small class="text-muted d-block mb-1"><strong>RUT</strong></small>
                    <div class="fw-semibold">{{ $student['rut'] }}</div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="border rounded p-3 bg-light">
                    <small class="text-muted d-block mb-1"><strong>Email</strong></small>
                    <div class="fw-semibold">{{ $student['email'] ?? 'Sin email' }}</div>
                  </div>
                </div>
                @if($student['telefono'])
                <div class="col-md-6">
                  <div class="border rounded p-3 bg-light">
                    <small class="text-muted d-block mb-1"><strong>Teléfono</strong></small>
                    <div class="fw-semibold">{{ $student['telefono'] }}</div>
                  </div>
                </div>
                @endif
                <div class="col-md-12">
                  <div class="border rounded p-3 bg-light">
                    <small class="text-muted d-block mb-1"><strong>Carrera</strong></small>
                    <div class="fw-semibold">{{ $student['program'] }}</div>
                  </div>
                </div>
              </div>
            </div>

            <hr>

            {{-- Ajustes Razonables --}}
            <div class="mb-3">
              <h6 class="text-muted mb-3">
                <i class="fas fa-sliders me-2"></i>Ajustes Razonables Formulados
              </h6>
              @if(!empty($student['adjustments']))
                @foreach($student['adjustments'] as $index => $ajuste)
                  <div class="card border mb-3">
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                          <h6 class="card-title mb-1">{{ $ajuste['name'] ?? 'Ajuste sin título' }}</h6>
                          <span class="badge rounded-pill bg-light text-secondary text-capitalize">{{ $ajuste['category'] ?? 'General' }}</span>
                        </div>
                        <span class="badge status-chip status-{{ Str::slug(strtolower($ajuste['status'])) }}">{{ $ajuste['status'] }}</span>
                      </div>
                      
                      <p class="text-muted mb-3">{{ $ajuste['description'] ?? 'Sin descripción disponible.' }}</p>
                      
                      <div class="row g-2">
                        <div class="col-md-6">
                          <small class="text-muted d-block">
                            <i class="fas fa-calendar-alt me-1"></i>
                            <strong>Fecha de solicitud:</strong> {{ $ajuste['fecha_solicitud'] ?? 'No especificada' }}
                          </small>
                        </div>
                        <div class="col-md-6">
                          <small class="text-muted d-block">
                            <i class="fas fa-clock me-1"></i>
                            <strong>Formulado el:</strong> {{ $ajuste['created_at'] ?? 'No disponible' }}
                          </small>
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
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
    border-radius: 1rem;
    padding: 1rem;
    background: #fff;
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
  .status-general {
    background: #e0e7ff;
    color: #3730a3;
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
</style>
@endpush
@endsection
