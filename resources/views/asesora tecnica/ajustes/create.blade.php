@extends('layouts.dashboard_asesoratecnica.app')

@section('title', 'Formular ajuste')

@section('content')
<div class="dashboard-page">
  <div class="page-header mb-4">
    <h1 class="h4 mb-1">Formular ajuste</h1>
    <p class="text-muted mb-0">Registra un ajuste razonable para un estudiante.</p>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <form action="{{ route('asesora-tecnica.ajustes.store') }}" method="POST" class="row g-3">
        @csrf
        <div class="col-md-6">
          <label for="nombre" class="form-label">Nombre del ajuste</label>
          <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" class="form-control @error('nombre') is-invalid @enderror" required>
          @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
          <label for="fecha_solicitud" class="form-label">Fecha de solicitud</label>
          <input type="date" id="fecha_solicitud" name="fecha_solicitud" value="{{ old('fecha_solicitud', now()->toDateString()) }}" class="form-control @error('fecha_solicitud') is-invalid @enderror" required>
          @error('fecha_solicitud')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
          <label for="estado" class="form-label">Estado</label>
          <input type="text" id="estado" name="estado" value="{{ old('estado', 'Enviado') }}" class="form-control @error('estado') is-invalid @enderror">
          @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label for="fecha_inicio" class="form-label">Fecha de inicio</label>
          <input type="date" id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio') }}" class="form-control @error('fecha_inicio') is-invalid @enderror">
          @error('fecha_inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label for="fecha_termino" class="form-label">Fecha de término</label>
          <input type="date" id="fecha_termino" name="fecha_termino" value="{{ old('fecha_termino') }}" class="form-control @error('fecha_termino') is-invalid @enderror">
          @error('fecha_termino')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label for="porcentaje_avance" class="form-label">Avance (%)</label>
          <input type="number" id="porcentaje_avance" name="porcentaje_avance" value="{{ old('porcentaje_avance') }}" min="0" max="100" class="form-control @error('porcentaje_avance') is-invalid @enderror">
          @error('porcentaje_avance')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
          <label for="solicitud_id" class="form-label">Solicitud asociada</label>
          <select id="solicitud_id" name="solicitud_id" class="form-select @error('solicitud_id') is-invalid @enderror" required>
            <option value="">Selecciona una solicitud</option>
            @foreach($solicitudes as $solicitud)
              <option value="{{ $solicitud->id }}" @selected(old('solicitud_id') == $solicitud->id)>
                {{ $solicitud->fecha_solicitud?->format('d/m/Y') ?? 's/f' }} - {{ $solicitud->estudiante->nombre ?? '' }} {{ $solicitud->estudiante->apellido ?? '' }}
              </option>
            @endforeach
          </select>
          @error('solicitud_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label for="estudiante_id" class="form-label">Estudiante</label>
          <select id="estudiante_id" name="estudiante_id" class="form-select @error('estudiante_id') is-invalid @enderror" required>
            <option value="">Selecciona un estudiante</option>
            @foreach($estudiantes as $estudiante)
              <option value="{{ $estudiante->id }}" @selected(old('estudiante_id') == $estudiante->id)>
                {{ $estudiante->nombre }} {{ $estudiante->apellido }} - {{ $estudiante->carrera->nombre ?? 'Sin carrera' }}
              </option>
            @endforeach
          </select>
          @error('estudiante_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-12 d-flex justify-content-end gap-2 mt-2">
          <a href="{{ route('asesora-tecnica.dashboard') }}" class="btn btn-outline-danger">Cancelar</a>
          <button type="submit" class="btn btn-danger">Guardar ajuste</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
