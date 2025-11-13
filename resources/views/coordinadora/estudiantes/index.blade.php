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
    <a href="{{ route('estudiantes.create') }}" class="btn btn-danger"><i class="fas fa-user-plus me-2"></i>Registrar Estudiante</a>
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
          <input type="text" class="form-control filters-group__input" placeholder="Buscar estudiantes...">
          <select class="form-select filters-group__input">
            <option>Todas las carreras</option>
          </select>
          <select class="form-select filters-group__input">
            <option>Todos los tipos</option>
          </select>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Estudiante</th>
              <th>Carrera</th>
              <th>Tipo de Discapacidad</th>
              <th>Estado</th>
              <th>Casos</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($estudiantes as $est)
              <tr>
                <td>
                  <div class="fw-semibold">{{ $est['nombre'] }} {{ $est['apellido'] }}</div>
                  <div class="text-muted small">{{ $est['rut'] ?? 'Sin rut' }}</div>
                  <div class="text-muted small">{{ $est['email'] ?? 'Sin email' }}</div>
                </td>
                <td>
                  <div class="fw-semibold">{{ $est['carrera'] ?? 'Sin carrera' }}</div>
                  <small class="text-muted">{{ $est['semestre'] }}</small>
                </td>
                <td><span class="badge rounded-pill bg-light text-dark">{{ $est['tipo_discapacidad'] }}</span></td>
                <td><span class="badge bg-success">{{ $est['estado'] }}</span></td>
                <td><span class="badge bg-warning text-dark">{{ $est['casos'] }}</span></td>
                <td>
                  <div class="d-flex gap-2">
                    <a href="{{ route('estudiantes.show', $est['id']) }}" class="btn btn-sm btn-outline-secondary" title="Ver"><i class="fas fa-eye"></i></a>
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
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-4">Aun no hay estudiantes registrados.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
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
  .table thead th {
    color: #6b6c7f;
    font-weight: 600;
    border-bottom: 1px solid #ececf4;
  }
  .filters-group .filters-group__input {
    width: 220px;
    flex: 0 0 auto;
  }
  @media (max-width: 768px) {
    .filters-group .filters-group__input {
      width: 100%;
      flex: 1 1 100%;
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
