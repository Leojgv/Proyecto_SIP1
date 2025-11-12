@extends('layouts.app')

@section('title', 'Detalle de solicitud')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Solicitud del {{ $solicitud->fecha_solicitud?->format('d/m/Y') }}</h1>
    <a href="{{ route('solicitudes.index') }}" class="btn btn-secondary">Volver</a>
  </div>

  <div class="card">
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Estudiante</dt>
        <dd class="col-sm-9">{{ $solicitud->estudiante->nombre ?? 'â€”' }} {{ $solicitud->estudiante->apellido ?? '' }}</dd>

        <dt class="col-sm-3">Asesora pedagogica</dt>
        <dd class="col-sm-9">{{ optional($solicitud->asesor)->nombre ? $solicitud->asesor->nombre . ' ' . $solicitud->asesor->apellido : 'â€”' }}</dd>

        <dt class="col-sm-3">Director de carrera</dt>
        <dd class="col-sm-9">{{ optional($solicitud->director)->nombre ? $solicitud->director->nombre . ' ' . $solicitud->director->apellido : 'â€”' }}</dd>

        <dt class="col-sm-3">Estado</dt>
        <dd class="col-sm-9">{{ $solicitud->estado ?? 'â€”' }}</dd>

        <dt class="col-sm-3">DescripciÃ³n</dt>
        <dd class="col-sm-9">{{ $solicitud->descripcion ?? 'â€”' }}</dd>
      </dl>
    </div>
  </div>
</div>
@endsection




