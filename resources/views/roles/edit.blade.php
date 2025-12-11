{{-- resources/views/roles/edit.blade.php --}}
@extends('layouts.dashboard_admin.admin')

@section('title', 'Editar rol')

@section('content')
<div class="container-fluid">
  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  @endif

  <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4">
    <div>
      <h1 class="h3 mb-1">Editar Rol</h1>
      <p class="text-muted mb-0">Modifica la información del rol: <strong>{{ $rol->nombre }}</strong></p>
    </div>
    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary mt-3 mt-lg-0">
      <i class="fas fa-arrow-left me-2"></i>Volver
    </a>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <form action="{{ route('roles.update', $rol) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
          <div class="col-md-8">
            <div class="mb-3">
              <label for="nombre" class="form-label">Nombre del rol <span class="text-danger">*</span></label>
              <input type="text" 
                     id="nombre" 
                     name="nombre" 
                     value="{{ old('nombre', $rol->nombre) }}" 
                     class="form-control @error('nombre') is-invalid @enderror" 
                     required>
              @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="mb-3">
              <label for="descripcion" class="form-label">Descripción</label>
              <textarea id="descripcion" 
                        name="descripcion" 
                        rows="4" 
                        class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion', $rol->descripcion) }}</textarea>
              @error('descripcion')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="d-flex justify-content-end gap-2">
              <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">Cancelar</a>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Actualizar rol
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
