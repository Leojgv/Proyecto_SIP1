@extends('layouts.dashboard_coordinadora.app')

@section('title', 'Gestion de Estudiantes')

@section('content')
@php
  $estadoOptions = ['Pendiente', 'En proceso', 'Terminado'];
@endphp
<div class="students-page">
  <div class="page-header d-flex flex-wrap justify-content-between align-items-start mb-4">
    <div>
      <h1 class="h4 mb-1">Gestion de Estudiantes</h1>
      <p class="text-muted mb-0">Registra y gestiona informacion de estudiantes con necesidades especiales.</p>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
      <div class="info-card">
        <div class="info-card__icon bg-danger-subtle text-danger"><i class="fas fa-users"></i></div>
        <div>
          <p class="text-muted mb-1">Total Estudiantes</p>
          <h3 class="mb-0">{{ $total }}</h3>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="info-card">
        <div class="info-card__icon bg-success-subtle text-success"><i class="fas fa-user-check"></i></div>
        <div>
          <p class="text-muted mb-1">Estudiantes Activos</p>
          <h3 class="mb-0">{{ $activos }}</h3>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="info-card">
        <div class="info-card__icon bg-warning-subtle text-warning"><i class="fas fa-clipboard-list"></i></div>
        <div>
          <p class="text-muted mb-1">Con casos activos</p>
          <h3 class="mb-0">{{ $conCasos }}</h3>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="info-card">
        <div class="info-card__icon bg-info-subtle text-info"><i class="fas fa-calendar-plus"></i></div>
        <div>
          <p class="text-muted mb-1">Nuevos este mes</p>
          <h3 class="mb-0">{{ $nuevosMes }}</h3>
        </div>
      </div>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
          <h5 class="card-title mb-0">Lista de Estudiantes</h5>
          <small class="text-muted">Estudiantes registrados con necesidades de apoyo educativo</small>
        </div>
        <div class="filters-group d-flex flex-wrap gap-2 align-items-center">
          <input type="text" class="form-control filters-group__input" placeholder="Buscar estudiantes..." id="searchInput" value="{{ $filters['search'] ?? '' }}">
          <select class="form-select filters-group__input" id="carreraFilter" name="carrera_id">
            <option value="">Todas las carreras</option>
            @foreach($carreras ?? [] as $carrera)
              <option value="{{ $carrera->id }}" {{ ($filters['carrera_id'] ?? '') == $carrera->id ? 'selected' : '' }}>
                {{ $carrera->nombre }}
              </option>
            @endforeach
          </select>
          <select class="form-select filters-group__input" id="ordenarPor" name="ordenar_por">
            <option value="nombre" {{ ($filters['ordenar_por'] ?? 'nombre') == 'nombre' ? 'selected' : '' }}>Ordenar por: Nombre (A-Z)</option>
            <option value="nombre_desc" {{ ($filters['ordenar_por'] ?? '') == 'nombre_desc' ? 'selected' : '' }}>Ordenar por: Nombre (Z-A)</option>
            <option value="carrera" {{ ($filters['ordenar_por'] ?? '') == 'carrera' ? 'selected' : '' }}>Ordenar por: Carrera (A-Z)</option>
            <option value="carrera_desc" {{ ($filters['ordenar_por'] ?? '') == 'carrera_desc' ? 'selected' : '' }}>Ordenar por: Carrera (Z-A)</option>
            <option value="casos" {{ ($filters['ordenar_por'] ?? '') == 'casos' ? 'selected' : '' }}>Ordenar por: Casos (Menor a Mayor)</option>
            <option value="casos_desc" {{ ($filters['ordenar_por'] ?? '') == 'casos_desc' ? 'selected' : '' }}>Ordenar por: Casos (Mayor a Menor)</option>
            <option value="fecha_desc" {{ ($filters['ordenar_por'] ?? '') == 'fecha_desc' ? 'selected' : '' }}>Ordenar por: Fecha (Más recientes)</option>
            <option value="fecha_asc" {{ ($filters['ordenar_por'] ?? '') == 'fecha_asc' ? 'selected' : '' }}>Ordenar por: Fecha (Más antiguos)</option>
          </select>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle students-table">
          <thead class="table-header">
            <tr>
              <th class="col-estudiante">Estudiante</th>
              <th class="col-carrera">Carrera</th>
              <th class="col-estado">Estado</th>
              <th class="col-casos">Casos</th>
              <th class="col-acciones">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($estudiantes as $est)
              <tr class="student-row">
                <td class="student-info">
                  <div class="student-name">{{ $est['nombre'] }} {{ $est['apellido'] }}</div>
                  <div class="student-details">
                    <span class="student-rut">{{ $est['rut'] ?? 'Sin rut' }}</span>
                    <span class="student-separator">•</span>
                    <span class="student-email">{{ $est['email'] ?? 'Sin email' }}</span>
                  </div>
                </td>
                <td class="career-info">
                  <div class="career-name">{{ $est['carrera'] ?? 'Sin carrera' }}</div>
                  @if($est['semestre'])
                    <div class="career-semester">{{ $est['semestre'] }}</div>
                  @endif
                </td>
                <td class="status-cell">
                  @if($est['casos'] > 0)
                    <span class="badge badge-status badge-active">{{ $est['estado'] }}</span>
                  @else
                    <span class="badge badge-status badge-inactive">Sin casos</span>
                  @endif
                </td>
                <td class="cases-cell">
                  <span class="badge badge-cases">{{ $est['casos'] }}</span>
                </td>
                <td class="actions-cell">
                  @if($est['casos'] > 0)
                    <button
                      type="button"
                      class="btn btn-action-details"
                      data-bs-toggle="modal"
                      data-bs-target="#modalVerDetalles"
                      data-estudiante-nombre="{{ $est['nombre'] }} {{ $est['apellido'] }}"
                      data-estudiante-rut="{{ $est['rut'] ?? 'Sin RUT' }}"
                      data-estudiante-email="{{ $est['email'] ?? 'Sin email' }}"
                      data-estudiante-carrera="{{ $est['carrera'] ?? 'Sin carrera' }}"
                      data-solicitudes="{{ json_encode($est['solicitudes']) }}"
                    >
                      <i class="fas fa-eye me-1"></i>Ver detalles
                    </button>
                  @else
                    <span class="no-cases-text">Sin casos</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="empty-state">
                  <div class="empty-state-content">
                    <i class="fas fa-users empty-state-icon"></i>
                    <p class="empty-state-text">Aun no hay estudiantes registrados.</p>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal Ver Detalles de Solicitudes -->
<div class="modal fade" id="modalVerDetalles" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header border-0">
        <div>
          <h5 class="modal-title fw-semibold">Detalle de Solicitudes</h5>
          <small class="text-muted">Información de las solicitudes del estudiante</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body p-4">
        <!-- Información del Estudiante -->
        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <div class="border rounded p-3 bg-light">
              <small class="text-muted d-block mb-1">
                <i class="fas fa-user-graduate me-1"></i><strong>Estudiante</strong>
              </small>
              <div class="fw-semibold" id="modal-detalle-estudiante-nombre">-</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border rounded p-3 bg-light">
              <small class="text-muted d-block mb-1">
                <i class="fas fa-id-card me-1"></i><strong>RUT</strong>
              </small>
              <div class="fw-semibold" id="modal-detalle-estudiante-rut">-</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border rounded p-3 bg-light">
              <small class="text-muted d-block mb-1">
                <i class="fas fa-envelope me-1"></i><strong>Correo</strong>
              </small>
              <div class="fw-semibold" id="modal-detalle-estudiante-email">-</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="border rounded p-3 bg-light">
              <small class="text-muted d-block mb-1">
                <i class="fas fa-school me-1"></i><strong>Carrera</strong>
              </small>
              <div class="fw-semibold" id="modal-detalle-estudiante-carrera">-</div>
            </div>
          </div>
        </div>

        <!-- Lista de Solicitudes -->
        <div id="modal-detalle-solicitudes-lista">
          <p class="text-muted text-center">Cargando información...</p>
        </div>
      </div>
      <div class="modal-footer border-top bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal para crear casos -->
<div class="modal fade" id="modalNuevoCaso" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form class="modal-content" method="POST" action="{{ route('solicitudes.store') }}">
      @csrf
      <div class="modal-header">
        <div>
          <h5 class="modal-title mb-0">Registrar nuevo caso</h5>
          <small class="text-muted">Completa los datos para crear la solicitud sin salir del panel.</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label for="modalFechaSolicitud" class="form-label">Fecha</label>
            <input
              type="date"
              id="modalFechaSolicitud"
              name="fecha_solicitud"
              class="form-control @error('fecha_solicitud') is-invalid @enderror"
              value="{{ old('fecha_solicitud', now()->format('Y-m-d')) }}"
              required
            >
            @error('fecha_solicitud')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="modalEstado" class="form-label">Estado</label>
            <select
              id="modalEstado"
              name="estado"
              class="form-select @error('estado') is-invalid @enderror"
              required
            >
              <option value="">Selecciona un estado</option>
              @foreach ($estadoOptions as $estadoOption)
                <option value="{{ $estadoOption }}" @selected(old('estado', 'Pendiente') === $estadoOption)>{{ $estadoOption }}</option>
              @endforeach
            </select>
            @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="modalEstudianteSelect" class="form-label">Estudiante</label>
            <select
              id="modalEstudianteSelect"
              name="estudiante_id"
              class="form-select @error('estudiante_id') is-invalid @enderror"
              required
            >
              <option value="">Selecciona un estudiante</option>
              @foreach ($estudiantesOptions as $estudianteOption)
                <option value="{{ $estudianteOption->id }}" @selected(old('estudiante_id') == $estudianteOption->id)>
                  {{ $estudianteOption->nombre }} {{ $estudianteOption->apellido }}
                </option>
              @endforeach
            </select>
            @error('estudiante_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="modalAsesorSelect" class="form-label">Asesora pedagogica</label>
            <select
              id="modalAsesorSelect"
              name="asesor_id"
              class="form-select @error('asesor_id') is-invalid @enderror"
              required
            >
              <option value="">Selecciona una asesora</option>
              @foreach ($asesores as $asesor)
                <option value="{{ $asesor->id }}" @selected(old('asesor_id') == $asesor->id)>
                  {{ $asesor->nombre }} {{ $asesor->apellido }}
                </option>
              @endforeach
            </select>
            @error('asesor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="modalDirectorSelect" class="form-label">Director de carrera</label>
            <select
              id="modalDirectorSelect"
              name="director_id"
              class="form-select @error('director_id') is-invalid @enderror"
              required
            >
              <option value="">Selecciona un director</option>
              @foreach ($directores as $director)
                <option value="{{ $director->id }}" @selected(old('director_id') == $director->id)>
                  {{ $director->nombre }} {{ $director->apellido }}
                </option>
              @endforeach
            </select>
            @error('director_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label for="modalDescripcion" class="form-label">Descripcion</label>
            <textarea
              id="modalDescripcion"
              name="descripcion"
              rows="4"
              class="form-control @error('descripcion') is-invalid @enderror"
              placeholder="Describe brevemente el caso"
            >{{ old('descripcion') }}</textarea>
            @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-danger">Guardar caso</button>
      </div>
    </form>
  </div>
</div>

<style>
  .info-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: #fff;
    border-radius: 16px;
    padding: 1.25rem;
    border: 1px solid #f0f0f5;
    box-shadow: 0 12px 25px rgba(15, 30, 60, .04);
  }
  .info-card__icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
  }
  /* Tabla de estudiantes - Modo claro */
  .students-table {
    border-collapse: separate;
    border-spacing: 0;
  }

  .table-header {
    background: #f8f9fa;
  }

  .table-header th {
    color: #495057;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 1rem 0.75rem;
    border-bottom: 2px solid #dee2e6;
    vertical-align: middle;
  }

  .student-row {
    transition: all 0.2s ease;
    border-bottom: 1px solid #e9ecef;
  }

  .student-row:hover {
    background-color: #f8f9fa;
  }

  .student-info {
    padding: 1rem 0.75rem;
  }

  .student-name {
    font-weight: 600;
    color: #212529;
    margin-bottom: 0.25rem;
    font-size: 0.95rem;
  }

  .student-details {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.813rem;
    color: #6c757d;
  }

  .student-separator {
    color: #adb5bd;
  }

  .career-info {
    padding: 1rem 0.75rem;
  }

  .career-name {
    font-weight: 500;
    color: #495057;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
  }

  .career-semester {
    font-size: 0.813rem;
    color: #6c757d;
  }

  .status-cell,
  .cases-cell,
  .actions-cell {
    padding: 1rem 0.75rem;
    vertical-align: middle;
  }

  .badge-status {
    font-weight: 500;
    padding: 0.4rem 0.75rem;
    font-size: 0.813rem;
    border-radius: 6px;
  }

  .badge-status.badge-active {
    background-color: #10b981;
    color: #ffffff;
  }

  .badge-status.badge-inactive {
    background-color: #6b7280;
    color: #ffffff;
  }

  .badge-cases {
    background-color: #f59e0b;
    color: #1e293b;
    font-weight: 600;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 8px;
    min-width: 2.5rem;
    text-align: center;
    display: inline-block;
  }

  .btn-action-details {
    background-color: transparent;
    border: 1.5px solid #3b82f6;
    color: #3b82f6;
    font-weight: 500;
    padding: 0.4rem 0.875rem;
    font-size: 0.813rem;
    border-radius: 6px;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
  }

  .btn-action-details:hover {
    background-color: #3b82f6;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
  }

  .no-cases-text {
    color: #6c757d;
    font-size: 0.813rem;
    font-style: italic;
  }

  .empty-state {
    padding: 3rem 1rem;
    text-align: center;
  }

  .empty-state-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
  }

  .empty-state-icon {
    font-size: 3rem;
    color: #adb5bd;
  }

  .empty-state-text {
    color: #6c757d;
    font-size: 1rem;
    margin: 0;
  }

  .filters-group .filters-group__input {
    width: 220px;
    flex: 0 0 auto;
  }

  #ordenarPor {
    min-width: 220px;
  }

  @media (max-width: 768px) {
    .filters-group .filters-group__input {
      width: 100%;
      flex: 1 1 100%;
    }
    #ordenarPor {
      min-width: 100%;
    }
  }

  /* Estilos para modo oscuro */
  [data-theme="dark"] .students-page {
    background: transparent;
  }

  [data-theme="dark"] .page-header h1 {
    color: #f1f5f9;
    font-weight: 600;
  }

  [data-theme="dark"] .page-header .text-muted {
    color: #cbd5e1;
  }

  /* Tarjetas de información superior */
  [data-theme="dark"] .info-card {
    background: #1e293b;
    border-color: #334155;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
  }

  [data-theme="dark"] .info-card .text-muted {
    color: #94a3b8;
  }

  [data-theme="dark"] .info-card h3 {
    color: #f1f5f9;
    font-weight: 700;
  }

  [data-theme="dark"] .info-card__icon {
    opacity: 0.9;
  }

  /* Card principal */
  [data-theme="dark"] .card {
    background: #1e293b;
    border-color: #334155;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
  }

  [data-theme="dark"] .card-title {
    color: #f1f5f9;
    font-weight: 600;
  }

  [data-theme="dark"] .card-body .text-muted {
    color: #94a3b8;
  }

  /* Tabla - Modo oscuro */
  [data-theme="dark"] .students-table {
    background: transparent;
  }

  [data-theme="dark"] .table-header {
    background: #0f172a;
  }

  [data-theme="dark"] .table-header th {
    color: #cbd5e1;
    border-bottom-color: #334155;
    font-weight: 600;
  }

  [data-theme="dark"] .student-row {
    background: #1e293b;
    border-bottom-color: #334155;
  }

  [data-theme="dark"] .student-row:hover {
    background-color: #334155;
  }

  [data-theme="dark"] .student-name {
    color: #f1f5f9;
    font-weight: 600;
  }

  [data-theme="dark"] .student-details {
    color: #94a3b8;
  }

  [data-theme="dark"] .student-separator {
    color: #64748b;
  }

  [data-theme="dark"] .career-name {
    color: #e2e8f0;
    font-weight: 500;
  }

  [data-theme="dark"] .career-semester {
    color: #94a3b8;
  }

  [data-theme="dark"] .badge-status.badge-active {
    background-color: #10b981 !important;
    color: #ffffff !important;
  }

  [data-theme="dark"] .badge-status.badge-inactive {
    background-color: #64748b !important;
    color: #ffffff !important;
  }

  [data-theme="dark"] .badge-cases {
    background-color: #f59e0b !important;
    color: #1e293b !important;
    font-weight: 600;
  }

  [data-theme="dark"] .btn-action-details {
    border-color: #60a5fa;
    color: #60a5fa;
  }

  [data-theme="dark"] .btn-action-details:hover {
    background-color: #3b82f6;
    border-color: #3b82f6;
    color: #ffffff;
    box-shadow: 0 4px 8px rgba(59, 130, 246, 0.4);
  }

  [data-theme="dark"] .no-cases-text {
    color: #64748b;
  }

  [data-theme="dark"] .empty-state-icon {
    color: #475569;
  }

  [data-theme="dark"] .empty-state-text {
    color: #94a3b8;
  }

  /* Badges */
  [data-theme="dark"] .badge.bg-success {
    background-color: #10b981 !important;
    color: #ffffff !important;
    font-weight: 500;
  }

  [data-theme="dark"] .badge.bg-warning {
    background-color: #f59e0b !important;
    color: #1e293b !important;
    font-weight: 600;
  }

  [data-theme="dark"] .badge.bg-danger {
    background-color: #ef4444 !important;
    color: #ffffff !important;
  }

  [data-theme="dark"] .badge.bg-secondary {
    background-color: #64748b !important;
    color: #ffffff !important;
  }

  [data-theme="dark"] .badge.bg-info {
    background-color: #3b82f6 !important;
    color: #ffffff !important;
  }

  /* Formularios */
  [data-theme="dark"] .form-control,
  [data-theme="dark"] .form-select {
    background: #0f172a;
    border-color: #475569;
    color: #f1f5f9;
    transition: all 0.2s ease;
  }

  [data-theme="dark"] .form-control:focus,
  [data-theme="dark"] .form-select:focus {
    background: #0f172a;
    border-color: #dc2626;
    color: #f1f5f9;
    box-shadow: 0 0 0 0.2rem rgba(220, 38, 38, 0.25);
  }

  [data-theme="dark"] .form-control::placeholder {
    color: #64748b;
    opacity: 0.8;
  }

  /* Botones */
  [data-theme="dark"] .btn-outline-primary {
    border-color: #60a5fa;
    color: #60a5fa;
    font-weight: 500;
    transition: all 0.2s ease;
  }

  [data-theme="dark"] .btn-outline-primary:hover,
  [data-theme="dark"] .btn-outline-primary:focus {
    background: #3b82f6;
    border-color: #3b82f6;
    color: #ffffff;
    box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
  }

  [data-theme="dark"] .btn-outline-danger {
    border-color: #f87171;
    color: #f87171;
    font-weight: 500;
  }

  [data-theme="dark"] .btn-outline-danger:hover,
  [data-theme="dark"] .btn-outline-danger:focus {
    background: #dc2626;
    border-color: #dc2626;
    color: #ffffff;
    box-shadow: 0 4px 6px rgba(220, 38, 38, 0.3);
  }

  [data-theme="dark"] .btn-secondary {
    background: #475569;
    border-color: #475569;
    color: #f1f5f9;
    font-weight: 500;
  }

  [data-theme="dark"] .btn-secondary:hover,
  [data-theme="dark"] .btn-secondary:focus {
    background: #64748b;
    border-color: #64748b;
    color: #ffffff;
  }

  [data-theme="dark"] .btn-danger {
    background: #dc2626;
    border-color: #dc2626;
    color: #ffffff;
  }

  [data-theme="dark"] .btn-danger:hover {
    background: #b91c1c;
    border-color: #b91c1c;
  }

  /* Modal */
  [data-theme="dark"] .modal-content {
    background: #1e293b;
    border-color: #334155;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
  }

  [data-theme="dark"] .modal-header {
    background: #0f172a;
    border-bottom-color: #334155;
  }

  [data-theme="dark"] .modal-title {
    color: #f1f5f9;
    font-weight: 600;
  }

  [data-theme="dark"] .modal-body {
    background: #1e293b;
    color: #f1f5f9;
  }

  [data-theme="dark"] .modal-footer {
    background: #0f172a;
    border-top-color: #334155;
  }

  [data-theme="dark"] .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
  }

  /* Elementos con bg-light */
  [data-theme="dark"] .bg-light {
    background: #0f172a !important;
    border-color: #334155 !important;
  }

  [data-theme="dark"] .bg-light .text-muted {
    color: #94a3b8 !important;
  }

  [data-theme="dark"] .bg-light .fw-semibold {
    color: #f1f5f9 !important;
  }

  [data-theme="dark"] .bg-light strong {
    color: #cbd5e1 !important;
  }

  /* Bordes */
  [data-theme="dark"] .border {
    border-color: #334155 !important;
  }

  [data-theme="dark"] .border.rounded {
    background: #0f172a;
  }

  [data-theme="dark"] .border.rounded .text-muted {
    color: #94a3b8;
  }

  [data-theme="dark"] .border.rounded .fw-semibold {
    color: #f1f5f9;
  }

  [data-theme="dark"] .border-danger {
    border-color: #dc2626 !important;
  }

  /* Texto */
  [data-theme="dark"] .text-muted {
    color: #94a3b8 !important;
  }

  [data-theme="dark"] .text-danger {
    color: #f87171 !important;
  }

  [data-theme="dark"] .text-center {
    color: #cbd5e1;
  }

  /* Table responsive */
  [data-theme="dark"] .table-responsive {
    border-color: #334155;
  }

  /* Estado "Sin casos" en la tabla */
  [data-theme="dark"] .table .text-muted.small {
    color: #64748b !important;
  }
</style>

<script>
  // Manejar filtros y búsqueda
  document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const carreraFilter = document.getElementById('carreraFilter');
    const ordenarPor = document.getElementById('ordenarPor');
    
    function aplicarFiltros() {
      const params = new URLSearchParams(window.location.search);
      
      if (searchInput.value.trim()) {
        params.set('search', searchInput.value.trim());
      } else {
        params.delete('search');
      }
      
      if (carreraFilter.value) {
        params.set('carrera_id', carreraFilter.value);
      } else {
        params.delete('carrera_id');
      }
      
      if (ordenarPor.value && ordenarPor.value !== 'nombre') {
        params.set('ordenar_por', ordenarPor.value);
      } else {
        params.delete('ordenar_por');
      }
      
      window.location.href = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
    }
    
    // Agregar event listeners
    if (searchInput) {
      let searchTimeout;
      searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(aplicarFiltros, 500); // Debounce de 500ms
      });
    }
    
    if (carreraFilter) {
      carreraFilter.addEventListener('change', aplicarFiltros);
    }
    
    if (ordenarPor) {
      ordenarPor.addEventListener('change', aplicarFiltros);
    }
  });

  document.addEventListener('DOMContentLoaded', function () {
    var modalEl = document.getElementById('modalNuevoCaso');
    if (!modalEl) {
      return;
    }
    modalEl.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      if (!button) {
        return;
      }
      var estudianteId = button.getAttribute('data-estudiante-id');
      var estudianteSelect = modalEl.querySelector('#modalEstudianteSelect');
      if (estudianteId && estudianteSelect) {
        estudianteSelect.value = estudianteId;
      }
    });

    @if ($errors->any())
      var autoModal = new bootstrap.Modal(modalEl);
      autoModal.show();
    @endif
  });

  // Modal Ver Detalles
  document.addEventListener('DOMContentLoaded', function () {
    const modalDetalles = document.getElementById('modalVerDetalles');
    if (!modalDetalles) return;

    modalDetalles.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      if (!button) return;

      const estudianteNombre = button.getAttribute('data-estudiante-nombre') || '-';
      const estudianteRut = button.getAttribute('data-estudiante-rut') || '-';
      const estudianteEmail = button.getAttribute('data-estudiante-email') || '-';
      const estudianteCarrera = button.getAttribute('data-estudiante-carrera') || '-';
      const solicitudesJson = button.getAttribute('data-solicitudes') || '[]';

      // Actualizar información del estudiante
      document.getElementById('modal-detalle-estudiante-nombre').textContent = estudianteNombre;
      document.getElementById('modal-detalle-estudiante-rut').textContent = estudianteRut;
      document.getElementById('modal-detalle-estudiante-email').textContent = estudianteEmail;
      document.getElementById('modal-detalle-estudiante-carrera').textContent = estudianteCarrera;

      // Procesar y mostrar solicitudes
      const solicitudesContainer = document.getElementById('modal-detalle-solicitudes-lista');
      try {
        const solicitudes = JSON.parse(solicitudesJson);
        
        if (!solicitudes || solicitudes.length === 0) {
          solicitudesContainer.innerHTML = '<p class="text-muted text-center">No hay solicitudes registradas para este estudiante.</p>';
          return;
        }

        let html = '<h6 class="mb-3 fw-semibold d-flex align-items-center"><i class="fas fa-file-alt me-2 text-danger"></i>Solicitudes</h6>';
        
        solicitudes.forEach((solicitud, index) => {
          const estadoClass = solicitud.estado.toLowerCase().includes('aprobado') ? 'bg-success' : 
                              solicitud.estado.toLowerCase().includes('rechazado') ? 'bg-danger' : 
                              'bg-secondary';
          
          html += `
            <div class="border rounded p-3 bg-light mb-3">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                  <h6 class="mb-1 fw-semibold">Solicitud #${index + 1}</h6>
                  <small class="text-muted">Fecha: ${solicitud.fecha_solicitud}</small>
                </div>
                <span class="badge ${estadoClass}">${solicitud.estado}</span>
              </div>
              
              ${solicitud.titulo ? `
              <div class="mb-3">
                <small class="text-muted d-block mb-1">
                  <strong>Título</strong>
                </small>
                <div class="fw-semibold">${solicitud.titulo}</div>
              </div>
              ` : ''}
              
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <small class="text-muted d-block mb-1">
                    <i class="fas fa-user-tie me-1"></i><strong>Coordinadora</strong>
                  </small>
                  <div class="fw-semibold">${solicitud.coordinadora}</div>
                </div>
                <div class="col-md-6">
                  <small class="text-muted d-block mb-1">
                    <i class="fas fa-user-shield me-1"></i><strong>Director de carrera</strong>
                  </small>
                  <div class="fw-semibold">${solicitud.director}</div>
                </div>
              </div>

              ${solicitud.motivo_rechazo ? `
              <div class="mb-3">
                <div class="border rounded p-3 bg-light border-danger">
                  <small class="text-muted d-block mb-2">
                    <i class="fas fa-exclamation-triangle me-1 text-danger"></i><strong>Motivo de rechazo</strong>
                  </small>
                  <div class="text-danger" style="line-height: 1.6;">${solicitud.motivo_rechazo}</div>
                </div>
              </div>
              ` : ''}

              <div class="mb-3">
                <div class="border rounded p-3 bg-light">
                  <small class="text-muted d-block mb-2">
                    <strong>Descripción</strong>
                  </small>
                  <div class="text-muted" style="line-height: 1.6;">${solicitud.descripcion}</div>
                </div>
              </div>

              ${solicitud.entrevistas && solicitud.entrevistas.length > 0 ? `
              <div class="mb-0">
                <h6 class="mb-3 fw-semibold d-flex align-items-center">
                  <i class="fas fa-comments me-2 text-danger"></i>Entrevistas
                </h6>
                ${solicitud.entrevistas.map(entrevista => {
                  const modalidad = entrevista.modalidad || '';
                  const isPresencial = modalidad.toLowerCase() === 'presencial';
                  const isVirtual = modalidad.toLowerCase() === 'virtual';
                  
                  return `
                  <div class="border rounded p-3 bg-light mb-2">
                    <div class="row g-3">
                      <div class="col-md-6">
                        <small class="text-muted d-block mb-1">
                          <i class="fas fa-calendar-day me-1"></i><strong>Fecha</strong>
                        </small>
                        <div class="fw-semibold">${entrevista.fecha}</div>
                      </div>
                      ${entrevista.hora_inicio && entrevista.hora_fin ? `
                      <div class="col-md-6">
                        <small class="text-muted d-block mb-1">
                          <i class="fas fa-clock me-1"></i><strong>Horario</strong>
                        </small>
                        <div class="fw-semibold">${entrevista.hora_inicio} - ${entrevista.hora_fin}</div>
                      </div>
                      ` : ''}
                      <div class="col-md-6">
                        <small class="text-muted d-block mb-1">
                          <i class="fas fa-laptop me-1"></i><strong>Modalidad</strong>
                        </small>
                        <div class="fw-semibold">
                          <span class="badge ${isVirtual ? 'bg-info' : 'bg-success'}">${modalidad || 'N/A'}</span>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <small class="text-muted d-block mb-1">
                          <i class="fas fa-user me-1"></i><strong>Coordinadora</strong>
                        </small>
                        <div class="fw-semibold">${entrevista.asesor}</div>
                      </div>
                      ${isPresencial ? `
                      <div class="col-md-12">
                        <small class="text-muted d-block mb-1">
                          <i class="fas fa-map-marker-alt me-1"></i><strong>Lugar</strong>
                        </small>
                        <div class="fw-semibold">Sala 4to Piso, Edificio A</div>
                      </div>
                      ` : ''}
                      ${isVirtual ? `
                      <div class="col-md-12">
                        <small class="text-muted d-block mb-1">
                          <i class="fas fa-link me-1"></i><strong>Link</strong>
                        </small>
                        <div class="fw-semibold">Por compartir</div>
                      </div>
                      ` : ''}
                      ${entrevista.tiene_acompanante && entrevista.acompanante_nombre ? `
                      <div class="col-md-12 mt-2">
                        <small class="text-muted d-block mb-1">
                          <i class="fas fa-user-friends me-1"></i><strong>Info de Acompañante/Tutor:</strong>
                        </small>
                        <div class="border rounded p-2 bg-white">
                          <div class="small mb-1"><strong>Nombre:</strong> ${entrevista.acompanante_nombre}</div>
                          ${entrevista.acompanante_rut ? `<div class="small mb-1"><strong>RUT:</strong> ${entrevista.acompanante_rut}</div>` : ''}
                          ${entrevista.acompanante_telefono ? `<div class="small"><strong>Teléfono:</strong> ${entrevista.acompanante_telefono}</div>` : ''}
                        </div>
                      </div>
                      ` : (isPresencial ? `
                      <div class="col-md-12 mt-2">
                        <small class="text-muted d-block mb-1">
                          <i class="fas fa-user-friends me-1"></i><strong>Info de Acompañante/Tutor:</strong>
                        </small>
                        <div class="small text-muted">No hay acompañante adicional</div>
                      </div>
                      ` : '')}
                    </div>
                  </div>
                  `;
                }).join('')}
              </div>
              ` : ''}

              ${solicitud.evidencias && solicitud.evidencias.length > 0 ? `
              <div class="mb-0">
                <h6 class="mb-3 fw-semibold d-flex align-items-center">
                  <i class="fas fa-file-pdf me-2 text-danger"></i>Archivos Adjuntos
                </h6>
                ${solicitud.evidencias.map(evidencia => {
                  const url = (evidencia.id && evidencia.ruta_archivo) ? `/evidencias/${evidencia.id}/download` : '#';
                  const nombreArchivo = evidencia.ruta_archivo ? evidencia.ruta_archivo.split('/').pop() : 'Sin nombre';
                  return `
                  <div class="border rounded p-3 bg-light mb-2">
                    <div class="d-flex align-items-center justify-content-between">
                      <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-file-pdf text-danger" style="font-size: 1.5rem;"></i>
                        <div>
                          <div class="fw-semibold">${nombreArchivo}</div>
                          ${evidencia.descripcion ? `<div class="text-muted small">${evidencia.descripcion}</div>` : ''}
                        </div>
                      </div>
                      ${url !== '#' ? `
                      <a href="${url}" target="_blank" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-download me-1"></i>Descargar
                      </a>
                      ` : ''}
                    </div>
                  </div>
                  `;
                }).join('')}
              </div>
              ` : ''}
            </div>
          `;
        });

        solicitudesContainer.innerHTML = html;
      } catch (e) {
        console.error('Error al procesar solicitudes:', e);
        solicitudesContainer.innerHTML = '<p class="text-danger text-center">Error al cargar las solicitudes.</p>';
      }
    });
  });
</script>
@endsection
