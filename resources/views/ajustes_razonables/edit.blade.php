@extends('layouts.app')

@section('title', 'Editar ajuste razonable')

@section('content')
<div class="container-fluid">
  @include('partials.alerts')

  <div class="card">
    <div class="card-header">Editar ajuste razonable</div>
    <div class="card-body">
      <form action="{{ route('ajustes-razonables.update', $ajuste) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-3">
          <div class="col-md-6">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $ajuste->nombre) }}" class="form-control @error('nombre') is-invalid @enderror" required>
            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="fecha_solicitud" class="form-label">Fecha de solicitud</label>
            <input type="date" id="fecha_solicitud" name="fecha_solicitud" value="{{ old('fecha_solicitud', optional($ajuste->fecha_solicitud)->format('Y-m-d')) }}" class="form-control @error('fecha_solicitud') is-invalid @enderror" required>
            @error('fecha_solicitud')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="estado" class="form-label">Estado</label>
            <input type="text" id="estado" name="estado" value="{{ old('estado', $ajuste->estado) }}" class="form-control @error('estado') is-invalid @enderror">
            @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="fecha_inicio" class="form-label">Fecha de inicio</label>
            <input type="date" id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio', optional($ajuste->fecha_inicio)->format('Y-m-d')) }}" class="form-control @error('fecha_inicio') is-invalid @enderror">
            @error('fecha_inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="fecha_termino" class="form-label">Fecha de término</label>
            <input type="date" id="fecha_termino" name="fecha_termino" value="{{ old('fecha_termino', optional($ajuste->fecha_termino)->format('Y-m-d')) }}" class="form-control @error('fecha_termino') is-invalid @enderror">
            @error('fecha_termino')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="porcentaje_avance" class="form-label">Avance (%)</label>
            <input type="number" id="porcentaje_avance" name="porcentaje_avance" value="{{ old('porcentaje_avance', $ajuste->porcentaje_avance) }}" min="0" max="100" class="form-control @error('porcentaje_avance') is-invalid @enderror">
            @error('porcentaje_avance')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="estudiante_id" class="form-label">Estudiante</label>
            <select id="estudiante_id" name="estudiante_id" class="form-select @error('estudiante_id') is-invalid @enderror" required>
              @foreach($estudiantes as $estudiante)
                <option value="{{ $estudiante->id }}" @selected(old('estudiante_id', $ajuste->estudiante_id) == $estudiante->id)>{{ $estudiante->nombre }} {{ $estudiante->apellido }}</option>
              @endforeach
            </select>
            @error('estudiante_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="solicitud_id" class="form-label">Solicitud</label>
            <select id="solicitud_id" name="solicitud_id" class="form-select @error('solicitud_id') is-invalid @enderror" required>
              @foreach($solicitudes as $solicitud)
                <option value="{{ $solicitud->id }}" @selected(old('solicitud_id', $ajuste->solicitud_id) == $solicitud->id)>
                  {{ $solicitud->fecha_solicitud?->format('d/m/Y') }} - {{ $solicitud->estudiante->nombre ?? '' }} {{ $solicitud->estudiante->apellido ?? '' }}
                </option>
              @endforeach
            </select>
            @error('solicitud_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="mt-4 d-flex justify-content-end gap-2">
          <a href="{{ route('ajustes-razonables.index') }}" class="btn btn-secondary">Cancelar</a>
          <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
