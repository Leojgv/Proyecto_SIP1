@extends('layouts.app')

@section('title', 'Editar asesor pedagógico')

@section('content')
<div class="container-fluid">
  @include('partials.alerts')

  <div class="card">
    <div class="card-header">Editar asesor pedagógico</div>
    <div class="card-body">
      <form action="{{ route('asesores-pedagogicos.update', $asesor) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-3">
          <div class="col-md-6">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $asesor->nombre) }}" class="form-control @error('nombre') is-invalid @enderror" required>
            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="apellido" class="form-label">Apellido</label>
            <input type="text" id="apellido" name="apellido" value="{{ old('apellido', $asesor->apellido) }}" class="form-control @error('apellido') is-invalid @enderror" required>
            @error('apellido')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="email" class="form-label">Correo</label>
            <input type="email" id="email" name="email" value="{{ old('email', $asesor->email) }}" class="form-control @error('email') is-invalid @enderror" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $asesor->telefono) }}" class="form-control @error('telefono') is-invalid @enderror">
            @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="mt-4 d-flex justify-content-end gap-2">
          <a href="{{ route('asesores-pedagogicos.index') }}" class="btn btn-secondary">Cancelar</a>
          <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
