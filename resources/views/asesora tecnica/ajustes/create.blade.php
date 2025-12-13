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
    <p class="text-muted mb-0">Registra un ajuste razonable para un estudiante, se puede agregar mas de 1 ajuste.</p>
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
                Estudiante <span class="text-danger">*</span>
              </label>
              <select id="estudiante_id" name="estudiante_id" class="form-select @error('estudiante_id') is-invalid @enderror" required>
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
                Solicitud asociada <span class="text-danger">*</span>
              </label>
              <select id="solicitud_id" name="solicitud_id" class="form-select @error('solicitud_id') is-invalid @enderror" required>
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
                  <option value="{{ $solicitud->id }}" 
                          data-estudiante-id="{{ $solicitud->estudiante_id }}"
                          @selected(old('solicitud_id') == $solicitud->id)>
                    {{ $solicitud->fecha_solicitud?->format('d/m/Y') ?? 's/f' }} - {{ $solicitud->estudiante->nombre ?? '' }} {{ $solicitud->estudiante->apellido ?? '' }}
                    @if($textoMostrar)
                      | {{ $textoMostrar }}
                    @endif
                  </option>
                @endforeach
              </select>
              @error('solicitud_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
              <small class="text-muted d-block mt-2">
                <i class="fas fa-info-circle me-1"></i>Se mostrarán solo las solicitudes del estudiante seleccionado
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
                Tipo de Ajuste Razonable
              </label>
              <select id="tipo_ajuste" class="form-select">
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
                Al seleccionar un estudiante, se filtrarán los tipos de ajustes según su discapacidad
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
                  Nombre del ajuste <span class="text-danger">*</span>
                </label>
                <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" class="form-control @error('nombre') is-invalid @enderror" placeholder="Ej: Tiempo extendido para evaluaciones" required>
                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="text-muted d-block mt-2">
                  Puedes usar el selector de tipo de ajuste arriba para completar automáticamente este campo
                </small>
              </div>

              <div class="col-12">
                <label for="descripcion" class="form-label fw-semibold">
                  Descripción detallada <span class="text-danger">*</span>
                </label>
                <textarea id="descripcion" name="descripcion" rows="6" class="form-control @error('descripcion') is-invalid @enderror" placeholder="Describe los detalles específicos del ajuste razonable, cómo se implementará, qué recursos se necesitarán, etc..." required>{{ old('descripcion') }}</textarea>
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
                <small>Los campos marcados con <span class="text-danger">*</span> son obligatorios</small>
              </div>
              <div class="d-flex gap-2">
                <a href="{{ route('asesora-tecnica.dashboard') }}" class="btn btn-outline-secondary">
                  <i class="fas fa-times me-2"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-danger">
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
    background: transparent;
  }

  .form-section-card {
    border-radius: 14px;
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
    border: 1px solid #e5e7eb;
    transition: all 0.2s ease;
  }

  .form-select-lg:focus,
  .form-control-lg:focus {
    border-color: #dc2626;
    box-shadow: 0 0 0 0.2rem rgba(220, 38, 38, 0.15);
  }

  textarea.form-control {
    border-radius: 0.5rem;
    border: 1px solid #e5e7eb;
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

  .bg-danger {
    background: #dc2626 !important;
  }

  .bg-primary {
    background: #3b82f6 !important;
  }

  .bg-success {
    background: #10b981 !important;
  }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const estudianteSelect = document.getElementById('estudiante_id');
  const solicitudSelect = document.getElementById('solicitud_id');
  const tipoAjusteSelect = document.getElementById('tipo_ajuste');
  const nombreInput = document.getElementById('nombre');
  const descripcionTextarea = document.getElementById('descripcion');

  // Función para filtrar solicitudes por estudiante seleccionado
  function filtrarSolicitudesPorEstudiante(estudianteId) {
    const todasLasOpciones = solicitudSelect.querySelectorAll('option[data-estudiante-id]');
    const opcionVacia = solicitudSelect.querySelector('option[value=""]');
    
    // Mostrar la opción vacía
    if (opcionVacia) {
      opcionVacia.style.display = '';
    }
    
    if (estudianteId && estudianteId !== '') {
      // Ocultar todas las opciones primero
      todasLasOpciones.forEach(option => {
        option.style.display = 'none';
      });
      
      // Mostrar solo las solicitudes del estudiante seleccionado
      todasLasOpciones.forEach(option => {
        const optionEstudianteId = option.getAttribute('data-estudiante-id');
        if (optionEstudianteId === estudianteId) {
          option.style.display = '';
        }
      });
      
      // Limpiar la selección actual si no pertenece al estudiante
      const solicitudSeleccionada = solicitudSelect.value;
      if (solicitudSeleccionada) {
        const opcionSeleccionada = solicitudSelect.querySelector(`option[value="${solicitudSeleccionada}"]`);
        if (opcionSeleccionada && opcionSeleccionada.getAttribute('data-estudiante-id') !== estudianteId) {
          solicitudSelect.value = '';
        }
      }
    } else {
      // Si no hay estudiante seleccionado, mostrar todas las solicitudes
      todasLasOpciones.forEach(option => {
        option.style.display = '';
      });
      solicitudSelect.value = '';
    }
  }

  // Cuando se selecciona un estudiante, filtrar solicitudes y tipos de ajustes
  estudianteSelect.addEventListener('change', function() {
    const estudianteId = this.value;
    const selectedOption = this.options[this.selectedIndex];
    const discapacidad = selectedOption.getAttribute('data-discapacidad');
    
    // Filtrar solicitudes por estudiante (doble verificación)
    filtrarSolicitudesPorEstudiante(estudianteId);
    
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

  // Inicializar el filtro de solicitudes al cargar la página si ya hay un estudiante seleccionado
  if (estudianteSelect.value) {
    filtrarSolicitudesPorEstudiante(estudianteSelect.value);
  }

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
