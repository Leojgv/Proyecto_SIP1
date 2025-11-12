@extends('layouts.app')

@section('title', 'Detalle de asignatura')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">{{ $asignatura->nombre }}</h1>
    <a href="{{ route('asignaturas.index') }}" class="btn btn-secondary">Volver</a>
  </div>

  <div class="card">
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Tipo</dt>
        <dd class="col-sm-9">{{ $asignatura->tipo ?? 'â€”' }}</dd>

        <dt class="col-sm-3">Estado</dt>
        <dd class="col-sm-9">{{ $asignatura->estado ?? 'â€”' }}</dd>

        <dt class="col-sm-3">Carrera</dt>
        <dd class="col-sm-9">{{ $asignatura->carrera->nombre ?? 'â€”' }}</dd>

        <dt class="col-sm-3">Asesora tecnica pedagogica</dt>
        <dd class="col-sm-9">{{ optional($asignatura->docente)->nombre ? $asignatura->docente->nombre . ' ' . $asignatura->docente->apellido : 'â€”' }}</dd>
      </dl>
    </div>
  </div>
</div>
@endsection

