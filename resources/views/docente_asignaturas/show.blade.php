@extends('layouts.app')

@section('title', 'Detalle de asignación docente-asignatura')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Asignación</h1>
    <a href="{{ route('docente-asignaturas.index') }}" class="btn btn-secondary">Volver</a>
  </div>

  <div class="card">
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Docente</dt>
        <dd class="col-sm-9">{{ $asignacion->docente->nombre ?? '—' }} {{ $asignacion->docente->apellido ?? '' }}</dd>

        <dt class="col-sm-3">Asignatura</dt>
        <dd class="col-sm-9">{{ $asignacion->asignatura->nombre ?? '—' }}</dd>

        <dt class="col-sm-3">Carrera</dt>
        <dd class="col-sm-9">{{ $asignacion->carrera->nombre ?? '—' }}</dd>
      </dl>
    </div>
  </div>
</div>
@endsection
