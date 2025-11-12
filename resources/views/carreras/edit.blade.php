@extends('layouts.app')

@section('title', 'Editar carrera')

@section('content')
<div class="container-fluid">
  @include('partials.alerts')

  <div class="card">
    <div class="card-header">Editar carrera</div>
    <div class="card-body">
      <form action="{{ route('carreras.update', $carrera) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-3">
          <div class="col-md-6">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $carrera->nombre) }}" class="form-control @error('nombre') is-invalid @enderror" required>
            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="jornada" class="form-label">Jornada</label>
            <input type="text" id="jornada" name="jornada" value="{{ old('jornada', $carrera->jornada) }}" class="form-control @error('jornada') is-invalid @enderror">
            @error('jornada')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="grado" class="form-label">Grado</label>
            <input type="text" id="grado" name="grado" value="{{ old('grado', $carrera->grado) }}" class="form-control @error('grado') is-invalid @enderror">
            @error('grado')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="mt-4 d-flex justify-content-end gap-2">
          <a href="{{ route('carreras.index') }}" class="btn btn-secondary">Cancelar</a>
          <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
