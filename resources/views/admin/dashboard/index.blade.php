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
              <h5 class="card-title mb-1">Nuevos Estudiantes</h5>
              <small class="text-muted" id="chart-description">Cantidad de estudiantes que se registraron en el sistema en los últimos 5 años</small>
            </div>
          </div>
          
          <!-- Pestañas -->
          <ul class="nav nav-tabs chart-tabs mb-3" id="chartTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="ano-tab" data-bs-toggle="tab" data-bs-target="#ano" type="button" role="tab" data-period="ano">
                <i class="fas fa-calendar-alt me-1"></i> Año
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="mes-tab" data-bs-toggle="tab" data-bs-target="#mes" type="button" role="tab" data-period="mes">
                <i class="fas fa-calendar me-1"></i> Mes
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="dia-tab" data-bs-toggle="tab" data-bs-target="#dia" type="button" role="tab" data-period="dia">
                <i class="fas fa-calendar-day me-1"></i> Día
              </button>
            </li>
          </ul>
          
          <!-- Contenido de las pestañas -->
          <div class="tab-content">
            <div class="tab-pane fade show active" id="ano" role="tabpanel">
              <div style="position: relative; height: 300px;">
                <canvas id="usuariosPorAnoChart"></canvas>
              </div>
            </div>
            <div class="tab-pane fade" id="mes" role="tabpanel">
              <div style="position: relative; height: 300px;">
                <canvas id="usuariosPorMesChart"></canvas>
              </div>
            </div>
            <div class="tab-pane fade" id="dia" role="tabpanel">
              <div style="position: relative; height: 300px;">
                <canvas id="usuariosPorDiaChart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-12 col-lg-6">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
              <h5 class="card-title mb-1">Resumen de Carreras</h5>
              <small class="text-muted">Información general sobre las carreras del sistema</small>
            </div>
            <div class="text-danger">
              <i class="fas fa-graduation-cap fa-2x"></i>
            </div>
          </div>
          <div class="info-container">
            <div class="info-item">
              <div class="info-label">
                <i class="fas fa-building text-danger me-2"></i>
                <span>Total de Carreras</span>
              </div>
              <div class="info-value">{{ number_format($stats['total_carreras']) }}</div>
            </div>
            <div class="info-item">
              <div class="info-label">
                <i class="fas fa-user-graduate text-danger me-2"></i>
                <span>Estudiantes por Carrera</span>
              </div>
              <div class="info-value">
                {{ $stats['total_carreras'] > 0 ? number_format($stats['total_estudiantes'] / $stats['total_carreras'], 1) : '0' }}
              </div>
            </div>
            <div class="info-item">
              <div class="info-label">
                <i class="fas fa-chart-line text-danger me-2"></i>
                <span>Cobertura del Sistema</span>
              </div>
              <div class="info-value">
                {{ $stats['total_estudiantes'] > 0 ? number_format(($stats['total_ajustes'] / $stats['total_estudiantes']) * 100, 1) : '0' }}%
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-lg-6">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
              <h5 class="card-title mb-1">Estado de Solicitudes</h5>
              <small class="text-muted">Distribución de solicitudes y ajustes razonables</small>
            </div>
            <div class="text-danger">
              <i class="fas fa-clipboard-list fa-2x"></i>
            </div>
          </div>
          <div class="info-container">
            <div class="info-item">
              <div class="info-label">
                <i class="fas fa-file-alt text-danger me-2"></i>
                <span>Total Solicitudes</span>
              </div>
              <div class="info-value">{{ number_format($stats['total_solicitudes']) }}</div>
            </div>
            <div class="info-item">
              <div class="info-label">
                <i class="fas fa-hourglass-half text-warning me-2"></i>
                <span>Solicitudes Pendientes</span>
              </div>
              <div class="info-value">{{ number_format($stats['solicitudes_pendientes']) }}</div>
            </div>
            <div class="info-item">
              <div class="info-label">
                <i class="fas fa-check-circle text-success me-2"></i>
                <span>Solicitudes Aprobadas</span>
              </div>
              <div class="info-value">{{ number_format($stats['solicitudes_aprobadas']) }}</div>
            </div>
            <div class="info-item">
              <div class="info-label">
                <i class="fas fa-tools text-danger me-2"></i>
                <span>Total Ajustes Razonables</span>
              </div>
              <div class="info-value">{{ number_format($stats['total_ajustes']) }}</div>
            </div>
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

  /* Modo oscuro para dashboard admin */
  .dark-mode .dashboard-page {
    background: transparent;
  }

  .dark-mode .page-header h1 {
    color: #e8e8e8 !important;
  }

  .dark-mode .page-header .text-muted {
    color: #b8b8b8 !important;
  }

  .dark-mode .page-header .text-danger {
    color: #f87171 !important;
  }

  .dark-mode .stats-card {
    background-color: #0b1220 !important;
    border-color: #2d3748 !important;
    box-shadow: 0 8px 18px rgba(0,0,0,.35) !important;
  }

  .dark-mode .stats-card__icon {
    color: rgba(255,255,255,.2) !important;
  }

  .info-container {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
  }

  .info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 0.75rem;
    border-left: 3px solid #dc2626;
    transition: all 0.2s ease;
  }

  .info-item:hover {
    background: #f0f0f5;
    transform: translateX(2px);
  }

  .info-label {
    display: flex;
    align-items: center;
    font-size: 0.9rem;
    color: #666;
    font-weight: 500;
  }

  .info-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f1f2d;
  }

  .dark-mode .info-item {
    background: #1e293b;
    border-left-color: #f87171;
  }

  .dark-mode .info-item:hover {
    background: #334155;
  }

  .dark-mode .info-label {
    color: #b8b8b8;
  }

  .dark-mode .info-value {
    color: #e8e8e8;
  }

  .chart-tabs {
    border-bottom: 2px solid #e5e7eb;
  }

  .chart-tabs .nav-link {
    color: #6b7280;
    border: none;
    border-bottom: 2px solid transparent;
    padding: 0.75rem 1.25rem;
    font-weight: 500;
    transition: all 0.2s ease;
  }

  .chart-tabs .nav-link:hover {
    color: #dc2626;
    border-bottom-color: #dc2626;
  }

  .chart-tabs .nav-link.active {
    color: #dc2626;
    background: transparent;
    border-bottom-color: #dc2626;
  }

  .dark-mode .chart-tabs {
    border-bottom-color: #374151;
  }

  .dark-mode .chart-tabs .nav-link {
    color: #9ca3af;
  }

  .dark-mode .chart-tabs .nav-link:hover {
    color: #f87171;
    border-bottom-color: #f87171;
  }

  .dark-mode .chart-tabs .nav-link.active {
    color: #f87171;
    border-bottom-color: #f87171;
  }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const usuariosPorAnoData = @json($usuariosPorAno);
    const usuariosPorMesData = @json($usuariosPorMes);
    const usuariosPorDiaData = @json($usuariosPorDia);

    const isDarkMode = () => document.documentElement.classList.contains('dark-mode');
    const chartDescription = document.getElementById('chart-description');
    
    let chartAno = null;
    let chartMes = null;
    let chartDia = null;

    // Función para crear configuración de gráfico
    const createChartConfig = (labels, data, labelText) => {
      return {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Nuevos Estudiantes',
            data: data,
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
              backgroundColor: isDarkMode() ? 'rgba(30, 41, 59, 0.95)' : 'rgba(0, 0, 0, 0.8)',
              titleColor: isDarkMode() ? '#e8e8e8' : '#fff',
              bodyColor: isDarkMode() ? '#b8b8b8' : '#fff',
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
                },
                color: isDarkMode() ? '#b8b8b8' : '#666'
              },
              grid: {
                color: isDarkMode() ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.05)'
              }
            },
            x: {
              ticks: {
                font: {
                  size: 11
                },
                maxRotation: labelText === 'dia' ? 45 : (labelText === 'mes' ? 45 : 0),
                minRotation: labelText === 'dia' ? 45 : (labelText === 'mes' ? 45 : 0),
                color: isDarkMode() ? '#b8b8b8' : '#666'
              },
              grid: {
                display: false
              }
            }
          }
        }
      };
    };

    // Función para actualizar colores de un gráfico
    const updateChartColors = (chart) => {
      if (!chart) return;
      const dark = isDarkMode();
      chart.options.scales.y.ticks.color = dark ? '#b8b8b8' : '#666';
      chart.options.scales.y.grid.color = dark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.05)';
      chart.options.scales.x.ticks.color = dark ? '#b8b8b8' : '#666';
      chart.options.plugins.tooltip.backgroundColor = dark ? 'rgba(30, 41, 59, 0.95)' : 'rgba(0, 0, 0, 0.8)';
      chart.options.plugins.tooltip.titleColor = dark ? '#e8e8e8' : '#fff';
      chart.options.plugins.tooltip.bodyColor = dark ? '#b8b8b8' : '#fff';
      chart.update('none');
    };

    // Inicializar gráfico por año
    const ctxAno = document.getElementById('usuariosPorAnoChart');
    if (ctxAno) {
      chartAno = new Chart(ctxAno, createChartConfig(usuariosPorAnoData.anos, usuariosPorAnoData.datos, 'ano'));
    }

    // Inicializar gráfico por mes
    const ctxMes = document.getElementById('usuariosPorMesChart');
    if (ctxMes) {
      chartMes = new Chart(ctxMes, createChartConfig(usuariosPorMesData.meses, usuariosPorMesData.datos, 'mes'));
    }

    // Inicializar gráfico por día
    const ctxDia = document.getElementById('usuariosPorDiaChart');
    if (ctxDia) {
      chartDia = new Chart(ctxDia, createChartConfig(usuariosPorDiaData.dias, usuariosPorDiaData.datos, 'dia'));
    }

    // Actualizar descripción y colores cuando cambie la pestaña
    const tabButtons = document.querySelectorAll('#chartTabs button[data-period]');
    tabButtons.forEach(button => {
      button.addEventListener('shown.bs.tab', function(e) {
        const period = e.target.getAttribute('data-period');
        
        // Actualizar descripción
        const descriptions = {
          'ano': 'Cantidad de estudiantes que se registraron en el sistema en los últimos 5 años',
          'mes': 'Cantidad de estudiantes que se registraron en el sistema en los últimos 12 meses',
          'dia': 'Cantidad de estudiantes que se registraron en el sistema en los últimos 30 días'
        };
        if (chartDescription) {
          chartDescription.textContent = descriptions[period] || descriptions['mes'];
        }

        // Actualizar colores del gráfico activo
        setTimeout(() => {
          if (period === 'ano' && chartAno) updateChartColors(chartAno);
          if (period === 'mes' && chartMes) updateChartColors(chartMes);
          if (period === 'dia' && chartDia) updateChartColors(chartDia);
        }, 100);
      });
    });

    // Observar cambios en la clase dark-mode
    const observer = new MutationObserver(() => {
      updateChartColors(chartAno);
      updateChartColors(chartMes);
      updateChartColors(chartDia);
    });
    observer.observe(document.documentElement, {
      attributes: true,
      attributeFilter: ['class']
    });
  });
</script>
@endpush
@endsection
