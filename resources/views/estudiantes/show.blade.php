@extends('layouts.app')

@section('title', 'Detalle del estudiante')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">{{ $estudiante->nombre }} {{ $estudiante->apellido }}</h1>
    <div class="btn-group">
      <a href="{{ route('estudiantes.edit', $estudiante) }}" class="btn btn-primary">Editar</a>
      <a href="{{ route('estudiantes.index') }}" class="btn btn-outline-secondary">Volver</a>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">RUT</dt>
        <dd class="col-sm-9">{{ $estudiante->rut }}</dd>

        <dt class="col-sm-3">Correo</dt>
        <dd class="col-sm-9">{{ $estudiante->email }}</dd>

        <dt class="col-sm-3">Teléfono</dt>
        <dd class="col-sm-9">{{ $estudiante->telefono ?: '—' }}</dd>

        <dt class="col-sm-3">Carrera</dt>
        <dd class="col-sm-9">{{ $estudiante->carrera?->nombre }}</dd>
      </dl>
    </div>
  </div>
</div>
@endsection
