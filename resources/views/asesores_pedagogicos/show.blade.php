@extends('layouts.app')

@section('title', 'Detalle de asesor pedagógico')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">{{ $asesor->nombre }} {{ $asesor->apellido }}</h1>
    <a href="{{ route('asesores-pedagogicos.index') }}" class="btn btn-secondary">Volver</a>
  </div>

  <div class="card">
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Correo</dt>
        <dd class="col-sm-9">{{ $asesor->email }}</dd>

        <dt class="col-sm-3">Teléfono</dt>
        <dd class="col-sm-9">{{ $asesor->telefono ?? '—' }}</dd>

        <dt class="col-sm-3">Creado</dt>
        <dd class="col-sm-9">{{ $asesor->created_at?->format('d/m/Y H:i') }}</dd>
      </dl>
    </div>
  </div>
</div>
@endsection
