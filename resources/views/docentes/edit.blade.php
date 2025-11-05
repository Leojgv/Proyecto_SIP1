@extends('layouts.app')

@section('title', 'Editar docente')

@section('content')
<div class="container-fluid">
  @include('partials.alerts')

  <div class="card">
    <div class="card-header">Editar docente</div>
    <div class="card-body">
      <form action="{{ route('docentes.update', $docente) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-3">
          <div class="col-md-4">
            <label for="rut" class="form-label">RUT</label>
            <input type="text" id="rut" name="rut" value="{{ old('rut', $docente->rut) }}" class="form-control @error('rut') is-invalid @enderror" required>
            @error('rut')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $docente->nombre) }}" class="form-control @error('nombre') is-invalid @enderror" required>
            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="apellido" class="form-label">Apellido</label>
            <input type="text" id="apellido" name="apellido" value="{{ old('apellido', $docente->apellido) }}" class="form-control @error('apellido') is-invalid @enderror" required>
            @error('apellido')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="email" class="form-label">Correo</label>
            <input type="email" id="email" name="email" value="{{ old('email', $docente->email) }}" class="form-control @error('email') is-invalid @enderror" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $docente->telefono) }}" class="form-control @error('telefono') is-invalid @enderror">
            @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="especialidad" class="form-label">Especialidad</label>
            <input type="text" id="especialidad" name="especialidad" value="{{ old('especialidad', $docente->especialidad) }}" class="form-control @error('especialidad') is-invalid @enderror">
            @error('especialidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="mt-4 d-flex justify-content-end gap-2">
          <a href="{{ route('docentes.index') }}" class="btn btn-secondary">Cancelar</a>
          <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
