@extends('layouts.dashboard_admin.admin')

@section('title', 'Dashboard Administrador')

@php
    use Illuminate\Support\Str;
    $adminUser = auth()->user();
@endphp

@section('content')
<div class="dashboard-page">
  @if (session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="page-header mb-4">
    <div>
      <p class="text-danger text-uppercase fw-semibold small mb-1">Gestión institucional</p>
      <h1 class="h4 mb-1">Dashboard Administrador</h1>
      <p class="text-muted mb-0">Resumen del sistema y accesos rápidos para la gestión institucional.</p>
    </div>
    <div class="text-muted small">
      <span class="me-3"><i class="fas fa-circle-dot text-danger me-1"></i>Sistema activo {{ now()->format('Y') }}</span>
      <span><i class="fas fa-users me-1 text-danger"></i>{{ number_format($stats['total_estudiantes']) }} estudiantes</span>
    </div>
  </div>

  @php
    $cardColors = ['#dc2626', '#dc2626', '#dc2626', '#dc2626', '#dc2626'];
    $summaryStats = [
      [
        'label' => 'Total Estudiantes',
        'value' => number_format($stats['total_estudiantes']),
        'subtext' => '+' . number_format($stats['nuevos_estudiantes_mes']) . ' este mes',
        'icon' => 'fa-user-graduate',
        'link' => route('estudiantes.index'),
      ],
      [
        'label' => 'Casos Activos',
        'value' => number_format($stats['casos_activos']),
        'subtext' => 'Requieren seguimiento',
        'icon' => 'fa-briefcase-medical',
        'link' => route('solicitudes.index'),
      ],
      [
        'label' => 'Casos Cerrados',
        'value' => number_format($stats['casos_cerrados']),
        'subtext' => number_format($stats['casos_cerrados_mes']) . ' este mes',
        'icon' => 'fa-circle-check',
        'link' => route('solicitudes.index'),
      ],
      [
        'label' => 'Casos Pendientes',
        'value' => number_format($stats['casos_pendientes']),
        'subtext' => 'Requieren revisión',
        'icon' => 'fa-hourglass-half',
        'link' => route('solicitudes.index'),
      ],
      [
        'label' => 'Pendientes Aprobación',
        'value' => number_format($stats['pendientes_aprobacion']),
        'subtext' => 'Esperando decisión',
        'icon' => 'fa-triangle-exclamation',
        'link' => route('ajustes-razonables.index'),
      ],
    ];
  @endphp

  <div class="row g-3 mb-4">
    @foreach ($summaryStats as $index => $stat)
      <div class="col-12 col-md-6 col-xl-4 col-xxl">
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
    <div class="col-xl-7">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
              <h5 class="card-title mb-1">Casos por Carrera</h5>
              <small class="text-muted">Distribución de casos activos y cerrados</small>
            </div>
            <a href="{{ route('solicitudes.index') }}" class="btn btn-outline-danger btn-sm">Ver detalle</a>
          </div>
            @forelse ($casosPorCarrera as $item)
              @php
                $activosPercent = $item->total > 0 ? round(($item->activos / $item->total) * 100) : 0;
              @endphp
              <div class="pb-3 mb-3 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <strong>{{ $item->carrera }}</strong>
                    <p class="text-muted small mb-0">{{ $item->total }} casos totales</p>
                  </div>
                  <span class="badge bg-light text-dark">{{ $activosPercent }}% activos</span>
                </div>
                <div class="progress mt-2" style="height: 6px;">
                  <div class="progress-bar bg-success" role="progressbar" style="width: {{ $activosPercent }}%"></div>
                </div>
                <div class="d-flex justify-content-between text-muted small mt-2">
                  <span>{{ $item->activos }} activos</span>
                  <span>{{ $item->cerrados }} cerrados</span>
                </div>
              </div>
            @empty
              <p class="text-muted mb-0">Aún no hay datos suficientes para mostrar esta sección.</p>
            @endforelse
          </div>
        </div>
      </div>
    <div class="col-xl-5">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
              <h5 class="card-title mb-1">Tipos de Discapacidad</h5>
              <small class="text-muted">Distribución de estudiantes por acompañamiento</small>
            </div>
          </div>
          <div class="tipos-discapacidad-scroll">
            @forelse ($tiposDiscapacidad as $tipo)
              <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center">
                  <strong>{{ $tipo['tipo'] }}</strong>
                  <span class="text-muted small">{{ $tipo['total'] }} {{ $tipo['total'] == 1 ? 'estudiante' : 'estudiantes' }}</span>
                </div>
                <div class="progress mt-2" style="height: 6px;">
                  <div class="progress-bar" role="progressbar"
                       style="width: {{ $tipo['porcentaje'] }}%; background-color: {{ $tipo['color'] }}"></div>
                </div>
                <small class="text-muted d-block mt-1">{{ $tipo['porcentaje'] }}% del total</small>
              </div>
            @empty
              <p class="text-muted mb-0">No hay estudiantes con ajustes aplicados aún.</p>
            @endforelse
          </div>
          </div>
        </div>
      </div>
    </div>

  <div class="row g-4 mb-4">
    {{-- Entrevistas --}}
    <div class="col-xl-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
              <h5 class="card-title mb-1">
                <i class="fas fa-calendar-check text-danger me-2"></i>Entrevistas
              </h5>
              <small class="text-muted">Entrevistas agendadas recientemente</small>
            </div>
          </div>
          <div class="list-group list-group-flush">
            @forelse ($actividadReciente['entrevistas'] as $actividad)
              <div class="case-item">
                <div>
                  <strong>{{ $actividad['titulo'] }}</strong>
                  <p class="mb-1 text-muted">{{ $actividad['detalle'] }}</p>
                  <small class="text-muted">
                    <i class="fas fa-clock me-1"></i>{{ $actividad['hace'] }}
                  </small>
                </div>
                <span class="badge bg-{{ $actividad['estado_badge'] }}">{{ $actividad['estado'] }}</span>
              </div>
            @empty
              <p class="text-muted mb-0">No hay entrevistas agendadas.</p>
            @endforelse
          </div>
        </div>
      </div>
    </div>

    {{-- Casos Completados --}}
    <div class="col-xl-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
              <h5 class="card-title mb-1">
                <i class="fas fa-circle-check text-success me-2"></i>Casos Completados
              </h5>
              <small class="text-muted">Casos y ajustes aprobados recientemente</small>
            </div>
          </div>
          <div class="list-group list-group-flush">
            @forelse ($actividadReciente['casos_completados'] as $actividad)
              <div class="case-item">
                <div>
                  <strong>{{ $actividad['titulo'] }}</strong>
                  <p class="mb-1 text-muted">{{ Str::limit($actividad['detalle'], 80) }}</p>
                  <small class="text-muted">
                    <i class="fas fa-clock me-1"></i>{{ $actividad['hace'] }}
                  </small>
                </div>
                <span class="badge bg-{{ $actividad['estado_badge'] }}">{{ $actividad['estado'] }}</span>
              </div>
            @empty
              <p class="text-muted mb-0">No hay casos completados recientemente.</p>
            @endforelse
          </div>
        </div>
      </div>
    </div>

    {{-- Casos Pendientes --}}
    <div class="col-xl-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
              <h5 class="card-title mb-1">
                <i class="fas fa-hourglass-half text-warning me-2"></i>Casos Pendientes
              </h5>
              <small class="text-muted">Casos que requieren atención</small>
            </div>
          </div>
          <div class="list-group list-group-flush">
            @forelse ($actividadReciente['casos_pendientes'] as $actividad)
              <div class="case-item">
                <div>
                  <strong>{{ $actividad['titulo'] }}</strong>
                  <p class="mb-1 text-muted">{{ Str::limit($actividad['detalle'], 80) }}</p>
                  <small class="text-muted">
                    <i class="fas fa-clock me-1"></i>{{ $actividad['hace'] }}
                  </small>
                </div>
                <span class="badge bg-{{ $actividad['estado_badge'] }}">{{ $actividad['estado'] }}</span>
              </div>
            @empty
              <p class="text-muted mb-0">No hay casos pendientes.</p>
            @endforelse
          </div>
        </div>
      </div>
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
  .tipos-discapacidad-scroll {
    max-height: 400px;
    overflow-y: auto;
    padding-right: 8px;
  }
  .tipos-discapacidad-scroll::-webkit-scrollbar {
    width: 8px;
  }
  .tipos-discapacidad-scroll::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
  }
  .tipos-discapacidad-scroll::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
  }
  .tipos-discapacidad-scroll::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
  }
</style>
@endpush
@endsection
