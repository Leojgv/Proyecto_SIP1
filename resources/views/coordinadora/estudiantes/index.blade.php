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
        <form method="GET" action="{{ route('coordinadora.estudiantes') }}" class="filters-group d-flex flex-wrap gap-2 align-items-center">
          <input
            type="text"
            name="search"
            value="{{ $filters['search'] ?? '' }}"
            class="form-control filters-group__input"
            placeholder="Buscar por nombre, rut o email..."
          >
          <select
            name="carrera_id"
            class="form-select filters-group__input"
            onchange="this.form.submit()"
          >
            <option value="">Todas las carreras</option>
            @foreach ($carreras as $carrera)
              <option value="{{ $carrera->id }}" @selected(($filters['carrera_id'] ?? '') == $carrera->id)>{{ $carrera->nombre }}</option>
            @endforeach
          </select>
          <select
            name="estado"
            class="form-select filters-group__input"
            onchange="this.form.submit()"
          >
            <option value="">Todos los estados</option>
            <option value="activo" @selected(($filters['estado'] ?? '') === 'activo')>Con casos</option>
            <option value="sin_casos" @selected(($filters['estado'] ?? '') === 'sin_casos')>Sin casos</option>
          </select>
          <button type="submit" class="btn btn-outline-secondary filters-group__submit">Filtrar</button>
          @if(($filters['search'] ?? '') || ($filters['carrera_id'] ?? '') || ($filters['estado'] ?? ''))
            <a href="{{ route('coordinadora.estudiantes') }}" class="btn btn-link text-decoration-none">Limpiar</a>
          @endif
        </form>
      </div>

      <div class="students-list">
        @forelse ($estudiantes as $est)
          <article class="student-card">
            <div class="d-flex gap-3 flex-wrap justify-content-between align-items-start">
              <div>
                <div class="student-card__name">{{ $est['nombre'] }} {{ $est['apellido'] }}</div>
                <div class="text-muted small">{{ $est['rut'] ?? 'Sin rut' }}</div>
                <div class="text-muted small">{{ $est['email'] ?? 'Sin email' }}</div>
              </div>
              <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="badge student-badge-light">{{ $est['tipo_discapacidad'] }}</span>
                <span class="badge bg-success">{{ $est['estado'] }}</span>
                <span class="badge student-badge-warning">{{ $est['casos'] }}</span>
              </div>
            </div>
            <div class="d-flex flex-wrap justify-content-between align-items-center mt-3">
              <div class="text-muted small">
                <strong class="student-card__label">Carrera:</strong> {{ $est['carrera'] ?? 'Sin carrera' }} <span class="ms-2">{{ $est['semestre'] }}</span>
              </div>
              <div class="d-flex gap-2">
                <button
                  type="button"
                  class="btn btn-sm btn-primary"
                  title="Nuevo caso"
                  data-bs-toggle="modal"
                  data-bs-target="#modalNuevoCaso"
                  data-estudiante-id="{{ $est['id'] }}"
                  data-estudiante-nombre="{{ $est['nombre'] }} {{ $est['apellido'] }}"
                >Nuevo Caso</button>
              </div>
            </div>
          </article>
        @empty
          <p class="text-center text-muted py-4 mb-0">Aun no hay estudiantes registrados.</p>
        @endforelse
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
    border: 1px solid #e5e7eb;
    box-shadow: 0 12px 25px rgba(15, 23, 42, .08);
    color: #1f2937;
  }
  .info-card__icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    background: transparent;
    color: #dc2626;
  }
  .filters-group .filters-group__input {
    width: 220px;
    flex: 0 0 auto;
  }
  .filters-group .filters-group__submit {
    flex: 0 0 auto;
    white-space: nowrap;
  }
  .students-page .students-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }
  .students-page .student-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 1rem 1.25rem;
    box-shadow: 0 10px 30px rgba(15, 23, 42, .08);
  }
  .students-page .student-card__name {
    color: #0f172a;
    font-weight: 700;
  }
  .students-page .student-badge-light {
    background-color: #f1f5f9;
    color: #0f172a;
    border: 1px solid #e2e8f0;
  }
  .students-page .student-badge-warning {
    background-color: #facc15;
    color: #1f2937;
  }
  .students-page .student-card__label {
    color: #000 !important;
  }
  .dark-mode .students-page .student-card__label {
    color: #e5e7eb !important;
  }
  .dark-mode .info-card {
    background: #0f172a !important;
    border-color: #1e293b !important;
    color: #e5e7eb !important;
    box-shadow: 0 12px 25px rgba(3, 7, 18, .35);
  }
  .dark-mode .info-card__icon {
    background: transparent !important;
    color: #e5e7eb !important;
  }
  .dark-mode .students-page {
    background: transparent;
    color: #e5e7eb;
  }
  .dark-mode .students-page .text-muted {
    color: #9ca3af !important;
  }
  .dark-mode .filters-group .filters-group__input {
    background: #0f172a;
    color: #e5e7eb;
    border-color: #1e293b;
  }
  .dark-mode .filters-group .filters-group__input::placeholder {
    color: #9ca3af;
  }
  .dark-mode .filters-group .filters-group__submit {
    color: #e5e7eb;
    border-color: #1e293b;
  }
  .dark-mode .filters-group .filters-group__submit:hover {
    background: #1e293b;
  }
  .dark-mode .students-page .student-card {
    background: #0f172a;
    border-color: #1e293b;
    box-shadow: 0 10px 30px rgba(3, 7, 18, .35);
  }
  .dark-mode .students-page .student-card__name {
    color: #e5e7eb;
  }
  .dark-mode .students-page .student-badge-light {
    background-color: #1f2937;
    color: #e5e7eb;
    border: 1px solid #273449;
  }
  .dark-mode .students-page .student-badge-warning {
    background-color: #facc15;
    color: #1f2937;
  }
  .dark-mode .filters-group__input,
  .dark-mode .filters-group__submit {
    background-color: #0f172a !important;
    color: #e5e7eb !important;
    border-color: #1f2937 !important;
  }
  .dark-mode .filters-group__input::placeholder {
    color: #9ca3af !important;
  }
  .dark-mode .filters-group__submit.btn-outline-secondary {
    color: #e5e7eb !important;
  }
  .dark-mode .filters-group__submit.btn-outline-secondary:hover {
    background-color: #1f2937 !important;
  }
  .dark-mode .btn-link {
    color: #9ca3af !important;
  }
  @media (max-width: 768px) {
    .filters-group .filters-group__input {
      width: 100%;
      flex: 1 1 100%;
    }
    .filters-group .filters-group__submit {
      width: 100%;
    }
  }
</style>

<script>
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
</script>
@endsection
