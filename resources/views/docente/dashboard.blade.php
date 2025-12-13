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

  {{-- Gráfico de Estudiantes que se unen al Sistema --}}
  <div class="row g-4 mb-4">
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h5 class="card-title mb-1">Estudiantes que se Unen al Sistema</h5>
          <p class="text-muted small mb-3">Evolución mensual de estudiantes nuevos (con o sin ajustes)</p>
          
          <div class="d-flex justify-content-center align-items-center" style="height: 350px;">
            <canvas id="estudiantesMountainChart" style="max-height: 350px;"></canvas>
          </div>
        </div>
      </div>
    </div>
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
                <span class="badge bg-success text-white" style="padding: 0.5rem 0.75rem; font-weight: 500;">
                  <i class="fas fa-check-circle me-1"></i>{{ $adjustment }}
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
              <div class="mb-4 p-3 rounded" style="background: #f9fafb; border: 1px solid #e5e7eb;">
                <h6 class="mb-3 fw-semibold">
                  <i class="fas fa-user-graduate text-danger me-2"></i>Información del Estudiante
                </h6>
                <div class="row g-2">
                  <div class="col-md-4">
                    <p class="mb-2 small"><strong>Nombre:</strong><br><span class="text-muted">{{ $student['student'] }}</span></p>
                  </div>
                  <div class="col-md-4">
                    <p class="mb-2 small"><strong>RUT:</strong><br><span class="text-muted">{{ $student['rut'] }}</span></p>
                  </div>
                  <div class="col-md-4">
                    <p class="mb-2 small"><strong>Carrera:</strong><br><span class="text-muted">{{ $student['program'] }}</span></p>
                  </div>
                </div>
              </div>

              <hr class="my-4">

              <div class="mb-3">
                <h6 class="fw-semibold mb-4">
                  <i class="fas fa-sliders text-danger me-2"></i>Ajustes Razonables Aprobados
                </h6>
                @if(!empty($student['adjustments']))
                  @foreach($student['adjustments'] as $index => $ajuste)
                    <div class="border rounded p-3 mb-3" style="background: #f9fafb; border-color: #e5e7eb !important;">
                      <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="fw-semibold mb-0">
                          <i class="fas fa-check-circle text-success me-2"></i>{{ $ajuste['name'] ?? 'Ajuste sin título' }}
                        </h6>
                        <span class="badge bg-success text-white" style="padding: 0.4rem 0.75rem; font-weight: 500;">Aprobado</span>
                      </div>
                      <p class="text-muted small mb-3" style="line-height: 1.6;">{{ $ajuste['description'] ?? 'No hay descripción disponible para este ajuste razonable.' }}</p>
                      
                      <div class="row g-2">
                        <div class="col-md-6">
                          <small class="text-muted d-block" style="line-height: 1.8;">
                            <i class="fas fa-calendar-alt me-1"></i>
                            <strong>Fecha de solicitud:</strong> {{ $ajuste['fecha_solicitud'] ?? 'No especificada' }}
                          </small>
                        </div>
                        <div class="col-md-6">
                          <small class="text-muted d-block" style="line-height: 1.8;">
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
  .student-item:last-child {
    margin-bottom: 0;
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
    background: #f9fafb;
    border-radius: .85rem;
    padding: 1.25rem;
    border: 1px solid #e5e7eb;
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
  .badge.bg-success {
    font-weight: 500;
    padding: 0.5rem 0.75rem;
    border: none;
  }

  /* Modo oscuro para el dashboard */
  .dark-mode .dashboard-page {
    color: #e5e7eb;
  }
  .dark-mode .page-header h1 {
    color: #e8e8e8;
  }
  .dark-mode .card {
    background-color: #1e293b !important;
    border-color: #2d3748 !important;
  }
  .dark-mode .card-title {
    color: #e8e8e8 !important;
  }
  .dark-mode .text-muted {
    color: #94a3b8 !important;
  }
  .dark-mode .student-item {
    background-color: #16213e !important;
    border-color: #2d3748 !important;
  }
  .dark-mode .student-item__body {
    background-color: #0f172a !important;
    border-color: #2d3748 !important;
  }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const estudiantesData = @json($estudiantesPorMes);
  
  if (estudiantesData.labels && estudiantesData.labels.length > 0) {
    const ctx = document.getElementById('estudiantesMountainChart');
    if (!ctx) return;

    const isDarkMode = document.body.classList.contains('dark-mode');
    
    const estudiantesChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: estudiantesData.labels,
        datasets: [{
          label: 'Estudiantes Nuevos',
          data: estudiantesData.datos,
          borderColor: '#dc2626',
          backgroundColor: isDarkMode 
            ? 'rgba(220, 38, 38, 0.15)' 
            : 'rgba(220, 38, 38, 0.1)',
          borderWidth: 3,
          fill: true,
          tension: 0.4,
          pointRadius: 5,
          pointHoverRadius: 7,
          pointBackgroundColor: '#dc2626',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointHoverBackgroundColor: '#b91c1c',
          pointHoverBorderColor: '#fff',
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true,
            position: 'top',
            labels: {
              color: isDarkMode ? '#e8e8e8' : '#374151',
              font: {
                size: 13,
                weight: '500'
              },
              padding: 15,
              usePointStyle: true,
              pointStyle: 'circle'
            }
          },
          tooltip: {
            backgroundColor: isDarkMode ? '#1e293b' : '#ffffff',
            titleColor: isDarkMode ? '#e8e8e8' : '#1f2937',
            bodyColor: isDarkMode ? '#cbd5e1' : '#4b5563',
            borderColor: isDarkMode ? '#2d3748' : '#e5e7eb',
            borderWidth: 1,
            padding: 12,
            displayColors: true,
            callbacks: {
              label: function(context) {
                return 'Estudiantes: ' + context.parsed.y;
              }
            }
          }
        },
        scales: {
          x: {
            grid: {
              color: isDarkMode ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)',
              drawBorder: false
            },
            ticks: {
              color: isDarkMode ? '#94a3b8' : '#6b7280',
              font: {
                size: 11
              }
            }
          },
          y: {
            beginAtZero: true,
            grid: {
              color: isDarkMode ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)',
              drawBorder: false
            },
            ticks: {
              color: isDarkMode ? '#94a3b8' : '#6b7280',
              font: {
                size: 11
              },
              stepSize: 1,
              precision: 0
            }
          }
        }
      }
    });

    // Actualizar colores cuando cambia el modo oscuro
    const observer = new MutationObserver(function(mutations) {
      mutations.forEach(function(mutation) {
        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
          const darkMode = document.body.classList.contains('dark-mode');
          estudiantesChart.options.plugins.legend.labels.color = darkMode ? '#e8e8e8' : '#374151';
          estudiantesChart.options.plugins.tooltip.backgroundColor = darkMode ? '#1e293b' : '#ffffff';
          estudiantesChart.options.plugins.tooltip.titleColor = darkMode ? '#e8e8e8' : '#1f2937';
          estudiantesChart.options.plugins.tooltip.bodyColor = darkMode ? '#cbd5e1' : '#4b5563';
          estudiantesChart.options.plugins.tooltip.borderColor = darkMode ? '#2d3748' : '#e5e7eb';
          estudiantesChart.options.scales.x.grid.color = darkMode ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';
          estudiantesChart.options.scales.x.ticks.color = darkMode ? '#94a3b8' : '#6b7280';
          estudiantesChart.options.scales.y.grid.color = darkMode ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';
          estudiantesChart.options.scales.y.ticks.color = darkMode ? '#94a3b8' : '#6b7280';
          estudiantesChart.data.datasets[0].backgroundColor = darkMode ? 'rgba(220, 38, 38, 0.15)' : 'rgba(220, 38, 38, 0.1)';
          estudiantesChart.update();
        }
      });
    });

    observer.observe(document.body, {
      attributes: true,
      attributeFilter: ['class']
    });
  }
});
</script>
@endpush
@endsection
