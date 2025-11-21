@extends('layouts.dashboard_asesorapedagogica.app')

@section('title', 'Detalle de Caso')

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

  <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
    <div>
      <p class="text-muted text-uppercase small mb-1">Caso de</p>
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
      <a href="{{ route('asesora-pedagogica.casos.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Volver
      </a>
      @if($solicitud->estado === 'Pendiente de preaprobación')
        <form action="{{ route('asesora-pedagogica.casos.enviar-director', $solicitud) }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de enviar este caso a Dirección de Carrera para aprobación final?');">
            <i class="fas fa-paper-plane me-2"></i>Enviar a Dirección
          </button>
        </form>
      @endif
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
          <h5 class="card-title mb-3">Información del Caso</h5>
          <dl class="row mb-0">
            <dt class="col-sm-4">Estudiante</dt>
            <dd class="col-sm-8">
              {{ optional($solicitud->estudiante)->nombre ?? 'Sin nombre' }}
              {{ optional($solicitud->estudiante)->apellido ?? '' }}
              @if (optional($solicitud->estudiante)->rut)
                <span class="text-muted">({{ $solicitud->estudiante->rut }})</span>
              @endif
            </dd>

            <dt class="col-sm-4">Carrera</dt>
            <dd class="col-sm-8">{{ optional(optional($solicitud->estudiante)->carrera)->nombre ?? 'Sin carrera asignada' }}</dd>

            <dt class="col-sm-4">Asesora Técnica</dt>
            <dd class="col-sm-8">
              @if (optional($solicitud->asesor)->nombre || optional($solicitud->asesor)->apellido)
                {{ $solicitud->asesor->nombre }} {{ $solicitud->asesor->apellido }}
              @else
                Sin asignar
              @endif
            </dd>

            <dt class="col-sm-4">Director de carrera</dt>
            <dd class="col-sm-8">
              @if (optional($solicitud->director)->nombre || optional($solicitud->director)->apellido)
                {{ $solicitud->director->nombre }} {{ $solicitud->director->apellido }}
              @else
                No asignado
              @endif
            </dd>

            <dt class="col-sm-4">Estado</dt>
            <dd class="col-sm-8">
              @if($solicitud->estado === 'Pendiente de preaprobación')
                <span class="badge bg-warning text-dark fs-6">{{ $solicitud->estado ?? 'Sin estado' }}</span>
              @else
                <span class="badge bg-secondary">{{ $solicitud->estado ?? 'Sin estado' }}</span>
              @endif
            </dd>

            @if ($solicitud->motivo_rechazo)
              <dt class="col-sm-4">Motivo de devolución</dt>
              <dd class="col-sm-8">
                <div class="alert alert-warning mb-0">
                  <i class="fas fa-exclamation-triangle me-2"></i>{{ $solicitud->motivo_rechazo }}
                </div>
              </dd>
            @endif

            <dt class="col-sm-4">Descripción</dt>
            <dd class="col-sm-8">{{ $solicitud->descripcion ?? 'Sin descripción registrada' }}</dd>
          </dl>
        </div>
      </div>

      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
          <h5 class="card-title mb-3">Ajustes Razonables</h5>
          @forelse ($solicitud->ajustesRazonables as $ajuste)
            <div class="border rounded p-3 mb-3">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="mb-0 fw-semibold">
                  <i class="fas fa-check-circle text-success me-2"></i>{{ $ajuste->nombre ?? 'Ajuste sin nombre' }}
                </h6>
                <span class="badge 
                  @if($ajuste->estado === 'Aprobado') bg-success
                  @elseif($ajuste->estado === 'Rechazado') bg-danger
                  @elseif(str_contains($ajuste->estado ?? '', 'Pendiente')) bg-warning text-dark
                  @else bg-secondary
                  @endif">
                  {{ $ajuste->estado ?? 'Sin estado' }}
                </span>
              </div>
              @if($ajuste->fecha_solicitud)
                <small class="text-muted">
                  <i class="fas fa-calendar me-1"></i>Fecha de solicitud: {{ $ajuste->fecha_solicitud->format('d/m/Y') }}
                </small>
              @endif
            </div>
          @empty
            <p class="text-muted mb-0">No hay ajustes registrados para este caso.</p>
          @endforelse
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
          <h5 class="card-title mb-3">Evidencias / Entrevistas</h5>
          
          <p class="mb-2 fw-semibold">Evidencias</p>
          @forelse ($solicitud->evidencias as $evidencia)
            <div class="border rounded p-2 mb-2">
              <strong>{{ $evidencia->titulo ?? 'Evidencia' }}</strong>
              <p class="text-muted mb-0 small">{{ $evidencia->descripcion ?? 'Sin descripción' }}</p>
            </div>
          @empty
            <p class="text-muted small mb-3">Sin evidencias registradas.</p>
          @endforelse

          <p class="mb-2 fw-semibold mt-3">Entrevistas</p>
          @forelse ($solicitud->entrevistas as $entrevista)
            <div class="border rounded p-2 mb-2">
              <strong>{{ $entrevista->titulo ?? 'Entrevista' }}</strong>
              <p class="text-muted mb-0 small">
                @if($entrevista->fecha)
                  <i class="fas fa-calendar me-1"></i>{{ $entrevista->fecha->format('d/m/Y') }}
                @endif
                @if($entrevista->asesor)
                  <br><i class="fas fa-user me-1"></i>{{ $entrevista->asesor->nombre }} {{ $entrevista->asesor->apellido }}
                @endif
              </p>
            </div>
          @empty
            <p class="text-muted small mb-0">Sin entrevistas registradas.</p>
          @endforelse
        </div>
      </div>

      @if($solicitud->estado === 'Pendiente de preaprobación')
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h6 class="card-title mb-3">
              <i class="fas fa-undo text-warning me-2"></i>Devolver al Asesor Técnico
            </h6>
            <p class="text-muted small mb-3">
              Si necesitas que el Asesor Técnico realice correcciones, puedes devolver el caso.
            </p>
            <button 
              class="btn btn-outline-warning btn-sm w-100" 
              type="button" 
              data-bs-toggle="collapse" 
              data-bs-target="#devolverForm" 
              aria-expanded="false" 
              aria-controls="devolverForm"
            >
              <i class="fas fa-arrow-left me-2"></i>Devolver para Correcciones
            </button>
            
            <div class="collapse mt-3" id="devolverForm">
              <form action="{{ route('asesora-pedagogica.casos.devolver-actt', $solicitud) }}" method="POST">
                @csrf
                <div class="mb-3">
                  <label for="motivo_devolucion" class="form-label">Motivo de devolución <span class="text-danger">*</span></label>
                  <textarea 
                    name="motivo_devolucion" 
                    id="motivo_devolucion" 
                    rows="4" 
                    class="form-control @error('motivo_devolucion') is-invalid @enderror" 
                    placeholder="Describe los motivos por los que necesitas devolver el caso al Asesor Técnico..." 
                    required
                  >{{ old('motivo_devolucion') }}</textarea>
                  @error('motivo_devolucion')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  <small class="text-muted">Mínimo 10 caracteres</small>
                </div>
                <button type="submit" class="btn btn-warning w-100" onclick="return confirm('¿Estás seguro de devolver este caso al Asesor Técnico?');">
                  <i class="fas fa-paper-plane me-2"></i>Enviar Devolución
                </button>
              </form>
            </div>
          </div>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection

