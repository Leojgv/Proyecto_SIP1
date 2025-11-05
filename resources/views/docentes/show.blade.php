@extends('layouts.app')

@section('title', 'Detalle de docente')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">{{ $docente->nombre }} {{ $docente->apellido }}</h1>
    <a href="{{ route('docentes.index') }}" class="btn btn-secondary">Volver</a>
  </div>

  <div class="card">
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">RUT</dt>
        <dd class="col-sm-9">{{ $docente->rut }}</dd>

        <dt class="col-sm-3">Correo</dt>
        <dd class="col-sm-9">{{ $docente->email }}</dd>

        <dt class="col-sm-3">Teléfono</dt>
        <dd class="col-sm-9">{{ $docente->telefono ?? '—' }}</dd>

        <dt class="col-sm-3">Especialidad</dt>
        <dd class="col-sm-9">{{ $docente->especialidad ?? '—' }}</dd>
      </dl>
    </div>
  </div>
</div>
@endsection
