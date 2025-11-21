@extends('layouts.dashboard_docente.app')

@php use Illuminate\Support\Str; @endphp

@section('title', 'Dashboard Docente')

@section('content')
@php
  $metricColors = ['#dc2626', '#dc2626', '#dc2626'];
@endphp
<div class="dashboard-page">
  <div class="page-header mb-4">
    <h1 class="h4 mb-1">Dashboard Docente</h1>
    <p class="text-muted mb-0">Gestiona a tus estudiantes con ajustes razonables y mantente al dia con las notificaciones.</p>
  </div>

  <div class="row g-3 mb-4">
    @foreach ($metrics as $index => $metric)
      <div class="col-12 col-md-4">
        <div class="stats-card" style="background: {{ $metricColors[$index % count($metricColors)] }};">
          <div class="stats-card__value">{{ $metric['value'] }}</div>
          <div class="stats-card__icon"><i class="fas {{ $metric['icon'] }}"></i></div>
          <p class="stats-card__title">{{ $metric['label'] }}</p>
          <small class="stats-card__sub">{{ $metric['helper'] }}</small>
        </div>
      </div>
    @endforeach
  </div>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between flex-wrap align-items-center mb-3">
        <div>
          <h5 class="card-title mb-1">Mis Estudiantes con Ajustes</h5>
          <small class="text-muted">Estudiantes bajo tu supervision con ajustes razonables activos.</small>
        </div>
        <a href="{{ route('estudiantes.index') }}" class="btn btn-outline-danger btn-sm">Ver todos</a>
      </div>
      @forelse ($studentAdjustments as $student)
        <div class="student-item">
          <div class="student-item__header">
            <div>
              <h5 class="mb-0">{{ $student['student'] }}</h5>
              <small class="text-muted d-block">RUT: {{ $student['rut'] }} � {{ $student['program'] }}</small>
            </div>
          </div>
          <div class="student-item__body">
            <p class="text-muted text-uppercase small mb-1">Ajustes aplicados</p>
            <div class="d-flex flex-wrap gap-2 mb-3">
              @foreach ($student['applied_adjustments'] as $adjustment)
                <span class="badge rounded-pill bg-light text-danger">{{ $adjustment }}</span>
              @endforeach
            </div>
            <div class="d-flex justify-content-between flex-wrap align-items-center gap-2">
              <small class="text-muted">Ultima actualizacion: {{ $student['last_update'] }}</small>
              <div class="d-flex gap-2">
                <a href="{{ $student['student_id'] ? route('estudiantes.show', $student['student_id']) : route('estudiantes.index') }}" class="btn btn-outline-secondary btn-sm">
                  <i class="fas fa-eye me-1"></i>Ver historial
                </a>
                @if($student['student_id'])
                  <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#detallesAjustesModal{{ $student['student_id'] }}">
                    <i class="fas fa-info-circle me-1"></i>Ver Detalles
                  </button>
                @else
                  <button type="button" class="btn btn-danger btn-sm" disabled>
                    <i class="fas fa-info-circle me-1"></i>Ver Detalles
                  </button>
                @endif
              </div>
            </div>
          </div>
        </div>
      @empty
        <p class="text-muted mb-0">Aun no registras estudiantes con ajustes activos.</p>
      @endforelse
    </div>
  </div>

  {{-- Modales de detalles de ajustes --}}
  @foreach ($studentAdjustments as $student)
    @if($student['student_id'])
      <div class="modal fade" id="detallesAjustesModal{{ $student['student_id'] }}" tabindex="-1" aria-labelledby="detallesAjustesModalLabel{{ $student['student_id'] }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="detallesAjustesModalLabel{{ $student['student_id'] }}">
                Detalles de Ajustes - {{ $student['student'] }}
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="mb-4">
                <h6 class="text-muted mb-2">Información del Estudiante</h6>
                <p class="mb-1"><strong>Nombre:</strong> {{ $student['student'] }}</p>
                <p class="mb-1"><strong>RUT:</strong> {{ $student['rut'] }}</p>
                <p class="mb-0"><strong>Carrera:</strong> {{ $student['program'] }}</p>
              </div>

              <hr>

              <div class="mb-3">
                <h6 class="text-muted mb-3">Ajustes Razonables Formulados por la Asesora Técnica</h6>
                @if(!empty($student['adjustments']))
                  @foreach($student['adjustments'] as $index => $ajuste)
                    <div class="card border mb-3">
                      <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                          <h6 class="card-title mb-0">{{ $ajuste['name'] ?? 'Ajuste sin título' }}</h6>
                          <span class="badge rounded-pill bg-light text-danger text-capitalize">{{ $ajuste['category'] ?? 'General' }}</span>
                        </div>
                        <p class="text-muted small mb-3">{{ $ajuste['description'] ?? 'Sin descripción disponible.' }}</p>
                        
                        <div class="row g-2 mb-2">
                          <div class="col-md-6">
                            <small class="text-muted d-block">
                              <i class="fas fa-calendar-alt me-1"></i>
                              <strong>Fecha de solicitud:</strong> {{ $ajuste['fecha_solicitud'] ?? 'No especificada' }}
                            </small>
                          </div>
                          <div class="col-md-6">
                            <small class="text-muted d-block">
                              <i class="fas fa-tag me-1"></i>
                              <strong>Estado:</strong> {{ $ajuste['status'] ?? 'Activo' }}
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
                  <p class="text-muted">No hay ajustes registrados para este estudiante.</p>
                @endif
              </div>

              <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Nota:</strong> Estos ajustes fueron formulados por la Asesora Técnica Pedagógica (CTP) y están pendientes de implementación.
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
      </div>
    @endif
  @endforeach

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between flex-wrap align-items-center mb-3">
        <div>
          <h5 class="card-title mb-1">Notificaciones Recientes</h5>
          <small class="text-muted">Actualizaciones importantes sobre tus estudiantes.</small>
        </div>
        <a href="{{ route('notificaciones.index') }}" class="btn btn-outline-danger btn-sm">Ver todas</a>
      </div>
      @forelse ($recentNotifications as $notification)
        <div class="notification-item">
          <div>
            <h6 class="mb-0">{{ $notification['title'] }}</h6>
            <p class="text-muted mb-0">{{ $notification['message'] }}</p>
          </div>
          <small class="text-muted">{{ $notification['time'] }}</small>
        </div>
      @empty
        <p class="text-muted mb-0">Sin notificaciones recientes.</p>
      @endforelse
    </div>
  </div>
</div>

@push('styles')
<style>
  .dashboard-page {
    background: #f7f6fb;
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
    box-shadow: 0 8px 20px rgba(15,23,42,.06);
    color: #fff;
  }
  .stats-card__value {
    font-size: 2rem;
    font-weight: 700;
  }
  .stats-card__title {
    margin-bottom: 0;
    font-size: .95rem;
  }
  .stats-card__sub {
    color: rgba(255,255,255,.8);
    font-size: .85rem;
  }
  .stats-card__icon {
    position: absolute;
    right: 1rem;
    top: 1rem;
    color: rgba(255,255,255,.25);
    font-size: 2rem;
  }
  .student-item {
    border: 1px solid #f0f0f5;
    border-radius: 1rem;
    padding: 1.25rem;
    background: #fff;
    margin-bottom: 1rem;
  }
  .student-item__body {
    background: #fff9f7;
    border-radius: .85rem;
    padding: 1rem;
    margin-top: 1rem;
  }
  .status-pill {
    border-radius: 999px;
    padding: .35rem .85rem;
    font-size: .75rem;
    font-weight: 600;
  }
  .status-activo {
    background: #fee2e2;
    color: #b91c1c;
  }
  .status-pendiente {
    background: #fef3c7;
    color: #b45309;
  }
  .status-finalizado {
    background: #d1fae5;
    color: #047857;
  }
  .notification-item {
    border: 1px solid #f4e4df;
    border-radius: .85rem;
    padding: 1rem;
    margin-bottom: .75rem;
    background: #fff9f8;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
  }
</style>
@endpush
@endsection
