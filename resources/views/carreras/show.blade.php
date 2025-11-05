@extends('layouts.app')

@section('title', 'Detalle de carrera')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">{{ $carrera->nombre }}</h1>
    <a href="{{ route('carreras.index') }}" class="btn btn-secondary">Volver</a>
  </div>

  <div class="card">
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Jornada</dt>
        <dd class="col-sm-9">{{ $carrera->jornada ?? '—' }}</dd>

        <dt class="col-sm-3">Grado</dt>
        <dd class="col-sm-9">{{ $carrera->grado ?? '—' }}</dd>

        <dt class="col-sm-3">Creada</dt>
        <dd class="col-sm-9">{{ $carrera->created_at?->format('d/m/Y H:i') }}</dd>
      </dl>
    </div>
  </div>
</div>
@endsection
