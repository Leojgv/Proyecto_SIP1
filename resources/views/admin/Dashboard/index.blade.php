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
    $cardColors = ['#dc2626', '#dc2626', '#dc2626'];
    $summaryStats = [
      [
        'label' => 'Total Estudiantes',
        'value' => number_format($stats['total_estudiantes']),
        'subtext' => '+' . number_format($stats['nuevos_estudiantes_mes']) . ' este mes',
        'icon' => 'fa-user-graduate',
      ],
      [
        'label' => 'Usuarios Activos',
        'value' => number_format($stats['usuarios_activos'] ?? 0),
        'subtext' => 'Conectados ahora',
        'icon' => 'fa-circle-dot',
      ],
      [
        'label' => 'Total Usuarios',
        'value' => number_format($stats['total_usuarios']),
        'subtext' => 'Usuarios del sistema',
        'icon' => 'fa-users',
      ],
    ];
  @endphp

  <div class="row g-3 mb-4">
    @foreach ($summaryStats as $index => $stat)
      <div class="col-12 col-md-6 col-xl-4">
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
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
              <h5 class="card-title mb-1">Nuevos Estudiantes por Mes</h5>
              <small class="text-muted">Cantidad de estudiantes que se registraron en el sistema en los últimos 12 meses</small>
            </div>
          </div>
          <div style="position: relative; height: 300px;">
            <canvas id="usuariosPorMesChart"></canvas>
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
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('usuariosPorMesChart');
    if (!ctx) return;

    const usuariosPorMesData = @json($usuariosPorMes);

    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: usuariosPorMesData.meses,
        datasets: [{
          label: 'Nuevos Estudiantes',
          data: usuariosPorMesData.datos,
          backgroundColor: 'rgba(220, 38, 38, 0.8)',
          borderColor: 'rgba(220, 38, 38, 1)',
          borderWidth: 2,
          borderRadius: 6,
          borderSkipped: false,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            titleFont: {
              size: 14,
              weight: 'bold'
            },
            bodyFont: {
              size: 13
            },
            callbacks: {
              label: function(context) {
                const value = context.parsed.y;
                return value + ' ' + (value === 1 ? 'estudiante' : 'estudiantes');
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1,
              precision: 0,
              font: {
                size: 11
              }
            },
            grid: {
              color: 'rgba(0, 0, 0, 0.05)'
            }
          },
          x: {
            ticks: {
              font: {
                size: 11
              },
              maxRotation: 45,
              minRotation: 45
            },
            grid: {
              display: false
            }
          }
        }
      }
    });
  });
</script>
@endpush
@endsection
