@extends('layouts.dashboard_estudiante.estudiante')

@section('title', 'Solicitar Entrevista')

@section('content')
<div class="container-fluid">
  <div class="row align-items-center mb-4">
    <div class="col-lg-8">
      <h1 class="h3 mb-1">Solicitud de Entrevista</h1>
      <p class="text-muted mb-0">Solicita una entrevista con el equipo de asesoria pedagogica.</p>
    </div>
  </div>

  <div class="row g-4 justify-content-center">
    <div class="col-12 col-xl-10 col-xxl-8">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h5 class="card-title mb-2">Formulario de Solicitud</h5>
          <p class="card-text text-muted mb-4">Selecciona un cupo disponible y completa los campos para enviar tu solicitud.</p>

          <form action="{{ route('estudiantes.entrevistas.store') }}" method="POST" class="row g-3">
            @csrf

            <div class="col-md-6">
              <label class="form-label">Nombres</label>
              <input type="text" class="form-control" value="{{ $estudiante->nombre }}" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label">Apellidos</label>
              <input type="text" class="form-control" value="{{ $estudiante->apellido }}" disabled>
            </div>

            <div class="col-md-6">
              <label class="form-label">RUT</label>
              <input type="text" class="form-control" value="{{ $estudiante->rut }}" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label">Tu Carrera</label>
              <input type="text" class="form-control" value="{{ $estudiante->carrera->nombre ?? 'Sin carrera' }}" disabled>
            </div>

            <div class="col-md-6">
              <label class="form-label">Correo Electronico</label>
              <input type="email" class="form-control" value="{{ $estudiante->email }}" disabled>
            </div>
            <div class="col-md-6">
              <label for="telefono" class="form-label">Telefono de Contacto</label>
              <input type="text" name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono', $estudiante->telefono) }}" required>
              @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
              <label for="titulo" class="form-label">Titulo de la Solicitud</label>
              <input type="text" name="titulo" id="titulo" class="form-control @error('titulo') is-invalid @enderror" placeholder="Breve descripcion del motivo de la entrevista" value="{{ old('titulo') }}" required>
              @error('titulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
              <label for="descripcion" class="form-label">Descripcion</label>
              <textarea name="descripcion" id="descripcion" rows="5" class="form-control @error('descripcion') is-invalid @enderror" placeholder="Describe detalladamente el motivo de tu solicitud de entrevista..." required>{{ old('descripcion') }}</textarea>
              @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
              <label for="modalidad" class="form-label">Modalidad de la Entrevista <span class="text-danger">*</span></label>
              <select name="modalidad" id="modalidad" class="form-select @error('modalidad') is-invalid @enderror" required>
                <option value="">Selecciona una modalidad</option>
                <option value="Virtual" {{ old('modalidad') === 'Virtual' ? 'selected' : '' }}>Virtual</option>
                <option value="Presencial" {{ old('modalidad') === 'Presencial' ? 'selected' : '' }}>Presencial</option>
              </select>
              @error('modalidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
              <label for="cupo" class="form-label">Selecciona un cupo disponible <span class="text-danger">*</span></label>
              <div class="input-group @error('cupo') is-invalid @enderror">
                <input 
                  type="text" 
                  id="cupo_display" 
                  class="form-control @error('cupo') is-invalid @enderror" 
                  placeholder="Haz clic para seleccionar día y horario" 
                  readonly 
                  required
                  value="{{ old('cupo') ? \Carbon\Carbon::parse(old('cupo'))->locale('es')->translatedFormat('l j \\de F \\de Y \\a \\l\\a\\s H:i') : '' }}"
                >
                <input type="hidden" id="cupo" name="cupo" value="{{ old('cupo') }}" required>
                <button 
                  type="button" 
                  class="btn btn-danger" 
                  data-bs-toggle="modal" 
                  data-bs-target="#modalCalendario"
                  {{ $cuposDisponibles->isEmpty() ? 'disabled' : '' }}
                >
                  <i class="fas fa-calendar-alt me-2"></i>Seleccionar Horario
                </button>
              </div>
              @if($cuposDisponibles->isEmpty())
                <div class="alert alert-warning mt-2 mb-0">
                  <i class="fas fa-exclamation-triangle me-2"></i>No hay cupos disponibles en las próximas semanas. Vuelve a intentarlo más tarde o contacta a la coordinadora.
                </div>
              @else
                <div class="form-text mt-2">
                  <i class="fas fa-info-circle me-1"></i>Cada cupo considera 45 minutos de entrevista y 15 minutos de descanso para la coordinadora.
                </div>
              @endif
              @error('cupo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
              <div class="form-check p-3 bg-light rounded border">
                <input class="form-check-input" type="checkbox" value="1" id="autorizacion" name="autorizacion" {{ old('autorizacion') ? 'checked' : '' }} required>
                <label class="form-check-label" for="autorizacion">
                  Autorizo el tratamiento de mis datos personales para fines academicos y de asesoria pedagogica.
                </label>
              </div>
              @error('autorizacion')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="col-12 d-flex justify-content-end gap-2 mt-2">
              <a href="{{ route('estudiantes.dashboard') }}" class="btn btn-outline-danger">Cancelar</a>
              <button type="submit" class="btn btn-danger" {{ $cuposDisponibles->isEmpty() ? 'disabled' : '' }}>Enviar Solicitud</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Calendario -->
<div class="modal fade" id="modalCalendario" tabindex="-1" aria-labelledby="modalCalendarioLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="modalCalendarioLabel">
          <i class="fas fa-calendar-alt me-2"></i>Selecciona Día y Horario
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Paso 1: Selección de Día -->
        <div id="paso1-seleccion-dia">
          <h6 class="mb-3">Paso 1: Selecciona un día</h6>
          <div id="calendario-container" class="mb-3">
            <!-- El calendario se generará con JavaScript -->
          </div>
          <div class="alert alert-info mb-0">
            <i class="fas fa-info-circle me-2"></i>Los días en verde tienen horarios disponibles.
          </div>
        </div>

        <!-- Paso 2: Selección de Horario -->
        <div id="paso2-seleccion-horario" style="display: none;">
          <div class="d-flex align-items-center mb-3">
            <button type="button" class="btn btn-sm btn-outline-secondary me-3" id="btn-volver-calendario">
              <i class="fas fa-arrow-left me-1"></i>Volver
            </button>
            <div>
              <h6 class="mb-0">Paso 2: Selecciona un horario</h6>
              <small class="text-muted" id="fecha-seleccionada-texto"></small>
            </div>
          </div>
          <div id="horarios-container" class="row g-2">
            <!-- Los horarios se cargarán dinámicamente -->
          </div>
          <div id="sin-horarios" class="alert alert-warning" style="display: none;">
            <i class="fas fa-exclamation-triangle me-2"></i>No hay horarios disponibles para este día.
          </div>
        </div>

        <!-- Loading -->
        <div id="loading-calendario" class="text-center py-4" style="display: none;">
          <div class="spinner-border text-danger" role="status">
            <span class="visually-hidden">Cargando...</span>
          </div>
          <p class="text-muted mt-2">Cargando disponibilidad...</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="btn-confirmar-horario" disabled>
          <i class="fas fa-check me-2"></i>Confirmar Selección
        </button>
      </div>
    </div>
  </div>
</div>

@push('styles')
<style>
  .calendar-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    font-weight: 500;
    background: #fff;
  }
  
  .calendar-day:hover:not(.disabled):not(.today):not(.selected) {
    background: #f8f9fa;
    border-color: #dc3545;
    transform: translateY(-2px);
  }
  
  .calendar-day.disabled {
    background: #f8f9fa;
    color: #adb5bd;
    cursor: not-allowed;
    opacity: 0.5;
  }
  
  .calendar-day.today {
    background: #fff3cd;
    border-color: #ffc107;
    font-weight: 600;
  }
  
  .calendar-day.available {
    background: #d1e7dd;
    border-color: #198754;
    color: #0f5132;
  }
  
  .calendar-day.available:hover {
    background: #b8e0cc;
    border-color: #198754;
  }
  
  .calendar-day.selected {
    background: #dc3545;
    border-color: #dc3545;
    color: #fff;
    font-weight: 600;
  }
  
  .calendar-day.weekend {
    background: #f8f9fa;
    color: #adb5bd;
  }
  
  .time-slot {
    padding: 0.75rem 1rem;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #fff;
    text-align: center;
    font-weight: 500;
  }
  
  .time-slot:hover {
    background: #f8f9fa;
    border-color: #dc3545;
    transform: translateY(-2px);
  }
  
  .time-slot.selected {
    background: #dc3545;
    border-color: #dc3545;
    color: #fff;
  }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const modal = new bootstrap.Modal(document.getElementById('modalCalendario'));
  const cupoInput = document.getElementById('cupo');
  const cupoDisplay = document.getElementById('cupo_display');
  const btnConfirmar = document.getElementById('btn-confirmar-horario');
  const btnVolver = document.getElementById('btn-volver-calendario');
  const paso1 = document.getElementById('paso1-seleccion-dia');
  const paso2 = document.getElementById('paso2-seleccion-horario');
  const loading = document.getElementById('loading-calendario');
  const horariosContainer = document.getElementById('horarios-container');
  const sinHorarios = document.getElementById('sin-horarios');
  const fechaSeleccionadaTexto = document.getElementById('fecha-seleccionada-texto');

  let fechaSeleccionada = null;
  let horarioSeleccionado = null;
  let diasDisponibles = [];
  let mesActual = new Date();
  mesActual.setDate(1); // Primer día del mes

  // Cargar días disponibles al abrir el modal
  document.getElementById('modalCalendario').addEventListener('show.bs.modal', function() {
    cargarDiasDisponibles();
  });

  // Botón volver al calendario
  btnVolver.addEventListener('click', function() {
    paso2.style.display = 'none';
    paso1.style.display = 'block';
    fechaSeleccionada = null;
    horarioSeleccionado = null;
    btnConfirmar.disabled = true;
  });

  // Botón confirmar horario
  btnConfirmar.addEventListener('click', function() {
    if (horarioSeleccionado && fechaSeleccionada) {
      const fechaCompleta = fechaSeleccionada + ' ' + horarioSeleccionado;
      cupoInput.value = fechaCompleta;
      
      // Formatear para mostrar
      const fecha = new Date(fechaSeleccionada + 'T' + horarioSeleccionado + ':00');
      const opciones = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      };
      cupoDisplay.value = fecha.toLocaleDateString('es-ES', opciones);
      
      modal.hide();
    }
  });

  function cargarDiasDisponibles() {
    loading.style.display = 'block';
    paso1.style.display = 'none';
    paso2.style.display = 'none';

    fetch('{{ route("estudiantes.entrevistas.dias-disponibles") }}')
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          diasDisponibles = data.dias.map(dia => dia.fecha);
          generarCalendario();
          loading.style.display = 'none';
          paso1.style.display = 'block';
        } else {
          alert('Error al cargar días disponibles');
          loading.style.display = 'none';
        }
      })
      .catch(error => {
        console.error('Error:', error);
        loading.style.display = 'none';
      });
  }

  function generarCalendario() {
    const container = document.getElementById('calendario-container');
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    
    // Navegación de mes
    const mesAnio = mesActual.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' });
    const mesAnioCapitalizado = mesAnio.charAt(0).toUpperCase() + mesAnio.slice(1);
    
    const navMes = `
      <div class="d-flex justify-content-between align-items-center mb-3">
        <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-mes-anterior">
          <i class="fas fa-chevron-left"></i>
        </button>
        <h6 class="mb-0" id="mes-anio-actual">${mesAnioCapitalizado}</h6>
        <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-mes-siguiente">
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
    `;

    // Días de la semana
    const diasSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
    const headerSemana = `
      <div class="row g-1 mb-2">
        ${diasSemana.map(dia => `<div class="col text-center fw-semibold text-muted small">${dia}</div>`).join('')}
      </div>
    `;

    // Generar días del calendario
    const primerDia = new Date(mesActual.getFullYear(), mesActual.getMonth(), 1);
    const ultimoDia = new Date(mesActual.getFullYear(), mesActual.getMonth() + 1, 0);
    const primerDiaSemana = primerDia.getDay();
    const diasEnMes = ultimoDia.getDate();

    let diasHTML = '<div class="row g-1">';
    
    // Días vacíos antes del primer día del mes
    for (let i = 0; i < primerDiaSemana; i++) {
      diasHTML += '<div class="col"></div>';
    }

    // Días del mes
    for (let dia = 1; dia <= diasEnMes; dia++) {
      const fechaCompleta = new Date(mesActual.getFullYear(), mesActual.getMonth(), dia);
      const fechaStr = fechaCompleta.toISOString().split('T')[0];
      const esHoy = fechaStr === hoy.toISOString().split('T')[0];
      const esPasado = fechaCompleta < hoy;
      const esFinSemana = fechaCompleta.getDay() === 0 || fechaCompleta.getDay() === 6;
      const tieneDisponibilidad = diasDisponibles.includes(fechaStr);
      const esSeleccionado = fechaStr === fechaSeleccionada;

      let clases = 'calendar-day col';
      if (esPasado) clases += ' disabled';
      else if (esFinSemana) clases += ' weekend';
      else if (esHoy) clases += ' today';
      else if (tieneDisponibilidad) clases += ' available';
      
      if (esSeleccionado) clases += ' selected';

      diasHTML += `
        <div class="${clases}" 
             data-fecha="${fechaStr}"
             ${esPasado || esFinSemana || !tieneDisponibilidad ? '' : 'onclick="seleccionarDia(\'' + fechaStr + '\')"'}
        >
          ${dia}
        </div>
      `;

      if ((primerDiaSemana + dia) % 7 === 0 && dia < diasEnMes) {
        diasHTML += '</div><div class="row g-1">';
      }
    }

    // Días vacíos después del último día del mes
    const ultimoDiaSemana = ultimoDia.getDay();
    const diasRestantes = 6 - ultimoDiaSemana;
    for (let i = 0; i < diasRestantes; i++) {
      diasHTML += '<div class="col"></div>';
    }

    diasHTML += '</div>';

    container.innerHTML = navMes + headerSemana + diasHTML;

    // Event listeners para navegación
    document.getElementById('btn-mes-anterior').addEventListener('click', function() {
      mesActual.setMonth(mesActual.getMonth() - 1);
      generarCalendario();
    });

    document.getElementById('btn-mes-siguiente').addEventListener('click', function() {
      mesActual.setMonth(mesActual.getMonth() + 1);
      generarCalendario();
    });
  }

  window.seleccionarDia = function(fechaStr) {
    fechaSeleccionada = fechaStr;
    const fecha = new Date(fechaStr);
    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    fechaSeleccionadaTexto.textContent = fecha.toLocaleDateString('es-ES', opciones);
    
    // Regenerar calendario para mostrar selección
    generarCalendario();
    
    // Cargar horarios para ese día
    cargarHorariosPorFecha(fechaStr);
    
    // Mostrar paso 2
    paso1.style.display = 'none';
    paso2.style.display = 'block';
    horarioSeleccionado = null;
    btnConfirmar.disabled = true;
  };

  function cargarHorariosPorFecha(fecha) {
    loading.style.display = 'block';
    horariosContainer.innerHTML = '';
    sinHorarios.style.display = 'none';

    fetch(`{{ route('estudiantes.entrevistas.horarios-por-fecha') }}?fecha=${fecha}`)
      .then(response => response.json())
      .then(data => {
        loading.style.display = 'none';
        
        if (data.success && data.horarios.length > 0) {
          horariosContainer.innerHTML = data.horarios.map(horario => `
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
              <div class="time-slot" 
                   data-valor="${horario.valor}"
                   data-hora="${horario.hora}"
                   onclick="seleccionarHorario('${horario.valor}', '${horario.hora}')"
              >
                <i class="fas fa-clock me-2"></i>${horario.label}
              </div>
            </div>
          `).join('');
        } else {
          sinHorarios.style.display = 'block';
        }
      })
      .catch(error => {
        console.error('Error:', error);
        loading.style.display = 'none';
        sinHorarios.style.display = 'block';
      });
  }

  window.seleccionarHorario = function(valor, hora) {
    horarioSeleccionado = hora;
    
    // Actualizar visualmente
    document.querySelectorAll('.time-slot').forEach(slot => {
      slot.classList.remove('selected');
    });
    event.target.closest('.time-slot').classList.add('selected');
    
    btnConfirmar.disabled = false;
  };

  // Cerrar modal al confirmar
  document.getElementById('modalCalendario').addEventListener('hidden.bs.modal', function() {
    // Resetear estado
    paso2.style.display = 'none';
    paso1.style.display = 'block';
    fechaSeleccionada = null;
    horarioSeleccionado = null;
    btnConfirmar.disabled = true;
  });
});
</script>
@endpush
@endsection
