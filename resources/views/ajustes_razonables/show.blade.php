@extends('layouts.app')

@section('title', 'Detalle de ajuste razonable')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">{{ $ajuste->nombre }}</h1>
    <a href="{{ route('ajustes-razonables.index') }}" class="btn btn-secondary">Volver</a>
  </div>

  <div class="card">
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Estudiante</dt>
        <dd class="col-sm-9">{{ $ajuste->estudiante->nombre ?? '—' }} {{ $ajuste->estudiante->apellido ?? '' }}</dd>

        <dt class="col-sm-3">Solicitud</dt>
        <dd class="col-sm-9">{{ $ajuste->solicitud->fecha_solicitud?->format('d/m/Y') ?? '—' }}</dd>

        <dt class="col-sm-3">Estado</dt>
        <dd class="col-sm-9">{{ $ajuste->estado ?? '—' }}</dd>

        <dt class="col-sm-3">Fecha de solicitud</dt>
        <dd class="col-sm-9">{{ $ajuste->fecha_solicitud?->format('d/m/Y') ?? '—' }}</dd>
      </dl>
    </div>
  </div>
</div>
@endsection
