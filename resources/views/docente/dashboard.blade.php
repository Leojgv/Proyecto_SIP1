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
          <h5 class="card-title mb-1">
            Mis Estudiantes con Ajustes
          </h5>
          <small class="text-muted">Estudiantes bajo tu supervisión con ajustes razonables aprobados por Dirección de Carrera.</small>
        </div>
        <a href="{{ route('docente.estudiantes') }}" class="btn btn-outline-danger btn-sm">Ver todos</a>
      </div>
      @forelse ($studentAdjustments as $student)
        <div class="student-item">
          <div class="student-item__header">
            <div class="flex-grow-1">
              <div class="d-flex align-items-center gap-2 mb-2">
                <div class="student-avatar">
                  <i class="fas fa-user-graduate"></i>
                </div>
                <div>
                  <h5 class="mb-0">{{ $student['student'] }}</h5>
                  <small class="text-muted d-block">
                    <i class="fas fa-id-card me-1"></i>RUT: {{ $student['rut'] }}
                  </small>
                  <small class="text-muted d-block">
                    <i class="fas fa-graduation-cap me-1"></i>{{ $student['program'] }}
                  </small>
                </div>
              </div>
            </div>
          </div>
          <div class="student-item__body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <h6 class="mb-1 fw-semibold">
                  <i class="fas fa-sliders text-danger me-2"></i>Ajustes Aprobados
                </h6>
                <small class="text-muted">Ajustes razonables aprobados por Dirección de Carrera</small>
              </div>
              <span class="badge bg-success">{{ count($student['applied_adjustments']) }}</span>
            </div>
            <div class="d-flex flex-wrap gap-2 mb-3">
              @foreach ($student['applied_adjustments'] as $adjustment)
                <span class="badge bg-light text-danger border border-danger">
                  <i class="fas fa-check-circle text-success me-1"></i>{{ $adjustment }}
                </span>
              @endforeach
            </div>
            <div class="d-flex justify-content-between flex-wrap align-items-center gap-2 pt-2 border-top">
              <small class="text-muted">
                <i class="fas fa-clock me-1"></i>Última actualización: {{ $student['last_update'] }}
              </small>
              <div class="d-flex gap-2">
                @if($student['student_id'])
                  <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#detallesAjustesModal{{ $student['student_id'] }}">
                    <i class="fas fa-eye me-1"></i>Ver Detalles
                  </button>
                @else
                  <button type="button" class="btn btn-sm btn-danger" disabled>
                    <i class="fas fa-eye me-1"></i>Ver Detalles
                  </button>
                @endif
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="text-center py-4">
          <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
          <p class="text-muted mb-0">Aún no hay estudiantes con ajustes aprobados.</p>
        </div>
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
                <h6 class="fw-semibold mb-3">
                  <i class="fas fa-sliders text-danger me-2"></i>Ajustes Razonables Aprobados
                </h6>
                @if(!empty($student['adjustments']))
                  @foreach($student['adjustments'] as $index => $ajuste)
                    <div class="border rounded p-3 mb-3 bg-light">
                      <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="fw-semibold mb-0">
                          <i class="fas fa-check-circle text-success me-2"></i>{{ $ajuste['name'] ?? 'Ajuste sin título' }}
                        </h6>
                        <span class="badge bg-success">Aprobado</span>
                      </div>
                      <p class="text-muted small mb-3">{{ $ajuste['description'] ?? 'No hay descripción disponible para este ajuste razonable.' }}</p>
                      
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
                            <strong>Aprobado el:</strong> {{ $ajuste['created_at'] ?? 'No disponible' }}
                          </small>
                        </div>
                      </div>
                    </div>
                  @endforeach
                @else
                  <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>No hay ajustes aprobados para este estudiante.
                  </div>
                @endif
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
          <div class="flex-grow-1">
            <h6 class="mb-1 fw-semibold">
              <i class="fas fa-bell text-danger me-2"></i>{{ $notification['title'] }}
            </h6>
            <p class="text-muted small mb-0">{{ $notification['message'] }}</p>
          </div>
          <small class="text-muted text-nowrap">{{ $notification['time'] }}</small>
        </div>
      @empty
        <div class="text-center py-4">
          <i class="fas fa-bell-slash fa-2x text-muted mb-2"></i>
          <p class="text-muted mb-0">Sin notificaciones recientes.</p>
        </div>
      @endforelse
    </div>
  </div>
</div>

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
    border: 1px solid #e5e7eb;
    border-radius: 1rem;
    padding: 1.5rem;
    background: #fff;
    margin-bottom: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,.05);
    transition: transform .2s ease, box-shadow .2s ease;
  }
  .student-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,.1);
  }
  .student-item__header {
    margin-bottom: 1rem;
  }
  .student-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
  }
  .student-item__body {
    background: #fff7f7;
    border-radius: .85rem;
    padding: 1.25rem;
    border: 1px solid #fce8e8;
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
    border: 1px solid #e5e7eb;
    border-radius: .85rem;
    padding: 1rem;
    margin-bottom: .75rem;
    background: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
    transition: background .2s ease;
  }
  .notification-item:hover {
    background: #fff7f7;
  }
  .badge.bg-light.text-danger {
    font-weight: 500;
    padding: .5rem .75rem;
  }
</style>
@endpush
@endsection
