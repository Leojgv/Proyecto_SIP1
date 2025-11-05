@extends('layouts.app')

@section('title', 'Detalle de director de carrera')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">{{ $director->nombre }} {{ $director->apellido }}</h1>
    <a href="{{ route('directores-carrera.index') }}" class="btn btn-secondary">Volver</a>
  </div>

  <div class="card">
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Correo</dt>
        <dd class="col-sm-9">{{ $director->email }}</dd>

        <dt class="col-sm-3">Teléfono</dt>
        <dd class="col-sm-9">{{ $director->telefono ?? '—' }}</dd>

        <dt class="col-sm-3">Carrera</dt>
        <dd class="col-sm-9">{{ $director->carrera->nombre ?? '—' }}</dd>
      </dl>
    </div>
  </div>
</div>
@endsection
