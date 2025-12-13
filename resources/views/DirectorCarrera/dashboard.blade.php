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
    $cardColors = ['#dc2626', '#dc2626', '#dc2626', '#dc2626', '#dc2626'];
  @endphp
  <div class="row g-2 mb-4 stats-cards-row">
    @foreach ($summaryStats as $index => $stat)
      <div class="col-12 col-md-6 col-lg stats-card-col">
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
              </div>
              <p class="case-card__focus mb-3">{{ Str::limit($case['support_focus'], 140) }}</p>
              
              @if(!empty($case['adjustments']))
                <div class="mb-3">
                  <div class="accordion" id="adjustmentsAccordion{{ $loop->index }}">
                    <div class="accordion-item border rounded">
                      <h2 class="accordion-header" id="heading{{ $loop->index }}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $loop->index }}" aria-expanded="false" aria-controls="collapse{{ $loop->index }}">
                          <i class="fas fa-sliders me-2"></i>
                          <strong>Ajustes Razonables Aplicados</strong>
                          <span class="badge bg-danger text-white ms-2">{{ count($case['adjustments']) }}</span>
                        </button>
                      </h2>
                      <div id="collapse{{ $loop->index }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $loop->index }}" data-bs-parent="#adjustmentsAccordion{{ $loop->index }}">
                        <div class="accordion-body">
                          @foreach($case['adjustments'] as $adjustment)
                            <div class="border rounded p-3 mb-2 bg-light">
                              <h6 class="fw-semibold mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>{{ $adjustment['nombre'] }}
                              </h6>
                              <p class="text-muted small mb-0">
                                {{ $adjustment['descripcion'] ?? 'No hay descripción disponible para este ajuste razonable.' }}
                              </p>
                            </div>
                          @endforeach
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              @else
                <div class="mb-3">
                  <span class="badge bg-light text-muted">Sin ajustes propuestos</span>
              </div>
              @endif
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
              <small class="text-muted">Resumen de solicitudes y ajustes razonables.</small>
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
  $docentesTotals = collect($careerStats)->pluck('total_docentes')->map(fn ($value) => (int) $value)->values();
@endphp
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
          <h5 class="card-title mb-1">Estadisticas por carrera</h5>
          <small class="text-muted" id="sectionDescription">Distribucion de estudiantes por carrera.</small>
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
                  <small class="text-muted" id="chartSubtitle">Participacion de cada carrera.</small>
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
                  <input type="radio" class="btn-check" name="chartFilter" id="filterDocentes" value="docentes">
                  <label class="btn btn-sm btn-outline-primary" for="filterDocentes">
                    <i class="fas fa-chalkboard-teacher me-1"></i>Docentes
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
              <div id="careerListContainer">
                <div class="career-list" id="careerTableBody">
                  @forelse ($careerStats as $career)
                    <div class="career-card" 
                         data-career-id="{{ $loop->index }}" 
                         data-students="{{ $career['total_students'] }}" 
                         data-adjustments-aprobados="{{ $career['ajustes_aprobados'] ?? 0 }}" 
                         data-adjustments-rechazados="{{ $career['ajustes_rechazados'] ?? 0 }}" 
                         data-adjustments-list="{{ json_encode($career['adjustments_list'] ?? []) }}"
                         data-students-list="{{ json_encode($career['students_list'] ?? []) }}"
                         data-docentes-list="{{ json_encode($career['docentes_list'] ?? []) }}">
                      <div class="career-card__content">
                        <div class="career-card__icon">
                          <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="career-card__info">
                          <h6 class="career-card__title">{{ $career['name'] }}</h6>
                          <div class="career-card__meta">
                            <span class="career-card__badge">
                              <i class="fas fa-clock me-1"></i>{{ $career['jornada'] }}
                            </span>
                          </div>
                        </div>
                        <div class="career-card__value">
                          <div class="value-number career-value">{{ $career['total_students'] }}</div>
                          <small class="value-label" id="valueLabel">Estudiantes</small>
                        </div>
                      </div>
                    </div>
                  @empty
                    <div class="text-center text-muted py-5">
                      <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                      <p>Aun no hay carreras asignadas a tu perfil.</p>
                    </div>
                  @endforelse
                </div>
              </div>
              
              {{-- Modal para mostrar ajustes o estudiantes de una carrera --}}
              <div class="modal fade" id="adjustmentsModal" tabindex="-1" aria-labelledby="adjustmentsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="adjustmentsModalLabel">
                        <i class="fas fa-sliders text-danger me-2"></i>Ajustes Aplicados
                      </h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="adjustmentsModalBody" style="max-height: 60vh; overflow-y: auto;">
                      <p class="text-muted">Cargando ajustes...</p>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                  </div>
                </div>
              </div>

              {{-- Modal para mostrar estudiantes de una carrera --}}
              <div class="modal fade" id="studentsModal" tabindex="-1" aria-labelledby="studentsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="studentsModalLabel">
                        <i class="fas fa-user-graduate text-danger me-2"></i>Estudiantes
                      </h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="studentsModalBody" style="max-height: 60vh; overflow-y: auto;">
                      <p class="text-muted">Cargando estudiantes...</p>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                  </div>
                </div>
              </div>

              {{-- Modal para mostrar docentes de una carrera --}}
              <div class="modal fade" id="docentesModal" tabindex="-1" aria-labelledby="docentesModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="docentesModalLabel">
                        <i class="fas fa-chalkboard-teacher text-primary me-2"></i>Docentes
                      </h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="docentesModalBody" style="max-height: 60vh; overflow-y: auto;">
                      <p class="text-muted">Cargando docentes...</p>
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
  /* Ajustar las tarjetas para que quepan 5 en una fila */
  @media (min-width: 992px) {
    .stats-cards-row {
      display: flex;
      flex-wrap: nowrap;
      justify-content: space-between;
    }
    .stats-card-col {
      flex: 1 1 0;
      min-width: 0;
      padding-left: 0.5rem;
      padding-right: 0.5rem;
    }
    .stats-card-col:first-child {
      padding-left: 0;
    }
    .stats-card-col:last-child {
      padding-right: 0;
    }
  }
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
  .accordion-item {
    border: 1px solid #e5e7eb;
    border-radius: .75rem;
    overflow: hidden;
    background: #fff;
  }
  .accordion-button {
    background-color: #fff;
    color: #1f2937;
    font-weight: 500;
    border: none;
  }
  .accordion-button:not(.collapsed) {
    background-color: #fff7f7;
    color: #b91c1c;
    box-shadow: none;
  }
  .accordion-button:focus {
    border-color: #fecdd3;
    box-shadow: 0 0 0 0.25rem rgba(220, 38, 38, 0.25);
  }
  .accordion-body {
    background-color: #fff;
    border-top: 1px solid #f0f0f5;
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

  /* Modo Oscuro */
  .dark-mode .dashboard-page {
    background: transparent !important;
  }
  .dark-mode .page-header h1 {
    color: #e8e8e8 !important;
  }
  .dark-mode .page-header .text-muted {
    color: #b8b8b8 !important;
  }
  .dark-mode .page-header .text-danger {
    color: #fca5a5 !important;
  }
  .dark-mode .card {
    background-color: #1e293b !important;
    border-color: #2d3748 !important;
    color: #e8e8e8 !important;
  }
  .dark-mode .card-title {
    color: #e8e8e8 !important;
  }
  .dark-mode .text-muted {
    color: #b8b8b8 !important;
  }
  .dark-mode .case-card {
    background-color: #1e293b !important;
    border-color: #2d3748 !important;
    color: #e8e8e8 !important;
  }
  .dark-mode .case-card__header h6 {
    color: #e8e8e8 !important;
  }
  .dark-mode .case-card__header .text-muted {
    color: #b8b8b8 !important;
  }
  .dark-mode .case-card__focus {
    color: #cbd5e1 !important;
  }
  .dark-mode .case-card__meta {
    background-color: #16213e !important;
    border-color: #2d3748 !important;
    color: #e8e8e8 !important;
  }
  .dark-mode .case-card__meta .text-muted {
    color: #94a3b8 !important;
  }
  .dark-mode .case-card__meta .fw-semibold {
    color: #e8e8e8 !important;
  }
  .dark-mode .accordion-item {
    background-color: #16213e !important;
    border-color: #2d3748 !important;
  }
  .dark-mode .accordion-button {
    background-color: #16213e !important;
    color: #e8e8e8 !important;
    border-color: #2d3748 !important;
  }
  .dark-mode .accordion-button:not(.collapsed) {
    background-color: #1e3a5f !important;
    color: #e8e8e8 !important;
  }
  .dark-mode .accordion-button:focus {
    border-color: #2d3748 !important;
    box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25) !important;
  }
  .dark-mode .accordion-button strong {
    color: #e8e8e8 !important;
  }
  .dark-mode .accordion-body {
    background-color: #16213e !important;
    border-top-color: #2d3748 !important;
    color: #e8e8e8 !important;
  }
  .dark-mode .accordion-body .bg-light {
    background-color: #0f172a !important;
    border-color: #1e293b !important;
    color: #e8e8e8 !important;
  }
  .dark-mode .accordion-body .border {
    border-color: #1e293b !important;
  }
  .dark-mode .accordion-body .fw-semibold {
    color: #e8e8e8 !important;
  }
  .dark-mode .accordion-body .text-muted {
    color: #cbd5e1 !important;
  }
  .dark-mode .accordion-body .text-success {
    color: #86efac !important;
  }
  .dark-mode .pipeline-stage {
    background-color: #1e293b !important;
    border-color: #2d3748 !important;
    color: #e8e8e8 !important;
  }
  .dark-mode .pipeline-stage .fw-semibold {
    color: #e8e8e8 !important;
  }
  .dark-mode .pipeline-stage .text-muted {
    color: #b8b8b8 !important;
  }
  .dark-mode .pipeline-stage__count {
    background-color: #3d1f1f !important;
    color: #fca5a5 !important;
  }
  .dark-mode .pastel-card {
    background: #1e293b !important;
  }
  .dark-mode .table-dark-mode {
    color: #e8e8e8 !important;
  }
  .dark-mode .table-dark-mode thead {
    background-color: #16213e !important;
    color: #e8e8e8 !important;
  }
  .dark-mode .table-dark-mode tbody tr {
    background-color: #1e293b !important;
    color: #e8e8e8 !important;
    border-color: #2d3748 !important;
  }
  .dark-mode .table-dark-mode tbody tr:hover {
    background-color: #16213e !important;
  }
  .dark-mode .table-dark-mode .fw-semibold {
    color: #e8e8e8 !important;
  }
  .dark-mode .table-dark-mode .text-muted {
    color: #b8b8b8 !important;
  }
  .dark-mode .table-dark-mode .text-success {
    color: #86efac !important;
  }
  .dark-mode .table-dark-mode .text-danger {
    color: #f87171 !important;
  }
  .dark-mode .badge {
    color: #fff !important;
  }
  .dark-mode .btn-outline-danger {
    border-color: #dc2626 !important;
    color: #dc2626 !important;
  }
  .dark-mode .btn-outline-danger:hover {
    background-color: #dc2626 !important;
    border-color: #dc2626 !important;
    color: #fff !important;
  }
  .dark-mode .btn-outline-primary {
    border-color: #3b82f6 !important;
    color: #3b82f6 !important;
  }
  .dark-mode .btn-outline-primary:hover {
    background-color: #3b82f6 !important;
    border-color: #3b82f6 !important;
    color: #fff !important;
  }
  .dark-mode .bg-light {
    background-color: #16213e !important;
    color: #e8e8e8 !important;
  }
  .dark-mode .bg-danger {
    background-color: #dc2626 !important;
  }
  .dark-mode .bg-success {
    background-color: #22c55e !important;
  }
  .dark-mode .action-button--outline-danger {
    background-color: #1e293b !important;
    border-color: #dc2626 !important;
    color: #dc2626 !important;
  }
  .dark-mode .action-button--outline-danger:hover {
    background-color: #dc2626 !important;
    color: #fff !important;
  }
  .dark-mode .action-button--danger {
    background-color: #dc2626 !important;
    color: #fff !important;
  }
  .dark-mode .action-button--danger:hover {
    background-color: #b91c1c !important;
  }
  .dark-mode .btn-check:checked + .btn-outline-danger,
  .dark-mode .btn-check:checked + .btn-outline-primary {
    background-color: inherit;
    border-color: inherit;
    color: inherit;
  }
  .dark-mode small {
    color: #b8b8b8 !important;
  }
  .dark-mode strong {
    color: #e8e8e8 !important;
  }
  .dark-mode .modal-content {
    background-color: #1e293b !important;
    border-color: #2d3748 !important;
    color: #e8e8e8 !important;
  }
  .dark-mode .modal-header {
    background-color: #16213e !important;
    border-bottom-color: #2d3748 !important;
    color: #e8e8e8 !important;
  }
  .dark-mode .modal-title {
    color: #e8e8e8 !important;
  }
  .dark-mode .modal-body {
    background-color: #1e293b !important;
    color: #e8e8e8 !important;
  }
  .dark-mode .modal-footer {
    background-color: #1e293b !important;
    border-top-color: #2d3748 !important;
  }
  .dark-mode .list-group-item {
    background-color: #16213e !important;
    border-color: #2d3748 !important;
    color: #e8e8e8 !important;
  }
  .dark-mode .list-group-item .fw-semibold {
    color: #e8e8e8 !important;
  }
  .dark-mode .list-group-item .text-muted {
    color: #b8b8b8 !important;
  }
  .dark-mode .btn-secondary {
    background-color: #475569 !important;
    border-color: #475569 !important;
    color: #fff !important;
  }
  .dark-mode .btn-secondary:hover {
    background-color: #334155 !important;
    border-color: #334155 !important;
  }

  /* Estilos para las tarjetas de carrera */
  .career-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
  }
  .career-card {
    border: 1px solid #e5e7eb;
    border-radius: 0.875rem;
    padding: 1rem;
    background: #fff;
    transition: all 0.2s ease;
    cursor: pointer;
  }
  .career-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-color: #dc2626;
  }
  .career-card__content {
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  .career-card__icon {
    width: 48px;
    height: 48px;
    border-radius: 0.75rem;
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .career-card__icon i {
    color: #dc2626;
    font-size: 1.25rem;
  }
  .career-card__info {
    flex: 1;
    min-width: 0;
  }
  .career-card__title {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.5rem;
    line-height: 1.4;
  }
  .career-card__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
  }
  .career-card__badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    background-color: #f3f4f6;
    border-radius: 0.5rem;
    font-size: 0.75rem;
    color: #6b7280;
    font-weight: 500;
  }
  .career-card__value {
    text-align: center;
    flex-shrink: 0;
    padding-left: 1rem;
    border-left: 1px solid #e5e7eb;
    min-width: 80px;
  }
  .value-number {
    font-size: 1.75rem;
    font-weight: 700;
    color: #dc2626;
    line-height: 1;
    margin-bottom: 0.25rem;
  }
  .value-label {
    font-size: 0.75rem;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 500;
  }
  .value-number .text-success {
    color: #22c55e !important;
    font-weight: 700;
  }
  .value-number .text-danger {
    color: #ef4444 !important;
    font-weight: 700;
  }

  /* Modo Oscuro para tarjetas de carrera */
  .dark-mode .career-card {
    background-color: #1e293b !important;
    border-color: #2d3748 !important;
  }
  .dark-mode .career-card:hover {
    border-color: #dc2626 !important;
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2) !important;
  }
  .dark-mode .career-card__icon {
    background: linear-gradient(135deg, #3d1f1f 0%, #4a2525 100%) !important;
  }
  .dark-mode .career-card__icon i {
    color: #fca5a5 !important;
  }
  .dark-mode .career-card__title {
    color: #e8e8e8 !important;
  }
  .dark-mode .career-card__badge {
    background-color: #16213e !important;
    color: #94a3b8 !important;
    border: 1px solid #2d3748 !important;
  }
  .dark-mode .career-card__value {
    border-left-color: #2d3748 !important;
  }
  .dark-mode .value-number {
    color: #fca5a5 !important;
  }
  .dark-mode .value-label {
    color: #94a3b8 !important;
  }
  .dark-mode .value-number .text-success {
    color: #86efac !important;
  }
  .dark-mode .value-number .text-danger {
    color: #f87171 !important;
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
    const docentesData = @json($docentesTotals);
    const careerStats = @json($careerStats);
    
    // Preparar datos totales de ajustes aprobados y rechazados (suma de todas las carreras)
    const totalAjustesAprobados = careerStats.reduce((sum, career) => sum + (career.ajustes_aprobados || 0), 0);
    const totalAjustesRechazados = careerStats.reduce((sum, career) => sum + (career.ajustes_rechazados || 0), 0);
    
    // Colores azules para docentes
    const docentesColors = ['#2563eb', '#3b82f6', '#60a5fa', '#93c5fd', '#dbeafe', '#1e40af', '#1e3a8a', '#1e3a5f', '#0ea5e9', '#0284c7'];
    const docentesBackground = labels.map((_, idx) => docentesColors[idx % docentesColors.length]);
    
    // Etiquetas y datos para ajustes: solo dos segmentos (Aprobados y Rechazados)
    const adjustmentLabels = [];
    const adjustmentData = [];
    const adjustmentColors = [];
    
    if (totalAjustesAprobados > 0) {
      adjustmentLabels.push('Ajustes Aprobados');
      adjustmentData.push(totalAjustesAprobados);
      adjustmentColors.push('#22c55e'); // Verde para aprobados
    }
    
    if (totalAjustesRechazados > 0) {
      adjustmentLabels.push('Ajustes Rechazados');
      adjustmentData.push(totalAjustesRechazados);
      adjustmentColors.push('#dc2626'); // Rojo para rechazados
    }
    
    // Si no hay ajustes, mostrar mensaje
    if (adjustmentLabels.length === 0) {
      adjustmentLabels.push('Sin ajustes');
      adjustmentData.push(1);
      adjustmentColors.push('#9ca3af'); // Gris
    }

    const palette = ['#dc2626','#f97316','#f59e0b','#22c55e','#0ea5e9','#6366f1','#a855f7','#ec4899','#14b8a6','#8b5cf6'];
    const backgroundColor = labels.map((_, idx) => palette[idx % palette.length]);

    let chart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          data: studentData,
          backgroundColor: backgroundColor,
          borderWidth: 1,
          hoverOffset: 6,
          cutout: '55%'
        }]
      },
      options: {
        plugins: {
          legend: { 
            position: 'bottom',
            labels: {
              color: document.body.classList.contains('dark-mode') ? '#e8e8e8' : '#1f2937'
            }
          },
          tooltip: { 
            backgroundColor: document.body.classList.contains('dark-mode') ? 'rgba(30, 41, 59, 0.95)' : 'rgba(255, 255, 255, 0.95)',
            titleColor: document.body.classList.contains('dark-mode') ? '#e8e8e8' : '#1f1f2d',
            bodyColor: document.body.classList.contains('dark-mode') ? '#e8e8e8' : '#1f1f2d',
            borderColor: document.body.classList.contains('dark-mode') ? '#2d3748' : '#e5e7eb',
            borderWidth: 1,
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
            const activeTab = document.querySelector('input[name="chartFilter"]:checked')?.value;
            
            if (activeTab === 'students' && career && career.students_list && career.students_list.length > 0) {
              showStudentsModal(career.name, career.students_list);
            } else if (activeTab === 'adjustments' && career && career.adjustments_list && career.adjustments_list.length > 0) {
              showAdjustmentsModal(career.name, career.adjustments_list);
            } else if (activeTab === 'docentes' && career && career.docentes_list && career.docentes_list.length > 0) {
              showDocentesModal(career.name, career.docentes_list);
            }
          }
        }
      }
    });

    // Función para actualizar colores del gráfico según modo oscuro
    function actualizarColoresGrafico() {
      const isDarkMode = document.body.classList.contains('dark-mode');
      if (chart) {
        chart.options.plugins.legend.labels.color = isDarkMode ? '#e8e8e8' : '#1f2937';
        chart.options.plugins.tooltip.backgroundColor = isDarkMode ? 'rgba(30, 41, 59, 0.95)' : 'rgba(255, 255, 255, 0.95)';
        chart.options.plugins.tooltip.titleColor = isDarkMode ? '#e8e8e8' : '#1f1f2d';
        chart.options.plugins.tooltip.bodyColor = isDarkMode ? '#e8e8e8' : '#1f1f2d';
        chart.options.plugins.tooltip.borderColor = isDarkMode ? '#2d3748' : '#e5e7eb';
        chart.update('none');
      }
    }

    // Observar cambios en el modo oscuro
    const observer = new MutationObserver(function(mutations) {
      mutations.forEach(function(mutation) {
        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
          actualizarColoresGrafico();
        }
      });
    });
    
    observer.observe(document.body, {
      attributes: true,
      attributeFilter: ['class']
    });

    // Manejar cambio de filtro
    const filterInputs = document.querySelectorAll('input[name="chartFilter"]');
    const tableDescription = document.getElementById('tableDescription');
    const careerCards = document.querySelectorAll('.career-card');

    filterInputs.forEach(input => {
      input.addEventListener('change', function() {
        const activeTab = this.value;
        const isStudents = activeTab === 'students';
        const isDocentes = activeTab === 'docentes';
        
        // Actualizar gráfico
        if (isStudents) {
          chart.data.labels = labels;
          chart.data.datasets[0].data = studentData;
          chart.data.datasets[0].backgroundColor = backgroundColor;
          chart.data.datasets[0].label = 'Estudiantes';
        } else if (isDocentes) {
          chart.data.labels = labels;
          chart.data.datasets[0].data = docentesData;
          chart.data.datasets[0].backgroundColor = docentesBackground;
          chart.data.datasets[0].label = 'Docentes';
        } else {
          chart.data.labels = adjustmentLabels.length > 0 ? adjustmentLabels : ['Sin ajustes'];
          chart.data.datasets[0].data = adjustmentData.length > 0 ? adjustmentData : [0];
          chart.data.datasets[0].backgroundColor = adjustmentColors.length > 0 ? adjustmentColors : backgroundColor;
          chart.data.datasets[0].label = 'Ajustes';
        }
        chart.update();

        // Actualizar subtítulo de la sección
        const sectionDescription = document.getElementById('sectionDescription');
        if (sectionDescription) {
          if (isStudents) {
            sectionDescription.textContent = 'Distribucion de estudiantes por carrera.';
          } else if (isDocentes) {
            sectionDescription.textContent = 'Distribucion de docentes por carrera.';
          } else {
            sectionDescription.textContent = 'Distribucion de ajustes aprobados y rechazados por carrera.';
          }
        }
        
        // Actualizar subtítulo de la gráfica
        const chartSubtitle = document.getElementById('chartSubtitle');
        if (chartSubtitle) {
          if (isStudents) {
            chartSubtitle.textContent = 'Participacion de cada carrera.';
          } else if (isDocentes) {
            chartSubtitle.textContent = 'Participacion de docentes por carrera.';
          } else {
            chartSubtitle.textContent = 'Ajustes aprobados y rechazados por carrera.';
          }
        }
        
        // Actualizar descripción
        if (isStudents) {
          tableDescription.textContent = 'Cantidad de estudiantes por carrera.';
        } else if (isDocentes) {
          tableDescription.textContent = 'Cantidad de docentes por carrera.';
        } else {
          tableDescription.textContent = 'Ajustes aprobados y rechazados por carrera.';
        }
        
        // Actualizar etiqueta de valor y valores en las tarjetas
        const valueLabels = document.querySelectorAll('.value-label');
        
        careerCards.forEach((card, index) => {
          const valueElement = card.querySelector('.career-value');
          const labelElement = card.querySelector('.value-label');
          
          if (valueElement && labelElement) {
            if (isStudents) {
              labelElement.textContent = 'Estudiantes';
              valueElement.textContent = card.getAttribute('data-students') || '0';
            } else if (isDocentes) {
              labelElement.textContent = 'Docentes';
              const career = careerStats[parseInt(card.getAttribute('data-career-id'))];
              valueElement.textContent = career?.total_docentes || '0';
            } else {
              labelElement.textContent = 'Ajustes';
              const aprobados = card.getAttribute('data-adjustments-aprobados') || '0';
              const rechazados = card.getAttribute('data-adjustments-rechazados') || '0';
              valueElement.innerHTML = `<span class="text-success">${aprobados}</span> / <span class="text-danger">${rechazados}</span>`;
            }
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
        let html = '<div class="list-group list-group-flush">';
        adjustments.forEach(ajuste => {
          const estado = ajuste.estado || 'Pendiente';
          const badgeClass = estado === 'Aprobado' ? 'bg-success' : estado === 'Rechazado' ? 'bg-danger' : 'bg-warning';
          const iconClass = estado === 'Aprobado' ? 'fa-check-circle text-success' : estado === 'Rechazado' ? 'fa-times-circle text-danger' : 'fa-clock text-warning';
          const fecha = ajuste.fecha || 'Sin fecha';
          
          html += `
            <div class="list-group-item mb-2 border rounded px-3 py-2">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="flex-grow-1">
                  <h6 class="mb-1 fw-semibold">
                    <i class="fas ${iconClass} me-2"></i>${ajuste.nombre || 'Ajuste sin título'}
                  </h6>
                  <small class="text-muted">
                    <i class="fas fa-calendar-alt me-1"></i>Fecha: ${fecha}
                  </small>
                </div>
                <span class="badge ${badgeClass} text-white ms-2">${estado}</span>
              </div>
              <p class="text-muted small mb-0 mt-2">${ajuste.descripcion || 'Sin descripción adicional'}</p>
            </div>
          `;
        });
        html += '</div>';
        modalBody.innerHTML = html;
      }
      
      const modal = new bootstrap.Modal(document.getElementById('adjustmentsModal'));
      modal.show();
    }

    // Función para mostrar modal de estudiantes
    function showStudentsModal(careerName, students) {
      const modalBody = document.getElementById('studentsModalBody');
      const modalLabel = document.getElementById('studentsModalLabel');
      
      modalLabel.innerHTML = `<i class="fas fa-user-graduate text-danger me-2"></i>Estudiantes - ${careerName}`;
      
      if (students.length === 0) {
        modalBody.innerHTML = '<p class="text-muted text-center">No hay estudiantes registrados en esta carrera.</p>';
      } else {
        let html = '<div class="list-group list-group-flush">';
        students.forEach(student => {
          html += `
            <div class="list-group-item mb-2 border rounded px-3 py-3">
              <div class="d-flex align-items-center mb-2">
                <div class="me-3">
                  <div class="bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fas fa-user text-danger"></i>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <h6 class="mb-1 fw-semibold">${student.nombre || 'Estudiante sin nombre'}</h6>
                  <div class="d-flex flex-wrap gap-3 small text-muted">
                    <span><i class="fas fa-id-card me-1"></i>RUT: ${student.rut || 'Sin RUT'}</span>
                    <span><i class="fas fa-envelope me-1"></i>${student.email || 'Sin email'}</span>
                    ${student.telefono ? `<span><i class="fas fa-phone me-1"></i>${student.telefono}</span>` : ''}
                  </div>
                </div>
              </div>
            </div>
          `;
        });
        html += '</div>';
        modalBody.innerHTML = html;
      }
      
      const modal = new bootstrap.Modal(document.getElementById('studentsModal'));
      modal.show();
    }

    // Función para mostrar modal de docentes
    function showDocentesModal(careerName, docentes) {
      const modalBody = document.getElementById('docentesModalBody');
      const modalLabel = document.getElementById('docentesModalLabel');
      
      modalLabel.innerHTML = `<i class="fas fa-chalkboard-teacher text-primary me-2"></i>Docentes - ${careerName}`;
      
      if (docentes.length === 0) {
        modalBody.innerHTML = '<p class="text-muted text-center">No hay docentes registrados en esta carrera.</p>';
      } else {
        let html = '<div class="list-group list-group-flush">';
        docentes.forEach(docente => {
          html += `
            <div class="list-group-item mb-2 border rounded px-3 py-3">
              <div class="d-flex align-items-center mb-2">
                <div class="me-3">
                  <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fas fa-chalkboard-teacher text-primary"></i>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <h6 class="mb-1 fw-semibold">${docente.nombre || 'Docente sin nombre'}</h6>
                  <div class="d-flex flex-wrap gap-3 small text-muted mb-2">
                    <span><i class="fas fa-id-card me-1"></i>RUT: ${docente.rut || 'Sin RUT'}</span>
                    <span><i class="fas fa-envelope me-1"></i>${docente.email || 'Sin email'}</span>
                  </div>
                  <div class="mt-2">
                    <span class="badge" style="background-color: #3b82f6; color: #ffffff;">
                      <i class="fas fa-user-graduate me-1"></i>${docente.cantidad_estudiantes || 0} estudiante${(docente.cantidad_estudiantes || 0) !== 1 ? 's' : ''}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          `;
        });
        html += '</div>';
        modalBody.innerHTML = html;
      }
      
      const modal = new bootstrap.Modal(document.getElementById('docentesModal'));
      modal.show();
    }

    // Hacer las tarjetas de carrera clickeables para mostrar estudiantes, ajustes o docentes según la pestaña activa
    careerCards.forEach(card => {
      card.style.cursor = 'pointer';
      card.addEventListener('click', function() {
        const activeTab = document.querySelector('input[name="chartFilter"]:checked')?.value;
        const careerName = this.querySelector('.career-card__title').textContent;
        const careerIndex = parseInt(this.getAttribute('data-career-id'));
        const career = careerStats[careerIndex];
        
        if (activeTab === 'students') {
          const studentsList = JSON.parse(this.getAttribute('data-students-list') || '[]');
          if (studentsList.length > 0) {
            showStudentsModal(careerName, studentsList);
          }
        } else if (activeTab === 'docentes') {
          if (career && career.docentes_list && career.docentes_list.length > 0) {
            showDocentesModal(careerName, career.docentes_list);
          }
        } else {
          const adjustmentsList = JSON.parse(this.getAttribute('data-adjustments-list') || '[]');
          if (adjustmentsList.length > 0) {
            showAdjustmentsModal(careerName, adjustmentsList);
          }
        }
      });
    });
  });
</script>
@endpush
@endsection
