@extends('layouts.dashboard_docente.app')

@php use Illuminate\Support\Str; @endphp

@section('title', 'Dashboard Docente')

@section('content')
@php
  $metricColors = ['#fce5e5', '#fbcaca', '#f9afaf'];
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
              <small class="text-muted d-block">RUT: {{ $student['rut'] }} · {{ $student['program'] }}</small>
            </div>
            <span class="badge status-pill status-{{ Str::slug(strtolower($student['status'])) }}">{{ $student['status'] }}</span>
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
                <a href="{{ $student['student_id'] ? route('estudiantes.edit', $student['student_id']) : route('estudiantes.index') }}" class="btn btn-danger btn-sm">
                  <i class="fas fa-check me-1"></i>Confirmar
                </a>
              </div>
            </div>
          </div>
        </div>
      @empty
        <p class="text-muted mb-0">Aun no registras estudiantes con ajustes activos.</p>
      @endforelse
    </div>
  </div>

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
    color: #1f1f2d;
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
