@extends('layouts.Dashboard_director.app')

@php use Illuminate\Support\Str; @endphp

@section('title', 'Dashboard Director de Carrera')

@section('content')
<div class="dashboard-page">
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

  <div class="page-header mb-4">
    <div>
      <p class="text-danger text-uppercase fw-semibold small mb-1">Supervision académica</p>
      <h1 class="h4 mb-1">Dashboard Director/a de Carrera</h1>
      <p class="text-muted mb-0">Monitoreo de solicitudes, aprobaciones y carga de ajustes razonables por carrera.</p>
    </div>
    <div class="text-muted small">
      <span class="me-3"><i class="fas fa-circle-dot text-danger me-1"></i>Casos activos {{ now()->format('Y') }}</span>
      <span><i class="fas fa-users me-1 text-danger"></i>{{ $totalStudents ?? 0 }} estudiantes asignados</span>
    </div>
  </div>

  @php
    $cardColors = ['#dc2626', '#dc2626', '#dc2626', '#dc2626'];
  @endphp
  <div class="row g-3 mb-4">
    @foreach ($summaryStats as $index => $stat)
      <div class="col-12 col-md-6 col-xl-3">
        <div class="stats-card" style="background: {{ $cardColors[$index % count($cardColors)] }};">
          <div class="stats-card__value">{{ $stat['value'] }}</div>
          <div class="stats-card__icon"><i class="fas {{ $stat['icon'] }}"></i></div>
          <p class="stats-card__title">{{ $stat['label'] }}</p>
          <small class="stats-card__sub">{{ $stat['subtext'] }}</small>
        </div>
      </div>
    @endforeach
  </div>

  <div class="row g-4 mb-4">
    <div class="col-xl-8">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
              <h5 class="card-title mb-1">Casos pendientes de aprobacion</h5>
              <small class="text-muted">Solicitudes que requieren tu decision para continuar.</small>
            </div>
            <a href="{{ route('director.casos') }}" class="btn btn-outline-danger btn-sm">Ver todos los casos</a>
          </div>
          @forelse ($pendingCases as $case)
            <div class="case-card">
              <div class="case-card__header">
                <div>
                  <h6 class="mb-0">{{ $case['student'] }}</h6>
                  <small class="text-muted">{{ $case['program'] }} &middot; {{ $case['requested_by'] }}</small>
                </div>
                <span class="priority-badge priority-{{ $case['priority_level'] }}">{{ $case['priority'] }}</span>
              </div>
              <p class="case-card__focus">{{ Str::limit($case['support_focus'], 120) }}</p>
              <div class="case-card__adjustments">
                @forelse ($case['adjustments'] as $adjustment)
                  <span class="badge rounded-pill bg-light text-danger">{{ $adjustment }}</span>
                @empty
                  <span class="badge rounded-pill bg-light text-muted">Sin ajustes propuestos</span>
                @endforelse
              </div>
              <div class="case-card__meta">
                <div>
                  <small class="text-muted text-uppercase">Estado actual</small>
                  <p class="mb-0 fw-semibold">{{ $case['status'] }}</p>
                </div>
                <div>
                  <small class="text-muted text-uppercase">Fecha de solicitud</small>
                  <p class="mb-0 fw-semibold">{{ $case['submitted_at'] }}</p>
                </div>
              </div>
              <div class="case-card__actions">
                <form action="{{ $case['approve_url'] }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-danger btn-sm">Aprobar</button>
                </form>
                <a href="{{ $case['reject_url'] }}" class="btn btn-outline-danger btn-sm">Rechazar</a>
                <a href="{{ $case['detail_url'] }}" class="btn btn-link btn-sm text-decoration-none">Ver detalles</a>
              </div>
            </div>
          @empty
            <p class="text-muted mb-0">No hay solicitudes pendientes, revisa nuevamente mas tarde.</p>
          @endforelse
        </div>
      </div>
    </div>
    <div class="col-xl-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between mb-3">
            <div>
              <h5 class="card-title mb-1">Flujo de aprobacion</h5>
              <small class="text-muted">Resumen de etapas de las solicitudes.</small>
            </div>
          </div>
          <div class="pipeline-list">
            @forelse ($pipelineSummary as $stage)
              <div class="pipeline-stage">
                <div class="pipeline-stage__count">{{ $stage['value'] }}</div>
                <div>
                  <p class="mb-0 fw-semibold">{{ $stage['label'] }}</p>
                  <small class="text-muted">{{ $stage['description'] }}</small>
                </div>
              </div>
            @empty
              <p class="text-muted mb-0">Sin actividad registrada para este periodo.</p>
            @endforelse
          </div>
          <hr>
          <div>
            <h6 class="mb-2">Notas rapidas</h6>
            <ul class="list-unstyled mb-0 small text-muted">
              @forelse ($insights as $insight)
                <li class="mb-2">
                  <span class="text-danger me-1"><i class="fas {{ $insight['icon'] }}"></i></span>{{ $insight['message'] }}
                </li>
              @empty
                <li>Sin novedades para mostrar.</li>
              @endforelse
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
          <h5 class="card-title mb-1">Estadisticas por carrera</h5>
          <small class="text-muted">Comparativa de carga academica y ajustes activos.</small>
        </div>
        <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger">Total carreras: {{ count($careerStats) }}</span>
      </div>
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
          <tr>
            <th>Carrera</th>
            <th class="text-center">Estudiantes</th>
            <th class="text-center">Con ajustes</th>
            <th class="text-center">Pendientes</th>
            <th class="text-center">Aprobados</th>
            <th class="text-center">Cobertura</th>
          </tr>
          </thead>
          <tbody>
          @forelse ($careerStats as $career)
            <tr>
              <td>
                <div class="fw-semibold">{{ $career['name'] }}</div>
                <small class="text-muted">{{ $career['jornada'] }}</small>
              </td>
              <td class="text-center">{{ $career['total_students'] }}</td>
              <td class="text-center">{{ $career['with_adjustments'] }}</td>
              <td class="text-center text-warning fw-semibold">{{ $career['pending_cases'] }}</td>
              <td class="text-center text-success fw-semibold">{{ $career['approved_cases'] }}</td>
              <td class="text-center">
                <span class="badge bg-light text-danger">{{ $career['coverage'] }}%</span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted">Aun no hay carreras asignadas a tu perfil.</td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="action-row">
    @foreach ($actionShortcuts as $action)
      <a href="{{ $action['route'] }}" class="action-button action-button--{{ $action['variant'] }}">
        <i class="fas {{ $action['icon'] }} me-2"></i>{{ $action['label'] }}
      </a>
    @endforeach
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
  .case-card {
    border: 1px solid #f0f0f5;
    border-radius: 1rem;
    padding: 1.1rem;
    margin-bottom: 1rem;
    background: #fff9f8;
  }
  .case-card__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
  }
  .case-card__focus {
    margin: .75rem 0;
    color: #4b5563;
  }
  .case-card__adjustments {
    display: flex;
    flex-wrap: wrap;
    gap: .4rem;
    margin-bottom: .75rem;
  }
  .case-card__meta {
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
    padding: .85rem;
    background: #fff;
    border-radius: .85rem;
    border: 1px dashed #f0c5c5;
  }
  .case-card__actions {
    display: flex;
    gap: .5rem;
    flex-wrap: wrap;
    margin-top: .75rem;
  }
  .priority-badge {
    border-radius: 999px;
    padding: .35rem .85rem;
    font-size: .75rem;
    font-weight: 600;
  }
  .priority-high {
    background: #fee2e2;
    color: #b91c1c;
  }
  .priority-medium {
    background: #fef3c7;
    color: #b45309;
  }
  .priority-low {
    background: #d1fae5;
    color: #047857;
  }
  .pipeline-list {
    display: flex;
    flex-direction: column;
    gap: .75rem;
  }
  .pipeline-stage {
    border: 1px solid #f0f0f5;
    border-radius: .9rem;
    padding: .9rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    background: #fff;
  }
  .pipeline-stage__count {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #fef2f2;
    color: #b91c1c;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
  }
  .action-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1rem;
  }
  .action-button {
    border-radius: 1rem;
    padding: 1rem 1.25rem;
    text-align: center;
    text-decoration: none;
    font-weight: 600;
    box-shadow: 0 10px 24px rgba(220,38,38,.1);
  }
  .action-button--danger {
    background: #b91c1c;
    color: #fff;
  }
  .action-button--outline-danger {
    border: 1px solid #b91c1c;
    background: #fff;
    color: #b91c1c;
  }
  .action-button:hover {
    transform: translateY(-2px);
  }
</style>
@endpush
@endsection
