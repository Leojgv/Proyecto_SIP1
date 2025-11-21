@extends('layouts.dashboard_asesoratecnica.app')

@section('title', 'Formular ajuste')

@section('content')
<div class="dashboard-page">
  <div class="page-header mb-4">
    <h1 class="h4 mb-1">Formular ajuste</h1>
    <p class="text-muted mb-0">Registra un ajuste razonable para un estudiante.</p>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <form action="{{ route('asesora-tecnica.ajustes.store') }}" method="POST" class="row g-3">
        @csrf
        
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

        <div class="col-12">
          <label for="estudiante_id" class="form-label">Estudiante <span class="text-danger">*</span></label>
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

        <div class="col-12">
          <label for="solicitud_id" class="form-label">Solicitud asociada <span class="text-danger">*</span></label>
          <select id="solicitud_id" name="solicitud_id" class="form-select @error('solicitud_id') is-invalid @enderror" required>
            <option value="">Selecciona una solicitud</option>
            @foreach($solicitudes as $solicitud)
              @php
                // Extraer título de la descripción si está entre corchetes
                $titulo = '';
                $descripcionCompleta = $solicitud->descripcion ?? '';
                if (preg_match('/^\[(.+?)\]/', $descripcionCompleta, $matches)) {
                  $titulo = $matches[1];
                  $descripcionSinTitulo = trim(substr($descripcionCompleta, strlen($matches[0])));
                } else {
                  $descripcionSinTitulo = $descripcionCompleta;
                }
                
                // Limitar el título/descripción para mostrar
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
          <small class="text-muted">Se muestra la fecha, estudiante y título/descripción de la solicitud</small>
        </div>

        <div class="col-12">
          <label for="tipo_ajuste" class="form-label">Tipo de Ajuste Razonable</label>
          <select id="tipo_ajuste" class="form-select">
            <option value="">Selecciona un tipo de ajuste (se sugerirán según la discapacidad del estudiante)</option>
            @foreach($tiposAjustes as $discapacidad => $ajustes)
              <optgroup label="{{ $discapacidad }}">
                @foreach($ajustes as $ajuste)
                  <option value="{{ $ajuste }}" data-discapacidad="{{ $discapacidad }}">{{ $ajuste }}</option>
                @endforeach
              </optgroup>
            @endforeach
          </select>
          <small class="text-muted">Al seleccionar un estudiante, se filtrarán los tipos de ajustes según su discapacidad</small>
        </div>

        <div class="col-12">
          <label for="nombre" class="form-label">Nombre del ajuste <span class="text-danger">*</span></label>
          <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" class="form-control @error('nombre') is-invalid @enderror" placeholder="Ej: Tiempo extendido para evaluaciones" required>
          @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <small class="text-muted">Puedes usar el selector de tipo de ajuste arriba para completar automáticamente este campo</small>
        </div>

        <div class="col-12">
          <label for="descripcion" class="form-label">Descripción detallada</label>
          <textarea id="descripcion" name="descripcion" rows="4" class="form-control @error('descripcion') is-invalid @enderror" placeholder="Describe los detalles específicos del ajuste razonable...">{{ old('descripcion') }}</textarea>
          @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-12">
          <label for="fecha_solicitud" class="form-label">Fecha de solicitud <span class="text-danger">*</span></label>
          <input type="date" id="fecha_solicitud" name="fecha_solicitud" value="{{ old('fecha_solicitud', now()->toDateString()) }}" class="form-control @error('fecha_solicitud') is-invalid @enderror" required>
          @error('fecha_solicitud')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>


        <div class="col-12 d-flex justify-content-end gap-2 mt-2">
          <a href="{{ route('asesora-tecnica.dashboard') }}" class="btn btn-outline-danger">Cancelar</a>
          <button type="submit" class="btn btn-danger">Guardar ajuste</button>
        </div>
      </form>
    </div>
  </div>
</div>

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
