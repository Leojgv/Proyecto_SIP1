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
      <div class="col-12">
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
              
              {{-- Barra de búsqueda --}}
              <div class="position-relative mb-2">
                <input 
                  type="text" 
                  id="buscarEstudiante" 
                  class="form-control" 
                  placeholder="Buscar estudiante por nombre, apellido, carrera o RUT..."
                  autocomplete="off"
                >
                <i class="fas fa-search position-absolute" style="right: 12px; top: 50%; transform: translateY(-50%); color: #6c757d; pointer-events: none;"></i>
              </div>
              
              <select id="estudiante_id" name="estudiante_id" class="form-select @error('estudiante_id') is-invalid @enderror" size="8" style="max-height: 200px; overflow-y: auto;" required>
                <option value="">Selecciona un estudiante</option>
                @foreach($estudiantes as $estudiante)
                  @php
                    $discapacidad = isset($estudiante->tipo_discapacidad) && $estudiante->tipo_discapacidad 
                      ? $estudiante->tipo_discapacidad 
                      : '';
                    $textoCompleto = strtolower($estudiante->nombre . ' ' . $estudiante->apellido . ' ' . ($estudiante->carrera->nombre ?? 'Sin carrera'));
                    if($discapacidad) {
                      $textoCompleto .= ' ' . strtolower($discapacidad);
                    }
                    if($estudiante->rut) {
                      $textoCompleto .= ' ' . strtolower(str_replace(['.', '-'], '', $estudiante->rut));
                    }
                  @endphp
                  <option value="{{ $estudiante->id }}" 
                          data-discapacidad="{{ $discapacidad }}"
                          data-texto-busqueda="{{ $textoCompleto }}"
                          @selected(old('estudiante_id') == $estudiante->id)>
                    {{ $estudiante->nombre }} {{ $estudiante->apellido }} - {{ $estudiante->carrera->nombre ?? 'Sin carrera' }}
                    @if($discapacidad)
                      ({{ $discapacidad }})
                    @endif
                    @if($estudiante->rut)
                      - {{ $estudiante->rut }}
                    @endif
                  </option>
                @endforeach
              </select>
              @error('estudiante_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
              <small class="text-muted d-block mt-2">
                <i class="fas fa-info-circle me-1"></i>Escribe en la barra de búsqueda para filtrar estudiantes
              </small>
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

      <!-- Contenedor de Ajustes -->
      <div class="col-12">
        <div id="ajustes-container">
          <!-- Primer Ajuste -->
          <div class="ajuste-item mb-4" data-ajuste-index="0">
            <div class="card border-0 shadow-sm form-section-card">
              <div class="card-header bg-success text-white">
                <div class="d-flex align-items-center justify-content-between">
                  <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-edit"></i>
                    <h5 class="mb-0">Detalles del Ajuste <span class="ajuste-numero">#1</span></h5>
                  </div>
                  <button type="button" class="btn btn-sm btn-light btn-eliminar-ajuste" style="display: none;">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div class="row g-3">
                  {{-- Tipo de Ajuste dentro de cada ajuste --}}
                  <div class="col-12">
                    <label class="form-label fw-semibold">
                      Tipo de Ajuste Razonable
                    </label>
                    
                    {{-- Barra de búsqueda para tipos de ajuste --}}
                    <div class="position-relative mb-2">
                      <input 
                        type="text" 
                        class="form-control buscar-tipo-ajuste" 
                        placeholder="Buscar tipo de ajuste por nombre o categoría..."
                        autocomplete="off"
                      >
                      <i class="fas fa-search position-absolute" style="right: 12px; top: 50%; transform: translateY(-50%); color: #6c757d; pointer-events: none;"></i>
                    </div>
                    
                    <select class="form-select tipo-ajuste-select" size="6" style="max-height: 150px; overflow-y: auto;">
                      <option value="">Selecciona un tipo de ajuste (se sugerirán según la discapacidad del estudiante)</option>
                      @php
                        // Tipos de ajustes razonables para Instituto Técnico según discapacidad
                        $tiposAjustes = [
                          'Discapacidad Visual' => [
                            'Materiales de estudio en formato ampliado',
                            'Documentos técnicos en Braille',
                            'Software lector de pantalla para laboratorios',
                            'Tiempo extendido para evaluaciones prácticas y teóricas',
                            'Asistente para lectura de manuales técnicos',
                            'Materiales con alto contraste en pantallas',
                            'Tecnología asistiva (lupas digitales, magnificadores)',
                            'Descripción verbal de procesos en talleres',
                            'Adaptación de herramientas de medición',
                            'Materiales táctiles para diagramas técnicos',
                          ],
                          'Discapacidad Auditiva' => [
                            'Intérprete de lengua de señas en clases y laboratorios',
                            'Materiales visuales complementarios para procesos técnicos',
                            'Subtítulos en videos instructivos y tutoriales',
                            'Ubicación preferencial en aula y talleres',
                            'Sistema de frecuencia modulada (FM) para clases',
                            'Apoyo con tomador de notas técnicas',
                            'Instrucciones escritas para prácticas de laboratorio',
                            'Señalización visual en talleres y laboratorios',
                            'Comunicación por escrito en trabajos grupales',
                          ],
                          'Discapacidad Motora' => [
                            'Acceso físico adaptado a talleres y laboratorios',
                            'Tiempo extendido para tareas prácticas y evaluaciones',
                            'Tecnología asistiva para uso de computadoras',
                            'Alternativas para tareas que requieren escritura manual',
                            'Asistente para toma de notas en clases',
                            'Adaptación de espacios físicos en talleres',
                            'Herramientas adaptadas para prácticas técnicas',
                            'Mesa de trabajo ajustable en laboratorios',
                            'Software de reconocimiento de voz para documentación',
                            'Adaptación de equipos de laboratorio',
                          ],
                          'Discapacidad Intelectual' => [
                            'Instrucciones simplificadas para procedimientos técnicos',
                            'Tiempo extendido para tareas y proyectos',
                            'Materiales adaptados con lenguaje claro',
                            'Apoyo con tutoría especializada',
                            'Evaluaciones diferenciadas según competencias',
                            'Rutinas estructuradas en talleres y laboratorios',
                            'Guías paso a paso para procesos técnicos',
                            'Apoyo en organización de trabajos prácticos',
                            'Materiales con ejemplos visuales concretos',
                          ],
                          'Trastorno del Espectro Autista (TEA)' => [
                            'Rutinas estructuradas en talleres y laboratorios',
                            'Espacios de descanso sensorial disponibles',
                            'Comunicación clara y directa en instrucciones técnicas',
                            'Anticipación de cambios en horarios y actividades',
                            'Apoyo en interacciones sociales en trabajos grupales',
                            'Materiales visuales para organización de tareas',
                            'Ambiente predecible en espacios de práctica',
                            'Tiempo de transición entre actividades',
                            'Instrucciones escritas y visuales para procesos',
                          ],
                          'Trastorno por Déficit de Atención e Hiperactividad (TDAH)' => [
                            'Asientos preferenciales en aulas y laboratorios',
                            'Pausas frecuentes durante clases y prácticas',
                            'Instrucciones por escrito para procedimientos',
                            'Organizadores visuales para proyectos técnicos',
                            'Tiempo extendido para tareas y evaluaciones',
                            'Apoyo en organización y planificación de trabajos',
                            'Recordatorios visuales de plazos y tareas',
                            'Estructura clara en actividades prácticas',
                            'Ambiente con mínimas distracciones',
                          ],
                          'Discapacidad Psicosocial' => [
                            'Flexibilidad en asistencia a clases y prácticas',
                            'Pausas cuando sea necesario durante actividades',
                            'Ambiente de apoyo y comprensión en el instituto',
                            'Comunicación abierta con docentes y coordinación',
                            'Apoyo en gestión del estrés académico',
                            'Plazos flexibles cuando corresponda',
                            'Espacios tranquilos para trabajo individual',
                            'Apoyo en organización de carga académica',
                            'Canales de comunicación alternativos',
                          ],
                          'Otra' => [
                            'Ajuste personalizado según necesidad específica del estudiante',
                          ],
                        ];
                      @endphp
                      @foreach($tiposAjustes as $discapacidad => $ajustes)
                        <optgroup label="{{ $discapacidad }}" data-categoria="{{ strtolower($discapacidad) }}">
                          @foreach($ajustes as $ajuste)
                            <option value="{{ $ajuste }}" 
                                    data-discapacidad="{{ $discapacidad }}"
                                    data-texto-busqueda="{{ strtolower($ajuste . ' ' . $discapacidad) }}">
                              {{ $ajuste }}
                            </option>
                          @endforeach
                        </optgroup>
                      @endforeach
                    </select>
                    <small class="text-muted d-block mt-2">
                      <i class="fas fa-info-circle me-1"></i>Selecciona un tipo para completar automáticamente el nombre del ajuste
                    </small>
                  </div>

                  <div class="col-12">
                    <label class="form-label fw-semibold">
                      Nombre del ajuste <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="ajustes[0][nombre]" class="form-control ajuste-nombre" placeholder="Ej: Tiempo extendido para evaluaciones" required>
                  </div>

                  <div class="col-12">
                    <label class="form-label fw-semibold">
                      Descripción detallada <span class="text-danger">*</span>
                    </label>
                    <textarea name="ajustes[0][descripcion]" rows="6" class="form-control ajuste-descripcion" placeholder="Describe los detalles específicos del ajuste razonable, cómo se implementará, qué recursos se necesitarán, etc..." required></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Botón para agregar más ajustes -->
        <div class="text-center mb-4">
          <button type="button" id="btn-agregar-ajuste" class="btn btn-outline-primary">
            <i class="fas fa-plus-circle me-2"></i>Agregar Otro Ajuste
          </button>
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
                  <i class="fas fa-save me-2"></i>Guardar Ajustes
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
  /* Estilos para la barra de búsqueda de estudiantes */
  #buscarEstudiante {
    padding-right: 40px;
    border: 2px solid #dee2e6;
    transition: all 0.3s ease;
  }
  
  #buscarEstudiante:focus {
    border-color: #dc2626;
    box-shadow: 0 0 0 0.2rem rgba(220, 38, 38, 0.25);
  }
  
  #estudiante_id {
    border: 2px solid #dee2e6;
    border-radius: 0.375rem;
    transition: all 0.3s ease;
  }
  
  #estudiante_id:focus {
    border-color: #dc2626;
    box-shadow: 0 0 0 0.2rem rgba(220, 38, 38, 0.25);
  }
  
  #estudiante_id option {
    padding: 0.5rem;
  }
  
  #estudiante_id option:hover {
    background-color: #f8f9fa;
  }
  
  /* Estilos para modo oscuro */
  [data-theme="dark"] #buscarEstudiante {
    background: #1e293b;
    border-color: #334155;
    color: #f1f5f9;
  }
  
  [data-theme="dark"] #buscarEstudiante:focus {
    border-color: #dc2626;
    background: #1e293b;
    color: #f1f5f9;
  }
  
  [data-theme="dark"] #buscarEstudiante::placeholder {
    color: #64748b;
  }
  
  [data-theme="dark"] #estudiante_id {
    background: #1e293b;
    border-color: #334155;
    color: #f1f5f9;
  }
  
  [data-theme="dark"] #estudiante_id:focus {
    border-color: #dc2626;
    background: #1e293b;
    color: #f1f5f9;
  }
  
  [data-theme="dark"] #estudiante_id option {
    background: #1e293b;
    color: #f1f5f9;
  }
  
  [data-theme="dark"] #estudiante_id option:hover {
    background-color: #334155;
  }

  /* Estilos para la barra de búsqueda de tipos de ajuste (dentro de cada ajuste) */
  .buscar-tipo-ajuste {
    padding-right: 40px;
    border: 2px solid #dee2e6;
    transition: all 0.3s ease;
  }
  
  .buscar-tipo-ajuste:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
  }
  
  .tipo-ajuste-select {
    border: 2px solid #dee2e6;
    border-radius: 0.375rem;
    transition: all 0.3s ease;
  }
  
  .tipo-ajuste-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
  }
  
  .tipo-ajuste-select option {
    padding: 0.5rem;
  }
  
  .tipo-ajuste-select option:hover {
    background-color: #f8f9fa;
  }

  /* Estilos para modo oscuro - tipos de ajuste */
  [data-theme="dark"] .buscar-tipo-ajuste {
    background: #1e293b;
    border-color: #334155;
    color: #f1f5f9;
  }
  
  [data-theme="dark"] .buscar-tipo-ajuste:focus {
    border-color: #3b82f6;
    background: #1e293b;
    color: #f1f5f9;
  }
  
  [data-theme="dark"] .buscar-tipo-ajuste::placeholder {
    color: #64748b;
  }
  
  [data-theme="dark"] .tipo-ajuste-select {
    background: #1e293b;
    border-color: #334155;
    color: #f1f5f9;
  }
  
  [data-theme="dark"] .tipo-ajuste-select:focus {
    border-color: #3b82f6;
    background: #1e293b;
    color: #f1f5f9;
  }
  
  [data-theme="dark"] .tipo-ajuste-select option {
    background: #1e293b;
    color: #f1f5f9;
  }
  
  [data-theme="dark"] .tipo-ajuste-select option:hover {
    background-color: #334155;
  }
  
  [data-theme="dark"] .tipo-ajuste-select optgroup {
    background: #0f172a;
    color: #cbd5e1;
  }
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

  /* Estilos para múltiples ajustes */
  .ajuste-item {
    animation: fadeIn 0.3s ease-in;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .ajuste-numero {
    font-size: 0.875rem;
    font-weight: 400;
    opacity: 0.9;
  }

  .btn-eliminar-ajuste {
    transition: all 0.2s ease;
  }

  .btn-eliminar-ajuste:hover {
    background-color: #dc2626 !important;
    color: #ffffff !important;
    transform: scale(1.05);
  }

  #btn-agregar-ajuste {
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.2s ease;
  }

  #btn-agregar-ajuste:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
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

  // Función para filtrar tipos de ajuste según discapacidad
  function filtrarTiposAjustePorDiscapacidad(discapacidad) {
    const todosLosSelectores = document.querySelectorAll('.tipo-ajuste-select');
    
    todosLosSelectores.forEach(select => {
      if (discapacidad && discapacidad.trim() !== '') {
        // Mostrar todas las opciones primero
        select.querySelectorAll('optgroup, option[data-discapacidad]').forEach(opt => {
          opt.style.display = '';
        });

        // Ocultar opciones que no coincidan
        select.querySelectorAll('option[data-discapacidad]').forEach(option => {
          const optionDiscapacidad = option.getAttribute('data-discapacidad');
          if (optionDiscapacidad && optionDiscapacidad !== discapacidad && optionDiscapacidad !== 'Otra') {
            option.style.display = 'none';
          }
        });

        // Ocultar optgroups que no tengan opciones visibles
        select.querySelectorAll('optgroup').forEach(group => {
          const visibleOptions = Array.from(group.querySelectorAll('option[data-discapacidad]')).filter(
            opt => opt.style.display !== 'none'
          );
          if (visibleOptions.length === 0 && group.label !== discapacidad && group.label !== 'Otra') {
            group.style.display = 'none';
          }
        });
      } else {
        // Si no hay discapacidad registrada, mostrar todas las opciones
        select.querySelectorAll('optgroup, option[data-discapacidad]').forEach(opt => {
          opt.style.display = '';
        });
      }
    });
  }

  // Cuando se selecciona un estudiante, filtrar solicitudes y tipos de ajustes
  estudianteSelect.addEventListener('change', function() {
    const estudianteId = this.value;
    const selectedOption = this.options[this.selectedIndex];
    const discapacidad = selectedOption.getAttribute('data-discapacidad');
    
    // Filtrar solicitudes por estudiante (doble verificación)
    filtrarSolicitudesPorEstudiante(estudianteId);
    
    // Filtrar tipos de ajuste en todos los selectores
    filtrarTiposAjustePorDiscapacidad(discapacidad);
    
    // Resetear selección de tipo de ajuste en todos los selectores
    document.querySelectorAll('.tipo-ajuste-select').forEach(select => {
      select.value = '';
    });
  });

  // Inicializar el filtro de solicitudes al cargar la página si ya hay un estudiante seleccionado
  if (estudianteSelect.value) {
    filtrarSolicitudesPorEstudiante(estudianteSelect.value);
  }

  // Funcionalidad de búsqueda de estudiantes
  const buscarEstudianteInput = document.getElementById('buscarEstudiante');
  if (buscarEstudianteInput) {
    buscarEstudianteInput.addEventListener('input', function() {
      const busqueda = this.value.toLowerCase().trim();
      const opciones = estudianteSelect.querySelectorAll('option');
      let opcionesVisibles = 0;
      
      opciones.forEach(function(option) {
        if (option.value === '') {
          // Mantener la opción por defecto visible
          option.style.display = '';
          return;
        }
        
        const textoBusqueda = option.getAttribute('data-texto-busqueda') || option.textContent.toLowerCase();
        
        if (busqueda === '' || textoBusqueda.includes(busqueda)) {
          option.style.display = '';
          opcionesVisibles++;
        } else {
          option.style.display = 'none';
        }
      });
      
      // Si hay una búsqueda activa y solo hay una opción visible (además de la opción por defecto), seleccionarla automáticamente
      if (busqueda !== '' && opcionesVisibles === 1) {
        const opcionVisible = Array.from(opciones).find(opt => 
          opt.value !== '' && opt.style.display !== 'none'
        );
        if (opcionVisible) {
          estudianteSelect.value = opcionVisible.value;
          estudianteSelect.dispatchEvent(new Event('change'));
        }
      }
      
      // Ajustar el tamaño del select según las opciones visibles
      const opcionesVisiblesCount = Array.from(opciones).filter(opt => 
        opt.style.display !== 'none' && opt.value !== ''
      ).length;
      estudianteSelect.size = Math.min(Math.max(opcionesVisiblesCount + 1, 3), 8);
    });
    
    // Limpiar búsqueda cuando se selecciona un estudiante
    estudianteSelect.addEventListener('change', function() {
      if (this.value) {
        buscarEstudianteInput.value = '';
        // Restaurar todas las opciones visibles
        estudianteSelect.querySelectorAll('option').forEach(opt => {
          opt.style.display = '';
        });
        estudianteSelect.size = 8;
      }
    });
  }

  // Funcionalidad de búsqueda de tipos de ajuste (para cada selector individual)
  function inicializarBusquedaTipoAjuste(buscarInput, tipoSelect) {
    if (!buscarInput || !tipoSelect) return;
    
    buscarInput.addEventListener('input', function() {
      const busqueda = this.value.toLowerCase().trim();
      const optgroups = tipoSelect.querySelectorAll('optgroup');
      let opcionesVisibles = 0;
      
      // Si la búsqueda está vacía, mostrar todas las opciones
      if (busqueda === '') {
        optgroups.forEach(group => {
          group.style.display = '';
          group.querySelectorAll('option').forEach(opt => {
            opt.style.display = '';
          });
        });
        tipoSelect.size = 6;
        return;
      }
      
      // Filtrar opciones y grupos
      optgroups.forEach(group => {
        const categoria = group.getAttribute('data-categoria') || group.label.toLowerCase();
        const opcionesGrupo = group.querySelectorAll('option');
        let tieneOpcionesVisibles = false;
        
        opcionesGrupo.forEach(option => {
          if (option.value === '') {
            option.style.display = 'none';
            return;
          }
          
          const textoBusqueda = option.getAttribute('data-texto-busqueda') || option.textContent.toLowerCase();
          
          if (textoBusqueda.includes(busqueda) || categoria.includes(busqueda)) {
            option.style.display = '';
            opcionesVisibles++;
            tieneOpcionesVisibles = true;
          } else {
            option.style.display = 'none';
          }
        });
        
        // Mostrar u ocultar el grupo según si tiene opciones visibles
        if (tieneOpcionesVisibles) {
          group.style.display = '';
        } else {
          group.style.display = 'none';
        }
      });
      
      // Ajustar el tamaño del select según las opciones visibles
      tipoSelect.size = Math.min(Math.max(opcionesVisibles + 1, 3), 6);
    });
    
    // Limpiar búsqueda cuando se selecciona un tipo de ajuste
    tipoSelect.addEventListener('change', function() {
      if (this.value) {
        buscarInput.value = '';
        // Restaurar todas las opciones visibles
        tipoSelect.querySelectorAll('optgroup, option').forEach(opt => {
          opt.style.display = '';
        });
        tipoSelect.size = 6;
      }
    });
  }

  // Inicializar búsqueda para todos los selectores existentes
  document.querySelectorAll('.ajuste-item').forEach(item => {
    const buscarInput = item.querySelector('.buscar-tipo-ajuste');
    const tipoSelect = item.querySelector('.tipo-ajuste-select');
    if (buscarInput && tipoSelect) {
      inicializarBusquedaTipoAjuste(buscarInput, tipoSelect);
    }
  });

  // Funcionalidad para agregar múltiples ajustes
  let contadorAjustes = 1;
  const ajustesContainer = document.getElementById('ajustes-container');
  const btnAgregarAjuste = document.getElementById('btn-agregar-ajuste');

  // Función para crear un nuevo ajuste
  function crearNuevoAjuste() {
    if (!ajustesContainer) return;
    
    const primerAjuste = ajustesContainer.querySelector('.ajuste-item');
    if (!primerAjuste) return;
    
    const nuevoAjuste = primerAjuste.cloneNode(true);
    nuevoAjuste.setAttribute('data-ajuste-index', contadorAjustes);
    
    // Actualizar número del ajuste
    nuevoAjuste.querySelector('.ajuste-numero').textContent = '#' + (contadorAjustes + 1);
    
    // Actualizar nombres de campos
    nuevoAjuste.querySelectorAll('input, textarea').forEach(input => {
      const name = input.getAttribute('name');
      if (name) {
        const newName = name.replace(/\[\d+\]/, `[${contadorAjustes}]`);
        input.setAttribute('name', newName);
        input.value = '';
        input.classList.remove('is-invalid');
      }
    });
    
    // Limpiar valores de los selectores de tipo de ajuste
    nuevoAjuste.querySelectorAll('.tipo-ajuste-select').forEach(select => {
      select.value = '';
    });
    nuevoAjuste.querySelectorAll('.buscar-tipo-ajuste').forEach(input => {
      input.value = '';
    });
    
    // Inicializar búsqueda para el nuevo selector
    const buscarInput = nuevoAjuste.querySelector('.buscar-tipo-ajuste');
    const tipoSelect = nuevoAjuste.querySelector('.tipo-ajuste-select');
    if (buscarInput && tipoSelect) {
      inicializarBusquedaTipoAjuste(buscarInput, tipoSelect);
    }
    
    // Inicializar evento de cambio para el selector de tipo de ajuste
    if (tipoSelect) {
      tipoSelect.addEventListener('change', function() {
        if (this.value) {
          const nombreInput = nuevoAjuste.querySelector('.ajuste-nombre');
          if (nombreInput) {
            nombreInput.value = this.value;
            
            // Actualizar placeholder de descripción
            const descripcionTextarea = nuevoAjuste.querySelector('.ajuste-descripcion');
            if (descripcionTextarea && !descripcionTextarea.value) {
              descripcionTextarea.placeholder = 'Detalla cómo se implementará: ' + this.value + '...';
            }
          }
        }
      });
      
      // Aplicar filtro de discapacidad si hay un estudiante seleccionado
      if (estudianteSelect && estudianteSelect.value) {
        const selectedOption = estudianteSelect.options[estudianteSelect.selectedIndex];
        const discapacidad = selectedOption ? selectedOption.getAttribute('data-discapacidad') : null;
        if (discapacidad) {
          // Aplicar filtro al nuevo selector
          tipoSelect.querySelectorAll('option[data-discapacidad]').forEach(option => {
            const optionDiscapacidad = option.getAttribute('data-discapacidad');
            if (optionDiscapacidad && optionDiscapacidad !== discapacidad && optionDiscapacidad !== 'Otra') {
              option.style.display = 'none';
            } else {
              option.style.display = '';
            }
          });
          
          tipoSelect.querySelectorAll('optgroup').forEach(group => {
            const visibleOptions = Array.from(group.querySelectorAll('option[data-discapacidad]')).filter(
              opt => opt.style.display !== 'none'
            );
            if (visibleOptions.length === 0 && group.label !== discapacidad && group.label !== 'Otra') {
              group.style.display = 'none';
            } else {
              group.style.display = '';
            }
          });
        }
      }
    }
    
    // Mostrar botón eliminar
    nuevoAjuste.querySelector('.btn-eliminar-ajuste').style.display = 'block';
    
    // Agregar al contenedor
    ajustesContainer.appendChild(nuevoAjuste);
    contadorAjustes++;
    
    // Actualizar números de ajustes
    actualizarNumerosAjustes();
  }

  // Función para actualizar números de ajustes
  function actualizarNumerosAjustes() {
    const ajustes = ajustesContainer.querySelectorAll('.ajuste-item');
    ajustes.forEach((ajuste, index) => {
      ajuste.setAttribute('data-ajuste-index', index);
      ajuste.querySelector('.ajuste-numero').textContent = '#' + (index + 1);
      
      // Actualizar nombres de campos
      ajuste.querySelectorAll('input, textarea').forEach(input => {
        const name = input.getAttribute('name');
        if (name) {
          const newName = name.replace(/\[\d+\]/, `[${index}]`);
          input.setAttribute('name', newName);
        }
      });
      
      // Ocultar botón eliminar si es el primer ajuste
      if (index === 0) {
        ajuste.querySelector('.btn-eliminar-ajuste').style.display = 'none';
      } else {
        ajuste.querySelector('.btn-eliminar-ajuste').style.display = 'block';
      }
    });
  }

  // Event listener para agregar ajuste
  btnAgregarAjuste.addEventListener('click', crearNuevoAjuste);

  // Event listener para eliminar ajuste
  ajustesContainer.addEventListener('click', function(e) {
    if (e.target.closest('.btn-eliminar-ajuste')) {
      const ajusteItem = e.target.closest('.ajuste-item');
      const totalAjustes = ajustesContainer.querySelectorAll('.ajuste-item').length;
      
      if (totalAjustes > 1) {
        ajusteItem.remove();
        actualizarNumerosAjustes();
        contadorAjustes--;
      } else {
        alert('Debe haber al menos un ajuste.');
      }
    }
  });

  // Inicializar eventos de cambio para todos los selectores de tipo de ajuste existentes
  document.querySelectorAll('.tipo-ajuste-select').forEach(tipoSelect => {
    tipoSelect.addEventListener('change', function() {
      if (this.value) {
        const ajusteItem = this.closest('.ajuste-item');
        if (ajusteItem) {
          const nombreInput = ajusteItem.querySelector('.ajuste-nombre');
          if (nombreInput) {
            nombreInput.value = this.value;
            
            // Actualizar placeholder de descripción
            const descripcionTextarea = ajusteItem.querySelector('.ajuste-descripcion');
            if (descripcionTextarea && !descripcionTextarea.value) {
              descripcionTextarea.placeholder = 'Detalla cómo se implementará: ' + this.value + '...';
            }
          }
        }
      }
    });
  });

  // Validación antes de enviar el formulario
  const form = document.querySelector('form');
  if (form) {
    form.addEventListener('submit', function(e) {
      const ajustesItems = ajustesContainer.querySelectorAll('.ajuste-item');
      let hayErrores = false;
      
      ajustesItems.forEach((item, index) => {
        const nombre = item.querySelector('.ajuste-nombre').value.trim();
        const descripcion = item.querySelector('.ajuste-descripcion').value.trim();
        
        if (!nombre || !descripcion) {
          hayErrores = true;
          if (!nombre) {
            item.querySelector('.ajuste-nombre').classList.add('is-invalid');
          }
          if (!descripcion) {
            item.querySelector('.ajuste-descripcion').classList.add('is-invalid');
          }
        } else {
          item.querySelector('.ajuste-nombre').classList.remove('is-invalid');
          item.querySelector('.ajuste-descripcion').classList.remove('is-invalid');
        }
      });
      
      if (hayErrores) {
        e.preventDefault();
        alert('Por favor, completa todos los campos requeridos en cada ajuste.');
        return false;
      }
    });
  }
});
</script>
@endpush

@endsection
