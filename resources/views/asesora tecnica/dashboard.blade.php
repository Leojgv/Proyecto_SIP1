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
    $cardPalette = ['#dc2626', '#dc2626'];
  @endphp

  <div class="row g-3 mb-4">
    @foreach ($metrics as $index => $metric)
      <div class="col-12 col-md-4">
        <div class="stats-card" style="background: {{ $cardPalette[$index % count($cardPalette)] }};">
          <div class="stats-card__value">{{ $metric['value'] }}</div>
          <div class="stats-card__icon"><i class="fas {{ $metric['icon'] }}"></i></div>
          <p class="stats-card__title">{{ $metric['label'] }}</p>
          <small class="stats-card__sub">{{ $metric['hint'] }}</small>
        </div>
      </div>
    @endforeach
  </div>

  {{-- Estadísticas adicionales --}}
  <div class="row g-4 mb-4">
    <div class="col-xl-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h5 class="card-title mb-0">
                <i class="fas fa-chart-line text-danger me-2"></i>Evolución de Ajustes
              </h5>
              <small class="text-muted">Ajustes formulados en los últimos 6 meses</small>
            </div>
          </div>
          <div class="chart-container" style="position: relative; height: 250px;">
            <canvas id="evolucionChart"></canvas>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h5 class="card-title mb-0">
                <i class="fas fa-star text-danger me-2"></i>Ajustes Más Comunes
              </h5>
              <small class="text-muted">Tipos de ajustes más formulados</small>
            </div>
          </div>
          @if(isset($ajustesComunes) && $ajustesComunes->count() > 0)
            <div class="d-flex flex-column gap-2">
              @foreach($ajustesComunes as $ajuste)
                <div class="d-flex align-items-center justify-content-between p-3 border rounded bg-light">
                  <div class="d-flex align-items-center gap-3">
                    <div class="badge bg-danger text-white" style="width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 0.875rem;">
                      {{ $loop->iteration }}
                    </div>
                    <div>
                      <strong>{{ $ajuste['nombre'] }}</strong>
                    </div>
                  </div>
                  <div class="h5 mb-0 text-danger">{{ $ajuste['total'] }}</div>
                </div>
              @endforeach
            </div>
          @else
            <p class="text-muted text-center py-3 mb-0">Aún no hay datos suficientes para mostrar ajustes comunes.</p>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body d-flex flex-wrap justify-content-between align-items-center">
      <div>
        <h5 class="mb-1">¿Necesitas formular un nuevo ajuste?</h5>
        <p class="text-muted mb-0">Centraliza desde aquí la creación de ajustes y su seguimiento.</p>
      </div>
      <a href="{{ route('asesora-tecnica.ajustes.create') }}" class="btn btn-danger">+ Formular ajuste</a>
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
          </div>
          @forelse ($assignedCases as $case)
            @php
              $badgeClass = match($case['status']) {
                'Pendiente de formulación de ajuste' => 'bg-warning text-dark',
                'Pendiente de preaprobación' => 'bg-primary',
                'Aprobado' => 'bg-success',
                'Rechazado' => 'bg-danger',
                default => 'bg-secondary'
              };
            @endphp
            <div class="case-item">
              <div class="w-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <div>
                    <strong>{{ $case['student'] }}</strong>
                    <p class="text-muted mb-1 small">{{ $case['program'] }}</p>
                  </div>
                  <span class="badge {{ $badgeClass }}">{{ $case['status'] }}</span>
                </div>
                <p class="text-muted small mb-2"><strong>Descripción:</strong> {{ $case['summary'] }}</p>
                <div class="d-flex flex-wrap align-items-center gap-3">
                  <div class="text-muted small">
                    <i class="far fa-calendar me-1"></i>{{ $case['fecha_solicitud'] }}
                  </div>
                  <div class="text-muted small">
                    Ajustes: <span class="fw-semibold">{{ $case['ajustes_count'] }}</span>
                  </div>
                  @if($case['puede_enviar_preaprobacion'])
                    <div class="ms-auto">
                      <form action="{{ route('asesora-tecnica.solicitudes.enviar-preaprobacion', $case['case_id']) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('(Seguridad) ¿Estás seguro de enviar a Preaprobación?');">
                          <i class="fas fa-paper-plane me-1"></i>Enviar a Preaprobación
                        </button>
                      </form>
                    </div>
                  @elseif($case['status'] === 'Pendiente de preaprobación')
                    <div class="ms-auto">
                      <span class="badge bg-warning text-dark">En preaprobación</span>
                    </div>
                  @elseif(in_array($case['status'], ['Pendiente de Aprobación', 'Aprobado', 'Rechazado']))
                    <div class="ms-auto">
                      <span class="text-muted small">Enviado</span>
                    </div>
                  @else
                    <div class="ms-auto">
                      <span class="text-muted small">Formular ajustes primero</span>
                    </div>
                  @endif
                </div>
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
          </div>
          @forelse ($recentAdjustments as $adjustment)
            <div class="timeline-item">
              <div>
                <strong>{{ $adjustment['student'] }}</strong>
                <p class="text-muted mb-1">{{ $adjustment['program'] }}</p>
                <p class="text-muted small mb-0">{{ count($adjustment['adjustments'] ?? []) }} ajustes recientes</p>
              </div>
              <div class="text-end">
                <button
                  type="button"
                  class="btn btn-sm btn-outline-danger"
                  data-bs-toggle="modal"
                  data-bs-target="#ajustesEstudianteModal"
                  data-estudiante="{{ $adjustment['student'] }}"
                  data-programa="{{ $adjustment['program'] }}"
                  data-ajustes='@json($adjustment['adjustments'] ?? [])'
                >
                  Ver ajustes
                </button>
              </div>
            </div>
          @empty
            <p class="text-muted mb-0">Todavía no registras ajustes completados.</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>

</div>

{{-- Modal ajustes por estudiante --}}
<div class="modal fade" id="ajustesEstudianteModal" tabindex="-1" aria-labelledby="ajustesEstudianteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title" id="ajustesEstudianteModalLabel">Ajustes del estudiante</h5>
          <small class="text-muted">Listado de ajustes registrados para este perfil.</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="mb-4">
          <div class="border rounded p-3 bg-light">
            <div class="fw-semibold mb-1" data-estudiante-name></div>
            <div class="text-muted small" data-program-name></div>
          </div>
        </div>
        <div data-ajustes-list></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Gráfico de evolución mensual de ajustes
    const evolucionData = @json($evolucionMensual ?? []);
    const mesesLabels = @json($mesesNombres ?? []);
    const ctx = document.getElementById('evolucionChart');
    
    if (ctx && evolucionData.length > 0) {
      new Chart(ctx, {
        type: 'line',
        data: {
          labels: mesesLabels,
          datasets: [{
            label: 'Ajustes formulados',
            data: evolucionData,
            borderColor: 'rgba(220, 38, 38, 1)',
            backgroundColor: 'rgba(220, 38, 38, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: 'rgba(220, 38, 38, 1)',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 6,
            pointHoverRadius: 8,
            pointHoverBackgroundColor: 'rgba(220, 38, 38, 1)',
            pointHoverBorderColor: '#ffffff',
            pointHoverBorderWidth: 3,
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
                usePointStyle: true,
                padding: 15,
                font: {
                  size: 12,
                  weight: '500'
                }
              }
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
                  return 'Ajustes: ' + context.parsed.y;
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                stepSize: 1,
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
                }
              },
              grid: {
                display: false
              }
            }
          }
        }
      });
    }

    var modal = document.getElementById('ajustesEstudianteModal');
    if (!modal) return;

    modal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      if (!button) return;

      var estudiante = button.getAttribute('data-estudiante') || 'Estudiante';
      var programa = button.getAttribute('data-programa') || 'Programa no asignado';
      var ajustesRaw = button.getAttribute('data-ajustes') || '[]';
      var ajustes = [];

      try {
        ajustes = JSON.parse(ajustesRaw);
      } catch (err) {
        ajustes = [];
      }

      var nameEl = modal.querySelector('[data-estudiante-name]');
      var programEl = modal.querySelector('[data-program-name]');
      var listEl = modal.querySelector('[data-ajustes-list]');

      if (nameEl) nameEl.textContent = estudiante;
      if (programEl) programEl.textContent = programa;

      if (listEl) {
        listEl.innerHTML = '';

        if (!ajustes.length) {
          listEl.innerHTML = '<div class="text-center py-4"><p class="text-muted mb-0">No hay ajustes registrados.</p></div>';
          return;
        }

        ajustes.forEach(function (ajuste) {
          var item = document.createElement('div');
          item.className = 'border rounded p-3 bg-light mb-3';

          var row = document.createElement('div');
          row.className = 'd-flex justify-content-between align-items-start';

          var left = document.createElement('div');
          left.className = 'flex-grow-1 d-flex align-items-start gap-2';
          
          // Agregar checkmark si está aprobado o en preaprobación
          var estado = ajuste.status || ajuste.estado || 'Pendiente';
          var showCheck = estado === 'Aprobado' || estado === 'Aprobada' || 
                         estado === 'Pendiente de preaprobación' || 
                         estado === 'Pendiente de Aprobación';
          
          if (showCheck) {
            var checkIcon = document.createElement('div');
            checkIcon.className = 'text-success mt-1';
            checkIcon.innerHTML = '<i class="fas fa-check-circle"></i>';
            left.appendChild(checkIcon);
          }
          
          var contentDiv = document.createElement('div');
          contentDiv.className = 'flex-grow-1';
          
          var title = document.createElement('div');
          title.className = 'fw-semibold mb-2';
          title.textContent = ajuste.name || 'Ajuste sin titulo';
          
          var description = document.createElement('div');
          description.className = 'text-muted small mb-2';
          description.style.lineHeight = '1.6';
          description.textContent = ajuste.description || 'No hay descripción';
          
          var date = document.createElement('div');
          date.className = 'text-muted small mt-2';
          
          // Formatear fecha si existe
          var fechaTexto = 's/f';
          if (ajuste.completed_at) {
            try {
              var fecha = new Date(ajuste.completed_at);
              if (!isNaN(fecha.getTime())) {
                fechaTexto = 'Actualizado ' + fecha.toLocaleDateString('es-CL', {
                  year: 'numeric',
                  month: '2-digit',
                  day: '2-digit'
                });
              }
            } catch(e) {
              fechaTexto = 'Actualizado ' + ajuste.completed_at;
            }
          }
          date.textContent = fechaTexto;
          
          contentDiv.appendChild(title);
          contentDiv.appendChild(description);
          contentDiv.appendChild(date);
          
          // Si está rechazado, mostrar motivo de rechazo después de la fecha
          if ((estado === 'Rechazado' || estado === 'Rechazada') && ajuste.motivo_rechazo) {
            var motivoDiv = document.createElement('div');
            motivoDiv.className = 'mt-2 p-2 border-start border-danger border-3 rounded';
            motivoDiv.style.backgroundColor = '#fff5f5';
            
            var motivoLabel = document.createElement('div');
            motivoLabel.className = 'fw-semibold small text-danger mb-1';
            motivoLabel.innerHTML = '<i class="fas fa-exclamation-circle me-1"></i>Motivo de rechazo por Directora de Carrera:';
            
            var motivoTexto = document.createElement('div');
            motivoTexto.className = 'small text-dark';
            motivoTexto.style.lineHeight = '1.5';
            motivoTexto.textContent = ajuste.motivo_rechazo;
            
            motivoDiv.appendChild(motivoLabel);
            motivoDiv.appendChild(motivoTexto);
            contentDiv.appendChild(motivoDiv);
          }
          
          left.appendChild(contentDiv);

          var badge = document.createElement('span');
          var badgeClass = 'badge ';
          
          if (estado === 'Aprobado' || estado === 'Aprobada') {
            badgeClass += 'bg-success';
          } else if (estado === 'Rechazado' || estado === 'Rechazada') {
            badgeClass += 'bg-danger';
          } else if (estado === 'Pendiente de preaprobación' || estado === 'Pendiente de Aprobación') {
            badgeClass += 'bg-primary';
          } else {
            badgeClass += 'bg-warning text-dark';
          }
          
          badge.className = badgeClass;
          badge.textContent = estado;
          badge.style.padding = '0.4rem 0.75rem';
          badge.style.fontWeight = '500';

          row.appendChild(left);
          row.appendChild(badge);
          item.appendChild(row);
          listEl.appendChild(item);
        });
      }
    });
  });
</script>

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
  .metric-card {
    transition: all 0.2s ease;
  }
  .metric-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,.1);
  }

  /* Estilos para modo oscuro */
  [data-theme="dark"] .metric-card {
    background: #1e293b;
    border-color: #334155;
  }

  [data-theme="dark"] .metric-card .text-muted {
    color: #94a3b8;
  }

  [data-theme="dark"] .metric-card .h4 {
    color: #e2e8f0;
  }

  [data-theme="dark"] .bg-light {
    background: #1e293b !important;
    border-color: #334155 !important;
  }

  [data-theme="dark"] .bg-light strong {
    color: #e2e8f0;
  }

  [data-theme="dark"] .bg-light .h5 {
    color: #e2e8f0;
  }

  .chart-container {
    position: relative;
  }

  [data-theme="dark"] .chart-container canvas {
    filter: brightness(0.9);
  }
</style>
@endsection
