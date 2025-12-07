@extends('layouts.dashboard_asesoratecnica.app')

@section('title', 'Formular ajuste')

@section('content')
<div class="dashboard-page">
  @if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="page-header mb-4">
    <h1 class="h4 mb-1">Formular ajuste</h1>
    <p class="text-muted mb-0">Registra un ajuste razonable para un estudiante.</p>
  </div>

  <form action="{{ route('asesora-tecnica.ajustes.store') }}" method="POST">
    @csrf
    
    <div class="row g-4">
      <!-- Sección: Información del Estudiante -->
      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100 form-section-card">
          <div class="card-header bg-danger text-white">
            <div class="d-flex align-items-center gap-2">
              <i class="fas fa-user-graduate"></i>
              <h5 class="mb-0">Información del Estudiante</h5>
            </div>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label for="estudiante_id" class="form-label fw-semibold">
                <i class="fas fa-user me-2 text-danger"></i>Estudiante <span class="text-danger">*</span>
              </label>
              <select id="estudiante_id" name="estudiante_id" class="form-select form-select-lg @error('estudiante_id') is-invalid @enderror" required>
                <option value="">Selecciona un estudiante</option>
                @foreach($estudiantes as $estudiante)
                  @php
                    $discapacidad = isset($estudiante->tipo_discapacidad) && $estudiante->tipo_discapacidad 
                      ? $estudiante->tipo_discapacidad 
                      : '';
                  @endphp
                  <option value="{{ $estudiante->id }}" 
                          data-discapacidad="{{ $discapacidad }}"
                          @selected(old('estudiante_id') == $estudiante->id)>
                    {{ $estudiante->nombre }} {{ $estudiante->apellido }} - {{ $estudiante->carrera->nombre ?? 'Sin carrera' }}
                    @if($discapacidad)
                      ({{ $discapacidad }})
                    @endif
                  </option>
                @endforeach
              </select>
              @error('estudiante_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-0">
              <label for="solicitud_id" class="form-label fw-semibold">
                <i class="fas fa-file-alt me-2 text-danger"></i>Solicitud asociada <span class="text-danger">*</span>
              </label>
              <select id="solicitud_id" name="solicitud_id" class="form-select form-select-lg @error('solicitud_id') is-invalid @enderror" required>
                <option value="">Selecciona una solicitud</option>
                @foreach($solicitudes as $solicitud)
                  @php
                    $titulo = $solicitud->titulo ?? '';
                    $descripcionCompleta = $solicitud->descripcion ?? '';
                    if (!$titulo && preg_match('/^\[(.+?)\]/', $descripcionCompleta, $matches)) {
                      $titulo = $matches[1];
                      $descripcionSinTitulo = trim(substr($descripcionCompleta, strlen($matches[0])));
                    } else {
                      $descripcionSinTitulo = $descripcionCompleta;
                    }
                    $textoMostrar = $titulo ?: $descripcionSinTitulo;
                    $textoMostrar = \Illuminate\Support\Str::limit($textoMostrar, 60);
                  @endphp
                  <option value="{{ $solicitud->id }}" @selected(old('solicitud_id') == $solicitud->id)>
                    {{ $solicitud->fecha_solicitud?->format('d/m/Y') ?? 's/f' }} - {{ $solicitud->estudiante->nombre ?? '' }} {{ $solicitud->estudiante->apellido ?? '' }}
                    @if($textoMostrar)
                      | {{ $textoMostrar }}
                    @endif
                  </option>
                @endforeach
              </select>
              @error('solicitud_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
              <small class="text-muted d-block mt-2">
                <i class="fas fa-info-circle me-1"></i>Se muestra la fecha, estudiante y título/descripción de la solicitud
              </small>
            </div>
          </div>
        </div>
      </div>

      <!-- Sección: Tipo de Ajuste -->
      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100 form-section-card">
          <div class="card-header bg-primary text-white">
            <div class="d-flex align-items-center gap-2">
              <i class="fas fa-list-ul"></i>
              <h5 class="mb-0">Tipo de Ajuste</h5>
            </div>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label for="tipo_ajuste" class="form-label fw-semibold">
                <i class="fas fa-tags me-2 text-primary"></i>Tipo de Ajuste Razonable
              </label>
              <select id="tipo_ajuste" class="form-select form-select-lg">
                <option value="">Selecciona un tipo de ajuste (se sugerirán según la discapacidad del estudiante)</option>
                @php
                  // Tipos de ajustes razonables según discapacidad
                  $tiposAjustes = [
                    'Discapacidad Visual' => [
                      'Materiales en formato ampliado',
                      'Materiales en Braille',
                      'Uso de lectores de pantalla',
                      'Tiempo extendido para evaluaciones',
                      'Asistente para lectura',
                      'Materiales con alto contraste',
                      'Uso de tecnología asistiva (lupas, magnificadores)',
                    ],
                    'Discapacidad Auditiva' => [
                      'Intérprete de lengua de señas',
                      'Materiales visuales complementarios',
                      'Apoyo con subtítulos en videos',
                      'Ubicación preferencial en aula',
                      'Uso de sistema de frecuencia modulada (FM)',
                      'Apoyo con tomador de notas',
                    ],
                    'Discapacidad Motora' => [
                      'Acceso físico adaptado',
                      'Tiempo extendido para tareas y evaluaciones',
                      'Uso de tecnología asistiva',
                      'Alternativas para tareas escritas',
                      'Asistente para toma de notas',
                      'Adaptación de espacios físicos',
                    ],
                    'Discapacidad Intelectual' => [
                      'Instrucciones simplificadas',
                      'Tiempo extendido para tareas',
                      'Materiales adaptados',
                      'Apoyo con tutoría',
                      'Evaluaciones diferenciadas',
                      'Rutinas estructuradas y predecibles',
                    ],
                    'Trastorno del Espectro Autista (TEA)' => [
                      'Rutinas estructuradas',
                      'Espacios de descanso sensorial',
                      'Comunicación clara y directa',
                      'Anticipación de cambios',
                      'Apoyo en interacciones sociales',
                      'Materiales visuales para organización',
                    ],
                    'Trastorno por Déficit de Atención e Hiperactividad (TDAH)' => [
                      'Asientos preferenciales',
                      'Pausas frecuentes',
                      'Instrucciones por escrito',
                      'Organizadores visuales',
                      'Tiempo extendido para tareas',
                      'Apoyo en organización y planificación',
                    ],
                    'Discapacidad Psicosocial' => [
                      'Flexibilidad en asistencia',
                      'Pausas cuando sea necesario',
                      'Ambiente de apoyo y comprensión',
                      'Comunicación abierta',
                      'Apoyo en gestión del estrés',
                      'Plazos flexibles cuando corresponda',
                    ],
                    'Otra' => [
                      'Ajuste personalizado según necesidad específica',
                    ],
                  ];
                @endphp
                @foreach($tiposAjustes as $discapacidad => $ajustes)
                  <optgroup label="{{ $discapacidad }}">
                    @foreach($ajustes as $ajuste)
                      <option value="{{ $ajuste }}" data-discapacidad="{{ $discapacidad }}">{{ $ajuste }}</option>
                    @endforeach
                  </optgroup>
                @endforeach
              </select>
              <small class="text-muted d-block mt-2">
                <i class="fas fa-lightbulb me-1"></i>Al seleccionar un estudiante, se filtrarán los tipos de ajustes según su discapacidad
              </small>
            </div>

          </div>
        </div>
      </div>

      <!-- Sección: Detalles del Ajuste -->
      <div class="col-12">
        <div class="card border-0 shadow-sm form-section-card">
          <div class="card-header bg-success text-white">
            <div class="d-flex align-items-center gap-2">
              <i class="fas fa-edit"></i>
              <h5 class="mb-0">Detalles del Ajuste</h5>
            </div>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-12">
                <label for="nombre" class="form-label fw-semibold">
                  <i class="fas fa-heading me-2 text-success"></i>Nombre del ajuste <span class="text-danger">*</span>
                </label>
                <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" class="form-control form-control-lg @error('nombre') is-invalid @enderror" placeholder="Ej: Tiempo extendido para evaluaciones" required>
                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="text-muted d-block mt-2">
                  <i class="fas fa-magic me-1"></i>Puedes usar el selector de tipo de ajuste arriba para completar automáticamente este campo
                </small>
              </div>

              <div class="col-12">
                <label for="descripcion" class="form-label fw-semibold">
                  <i class="fas fa-align-left me-2 text-success"></i>Descripción detallada
                </label>
                <textarea id="descripcion" name="descripcion" rows="6" class="form-control @error('descripcion') is-invalid @enderror" placeholder="Describe los detalles específicos del ajuste razonable, cómo se implementará, qué recursos se necesitarán, etc...">{{ old('descripcion') }}</textarea>
                @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Botones de acción -->
      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
              <div class="text-muted">
                <i class="fas fa-info-circle me-2"></i>
                <small>Los campos marcados con <span class="text-danger">*</span> son obligatorios</small>
              </div>
              <div class="d-flex gap-2">
                <a href="{{ route('asesora-tecnica.dashboard') }}" class="btn btn-lg btn-outline-secondary">
                  <i class="fas fa-times me-2"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-lg btn-danger">
                  <i class="fas fa-save me-2"></i>Guardar Ajuste
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

@push('styles')
<style>
  .dashboard-page {
    background: transparentq;
    padding: 1.5rem;
    border-radius: 1.5rem;
  }


  .form-section-card {
    border: 1px solid #e5e7eb;
    border-radius: 1rem;
    overflow: hidden;
  }

  .form-section-card .card-header {
    border-radius: 0;
    padding: 1rem 1.5rem;
    font-weight: 600;
  }

  .form-section-card .card-body {
    padding: 1.5rem;
  }

  .form-label {
    margin-bottom: 0.75rem;
    color: #374151;
    font-size: 0.95rem;
  }

  .form-select-lg,
  .form-control-lg {
    border-radius: 0.5rem;
    border: 2px solid #e5e7eb;
    transition: all 0.2s ease;
  }

  .form-select-lg:focus,
  .form-control-lg:focus {
    border-color: #dc2626;
    box-shadow: 0 0 0 0.2rem rgba(220, 38, 38, 0.15);
  }

  textarea.form-control {
    border-radius: 0.5rem;
    border: 2px solid #e5e7eb;
    transition: all 0.2s ease;
    resize: vertical;
  }

  textarea.form-control:focus {
    border-color: #dc2626;
    box-shadow: 0 0 0 0.2rem rgba(220, 38, 38, 0.15);
  }

  .btn-lg {
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
  }

  .btn-lg:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }

  .bg-danger {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
  }

  .bg-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
  }

  .bg-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
  }

  @media (max-width: 768px) {
    .dashboard-page {
      padding: 1rem;
    }
  }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const estudianteSelect = document.getElementById('estudiante_id');
  const tipoAjusteSelect = document.getElementById('tipo_ajuste');
  const nombreInput = document.getElementById('nombre');
  const descripcionTextarea = document.getElementById('descripcion');

  // Cuando se selecciona un estudiante, filtrar tipos de ajustes
  estudianteSelect.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const discapacidad = selectedOption.getAttribute('data-discapacidad');
    
    if (discapacidad && discapacidad.trim() !== '') {
      // Filtrar opciones del select de tipo de ajuste
      const options = tipoAjusteSelect.querySelectorAll('optgroup, option[data-discapacidad]');
      
      // Mostrar todas las opciones primero
      tipoAjusteSelect.querySelectorAll('optgroup, option[data-discapacidad]').forEach(opt => {
        if (opt.tagName === 'OPTGROUP') {
          opt.style.display = '';
        } else {
          opt.style.display = '';
        }
      });

      // Ocultar opciones que no coincidan
      tipoAjusteSelect.querySelectorAll('option[data-discapacidad]').forEach(option => {
        const optionDiscapacidad = option.getAttribute('data-discapacidad');
        if (optionDiscapacidad && optionDiscapacidad !== discapacidad && optionDiscapacidad !== 'Otra') {
          option.style.display = 'none';
        }
      });

      // Ocultar optgroups que no tengan opciones visibles
      tipoAjusteSelect.querySelectorAll('optgroup').forEach(group => {
        const visibleOptions = Array.from(group.querySelectorAll('option[data-discapacidad]')).filter(
          opt => opt.style.display !== 'none'
        );
        if (visibleOptions.length === 0 && group.label !== discapacidad && group.label !== 'Otra') {
          group.style.display = 'none';
        }
      });

      // Mostrar mensaje si hay ajustes disponibles
      const availableGroups = Array.from(tipoAjusteSelect.querySelectorAll('optgroup')).filter(
        group => group.style.display !== 'none'
      );
      
      if (availableGroups.length > 0) {
        tipoAjusteSelect.disabled = false;
      }
    } else {
      // Si no hay discapacidad registrada, mostrar todas las opciones
      tipoAjusteSelect.querySelectorAll('optgroup, option[data-discapacidad]').forEach(opt => {
        opt.style.display = '';
      });
    }

    // Resetear selección de tipo de ajuste
    tipoAjusteSelect.value = '';
  });

  // Cuando se selecciona un tipo de ajuste, completar automáticamente el nombre
  tipoAjusteSelect.addEventListener('change', function() {
    if (this.value) {
      nombreInput.value = this.value;
      nombreInput.dispatchEvent(new Event('input'));
    }
  });

  // También permitir sugerencias en el campo de descripción
  tipoAjusteSelect.addEventListener('change', function() {
    if (this.value && !descripcionTextarea.value) {
      descripcionTextarea.placeholder = 'Detalla cómo se implementará: ' + this.value + '...';
    }
  });
});
</script>
@endpush

@endsection
