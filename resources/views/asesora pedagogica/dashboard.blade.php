@extends('layouts.dashboard_asesorapedagogica.app')

@php use Illuminate\Support\Str; @endphp

@section('title', 'Dashboard Asesora Pedagogica')

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
    <h1 class="h4 mb-1">Dashboard Asesora Pedagogica</h1>
    <p class="text-muted mb-0">Resumen de casos, autorizaciones y accesos rapidos para la gestion institucional.</p>
  </div>

  <div class="row g-3 mb-4">
    @foreach ($metrics as $metric)
      <div class="col-12 col-md-6 col-xl-3">
        <div class="stats-card" style="background: #dc2626;">
          <div class="stats-card__value">{{ $metric['value'] }}</div>
          <div class="stats-card__icon"><i class="fas {{ $metric['icon'] ?? 'fa-circle' }}"></i></div>
          <p class="stats-card__title">{{ $metric['label'] }}</p>
          <small class="stats-card__sub">{{ $metric['helper'] }}</small>
        </div>
      </div>
    @endforeach
  </div>

  {{-- Gráficos de Estadísticas --}}
  <div class="row g-4 mb-4">
    {{-- Gráfico de Dona: Calidad de las Propuestas --}}
    <div class="col-12 col-md-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-title mb-3">Calidad de las Propuestas</h5>
          <p class="text-muted small mb-3">Tasa de devolución</p>
          
          {{-- Pestañas de filtro por carrera --}}
          <ul class="nav nav-tabs mb-3" id="carreraTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="todas-tab" data-bs-toggle="tab" type="button" data-carrera-id="0">
                Todas
              </button>
            </li>
            @foreach($carreras as $carrera)
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="carrera-{{ $carrera->id }}-tab" data-bs-toggle="tab" type="button" data-carrera-id="{{ $carrera->id }}">
                  {{ Str::limit($carrera->nombre, 15) }}
                </button>
              </li>
            @endforeach
          </ul>

          <div id="calidadChartContainer">
            <div id="calidadChartWrapper" class="d-flex justify-content-center align-items-center" style="height: 300px;">
              <canvas id="calidadPropuestasChart" style="max-height: 300px;"></canvas>
            </div>
            <div id="calidadNoDataMessage" class="d-none text-center py-5" style="height: 300px; display: flex; align-items: center; justify-content: center;">
              <div>
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">No hay casos para esta carrera</p>
              </div>
            </div>
            <div class="mt-3 text-center" id="calidadLegend">
              <div class="d-flex justify-content-center gap-4">
                <div>
                  <span class="badge bg-success me-2">●</span>
                  <small class="text-muted">Aprobación Directa: <span id="aprobaciones-porcentaje">{{ $calidadPropuestas['porcentaje_aprobaciones'] }}%</span></small>
                </div>
                <div>
                  <span class="badge bg-danger me-2">●</span>
                  <small class="text-muted">Devoluciones: <span id="devoluciones-porcentaje">{{ $calidadPropuestas['porcentaje_devoluciones'] }}%</span></small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Gráfico de Línea: Ritmo de Trabajo --}}
    <div class="col-12 col-md-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-title mb-3">Ritmo de Trabajo</h5>
          <p class="text-muted small mb-3">Evolución mensual</p>

          {{-- Pestañas de filtro por carrera --}}
          <ul class="nav nav-tabs mb-3" id="ritmoCarreraTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="ritmo-todas-tab" data-bs-toggle="tab" type="button" data-carrera-id="0">
                Todas
              </button>
            </li>
            @foreach($carreras as $carrera)
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="ritmo-carrera-{{ $carrera->id }}-tab" data-bs-toggle="tab" type="button" data-carrera-id="{{ $carrera->id }}">
                  {{ Str::limit($carrera->nombre, 15) }}
                </button>
              </li>
            @endforeach
          </ul>

          <div id="ritmoChartContainer">
            <div id="ritmoChartWrapper" class="d-flex justify-content-center align-items-center" style="height: 300px;">
              <canvas id="ritmoTrabajoChart" style="max-height: 300px;"></canvas>
            </div>
            <div id="ritmoNoDataMessage" class="d-none text-center py-5" style="height: 300px; display: flex; align-items: center; justify-content: center;">
              <div>
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">No hay casos para esta carrera</p>
              </div>
            </div>
          </div>
          <div class="mt-3 text-center">
            <div class="d-flex justify-content-center gap-4">
              <div>
                <span class="badge bg-primary me-2">─</span>
                <small class="text-muted">Recibidas</small>
              </div>
              <div>
                <span class="badge bg-success me-2">─</span>
                <small class="text-muted">Procesadas</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  {{-- Alertas de Prioridad --}}
  <div class="row g-4 mb-4">
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h5 class="card-title mb-3">Alertas de Prioridad</h5>
          <div class="row g-3">
            @if($alertas['casos_estancados'] > 0)
              <div class="col-12 col-md-6">
                <div class="alert alert-danger border-start border-danger border-4 d-flex align-items-center mb-0" style="background-color: #fff5f5;">
                  <div class="me-3" style="font-size: 1.5rem;">
                    <i class="fas fa-exclamation-triangle"></i>
                  </div>
                  <div>
                    <strong>{{ $alertas['casos_estancados'] }} Casos esperando hace más de 5 días</strong>
                    <div class="small mt-1">Revisa estos casos urgentemente para mantener el flujo de trabajo.</div>
                  </div>
                </div>
              </div>
            @endif
            @if($alertas['casos_por_vencer'] > 0)
              <div class="col-12 col-md-6">
                <div class="alert alert-warning border-start border-warning border-4 d-flex align-items-center mb-0" style="background-color: #fffbf0;">
                  <div class="me-3" style="font-size: 1.5rem;">
                    <i class="fas fa-clock"></i>
                  </div>
                  <div>
                    <strong>{{ $alertas['casos_por_vencer'] }} Casos por vencer esta semana</strong>
                    <div class="small mt-1">Prioriza estos casos para evitar retrasos.</div>
                  </div>
                </div>
              </div>
            @endif
            @if($alertas['casos_estancados'] == 0 && $alertas['casos_por_vencer'] == 0)
              <div class="col-12">
                <div class="alert alert-success border-start border-success border-4 d-flex align-items-center mb-0" style="background-color: #f0fdf4;">
                  <div class="me-3" style="font-size: 1.5rem;">
                    <i class="fas fa-check-circle"></i>
                  </div>
                  <div>
                    <strong>No hay alertas pendientes</strong>
                    <div class="small mt-1">Todos los casos están siendo gestionados correctamente.</div>
                  </div>
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body d-flex flex-wrap justify-content-between align-items-center">
      <div>
        <h5 class="mb-1">Necesitas registrar un nuevo caso?</h5>
        <p class="text-muted mb-0">Centraliza desde aqui la creacion de solicitudes y su seguimiento.</p>
      </div>
      <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalRegistrarSolicitud">
        <i class="fas fa-plus me-2"></i>Registrar solicitud
      </button>
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
            <a href="{{ route('asesora-pedagogica.casos.index') }}" class="btn btn-sm btn-outline-danger">Ver todos</a>
          </div>
          @forelse ($casesForReview as $case)
            <div class="case-item">
              <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <div>
                    <strong>{{ $case['student'] }}</strong>
                    <p class="text-muted mb-1 small">{{ $case['program'] }}</p>
                  </div>
                  <div class="text-end">
                    <span class="badge status-badge">{{ $case['status'] }}</span>
                  </div>
                </div>
                @if(!empty($case['ajustes_razonables']))
                  <div class="mb-2">
                    @foreach($case['ajustes_razonables'] as $ajuste)
                      <div class="border rounded p-2 mb-2 bg-light">
                        <div class="flex-grow-1">
                          <strong class="small">
                            <i class="fas fa-check-circle text-success me-1"></i>{{ $ajuste['nombre'] }}
                          </strong>
                          <div class="mt-1">
                            <small class="text-muted d-block">
                              {{ $ajuste['descripcion'] ?? 'sin desc' }}
                            </small>
                            <small class="text-muted">
                              <i class="fas fa-calendar me-1"></i>Fecha de solicitud: {{ $ajuste['fecha_solicitud'] }}
                            </small>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                @else
                  <p class="text-muted small mb-0">{{ $case['proposed_adjustment'] }}</p>
                @endif
              </div>
              <div class="case-item-actions">
                <p class="text-muted small mb-2">Recibido: {{ $case['received_at'] }}</p>
                <div class="d-flex flex-column gap-2">
                  <a href="{{ $case['detail_url'] ?? route('asesora-pedagogica.casos.show', $case['case_id']) }}" class="btn btn-sm btn-outline-danger w-100">
                    <i class="fas fa-eye me-1"></i>Ver detalles
                  </a>
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
    align-items: flex-start;
    gap: 1.5rem;
    background: #fff;
    flex-wrap: nowrap;
  }
  .case-item > .flex-grow-1 {
    flex: 1 1 0;
    min-width: 0;
  }
  .case-item-actions {
    flex-shrink: 0;
    flex-grow: 0;
    text-align: right !important;
    align-self: flex-start;
    margin-left: auto;
  }
  .case-item-actions p {
    text-align: right !important;
    margin-bottom: 0.5rem;
  }
  .case-item-actions .d-flex {
    justify-content: flex-end !important;
  }
  @media (max-width: 992px) {
    .case-item {
      flex-wrap: wrap;
    }
    .case-item-actions {
      width: 100%;
      margin-left: 0;
      margin-top: 1rem;
      text-align: left !important;
    }
    .case-item-actions p {
      text-align: left !important;
    }
    .case-item-actions .d-flex {
      justify-content: flex-start !important;
    }
  }
  .status-badge {
    border-radius: 999px;
    padding: .35rem .85rem;
    font-size: .75rem;
    background: #eef2ff;
    color: #4338ca;
  }
  @media (max-width: 768px) {
    .stats-card {
      min-height: 160px;
    }
  }

  /* Modo Oscuro */
  .dark-mode .dashboard-page {
    background: #1a1a2e !important;
    color: #e8e8e8 !important;
  }
  .dark-mode .page-header h1 {
    color: #e8e8e8 !important;
  }
  .dark-mode .page-header .text-muted {
    color: #b8b8b8 !important;
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
  .dark-mode .nav-tabs {
    border-bottom-color: #2d3748 !important;
  }
  .dark-mode .nav-tabs .nav-link {
    color: #b8b8b8 !important;
    background-color: transparent !important;
    border-color: transparent !important;
  }
  .dark-mode .nav-tabs .nav-link:hover {
    color: #e8e8e8 !important;
    border-color: #2d3748 #2d3748 transparent !important;
  }
  .dark-mode .nav-tabs .nav-link.active {
    color: #1f2937 !important;
    background-color: #e6e6e6 !important;
    border-color: #2d3748 #2d3748 #e6e6e6 !important;
    font-weight: 600 !important;
  }
  .dark-mode .badge {
    color: #fff !important;
  }
  .dark-mode .bg-light {
    background-color: #16213e !important;
    border-color: #2d3748 !important;
    color: #e8e8e8 !important;
  }
  .dark-mode .border {
    border-color: #2d3748 !important;
  }
  .dark-mode .case-item,
  .dark-mode .timeline-item {
    background-color: #1e293b !important;
    border-color: #2d3748 !important;
    color: #e8e8e8 !important;
  }
  .dark-mode .case-item .text-muted,
  .dark-mode .timeline-item .text-muted {
    color: #b8b8b8 !important;
  }
  .dark-mode .status-badge {
    background-color: #2d3748 !important;
    color: #a5b4fc !important;
  }
  .dark-mode .alert {
    background-color: #1e293b !important;
    border-color: #2d3748 !important;
    color: #e8e8e8 !important;
  }
  .dark-mode .alert-danger {
    background-color: #3d1f1f !important;
    border-color: #7f1d1d !important;
    color: #fca5a5 !important;
  }
  .dark-mode .alert-warning {
    background-color: #3d3521 !important;
    border-color: #854d0e !important;
    color: #fde047 !important;
  }
  .dark-mode .alert-success {
    background-color: #1e3a2a !important;
    border-color: #166534 !important;
    color: #86efac !important;
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
  .dark-mode .bg-success-subtle {
    background-color: #1e3a2a !important;
  }
  .dark-mode .text-success {
    color: #86efac !important;
  }
  .dark-mode .bg-primary {
    background-color: #3b82f6 !important;
  }
  .dark-mode .bg-success {
    background-color: #22c55e !important;
  }
  .dark-mode .bg-danger {
    background-color: #ef4444 !important;
  }
  .dark-mode .bg-warning {
    background-color: #eab308 !important;
    color: #1f2937 !important;
  }
  .dark-mode .btn-danger {
    background-color: #dc2626 !important;
    border-color: #dc2626 !important;
    color: #fff !important;
  }
  .dark-mode .btn-danger:hover {
    background-color: #b91c1c !important;
    border-color: #b91c1c !important;
  }
  .dark-mode .btn-primary {
    background-color: #3b82f6 !important;
    border-color: #3b82f6 !important;
    color: #fff !important;
  }
  .dark-mode .btn-primary:hover {
    background-color: #2563eb !important;
    border-color: #2563eb !important;
  }
  .dark-mode .small {
    color: #b8b8b8 !important;
  }
  .dark-mode strong {
    color: #e8e8e8 !important;
  }
</style>
@endpush

<!-- Modal Registrar Solicitud -->
<div class="modal fade" id="modalRegistrarSolicitud" tabindex="-1" aria-labelledby="modalRegistrarSolicitudLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="modalRegistrarSolicitudLabel">
          <i class="fas fa-plus-circle me-2"></i>Registrar Nueva Solicitud
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('asesora-pedagogica.solicitud.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          @if($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <div class="mb-3">
            <label for="estudiante_id" class="form-label">
              Estudiante <span class="text-danger">*</span>
            </label>
            <select 
              name="estudiante_id" 
              id="estudiante_id" 
              class="form-select @error('estudiante_id') is-invalid @enderror" 
              required
            >
              <option value="">Selecciona un estudiante</option>
              @foreach($estudiantes ?? [] as $estudiante)
                <option value="{{ $estudiante->id }}" @selected(old('estudiante_id') == $estudiante->id)>
                  {{ $estudiante->nombre }} {{ $estudiante->apellido }}
                  @if($estudiante->rut)
                    ({{ $estudiante->rut }})
                  @endif
                  @if($estudiante->carrera)
                    - {{ $estudiante->carrera->nombre }}
                  @endif
                </option>
              @endforeach
            </select>
            @error('estudiante_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="titulo" class="form-label">
              Título <span class="text-danger">*</span>
            </label>
            <input 
              type="text" 
              name="titulo" 
              id="titulo" 
              class="form-control @error('titulo') is-invalid @enderror" 
              placeholder="Ingresa un título para la solicitud" 
              value="{{ old('titulo') }}"
              required
            >
            @error('titulo')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="descripcion" class="form-label">
              Descripción para la Entrevista <span class="text-danger">*</span>
            </label>
            <textarea 
              name="descripcion" 
              id="descripcion" 
              rows="5" 
              class="form-control @error('descripcion') is-invalid @enderror" 
              placeholder="Describe el motivo de la solicitud de entrevista..." 
              required
            >{{ old('descripcion') }}</textarea>
            @error('descripcion')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Mínimo 10 caracteres.</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i>Cancelar
          </button>
          <button type="submit" class="btn btn-danger">
            <i class="fas fa-save me-2"></i>Registrar Solicitud
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Datos de calidad por carrera desde PHP
  const calidadGlobal = @json($calidadPropuestas);
  const calidadPorCarrera = @json($calidadPropuestasPorCarrera);
  
  let calidadChart = null;

  // Función para actualizar el gráfico según la carrera seleccionada
  function actualizarGraficoCalidad(carreraId) {
    let datos;
    const chartWrapper = document.getElementById('calidadChartWrapper');
    const noDataMessage = document.getElementById('calidadNoDataMessage');
    const calidadLegend = document.getElementById('calidadLegend');
    
    if (carreraId === 0 || carreraId === '0') {
      // Mostrar datos globales
      datos = calidadGlobal;
    } else {
      // Mostrar datos de la carrera específica
      datos = calidadPorCarrera[carreraId] || {
        aprobaciones_directas: 0,
        devoluciones: 0,
        total: 0,
        porcentaje_aprobaciones: 0,
        porcentaje_devoluciones: 0
      };
    }

    // Verificar si hay datos
    let tieneDatos = false;
    if (carreraId === 0 || carreraId === '0') {
      // Para la vista "Todas", verificar si hay datos sumando aprobaciones y devoluciones
      tieneDatos = (datos.aprobaciones_directas + datos.devoluciones) > 0;
    } else {
      // Para carreras específicas, verificar el total
      tieneDatos = datos.total > 0;
    }

    if (!tieneDatos) {
      // Ocultar gráfico y leyenda, mostrar mensaje
      if (chartWrapper) chartWrapper.classList.add('d-none');
      if (noDataMessage) noDataMessage.classList.remove('d-none');
      if (calidadLegend) calidadLegend.classList.add('d-none');
      return;
    }

    // Mostrar gráfico y leyenda, ocultar mensaje
    if (chartWrapper) chartWrapper.classList.remove('d-none');
    if (noDataMessage) noDataMessage.classList.add('d-none');
    if (calidadLegend) calidadLegend.classList.remove('d-none');

    // Actualizar los porcentajes en la leyenda
    document.getElementById('aprobaciones-porcentaje').textContent = datos.porcentaje_aprobaciones + '%';
    document.getElementById('devoluciones-porcentaje').textContent = datos.porcentaje_devoluciones + '%';

    // Actualizar el gráfico
    if (calidadChart) {
      calidadChart.data.datasets[0].data = [
        datos.aprobaciones_directas,
        datos.devoluciones
      ];
      calidadChart.update();
    } else {
      // Crear el gráfico por primera vez
      const calidadCtx = document.getElementById('calidadPropuestasChart');
      if (calidadCtx) {
        calidadChart = new Chart(calidadCtx, {
          type: 'doughnut',
          data: {
            labels: ['Aprobación Directa', 'Devoluciones'],
            datasets: [{
              data: [
                datos.aprobaciones_directas,
                datos.devoluciones
              ],
              backgroundColor: [
                '#22c55e', // Verde para aprobaciones
                '#ef4444'  // Rojo para devoluciones
              ],
              borderWidth: 0
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
              legend: {
                display: false
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
                    return label + ': ' + value + ' (' + percentage + '%)';
                  }
                }
              }
            }
          }
        });
      }
    }
  }

  // Función para actualizar colores de gráficos según modo oscuro
  function actualizarColoresGraficos() {
    const isDarkMode = document.body.classList.contains('dark-mode');
    const gridColor = isDarkMode ? '#2d3748' : '#e5e7eb';
    const textColor = isDarkMode ? '#b8b8b8' : '#6b7280';
    
    if (calidadChart) {
      calidadChart.options.plugins.tooltip.backgroundColor = isDarkMode ? 'rgba(30, 41, 59, 0.95)' : 'rgba(255, 255, 255, 0.95)';
      calidadChart.options.plugins.tooltip.titleColor = isDarkMode ? '#e8e8e8' : '#1f1f2d';
      calidadChart.options.plugins.tooltip.bodyColor = isDarkMode ? '#e8e8e8' : '#1f1f2d';
      calidadChart.options.plugins.tooltip.borderColor = isDarkMode ? '#2d3748' : '#e5e7eb';
      calidadChart.update('none');
    }
    
    if (ritmoChart) {
      ritmoChart.options.scales.x.ticks.color = textColor;
      ritmoChart.options.scales.x.grid.color = gridColor;
      ritmoChart.options.scales.y.ticks.color = textColor;
      ritmoChart.options.scales.y.grid.color = gridColor;
      ritmoChart.options.plugins.tooltip.backgroundColor = isDarkMode ? 'rgba(30, 41, 59, 0.95)' : 'rgba(255, 255, 255, 0.95)';
      ritmoChart.options.plugins.tooltip.titleColor = isDarkMode ? '#e8e8e8' : '#1f1f2d';
      ritmoChart.options.plugins.tooltip.bodyColor = isDarkMode ? '#e8e8e8' : '#1f1f2d';
      ritmoChart.options.plugins.tooltip.borderColor = isDarkMode ? '#2d3748' : '#e5e7eb';
      ritmoChart.update('none');
    }
  }

  // Observar cambios en el modo oscuro
  const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
      if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
        actualizarColoresGraficos();
      }
    });
  });
  
  observer.observe(document.body, {
    attributes: true,
    attributeFilter: ['class']
  });

  // Inicializar con datos globales
  actualizarGraficoCalidad(0);

  // Event listeners para las pestañas
  const tabButtons = document.querySelectorAll('#carreraTabs button[data-carrera-id]');
  tabButtons.forEach(button => {
    button.addEventListener('click', function(event) {
      event.preventDefault();
      // Remover active de todos los botones
      tabButtons.forEach(btn => btn.classList.remove('active'));
      // Agregar active al botón clickeado
      this.classList.add('active');
      
      const carreraId = this.getAttribute('data-carrera-id');
      actualizarGraficoCalidad(carreraId);
    });
  });

  // Datos de ritmo de trabajo por carrera desde PHP
  const ritmoGlobal = @json($ritmoTrabajo);
  const ritmoPorCarrera = @json($ritmoTrabajoPorCarrera);

  let ritmoChart = null;

  // Función para actualizar el gráfico de ritmo según la carrera seleccionada
  function actualizarGraficoRitmo(carreraId) {
    let datos;
    const chartWrapper = document.getElementById('ritmoChartWrapper');
    const noDataMessage = document.getElementById('ritmoNoDataMessage');
    
    if (carreraId === 0 || carreraId === '0') {
      // Mostrar datos globales
      datos = ritmoGlobal;
    } else {
      // Mostrar datos de la carrera específica
      const carreraData = ritmoPorCarrera[carreraId];
      if (carreraData && carreraData.datos) {
        datos = carreraData.datos;
      } else {
        datos = [];
      }
    }

    // Verificar si hay datos
    const tieneDatos = datos.length > 0 && datos.some(item => item.recibidas > 0 || item.procesadas > 0);

    if (!tieneDatos) {
      // Ocultar gráfico, mostrar mensaje
      if (chartWrapper) chartWrapper.classList.add('d-none');
      if (noDataMessage) noDataMessage.classList.remove('d-none');
      return;
    }

    // Mostrar gráfico, ocultar mensaje
    if (chartWrapper) chartWrapper.classList.remove('d-none');
    if (noDataMessage) noDataMessage.classList.add('d-none');

    // Actualizar el gráfico
    if (ritmoChart) {
      ritmoChart.data.labels = datos.map(item => item.fecha);
      ritmoChart.data.datasets[0].data = datos.map(item => item.recibidas);
      ritmoChart.data.datasets[1].data = datos.map(item => item.procesadas);
      ritmoChart.update();
    } else {
      // Crear el gráfico por primera vez
      const ritmoCtx = document.getElementById('ritmoTrabajoChart');
      if (ritmoCtx) {
        ritmoChart = new Chart(ritmoCtx, {
          type: 'line',
          data: {
            labels: datos.map(item => item.fecha),
            datasets: [
              {
                label: 'Recibidas',
                data: datos.map(item => item.recibidas),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
              },
              {
                label: 'Procesadas',
                data: datos.map(item => item.procesadas),
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.4,
                fill: true
              }
            ]
          },
          options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
              legend: {
                display: false
              },
              tooltip: {
                mode: 'index',
                intersect: false,
                backgroundColor: document.body.classList.contains('dark-mode') ? 'rgba(30, 41, 59, 0.95)' : 'rgba(255, 255, 255, 0.95)',
                titleColor: document.body.classList.contains('dark-mode') ? '#e8e8e8' : '#1f1f2d',
                bodyColor: document.body.classList.contains('dark-mode') ? '#e8e8e8' : '#1f1f2d',
                borderColor: document.body.classList.contains('dark-mode') ? '#2d3748' : '#e5e7eb',
                borderWidth: 1
              }
            },
            scales: {
              x: {
                ticks: {
                  color: document.body.classList.contains('dark-mode') ? '#b8b8b8' : '#6b7280'
                },
                grid: {
                  color: document.body.classList.contains('dark-mode') ? '#2d3748' : '#e5e7eb'
                }
              },
              y: {
                beginAtZero: true,
                ticks: {
                  stepSize: 1,
                  color: document.body.classList.contains('dark-mode') ? '#b8b8b8' : '#6b7280'
                },
                grid: {
                  color: document.body.classList.contains('dark-mode') ? '#2d3748' : '#e5e7eb'
                }
              }
            },
            interaction: {
              mode: 'nearest',
              axis: 'x'
            }
          }
        });
      }
    }
  }

  // Inicializar con datos globales
  actualizarGraficoRitmo(0);

  // Event listeners para las pestañas de ritmo
  const ritmoTabButtons = document.querySelectorAll('#ritmoCarreraTabs button[data-carrera-id]');
  ritmoTabButtons.forEach(button => {
    button.addEventListener('click', function(event) {
      event.preventDefault();
      // Remover active de todos los botones
      ritmoTabButtons.forEach(btn => btn.classList.remove('active'));
      // Agregar active al botón clickeado
      this.classList.add('active');
      
      const carreraId = this.getAttribute('data-carrera-id');
      actualizarGraficoRitmo(carreraId);
    });
  });
});
</script>
@endpush

@endsection
