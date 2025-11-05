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

        <dt class="col-sm-3">Asesor</dt>
        <dd class="col-sm-9">{{ optional($entrevista->asesorPedagogico)->nombre ? $entrevista->asesorPedagogico->nombre . ' ' . $entrevista->asesorPedagogico->apellido : '—' }}</dd>

        <dt class="col-sm-3">Observaciones</dt>
        <dd class="col-sm-9">{{ $entrevista->observaciones ?? '—' }}</dd>
      </dl>
    </div>
  </div>
</div>
@endsection
