@extends('layouts.dashboard_coordinadora.app')

@section('title', 'Gestion de Estudiantes')

@section('content')
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
        <div class="d-flex flex-wrap gap-2">
          <input type="text" class="form-control" placeholder="Buscar estudiantes..." style="min-width: 200px;">
          <select class="form-select">
            <option>Todas las carreras</option>
          </select>
          <select class="form-select">
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
                <td class="d-flex gap-2">
                  <a href="{{ route('estudiantes.show', $est['id']) }}" class="btn btn-sm btn-outline-secondary" title="Ver"><i class="fas fa-eye"></i></a>
                  <a href="{{ route('solicitudes.create') }}" class="btn btn-sm btn-primary" title="Nuevo caso">Nuevo Caso</a>
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
</style>
@endsection
