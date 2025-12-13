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
      <a href="{{ route('director.casos') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Volver
      </a>
      @if(!in_array($solicitud->estado, ['Aprobado', 'Rechazado']))
        <button class="btn btn-sm btn-outline-danger" type="button" data-bs-toggle="collapse" data-bs-target="#rechazoForm" aria-expanded="{{ request('rechazar') ? 'true' : 'false' }}" aria-controls="rechazoForm">
          <i class="fas fa-times me-1"></i>Rechazar/Devolver a A. Pedagogica
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
        <dd class="col-sm-9">
          @if($solicitud->estado === 'Pendiente de Aprobación' || $solicitud->estado === 'Pendiente de Aprobacion')
            <span class="badge bg-warning text-dark">{{ $solicitud->estado ?? 'Sin estado' }}</span>
          @elseif($solicitud->estado === 'Aprobado')
            <span class="badge bg-success">{{ $solicitud->estado ?? 'Sin estado' }}</span>
          @elseif($solicitud->estado === 'Rechazado')
            <span class="badge bg-danger">{{ $solicitud->estado ?? 'Sin estado' }}</span>
          @else
            <span class="badge bg-secondary">{{ $solicitud->estado ?? 'Sin estado' }}</span>
          @endif
        </dd>

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
          <p class="text-muted small mb-3">Selecciona para cada ajuste si deseas aprobarlo o rechazarlo.</p>
          @if(!in_array($solicitud->estado, ['Aprobado', 'Rechazado']))
            <form action="{{ route('director.casos.approve', $solicitud) }}" method="POST" id="adjustmentsForm">
              @csrf
          @endif
          @forelse ($solicitud->ajustesRazonables as $ajuste)
            <div class="border rounded p-3 mb-3 bg-light" data-ajuste-id="{{ $ajuste->id }}">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="flex-grow-1">
                  <h6 class="fw-semibold mb-2">
                    @if(strtolower(trim($ajuste->estado ?? '')) === 'rechazado')
                      <i class="fas fa-times-circle text-danger me-2"></i>
                    @else
                      <i class="fas fa-check-circle text-success me-2"></i>
                    @endif
                    {{ $ajuste->nombre ?? 'Ajuste sin nombre' }}
                  </h6>
                  <p class="text-muted small mb-0">
                    {{ $ajuste->descripcion ?? 'No hay descripción disponible para este ajuste razonable.' }}
                  </p>
                </div>
                @if(!in_array($solicitud->estado, ['Aprobado', 'Rechazado']))
                  <div class="form-check form-check-inline ms-2">
                    <input class="form-check-input ajuste-radio" type="radio" name="ajustes[{{ $ajuste->id }}]" id="aprobado_{{ $ajuste->id }}" value="aprobado" checked data-ajuste-id="{{ $ajuste->id }}" data-ajuste-container="motivo_container_{{ $ajuste->id }}">
                    <label class="form-check-label" for="aprobado_{{ $ajuste->id }}">
                      <i class="fas fa-check text-success"></i> Aprobar
                    </label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input ajuste-radio" type="radio" name="ajustes[{{ $ajuste->id }}]" id="rechazado_{{ $ajuste->id }}" value="rechazado" data-ajuste-id="{{ $ajuste->id }}" data-ajuste-container="motivo_container_{{ $ajuste->id }}">
                    <label class="form-check-label" for="rechazado_{{ $ajuste->id }}">
                      <i class="fas fa-times text-danger"></i> Rechazar
                    </label>
                  </div>
                @else
                  @if($ajuste->estado === 'Aprobado')
                    <span class="badge bg-success ms-2">Aprobado</span>
                  @elseif($ajuste->estado === 'Rechazado')
                    <span class="badge bg-danger ms-2">Rechazado</span>
                  @else
                    <span class="badge bg-secondary ms-2">{{ $ajuste->estado }}</span>
                  @endif
                @endif
              </div>
              @if(!in_array($solicitud->estado, ['Aprobado', 'Rechazado']))
                <div class="motivo-rechazo-container" id="motivo_container_{{ $ajuste->id }}" style="display: none;">
                  <label for="motivo_{{ $ajuste->id }}" class="form-label small fw-semibold">
                    Motivo del rechazo <span class="text-danger">*</span>
                  </label>
                  <textarea 
                    name="motivos_rechazo[{{ $ajuste->id }}]" 
                    id="motivo_{{ $ajuste->id }}" 
                    class="form-control form-control-sm motivo-rechazo-input" 
                    rows="2" 
                    placeholder="Ingresa el motivo por el cual rechazas este ajuste..."
                    required
                  ></textarea>
                  <div class="invalid-feedback">El motivo de rechazo es obligatorio cuando se rechaza un ajuste.</div>
                </div>
              @else
                @php
                  $esRechazoDirectora = strtolower(trim($ajuste->estado ?? '')) === 'rechazado';
                @endphp
                @if($ajuste->motivo_rechazo && $esRechazoDirectora)
                  <div class="alert alert-warning small mt-2 mb-0">
                    <i class="fas fa-exclamation-triangle me-1"></i><strong>Motivo de rechazo:</strong> {{ $ajuste->motivo_rechazo }}
                  </div>
                @endif
              @endif
            </div>
          @empty
            <p class="text-muted mb-0">No hay ajustes registrados para este caso.</p>
          @endforelse
          @if(!in_array($solicitud->estado, ['Aprobado', 'Rechazado']))
            <div class="mt-3 pt-3 border-top">
              <button type="submit" form="adjustmentsForm" class="btn btn-sm btn-danger" id="approveButton">
                <i class="fas fa-check me-1"></i>Confirmar Selección de Ajustes
              </button>
            </div>
            </form>
          @endif
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-title mb-3">
            <i class="fas fa-file-alt text-danger me-2"></i>Resumen del Caso
          </h5>
          
          <div class="resumen-caso">
            <div class="resumen-caso__header mb-3">
              <h6 class="resumen-caso__titulo mb-2">
                {{ $solicitud->titulo ?? 'Resumen del Caso' }}
              </h6>
              <div class="resumen-caso__fecha">
                <i class="fas fa-calendar-alt me-1"></i>
                <span class="resumen-caso__fecha-texto">
                  @php
                    $fechaHora = null;
                    $primeraEntrevista = $solicitud->entrevistas->first();
                    if ($primeraEntrevista && $primeraEntrevista->fecha_hora_inicio) {
                      $fechaHora = $primeraEntrevista->fecha_hora_inicio;
                    } elseif ($solicitud->fecha_solicitud) {
                      $fechaHora = $solicitud->fecha_solicitud;
                    } else {
                      $fechaHora = $solicitud->created_at;
                    }
                  @endphp
                  @if($fechaHora)
                    {{ $fechaHora->format('d/m/Y') }}
                    @if($primeraEntrevista && $primeraEntrevista->fecha_hora_inicio)
                      <span class="text-muted ms-2">{{ $primeraEntrevista->fecha_hora_inicio->format('H:i') }}</span>
                    @endif
                  @else
                    Sin fecha
                  @endif
                </span>
              </div>
            </div>

            <div class="resumen-caso__descripcion mb-4">
              <p class="resumen-caso__descripcion-texto mb-0">
                {{ $solicitud->descripcion ?? 'No hay descripción disponible para este caso.' }}
              </p>
            </div>

            @if($solicitud->observaciones_pdf_ruta)
              <div class="resumen-caso__pdf">
                <a href="{{ asset('storage/' . $solicitud->observaciones_pdf_ruta) }}" target="_blank" class="btn btn-outline-danger btn-sm w-100">
                  <i class="fas fa-file-pdf me-2"></i>Ver Observaciones de la Entrevista (PDF)
                </a>
              </div>
            @else
              <div class="alert alert-light border mb-0">
                <i class="fas fa-info-circle me-2"></i>
                <small class="text-muted">No hay PDF de observaciones disponible para este caso.</small>
              </div>
            @endif
          </div>
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
            <button type="submit" class="btn btn-sm btn-outline-danger">
              <i class="fas fa-paper-plane me-1"></i>Enviar Devolución
            </button>
          </form>
        </div>
      </div>
    </div>
  @endif
</div>

@if(!in_array($solicitud->estado, ['Aprobado', 'Rechazado']))
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Manejar cambios en los radio buttons
  document.querySelectorAll('.ajuste-radio').forEach(function(radio) {
    radio.addEventListener('change', function() {
      const ajusteId = this.getAttribute('data-ajuste-id');
      if (!ajusteId) return;
      
      const containerId = this.getAttribute('data-ajuste-container') || 'motivo_container_' + ajusteId;
      const motivoContainer = document.getElementById(containerId);
      const motivoInput = document.getElementById('motivo_' + ajusteId);
      
      if (!motivoContainer || !motivoInput) return;
      
      // Ocultar o mostrar según la selección
      if (this.value === 'rechazado') {
        motivoContainer.style.display = 'block';
        motivoInput.required = true;
      } else if (this.value === 'aprobado') {
        motivoContainer.style.display = 'none';
        motivoInput.required = false;
        motivoInput.value = '';
        motivoInput.classList.remove('is-invalid');
      }
    });
    
    // Asegurar que el estado inicial sea correcto
    if (radio.value === 'aprobado' && radio.checked) {
      const ajusteId = radio.getAttribute('data-ajuste-id');
      if (ajusteId) {
        const containerId = radio.getAttribute('data-ajuste-container') || 'motivo_container_' + ajusteId;
        const motivoContainer = document.getElementById(containerId);
        if (motivoContainer) {
          motivoContainer.style.display = 'none';
        }
      }
    }
  });

  // Validar formulario antes de enviar
  const approveButton = document.getElementById('approveButton');
  const adjustmentsForm = document.getElementById('adjustmentsForm');
  
  if (approveButton && adjustmentsForm) {
    approveButton.addEventListener('click', function(e) {
      e.preventDefault();
      
      let isValid = true;
      let firstInvalid = null;
      const rechazados = document.querySelectorAll('.ajuste-radio[value="rechazado"]:checked');
      
      rechazados.forEach(function(radio) {
        const ajusteId = radio.getAttribute('data-ajuste-id');
        const motivoInput = document.getElementById('motivo_' + ajusteId);
        
        if (!motivoInput || !motivoInput.value.trim()) {
          if (motivoInput) {
            motivoInput.classList.add('is-invalid');
          }
          isValid = false;
          if (!firstInvalid && motivoInput) {
            firstInvalid = motivoInput;
          }
        } else {
          if (motivoInput) {
            motivoInput.classList.remove('is-invalid');
          }
        }
      });
      
      if (!isValid) {
        if (firstInvalid) {
          firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
          firstInvalid.focus();
        }
        alert('Por favor, completa el motivo de rechazo para todos los ajustes que has seleccionado rechazar.');
        return false;
      }
      
      adjustmentsForm.submit();
    });
  }
});
</script>
@endif

@push('styles')
<style>
  .resumen-caso {
    padding: 0;
  }
  .resumen-caso__header {
    padding-bottom: 1rem;
    border-bottom: 1px solid #e5e7eb;
  }
  .resumen-caso__titulo {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    line-height: 1.5;
  }
  .resumen-caso__fecha {
    display: flex;
    align-items: center;
    font-size: 0.875rem;
    color: #6b7280;
  }
  .resumen-caso__fecha i {
    color: #dc2626;
  }
  .resumen-caso__descripcion {
    min-height: 80px;
  }
  .resumen-caso__descripcion-texto {
    color: #374151;
    line-height: 1.6;
    font-size: 0.9375rem;
  }
  .resumen-caso__pdf .btn {
    transition: all 0.2s ease;
  }
  .resumen-caso__pdf .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(220, 38, 38, 0.2);
  }

  /* Modo Oscuro */
  .dark-mode .resumen-caso__header {
    border-bottom-color: #2d3748 !important;
  }
  .dark-mode .resumen-caso__titulo {
    color: #e8e8e8 !important;
  }
  .dark-mode .resumen-caso__fecha {
    color: #94a3b8 !important;
  }
  .dark-mode .resumen-caso__fecha i {
    color: #fca5a5 !important;
  }
  .dark-mode .resumen-caso__descripcion-texto {
    color: #cbd5e1 !important;
  }
  .dark-mode .resumen-caso__fecha-texto .text-muted {
    color: #64748b !important;
  }
  .dark-mode .alert-light {
    background-color: #16213e !important;
    border-color: #2d3748 !important;
    color: #cbd5e1 !important;
  }
  .dark-mode .alert-light .text-muted {
    color: #94a3b8 !important;
  }
  .dark-mode .btn-outline-danger {
    border-color: #dc2626 !important;
    color: #fca5a5 !important;
  }
  .dark-mode .btn-outline-danger:hover {
    background-color: #dc2626 !important;
    border-color: #dc2626 !important;
    color: #fff !important;
  }
</style>
@endpush
@endsection
