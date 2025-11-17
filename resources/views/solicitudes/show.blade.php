@extends('layouts.app')

@section('title', 'Detalle de solicitud')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
    <div>
      <p class="text-muted text-uppercase small mb-1">Solicitud de</p>
      <h1 class="h3 mb-1">
        {{ optional($solicitud->estudiante)->nombre ?? 'Estudiante' }}
        {{ optional($solicitud->estudiante)->apellido ?? '' }}
      </h1>
      <p class="text-muted mb-0">
        Fecha de solicitud:
        {{ $solicitud->fecha_solicitud?->format('d/m/Y') ?? $solicitud->created_at?->format('d/m/Y') ?? 's/f' }}
      </p>
    </div>
    <a href="{{ route('solicitudes.index') }}" class="btn btn-secondary">Volver</a>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Estudiante</dt>
        <dd class="col-sm-9">
          {{ optional($solicitud->estudiante)->nombre ?? 'Sin nombre' }}
          {{ optional($solicitud->estudiante)->apellido ?? '' }}
          @if (optional($solicitud->estudiante)->rut)
            <span class="text-muted">({{ $solicitud->estudiante->rut }})</span>
          @endif
        </dd>

        <dt class="col-sm-3">Carrera</dt>
        <dd class="col-sm-9">{{ optional(optional($solicitud->estudiante)->carrera)->nombre ?? 'Sin carrera asignada' }}</dd>

        <dt class="col-sm-3">Asesora pedagógica</dt>
        <dd class="col-sm-9">
          @if (optional($solicitud->asesor)->nombre || optional($solicitud->asesor)->apellido)
            {{ $solicitud->asesor->nombre }} {{ $solicitud->asesor->apellido }}
          @else
            Sin asignar
          @endif
        </dd>

        <dt class="col-sm-3">Director de carrera</dt>
        <dd class="col-sm-9">
          @if (optional($solicitud->director)->nombre || optional($solicitud->director)->apellido)
            {{ $solicitud->director->nombre }} {{ $solicitud->director->apellido }}
          @else
            No asignado
          @endif
        </dd>

        <dt class="col-sm-3">Estado</dt>
        <dd class="col-sm-9">{{ $solicitud->estado ?? 'Sin estado' }}</dd>

        @if($solicitud->motivo_rechazo)
          <dt class="col-sm-3">Motivo de rechazo</dt>
          <dd class="col-sm-9">{{ $solicitud->motivo_rechazo }}</dd>
        @endif

        <dt class="col-sm-3">Descripción</dt>
        <dd class="col-sm-9">{{ $solicitud->descripcion ?? 'Sin descripción registrada' }}</dd>
      </dl>
    </div>
  </div>
</div>
@endsection
