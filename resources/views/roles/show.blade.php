@extends('layouts.app')

@section('title', 'Detalle de rol')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">{{ $rol->nombre }}</h1>
    <a href="{{ route('roles.index') }}" class="btn btn-secondary">Volver</a>
  </div>

  <div class="card">
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Descripción</dt>
        <dd class="col-sm-9">{{ $rol->descripcion ?? '—' }}</dd>

        <dt class="col-sm-3">Creado</dt>
        <dd class="col-sm-9">{{ $rol->created_at?->format('d/m/Y H:i') }}</dd>
      </dl>
    </div>
  </div>
</div>
@endsection
