{{-- resources/views/roles/show.blade.php --}}
@extends('layouts.dashboard_admin.admin')

@section('title', 'Detalle del rol')

@section('content')
<div class="container-fluid">
  <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4">
    <div>
      <h1 class="h3 mb-1">Detalle del Rol</h1>
      <p class="text-muted mb-0">Información completa del rol: <strong>{{ $rol->nombre }}</strong></p>
    </div>
    <div class="d-flex gap-2 mt-3 mt-lg-0">
      <a href="{{ route('roles.edit', $rol) }}" class="btn btn-primary">
        <i class="fas fa-pen me-2"></i>Editar
      </a>
      <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Volver
      </a>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-md-8">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h5 class="card-title mb-4">Información del Rol</h5>
          <dl class="row mb-0">
            <dt class="col-sm-3">Nombre:</dt>
            <dd class="col-sm-9">
              <span class="badge bg-primary fs-6">{{ $rol->nombre }}</span>
            </dd>
            
            <dt class="col-sm-3">Descripción:</dt>
            <dd class="col-sm-9">
              <p class="mb-0">{{ $rol->descripcion ?? 'Sin descripción' }}</p>
            </dd>
            
            <dt class="col-sm-3">Fecha de creación:</dt>
            <dd class="col-sm-9">
              {{ $rol->created_at->format('d/m/Y H:i') }}
            </dd>
            
            <dt class="col-sm-3">Última actualización:</dt>
            <dd class="col-sm-9">
              {{ $rol->updated_at->format('d/m/Y H:i') }}
            </dd>
          </dl>
        </div>
      </div>
    </div>
    
    <div class="col-md-4">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h5 class="card-title mb-3">Estadísticas</h5>
          <div class="d-flex align-items-center gap-3 mb-3">
            <div class="bg-primary bg-opacity-10 rounded p-3">
              <i class="fas fa-users fa-lg text-primary"></i>
            </div>
            <div>
              <p class="text-muted mb-0 small">Usuarios asignados</p>
              <h4 class="mb-0">{{ $rol->users->count() }}</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
