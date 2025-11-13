@extends('layouts.dashboard_asesoratecnica.app')

@php use Illuminate\Support\Str; @endphp

@section('title', 'Dashboard Asesora Técnica')

@section('content')
<div class="dashboard-page">
  <div class="page-header mb-4">
    <h1 class="h4 mb-1">Dashboard - Asesora Técnica Pedagógica</h1>
    <p class="text-muted mb-0">Resumen de casos y accesos rápidos siguiendo el diseño rojo institucional.</p>
  </div>

  @php
    $cardPalette = ['#d62828', '#b51b1b', '#951010', '#f4a5a5'];
  @endphp

  <div class="row g-3 mb-4">
    @foreach ($metrics as $index => $metric)
      <div class="col-12 col-md-6 col-xl-3">
        <div class="stats-card" style="background: {{ $cardPalette[$index % count($cardPalette)] }};">
          <div class="stats-card__value">{{ $metric['value'] }}</div>
          <div class="stats-card__icon"><i class="fas {{ $metric['icon'] }}"></i></div>
          <p class="stats-card__title">{{ $metric['label'] }}</p>
          <small class="stats-card__sub">{{ $metric['hint'] }}</small>
        </div>
      </div>
    @endforeach
  </div>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body d-flex flex-wrap justify-content-between align-items-center">
      <div>
        <h5 class="mb-1">¿Necesitas formular un nuevo ajuste?</h5>
        <p class="text-muted mb-0">Centraliza desde aquí la creación de ajustes y su seguimiento.</p>
      </div>
      <a href="{{ route('ajustes-razonables.create') }}" class="btn btn-danger">+ Formular ajuste</a>
    </div>
  </div>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <h5 class="card-title mb-3">Acciones rápidas</h5>
      <div class="row g-3">
        @foreach ($quickActions as $action)
          <div class="col-md-3 col-sm-6">
            <a href="{{ $action['url'] }}" class="quick-link">
              <span>{{ $action['label'] }}</span>
              <small>{{ $action['helper'] }}</small>
            </a>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-xl-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h5 class="card-title mb-0">Casos asignados</h5>
              <small class="text-muted">Casos que requieren ajustes razonables</small>
            </div>
            <a href="{{ route('solicitudes.index') }}" class="btn btn-sm btn-outline-danger">Ver todos</a>
          </div>
          @forelse ($assignedCases as $case)
            <div class="case-item">
              <div>
                <strong>{{ $case['student'] }}</strong>
                <p class="text-muted mb-1">{{ $case['program'] }}</p>
                <p class="text-muted small mb-0">{{ $case['summary'] }}</p>
              </div>
              <div class="text-end">
                <span class="badge priority-badge priority-{{ Str::slug($case['priority']) }}">{{ $case['priority'] }}</span>
                <span class="badge status-badge status-{{ Str::slug($case['status']) }}">{{ $case['status'] }}</span>
                <p class="text-muted small mb-0">Asignado: {{ $case['assigned_at'] }}</p>
              </div>
            </div>
          @empty
            <p class="text-muted mb-0">No tienes casos asignados actualmente.</p>
          @endforelse
        </div>
      </div>
    </div>
    <div class="col-xl-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h5 class="card-title mb-0">Ajustes completados recientemente</h5>
              <small class="text-muted">Últimos ajustes enviados a revisión</small>
            </div>
            <a href="{{ route('ajustes-razonables.index') }}" class="btn btn-sm btn-outline-danger">Historial</a>
          </div>
          @forelse ($recentAdjustments as $adjustment)
            <div class="timeline-item">
              <div>
                <strong>{{ $adjustment['student'] }}</strong>
                <p class="text-muted mb-1">{{ $adjustment['program'] }}</p>
                <p class="text-muted small mb-0">{{ $adjustment['description'] }}</p>
              </div>
              <div class="text-end">
                <span class="badge bg-success-subtle text-success">{{ $adjustment['status'] }}</span>
                <p class="text-muted small mb-0">Completado {{ $adjustment['completed_at'] }}</p>
              </div>
            </div>
          @empty
            <p class="text-muted mb-0">Todavía no registras ajustes completados.</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-xl-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-title mb-3">Seguimiento general</h5>
          <p class="text-muted mb-0">Aún no hay datos suficientes para mostrar esta sección.</p>
        </div>
      </div>
    </div>
    <div class="col-xl-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-title mb-3">Actividad reciente</h5>
          <p class="text-muted mb-0">Todavía no hay movimientos registrados.</p>
        </div>
      </div>
    </div>
  </div>
</div>

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
    color: #fff;
    border-radius: 18px;
    padding: 1.25rem;
    position: relative;
  }
  .stats-card__value {
    font-size: 2rem;
    font-weight: 700;
  }
  .stats-card__title {
    font-size: .95rem;
    margin-bottom: 0;
    text-transform: capitalize;
  }
  .stats-card__icon {
    position: absolute;
    top: 1rem;
    right: 1rem;
    opacity: .25;
    font-size: 2.5rem;
  }
  .stats-card__sub {
    font-size: .85rem;
    color: rgba(255,255,255,.8);
  }
  .quick-link {
    display: block;
    border: 1px solid #f1b0b0;
    border-radius: 12px;
    padding: 1rem;
    text-decoration: none;
    transition: all .2s ease;
    color: inherit;
    background: #fff;
  }
  .quick-link span {
    display: block;
    font-weight: 600;
    color: #b51b1b;
  }
  .quick-link small {
    color: #6c6c7a;
  }
  .quick-link:hover {
    background: #fff2f2;
    border-color: #d62828;
  }
  .timeline-item,
  .case-item {
    border: 1px solid #f0f0f5;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    background: #fff;
  }
  .priority-badge,
  .status-badge {
    border-radius: 999px;
    padding: .35rem .85rem;
    font-size: .75rem;
  }
  .priority-alta {
    background: #fee2e2;
    color: #b91c1c;
  }
  .priority-media {
    background: #fef3c7;
    color: #b45309;
  }
  .priority-baja {
    background: #d1fae5;
    color: #047857;
  }
  .status-en-proceso {
    background: #ffe4e6;
    color: #be123c;
  }
  .status-pendiente {
    background: #fff7ed;
    color: #b45309;
  }
</style>
@endsection
