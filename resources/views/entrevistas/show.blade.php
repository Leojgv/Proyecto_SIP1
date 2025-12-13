@extends('layouts.app')

@section('title', 'Detalle de entrevista')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Entrevista del {{ $entrevista->fecha?->format('d/m/Y') }}</h1>
    <a href="{{ route('entrevistas.index') }}" class="btn btn-secondary">Volver</a>
  </div>

  <div class="card">
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Estudiante</dt>
        <dd class="col-sm-9">{{ $entrevista->solicitud->estudiante->nombre ?? '—' }} {{ $entrevista->solicitud->estudiante->apellido ?? '' }}</dd>

        <dt class="col-sm-3">Asesora pedagogica</dt>
        <dd class="col-sm-9">{{ optional($entrevista->asesor)->nombre ? $entrevista->asesor->nombre . ' ' . $entrevista->asesor->apellido : '—' }}</dd>

        <dt class="col-sm-3">Modalidad</dt>
        <dd class="col-sm-9">
          @if($entrevista->modalidad)
            <span class="badge {{ $entrevista->modalidad === 'Virtual' ? 'bg-info' : 'bg-success' }}">
              {{ $entrevista->modalidad }}
            </span>
            @if(strtolower($entrevista->modalidad) === 'presencial')
              <div class="mt-2">
                <small class="text-muted d-block mb-1">
                  <i class="fas fa-map-marker-alt me-1"></i><strong>Lugar:</strong>
                </small>
                <div class="small">Sala 4to Piso, Edificio A</div>
              </div>
            @endif
          @else
            —
          @endif
        </dd>

        <dt class="col-sm-3">Observaciones</dt>
        <dd class="col-sm-9">{{ $entrevista->observaciones ?? '—' }}</dd>
      </dl>
    </div>
  </div>
</div>
@endsection

