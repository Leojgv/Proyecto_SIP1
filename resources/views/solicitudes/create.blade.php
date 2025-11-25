@extends('layouts.app')

@section('title', 'Nueva solicitud')

@section('content')
<div class="container-fluid">
  @include('partials.alerts')

  <div class="card">
    <div class="card-header">Registrar solicitud</div>
    <div class="card-body">
      <form action="{{ route('solicitudes.store') }}" method="POST">
        @csrf
        <div class="row g-3">
          <div class="col-md-4">
            <label for="fecha_solicitud" class="form-label">Fecha</label>
            <input type="date" id="fecha_solicitud" name="fecha_solicitud" value="{{ old('fecha_solicitud') }}" class="form-control @error('fecha_solicitud') is-invalid @enderror" required>
            @error('fecha_solicitud')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-8">
            <label for="estudiante_id" class="form-label">Estudiante</label>
            <select id="estudiante_id" name="estudiante_id" class="form-select @error('estudiante_id') is-invalid @enderror" required>
              <option value="">Selecciona un estudiante</option>
              @foreach($estudiantes as $estudiante)
                <option value="{{ $estudiante->id }}" @selected(old('estudiante_id') == $estudiante->id)>{{ $estudiante->nombre }} {{ $estudiante->apellido }}</option>
              @endforeach
            </select>
            @error('estudiante_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-12">
            <label for="asesor_id" class="form-label">Asesora pedagogica</label>
            <select id="asesor_id" name="asesor_id" class="form-select @error('asesor_id') is-invalid @enderror" required>
              <option value="">Selecciona una asesora</option>
              @foreach($asesores as $asesor)
                <option value="{{ $asesor->id }}" @selected(old('asesor_id') == $asesor->id)>{{ $asesor->nombre }} {{ $asesor->apellido }}</option>
              @endforeach
            </select>
            @error('asesor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="form-text text-muted">El Director de Carrera se asignará automáticamente según la carrera del estudiante.</small>
          </div>
          <div class="col-12">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" id="titulo" name="titulo" class="form-control @error('titulo') is-invalid @enderror" value="{{ old('titulo') }}" placeholder="Título de la solicitud">
            @error('titulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="4" class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion') }}</textarea>
            @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="mt-4 d-flex justify-content-end gap-2">
          <a href="{{ route('solicitudes.index') }}" class="btn btn-secondary">Cancelar</a>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
