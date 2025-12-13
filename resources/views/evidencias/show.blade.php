@extends('layouts.app')

@section('title', 'Detalle de evidencia')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h3 mb-0">
        @php
          $tipoEvidencia = $evidencia->tipo ?? 'Evidencia';
          if ($tipoEvidencia === 'Documento médico/psicológico' || $tipoEvidencia === 'Documento medico/psicologico') {
            $tipoEvidencia = 'Documentos Adicionales';
          }
        @endphp
        {{ $tipoEvidencia }}
      </h1>
    <a href="{{ route('evidencias.index') }}" class="btn btn-secondary">Volver</a>
  </div>

  <div class="card">
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Solicitud</dt>
        <dd class="col-sm-9">{{ $evidencia->solicitud->fecha_solicitud?->format('d/m/Y') ?? '—' }}</dd>

        <dt class="col-sm-3">Descripción</dt>
        <dd class="col-sm-9">{{ $evidencia->descripcion ?? '—' }}</dd>

        <dt class="col-sm-3">Ruta de archivo</dt>
        <dd class="col-sm-9">{{ $evidencia->ruta_archivo ?? '—' }}</dd>
      </dl>
    </div>
  </div>
</div>
@endsection
