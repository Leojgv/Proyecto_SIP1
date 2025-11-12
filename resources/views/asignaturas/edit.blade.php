@extends('layouts.app')

@section('title', 'Editar asignatura')

@section('content')
<div class="container-fluid">
  @include('partials.alerts')

  <div class="card">
    <div class="card-header">Editar asignatura</div>
    <div class="card-body">
      <form action="{{ route('asignaturas.update', $asignatura) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-3">
          <div class="col-md-6">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $asignatura->nombre) }}" class="form-control @error('nombre') is-invalid @enderror" required>
            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="tipo" class="form-label">Tipo</label>
            <input type="text" id="tipo" name="tipo" value="{{ old('tipo', $asignatura->tipo) }}" class="form-control @error('tipo') is-invalid @enderror">
            @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="estado" class="form-label">Estado</label>
            <input type="text" id="estado" name="estado" value="{{ old('estado', $asignatura->estado) }}" class="form-control @error('estado') is-invalid @enderror">
            @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="carrera_id" class="form-label">Carrera</label>
            <select id="carrera_id" name="carrera_id" class="form-select @error('carrera_id') is-invalid @enderror" required>
              <option value="">Selecciona una carrera</option>
              @foreach($carreras as $carrera)
                <option value="{{ $carrera->id }}" @selected(old('carrera_id', $asignatura->carrera_id) == $carrera->id)>{{ $carrera->nombre }}</option>
              @endforeach
            </select>
            @error('carrera_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="docente_id" class="form-label">Asesora tecnica pedagogica</label>
            <select id="docente_id" name="docente_id" class="form-select @error('docente_id') is-invalid @enderror">
              <option value="">Sin asignar</option>
              @foreach($docentes as $docente)
                <option value="{{ $docente->id }}" @selected(old('docente_id', $asignatura->docente_id) == $docente->id)>{{ $docente->nombre }} {{ $docente->apellido }}</option>
              @endforeach
            </select>
            @error('docente_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="mt-4 d-flex justify-content-end gap-2">
          <a href="{{ route('asignaturas.index') }}" class="btn btn-secondary">Cancelar</a>
          <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

