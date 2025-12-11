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
      <div class="card border-0 shadow-sm">
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
                  <small class="text-muted d-block">{{ $case['program'] }}</small>
                  <small class="text-muted">Encargo: {{ $case['requested_by'] }}</small>
                </div>
                <span class="priority-badge priority-{{ $case['priority_level'] }}">{{ $case['priority'] }}</span>
              </div>
              <p class="case-card__focus mb-2">{{ Str::limit($case['support_focus'], 140) }}</p>
              <div class="case-card__adjustments mb-3">
                @forelse ($case['adjustments'] as $adjustment)
                  <span class="badge bg-light text-danger">{{ $adjustment }}</span>
                @empty
                  <span class="badge bg-light text-muted">Sin ajustes propuestos</span>
                @endforelse
              </div>
              <div class="case-card__meta">
                <div>
                  <small class="text-muted text-uppercase">Estado</small>
                  <div class="fw-semibold">{{ $case['status'] }}</div>
                </div>
                <div>
                  <small class="text-muted text-uppercase">Fecha solicitud</small>
                  <div class="fw-semibold">{{ $case['submitted_at'] }}</div>
                </div>
              </div>
              <div class="case-card__actions">
                <form action="{{ $case['approve_url'] }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-danger btn-sm">Aprobar caso</button>
                </form>
                <a href="{{ $case['reject_url'] }}" class="btn btn-sm btn-outline-danger">Rechazar/Devolver</a>
                <a href="{{ $case['detail_url'] }}" class="btn btn-sm btn-link text-decoration-none">Ver detalles</a>
              </div>
            </div>
          @empty
            <p class="text-muted mb-0 text-center py-3">No hay solicitudes pendientes, revisa nuevamente mas tarde.</p>
          @endforelse
        </div>
      </div>
    </div>
    <div class="col-xl-4">
      <div class="card border-0 shadow-sm">
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

@php
  $careerLabels = collect($careerStats)->pluck('name')->map(fn ($name) => $name ?: 'Carrera sin nombre')->values();
  $careerTotals = collect($careerStats)->pluck('total_students')->map(fn ($value) => (int) $value)->values();
@endphp
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
          <h5 class="card-title mb-1">Estadisticas por carrera</h5>
          <small class="text-muted">Distribucion de estudiantes por carrera.</small>
        </div>
        <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger">Total carreras: {{ count($careerStats) }}</span>
      </div>

      <div class="row g-4 align-items-stretch">
        <div class="col-lg-6">
          <div class="card h-100 border-0 shadow-sm pastel-card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                  <h6 class="mb-0"><i class="fas fa-chart-pie text-danger me-2"></i>Grafica de pastel</h6>
                  <small class="text-muted">Participacion de cada carrera.</small>
                </div>
                <div class="btn-group" role="group" aria-label="Filtro de visualización">
                  <input type="radio" class="btn-check" name="chartFilter" id="filterStudents" value="students" checked>
                  <label class="btn btn-sm btn-outline-danger" for="filterStudents">
                    <i class="fas fa-user-graduate me-1"></i>Estudiantes
                  </label>
                  <input type="radio" class="btn-check" name="chartFilter" id="filterAdjustments" value="adjustments">
                  <label class="btn btn-sm btn-outline-danger" for="filterAdjustments">
                    <i class="fas fa-sliders me-1"></i>Ajustes
                  </label>
                </div>
              </div>
              <canvas id="careerPieChart" height="320"></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                  <h6 class="mb-0"><i class="fas fa-list text-danger me-2"></i>Lista de carreras</h6>
                  <small class="text-muted" id="tableDescription">Cantidad de estudiantes por carrera.</small>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-dark-mode align-middle mb-0" id="careerTable">
                  <thead>
                  <tr>
                    <th>Carrera</th>
                    <th class="text-center" id="tableHeader">Estudiantes</th>
                  </tr>
                  </thead>
                  <tbody id="careerTableBody">
                  @forelse ($careerStats as $career)
                    <tr data-career-id="{{ $loop->index }}" data-students="{{ $career['total_students'] }}" data-adjustments="{{ $career['total_adjustments'] ?? 0 }}" data-adjustments-list="{{ json_encode($career['adjustments_list'] ?? []) }}">
                      <td>
                        <div class="fw-semibold">{{ $career['name'] }}</div>
                        <small class="text-muted">{{ $career['jornada'] }}</small>
                      </td>
                      <td class="text-center fw-semibold career-value">{{ $career['total_students'] }}</td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="2" class="text-center text-muted">Aun no hay carreras asignadas a tu perfil.</td>
                    </tr>
                  @endforelse
                  </tbody>
                </table>
              </div>
              
              {{-- Modal para mostrar ajustes de una carrera --}}
              <div class="modal fade" id="adjustmentsModal" tabindex="-1" aria-labelledby="adjustmentsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="adjustmentsModalLabel">
                        <i class="fas fa-sliders text-danger me-2"></i>Ajustes Aplicados
                      </h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="adjustmentsModalBody">
                      <p class="text-muted">Cargando ajustes...</p>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
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
  .case-card {
    border: 1px solid #f0f0f5;
    border-radius: 1rem;
    padding: 1.1rem;
    margin-bottom: 1rem;
    background: #fff;
    box-shadow: 0 8px 18px rgba(15,23,42,.05);
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
    background: #fff7f7;
    border-radius: .85rem;
    border: 1px solid #fce8e8;
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
    border: 1px solid #f0f0f5;
    background: #fff;
  }
  .priority-high {
    background: #fee2e2;
    color: #b91c1c;
    border-color: #fecdd3;
  }
  .priority-medium {
    background: #fef3c7;
    color: #b45309;
    border-color: #fde68a;
  }
  .priority-low {
    background: #d1fae5;
    color: #047857;
    border-color: #a7f3d0;
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
  .pastel-card {
    background: radial-gradient(circle at 15% 20%, #fff5f5, #ffffff 55%);
  }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('careerPieChart');
    if (!ctx) return;

    const labels = @json($careerLabels);
    const studentData = @json($careerTotals);
    const careerStats = @json($careerStats);
    
    // Preparar datos de ajustes
    const adjustmentData = careerStats.map(career => career.total_adjustments || 0);

    const palette = ['#dc2626','#f97316','#f59e0b','#22c55e','#0ea5e9','#6366f1','#a855f7','#ec4899','#14b8a6','#8b5cf6'];
    const backgroundColor = labels.map((_, idx) => palette[idx % palette.length]);

    let chart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels,
        datasets: [{
          data: studentData,
          backgroundColor,
          borderWidth: 1,
          hoverOffset: 6,
          cutout: '55%'
        }]
      },
      options: {
        plugins: {
          legend: { position: 'bottom' },
          tooltip: { 
            callbacks: { 
              label: function(context) {
                const label = context.label || '';
                const value = context.parsed || 0;
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                return `${label}: ${value} (${percentage}%)`;
              }
            } 
          }
        },
        onClick: function(event, elements) {
          if (elements.length > 0) {
            const index = elements[0].index;
            const career = careerStats[index];
            if (career && career.adjustments_list && career.adjustments_list.length > 0) {
              showAdjustmentsModal(career.name, career.adjustments_list);
            }
          }
        }
      }
    });

    // Manejar cambio de filtro
    const filterInputs = document.querySelectorAll('input[name="chartFilter"]');
    const tableDescription = document.getElementById('tableDescription');
    const tableHeader = document.getElementById('tableHeader');
    const careerValues = document.querySelectorAll('.career-value');
    const tableRows = document.querySelectorAll('#careerTableBody tr[data-career-id]');

    filterInputs.forEach(input => {
      input.addEventListener('change', function() {
        const isStudents = this.value === 'students';
        
        // Actualizar gráfico
        chart.data.datasets[0].data = isStudents ? studentData : adjustmentData;
        chart.data.datasets[0].label = isStudents ? 'Estudiantes' : 'Ajustes';
        chart.update();

        // Actualizar tabla
        tableDescription.textContent = isStudents 
          ? 'Cantidad de estudiantes por carrera.' 
          : 'Cantidad de ajustes aplicados por carrera.';
        tableHeader.textContent = isStudents ? 'Estudiantes' : 'Ajustes';
        
        tableRows.forEach(row => {
          const value = isStudents 
            ? row.getAttribute('data-students') 
            : row.getAttribute('data-adjustments');
          const valueCell = row.querySelector('.career-value');
          if (valueCell) {
            valueCell.textContent = value || '0';
          }
        });
      });
    });

    // Función para mostrar modal de ajustes
    function showAdjustmentsModal(careerName, adjustments) {
      const modalBody = document.getElementById('adjustmentsModalBody');
      const modalLabel = document.getElementById('adjustmentsModalLabel');
      
      modalLabel.innerHTML = `<i class="fas fa-sliders text-danger me-2"></i>Ajustes Aplicados - ${careerName}`;
      
      if (adjustments.length === 0) {
        modalBody.innerHTML = '<p class="text-muted text-center">No hay ajustes aplicados en esta carrera.</p>';
      } else {
        let html = '<div class="list-group">';
        adjustments.forEach(ajuste => {
          html += `
            <div class="list-group-item mb-2 border rounded">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="mb-1 fw-semibold">
                  <i class="fas fa-check-circle text-success me-2"></i>${ajuste.nombre || 'Ajuste sin título'}
                </h6>
                <span class="badge bg-warning text-dark">${ajuste.estado || 'Pendiente'}</span>
              </div>
              <p class="text-muted small mb-0">${ajuste.descripcion || 'Sin descripción adicional'}</p>
            </div>
          `;
        });
        html += '</div>';
        modalBody.innerHTML = html;
      }
      
      const modal = new bootstrap.Modal(document.getElementById('adjustmentsModal'));
      modal.show();
    }

    // Hacer las filas de la tabla clickeables para mostrar ajustes
    tableRows.forEach(row => {
      row.style.cursor = 'pointer';
      row.addEventListener('click', function() {
        const adjustmentsList = JSON.parse(this.getAttribute('data-adjustments-list') || '[]');
        const careerName = this.querySelector('.fw-semibold').textContent;
        if (adjustmentsList.length > 0) {
          showAdjustmentsModal(careerName, adjustmentsList);
        }
      });
      row.addEventListener('mouseenter', function() {
        this.style.backgroundColor = '#f8f9fa';
      });
      row.addEventListener('mouseleave', function() {
        this.style.backgroundColor = '';
      });
    });
  });
</script>
@endpush
@endsection
