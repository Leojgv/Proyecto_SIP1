@extends('layouts.dashboard_estudiante.estudiante')

@section('title', 'Crear perfil de estudiante')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-7 col-xl-6">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4 p-md-5">
          <div class="text-center mb-4">
            <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
            <h1 class="h4 mb-2">Crea tu perfil de estudiante</h1>
            <p class="text-muted mb-0">
              Para acceder a tu panel personal necesitamos algunos datos básicos. Completa el formulario
              y quedará vinculado automáticamente a tu cuenta.
            </p>
          </div>

          <form method="POST" action="{{ route('estudiantes.dashboard.store-profile') }}" novalidate>
            @csrf

            <div class="mb-3">
              <label for="nombre" class="form-label">Nombre</label>
              <input
                type="text"
                id="nombre"
                name="nombre"
                class="form-control @error('nombre') is-invalid @enderror"
                value="{{ old('nombre', $user?->name) }}"
                required
              >
              @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="apellido" class="form-label">Apellido</label>
              <input
                type="text"
                id="apellido"
                name="apellido"
                class="form-control @error('apellido') is-invalid @enderror"
                value="{{ old('apellido') }}"
                required
              >
              @error('apellido')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="rut" class="form-label">RUT</label>
              <input
                type="text"
                id="rut"
                name="rut"
                class="form-control @error('rut') is-invalid @enderror"
                value="{{ old('rut') }}"
                placeholder="12.345.678-9"
                required
              >
              @error('rut')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Correo institucional</label>
              <input type="email" class="form-control" value="{{ $user?->email }}" readonly>
              <small class="text-muted">Se vinculará automáticamente a tu perfil.</small>
            </div>

            <div class="mb-3">
              <label for="telefono" class="form-label">Teléfono (opcional)</label>
              <input
                type="text"
                id="telefono"
                name="telefono"
                class="form-control @error('telefono') is-invalid @enderror"
                value="{{ old('telefono') }}"
              >
              @error('telefono')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-4">
              <label for="carrera_id" class="form-label">Carrera</label>
              <select
                id="carrera_id"
                name="carrera_id"
                class="form-select @error('carrera_id') is-invalid @enderror"
                required
              >
                <option value="" disabled {{ old('carrera_id') ? '' : 'selected' }}>Selecciona tu carrera</option>
                @foreach ($carreras as $carrera)
                  <option value="{{ $carrera->id }}" {{ old('carrera_id') == $carrera->id ? 'selected' : '' }}>
                    {{ $carrera->nombre }}
                  </option>
                @endforeach
              </select>
              @error('carrera_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary btn-lg">Crear perfil y continuar</button>
              <a href="{{ route('home') }}" class="btn btn-outline-secondary">Volver al inicio</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
