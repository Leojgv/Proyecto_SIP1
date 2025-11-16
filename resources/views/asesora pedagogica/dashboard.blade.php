@extends('layouts.dashboard_asesorapedagogica.app')

@php use Illuminate\Support\Str; @endphp

@section('title', 'Dashboard Asesora Pedagogica')

@section('content')
<div class="dashboard-page">
  <div class="page-header mb-4">
    <h1 class="h4 mb-1">Dashboard Asesora Pedagogica</h1>
    <p class="text-muted mb-0">Resumen de casos, autorizaciones y accesos rapidos para la gestion institucional.</p>
  </div>

  <div class="row g-3 mb-4">
    @foreach ($metrics as $metric)
      <div class="col-12 col-md-6 col-xl-3">
        <div class="stats-card" style="background: #d62828;">
          <div class="stats-card__value">{{ $metric['value'] }}</div>
          <div class="stats-card__icon"><i class="fas {{ $metric['icon'] ?? 'fa-circle' }}"></i></div>
          <p class="stats-card__title">{{ $metric['label'] }}</p>
          <small class="stats-card__sub">{{ $metric['helper'] }}</small>
        </div>
      </div>
    @endforeach
  </div>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body d-flex flex-wrap justify-content-between align-items-center">
      <div>
        <h5 class="mb-1">Necesitas registrar un nuevo caso?</h5>
        <p class="text-muted mb-0">Centraliza desde aqui la creacion de solicitudes y su seguimiento.</p>
      </div>
      <a href="{{ route('solicitudes.create') }}" class="btn btn-danger">+ Registrar solicitud</a>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-xl-7">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h5 class="card-title mb-0">Casos para revision</h5>
              <small class="text-muted">Solicitudes que requieren tu autorizacion</small>
            </div>
            <a href="{{ route('solicitudes.index') }}" class="btn btn-sm btn-outline-danger">Ver todos</a>
          </div>
          @forelse ($casesForReview as $case)
            <div class="case-item">
              <div>
                <strong>{{ $case['student'] }}</strong>
                <p class="text-muted mb-1">{{ $case['program'] }}</p>
                <p class="text-muted small mb-0">{{ $case['proposed_adjustment'] }}</p>
              </div>
              <div class="text-end">
                <span class="badge priority-badge priority-{{ Str::slug(strtolower($case['priority'])) }}">{{ $case['priority'] }}</span>
                <span class="badge status-badge">{{ $case['status'] }}</span>
                <p class="text-muted small mb-0">Recibido: {{ $case['received_at'] }}</p>
                <div class="mt-2 d-flex gap-2 justify-content-end flex-wrap">
                  <a href="{{ $case['case_id'] ? route('solicitudes.edit', $case['case_id']) : route('solicitudes.index') }}" class="btn btn-sm btn-outline-danger">Editar ajuste</a>
                  <a href="{{ $case['case_id'] ? route('solicitudes.show', $case['case_id']) : route('solicitudes.index') }}" class="btn btn-sm btn-danger">Autorizar y enviar</a>
                </div>
              </div>
            </div>
          @empty
            <p class="text-muted mb-0">No tienes casos pendientes actualmente.</p>
          @endforelse
        </div>
      </div>
    </div>
    <div class="col-xl-5">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h5 class="card-title mb-0">Casos autorizados</h5>
              <small class="text-muted">Enviados a Direccion para aprobacion final</small>
            </div>
          </div>
          @forelse ($authorizedCases as $case)
            <div class="timeline-item">
              <div>
                <strong>{{ $case['student'] }}</strong>
                <p class="text-muted mb-1">{{ $case['program'] }}</p>
                <p class="text-muted small mb-0">{{ $case['follow_up'] }}</p>
              </div>
              <div class="text-end">
                <span class="badge bg-success-subtle text-success">{{ $case['status'] }}</span>
                <p class="text-muted small mb-0">Autorizado: {{ $case['authorized_at'] }}</p>
              </div>
            </div>
          @empty
            <p class="text-muted mb-0">No hay casos autorizados recientemente.</p>
          @endforelse
        </div>
      </div>
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
    flex-wrap: wrap;
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
  .status-badge {
    background: #eef2ff;
    color: #4338ca;
  }
  @media (max-width: 768px) {
    .stats-card {
      min-height: 160px;
    }
  }
</style>
@endpush
@endsection
