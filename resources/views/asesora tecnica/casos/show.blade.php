@extends('layouts.dashboard_asesoratecnica.app')

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
      <a href="{{ route('asesora-tecnica.casos.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Volver
      </a>
      @php
        $ajustesCount = $solicitud->ajustesRazonables()->count();
        $estadosPermitidos = ['Listo para Enviar', 'Pendiente de formulación de ajuste'];
        $puedeEnviarAPreaprobacion = in_array($solicitud->estado, $estadosPermitidos) && $ajustesCount > 0;
      @endphp
      @if($puedeEnviarAPreaprobacion)
        <form action="{{ route('asesora-tecnica.solicitudes.enviar-preaprobacion', $solicitud) }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('(Seguridad) ¿Estás seguro de enviar a Preaprobación?');">
            <i class="fas fa-paper-plane me-1"></i>Enviar a Preaprobación
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
              @if($solicitud->estado === 'Pendiente de formulación de ajuste')
                <span class="badge bg-warning text-dark">{{ $solicitud->estado ?? 'Sin estado' }}</span>
              @elseif($solicitud->estado === 'Listo para Enviar')
                <span class="badge bg-info">{{ $solicitud->estado ?? 'Sin estado' }}</span>
              @elseif($solicitud->estado === 'Pendiente de preaprobación')
                <span class="badge bg-primary">{{ $solicitud->estado ?? 'Sin estado' }}</span>
              @elseif($solicitud->estado === 'Aprobado')
                <span class="badge bg-success">{{ $solicitud->estado ?? 'Sin estado' }}</span>
              @elseif($solicitud->estado === 'Rechazado')
                <span class="badge bg-danger">{{ $solicitud->estado ?? 'Sin estado' }}</span>
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
                <div class="flex-grow-1">
                  <h6 class="mb-1 fw-semibold">
                    <i class="fas fa-check-circle text-success me-2"></i>{{ $ajuste->nombre ?? 'Ajuste sin nombre' }}
                  </h6>
                  <p class="text-muted small mb-1">{{ $ajuste->descripcion ?? 'sin desc' }}</p>
                  @if($ajuste->fecha_solicitud)
                    <small class="text-muted">
                      <i class="fas fa-calendar me-1"></i>Fecha de solicitud: {{ $ajuste->fecha_solicitud->format('d/m/Y') }}
                    </small>
                  @endif
                </div>
                <span class="badge 
                  @if($ajuste->estado === 'Aprobado') bg-success
                  @elseif($ajuste->estado === 'Rechazado') bg-danger
                  @elseif(str_contains($ajuste->estado ?? '', 'Pendiente')) bg-warning text-dark
                  @else bg-secondary
                  @endif ms-2">
                  {{ $ajuste->estado ?? 'Sin estado' }}
                </span>
              </div>
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
            <div class="border rounded p-3 mb-2 bg-light">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="flex-grow-1">
                  <strong class="d-block mb-1">
                    @php
                      $tipoEvidencia = $evidencia->tipo ?? 'Evidencia';
                      if ($tipoEvidencia === 'Documento médico/psicológico' || $tipoEvidencia === 'Documento medico/psicologico') {
                        $tipoEvidencia = 'Documentos Adicionales';
                      }
                    @endphp
                    {{ $tipoEvidencia }}
                  </strong>
                  @if($evidencia->descripcion)
                    <p class="text-muted mb-2 small">{{ $evidencia->descripcion }}</p>
                  @endif
                </div>
              </div>
              @if($evidencia->ruta_archivo)
                @php
                  $evidenciaUrl = asset('storage/' . $evidencia->ruta_archivo);
                @endphp
                <a href="{{ $evidenciaUrl }}" target="_blank" class="btn btn-sm btn-outline-danger">
                  <i class="fas fa-file-pdf me-1"></i>Ver Documento
                </a>
              @else
                <span class="text-muted small">Sin documento adjunto</span>
              @endif
            </div>
          @empty
            <p class="text-muted small mb-3">Sin evidencias registradas.</p>
          @endforelse

          <p class="mb-2 fw-semibold mt-3">Entrevistas</p>
          @forelse ($solicitud->entrevistas as $entrevista)
            <div class="border rounded p-3 mb-2 bg-light">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="flex-grow-1">
                  <strong class="d-block mb-1">Entrevista</strong>
                  <p class="text-muted mb-0 small">
                    @if($entrevista->fecha || $entrevista->fecha_hora_inicio)
                      <i class="fas fa-calendar me-1"></i>{{ $entrevista->fecha_hora_inicio?->format('d/m/Y') ?? ($entrevista->fecha?->format('d/m/Y') ?? 'Fecha no definida') }}
                    @endif
                    @if($entrevista->asesor)
                      <br><i class="fas fa-user me-1"></i>{{ $entrevista->asesor->nombre }} {{ $entrevista->asesor->apellido }}
                    @endif
                  </p>
                </div>
              </div>
              @if($solicitud->observaciones_pdf_ruta)
                @php
                  $observacionesUrl = asset('storage/' . $solicitud->observaciones_pdf_ruta);
                @endphp
                <a href="{{ $observacionesUrl }}" target="_blank" class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-file-pdf me-1"></i>Ver Observaciones PDF
                </a>
              @else
                <span class="text-muted small">Sin documento de observaciones</span>
              @endif
            </div>
          @empty
            <p class="text-muted small mb-0">Sin entrevistas registradas.</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

