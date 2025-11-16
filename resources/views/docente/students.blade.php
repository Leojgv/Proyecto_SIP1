@extends('layouts.dashboard_docente.app')

@php use Illuminate\Support\Str; @endphp

@section('title', 'Mis Estudiantes')

@section('content')
@php
  $metricColors = ['#fde3e3', '#fcc6c6', '#fab2b2', '#f89c9c'];
@endphp
<div class="dashboard-page">
  <div class="page-header mb-4">
    <h1 class="h4 mb-1">Mis Estudiantes</h1>
    <p class="text-muted mb-0">Estudiantes con ajustes razonables bajo tu supervision.</p>
  </div>

  <div class="row g-3 mb-4">
    @foreach ($metrics as $index => $metric)
      <div class="col-12 col-lg-3 col-md-6">
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

  @foreach ($students as $student)
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-body">
        <div class="d-flex justify-content-between flex-wrap align-items-center mb-3">
          <div>
            <h5 class="mb-0">{{ $student['student'] }}</h5>
            <small class="text-muted d-block">RUT: {{ $student['rut'] }} · {{ $student['program'] }}</small>
            <small class="text-muted">Ultima actualizacion: {{ $student['last_update'] }}</small>
          </div>
          <div class="d-flex align-items-center gap-3 flex-wrap">
            <span class="badge status-pill status-{{ Str::slug(strtolower($student['status'])) }}">{{ $student['status'] }}</span>
            <a href="{{ $student['student_id'] ? route('estudiantes.show', $student['student_id']) : route('estudiantes.index') }}" class="btn btn-outline-secondary btn-sm">
              <i class="fas fa-eye me-1"></i>Ver historial
            </a>
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
  @endforeach
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
    border: 1px solid #f3e3e3;
    border-radius: 1rem;
    padding: 1rem;
    background: #fff9f8;
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
</style>
@endpush
@endsection
