@extends('layouts.Dashboard_director.app')

@section('title', 'Detalle de caso')

@section('content')
<div class="dashboard-page">
  @if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
    <div>
      <p class="text-muted text-uppercase small mb-1">Solicitud de</p>
      <h1 class="h4 mb-1">
        {{ optional($solicitud->estudiante)->nombre ?? 'Estudiante' }}
        {{ optional($solicitud->estudiante)->apellido ?? '' }}
      </h1>
      <p class="text-muted mb-0">
        Fecha de solicitud:
        {{ $solicitud->fecha_solicitud?->format('d/m/Y') ?? $solicitud->created_at?->format('d/m/Y') ?? 's/f' }}
      </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a href="{{ route('director.casos') }}" class="btn btn-secondary">Volver</a>
      @if(!in_array($solicitud->estado, ['Aprobado', 'Rechazado']))
        <form action="{{ route('director.casos.approve', $solicitud) }}" method="POST">
          @csrf
          <button type="submit" class="btn btn-danger">Aprobar Caso</button>
        </form>
        <button class="btn btn-outline-danger" type="button" data-bs-toggle="collapse" data-bs-target="#rechazoForm" aria-expanded="{{ request('rechazar') ? 'true' : 'false' }}" aria-controls="rechazoForm">
          Rechazar/Devolver a A. Pedagogica
        </button>
      @endif
    </div>
  </div>

  <div class="card border-0 shadow-sm mb-3">
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

        @if ($solicitud->motivo_rechazo)
          <dt class="col-sm-3">Motivo de rechazo</dt>
          <dd class="col-sm-9">{{ $solicitud->motivo_rechazo }}</dd>
        @endif

        <dt class="col-sm-3">Descripción</dt>
        <dd class="col-sm-9">{{ $solicitud->descripcion ?? 'Sin descripción registrada' }}</dd>
      </dl>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-title mb-3">Ajustes razonables</h5>
          @forelse ($solicitud->ajustesRazonables as $ajuste)
            <div class="border rounded p-2 mb-2">
              <strong>{{ $ajuste->nombre ?? 'Ajuste sin nombre' }}</strong>
              <p class="text-muted mb-0 small">{{ $ajuste->descripcion ?? 'Sin descripción' }}</p>
            </div>
          @empty
            <p class="text-muted mb-0">No hay ajustes registrados.</p>
          @endforelse
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-title mb-3">Evidencias / Entrevistas</h5>
          <p class="mb-1 fw-semibold">Evidencias</p>
          @forelse ($solicitud->evidencias as $evidencia)
            <div class="border rounded p-2 mb-2">
              <strong>{{ $evidencia->titulo ?? 'Evidencia' }}</strong>
              <p class="text-muted mb-0 small">{{ $evidencia->descripcion ?? 'Sin descripción' }}</p>
            </div>
          @empty
            <p class="text-muted small mb-3">Sin evidencias registradas.</p>
          @endforelse

          <p class="mb-1 fw-semibold">Entrevistas</p>
          @forelse ($solicitud->entrevistas as $entrevista)
            <div class="border rounded p-2 mb-2">
              <strong>{{ $entrevista->titulo ?? 'Entrevista' }}</strong>
              <p class="text-muted mb-0 small">{{ $entrevista->descripcion ?? 'Sin descripción' }}</p>
            </div>
          @empty
            <p class="text-muted small mb-0">Sin entrevistas registradas.</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  @if(!in_array($solicitud->estado, ['Aprobado', 'Rechazado']))
    <div class="collapse {{ request('rechazar') ? 'show' : '' }}" id="rechazoForm">
      <div class="card border-0 shadow-sm mt-3">
        <div class="card-body">
          <h5 class="card-title">Rechazar/Devolver a A. Pedagogica</h5>
          <p class="text-muted small mb-3">Ingresa el motivo para enviar de vuelta a la Asesora Pedagógica y al estudiante.</p>
          <form action="{{ route('director.casos.reject', $solicitud) }}" method="POST">
            @csrf
            <div class="mb-3">
              <label class="form-label">Motivo del rechazo</label>
              <textarea name="motivo_rechazo" rows="3" class="form-control @error('motivo_rechazo') is-invalid @enderror" required>{{ old('motivo_rechazo') }}</textarea>
              @error('motivo_rechazo')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <button type="submit" class="btn btn-outline-danger">Rechazar/Devolver a A. Pedagogica</button>
          </form>
        </div>
      </div>
    </div>
  @endif
</div>
@endsection
