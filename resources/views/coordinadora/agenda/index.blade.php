@extends('layouts.dashboard_coordinadora.app')

@section('title', 'Agenda de atencion')

@section('content')
<div class="container-fluid">
  <div class="row align-items-center mb-4">
    <div class="col">
      <h1 class="h3 mb-1">Agenda de la Coordinadora</h1>
      <p class="text-muted mb-0">Tu horario laboral esta fijado de {{ $horarioLaboral['inicio'] }} a {{ $horarioLaboral['fin'] }} todos los dias. Solo necesitas bloquear las franjas en que no puedas atender.</p>
    </div>
  </div>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="row g-4">
    <div class="col-12 col-xl-4">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0">Calendario de Entrevistas</h5>
            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#calendarioEntrevistasModal">
              Ver calendario completo
            </button>
          </div>
          <p class="card-text text-muted small mb-3">Todas las entrevistas agendadas por los estudiantes.</p>
          
          <div class="d-flex align-items-center gap-2 mb-3">
            <button class="btn btn-sm btn-outline-secondary" id="prevMonthBtnAgenda" type="button">
              <i class="fas fa-chevron-left"></i>
            </button>
            <div class="fw-bold flex-grow-1 text-center" id="calendarMonthLabelAgenda"></div>
            <button class="btn btn-sm btn-outline-secondary" id="nextMonthBtnAgenda" type="button">
              <i class="fas fa-chevron-right"></i>
            </button>
          </div>
          
          <div id="calendarGridAgenda" class="calendar-grid-agenda rounded-3 mb-3"></div>
          
          <div class="mt-3">
            <p class="small text-muted mb-2">Próximas entrevistas</p>
            <div class="d-flex flex-wrap gap-2" id="calendarEventsListAgenda"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-xl-8">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body">
          <h5 class="card-title mb-3">Bloqueos puntuales</h5>
          <p class="card-text text-muted">Agenda eventos personales o descansos fuera de la regla 45/15.</p>

          <!-- Botones rápidos -->
          <div class="mb-3">
            <label class="form-label small text-muted mb-2">Bloqueos rápidos</label>
            <div class="d-flex flex-wrap gap-2">
              <button type="button" class="btn btn-sm btn-outline-secondary quick-block-btn" data-hora-inicio="12:00" data-hora-fin="13:00" data-motivo="Almuerzo">
                <i class="fas fa-utensils me-1"></i>Almuerzo (12:00-13:00)
              </button>
              <button type="button" class="btn btn-sm btn-outline-secondary quick-block-btn" data-hora-inicio="15:00" data-hora-fin="15:30" data-motivo="Descanso">
                <i class="fas fa-coffee me-1"></i>Descanso (15:00-15:30)
              </button>
              <button type="button" class="btn btn-sm btn-outline-secondary quick-block-btn" data-hora-inicio="18:00" data-hora-fin="19:00" data-motivo="Reunión">
                <i class="fas fa-users me-1"></i>Reunión (18:00-19:00)
              </button>
              <button type="button" class="btn btn-sm btn-outline-secondary" id="clearQuickBlock">
                <i class="fas fa-times me-1"></i>Limpiar
              </button>
            </div>
          </div>

          <form action="{{ route('coordinadora.agenda.bloqueos.store') }}" method="POST" class="row g-3 mb-4" id="bloqueoForm">
            @csrf
            <div class="col-md-4">
              <label class="form-label">Fecha</label>
              <input type="date" name="fecha" id="bloqueoFecha" class="form-control @error('fecha') is-invalid @enderror" value="{{ old('fecha', now()->format('Y-m-d')) }}" required>
              @error('fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
              <label class="form-label">Inicio</label>
              <input type="time" name="hora_inicio" id="bloqueoHoraInicio" class="form-control @error('hora_inicio') is-invalid @enderror" value="{{ old('hora_inicio') }}" required>
              @error('hora_inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
              <label class="form-label">Fin</label>
              <input type="time" name="hora_fin" id="bloqueoHoraFin" class="form-control @error('hora_fin') is-invalid @enderror" value="{{ old('hora_fin') }}" required>
              @error('hora_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
              <label class="form-label">Motivo (opcional)</label>
              <input type="text" name="motivo" id="bloqueoMotivo" class="form-control @error('motivo') is-invalid @enderror" placeholder="Ej: Almuerzo, reunion de equipo..." value="{{ old('motivo') }}">
              @error('motivo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 d-flex justify-content-between align-items-center">
              <button class="btn btn-danger" type="submit">
                <i class="fas fa-plus me-1"></i>Agregar bloqueo
              </button>
              <button type="button" class="btn btn-outline-secondary btn-sm" id="addAnotherBlock">
                <i class="fas fa-redo me-1"></i>Agregar y continuar
              </button>
            </div>
          </form>

          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Horario</th>
                  <th>Motivo</th>
                  <th class="text-end">Acciones</th>
                </tr>
              </thead>
              <tbody>
                @forelse($bloqueos as $bloqueo)
                  <tr>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-calendar-day text-muted"></i>
                        <span>{{ $bloqueo->fecha->format('d/m/Y') }}</span>
                      </div>
                    </td>
                    <td>
                      @php
                        $horaInicio = \Carbon\Carbon::parse($bloqueo->hora_inicio)->format('H:i');
                        $horaFin = \Carbon\Carbon::parse($bloqueo->hora_fin)->format('H:i');
                      @endphp
                      <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-clock text-muted"></i>
                        <span>{{ $horaInicio }} - {{ $horaFin }}</span>
                      </div>
                    </td>
                    <td>
                      <span class="badge bg-secondary-subtle text-secondary">{{ $bloqueo->motivo ?? 'Sin detalle' }}</span>
                    </td>
                    <td class="text-end">
                      <form action="{{ route('coordinadora.agenda.bloqueos.destroy', $bloqueo) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este bloqueo?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" type="submit" title="Eliminar bloqueo">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center py-4" style="background: transparent;">
                      <i class="fas fa-calendar-times mb-2" style="font-size: 2rem; color: #64748b;"></i>
                      <p class="mb-0 text-muted">Aun no tienes bloqueos registrados.</p>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Modal calendario completo --}}
<div class="modal fade" id="calendarioEntrevistasModal" tabindex="-1" aria-labelledby="calendarioEntrevistasModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow">
      <div class="modal-header border-0">
        <div>
          <h5 class="modal-title" id="calendarioEntrevistasModalLabel">Calendario de Entrevistas</h5>
          <small class="text-muted">Todas las entrevistas agendadas</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body pt-0">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-outline-secondary" id="prevMonthBtnModal" type="button">
              <i class="fas fa-chevron-left"></i>
            </button>
            <div class="fw-bold" id="calendarMonthLabelModal"></div>
            <button class="btn btn-sm btn-outline-secondary" id="nextMonthBtnModal" type="button">
              <i class="fas fa-chevron-right"></i>
            </button>
          </div>
          <span class="badge bg-danger-subtle text-danger"><i class="fas fa-calendar-day me-1"></i>Entrevistas</span>
        </div>
        <div id="calendarGridModal" class="calendar-grid rounded-3"></div>
        <div class="mt-3">
          <p class="small text-muted mb-2">Próximas entrevistas</p>
          <div class="d-flex flex-wrap gap-2" id="calendarEventsListModal"></div>
        </div>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<style>
  .calendar-grid-agenda {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: .35rem;
    background: #f9f7fb;
    padding: .5rem;
  }
  .calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: .5rem;
    background: #f9f7fb;
    padding: .75rem;
  }
  .calendar-weekday {
    text-align: center;
    font-weight: 600;
    color: #555;
    padding: .35rem 0;
    font-size: .85rem;
  }
  .calendar-cell {
    background: #fff;
    border: 1px solid #f0f0f5;
    border-radius: 8px;
    min-height: 50px;
    padding: .4rem;
    position: relative;
    box-shadow: 0 2px 6px rgba(0,0,0,.03);
  }
  .calendar-grid-agenda .calendar-cell {
    min-height: 40px;
    padding: .3rem;
    font-size: .8rem;
  }
  .calendar-cell.is-today {
    border-color: #dc2626;
    box-shadow: 0 6px 14px rgba(220, 38, 38, .18);
  }
  .calendar-cell .day {
    font-weight: 700;
    color: #444;
    font-size: .9rem;
  }
  .calendar-grid-agenda .calendar-cell .day {
    font-size: .75rem;
  }
  .calendar-cell .event-dot {
    position: absolute;
    bottom: .3rem;
    left: .3rem;
    right: .3rem;
    color: #fff;
    border-radius: 4px;
    padding: .15rem .3rem;
    font-size: .65rem;
    text-align: center;
    margin-bottom: .2rem;
  }
  .calendar-cell .event-dot:first-of-type {
    bottom: .3rem;
  }
  .calendar-cell .event-dot:last-of-type {
    bottom: .1rem;
  }
  .calendar-cell .event-dot-entrevista {
    background: #dc2626;
  }
  .calendar-cell .event-dot-entrevista-virtual {
    background: #0dcaf0;
  }
  .calendar-cell .event-dot-entrevista-presencial {
    background: #198754;
  }
  .calendar-cell .event-dot-bloqueo {
    background: #6b7280;
  }
  .calendar-grid-agenda .calendar-cell .event-dot {
    font-size: .6rem;
    padding: .1rem .2rem;
  }
  .calendar-grid-agenda .calendar-cell .event-dot:last-of-type {
    bottom: .05rem;
  }
  .calendar-grid-agenda .calendar-cell .event-dot-indicator {
    position: absolute;
    bottom: .2rem;
    left: .2rem;
    right: .2rem;
    height: 4px;
    border-radius: 2px;
    margin-bottom: .1rem;
  }
  .calendar-grid-agenda .calendar-cell .event-dot-indicator-virtual {
    background: #0dcaf0;
  }
  .calendar-grid-agenda .calendar-cell .event-dot-indicator-presencial {
    background: #198754;
  }
  .calendar-grid-agenda .calendar-cell .event-dot-indicator-default {
    background: #dc2626;
  }
  .calendar-grid-agenda .calendar-cell .event-dot-indicator-bloqueo {
    background: #6b7280;
  }
  .event-chip {
    border: 1px solid #f0f0f5;
    border-radius: 999px;
    padding: .4rem .75rem;
    background: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,.05);
    font-size: .85rem;
  }
  .event-chip i { color: #dc2626; }
  .event-chip-bloqueo {
    border-color: #d1d5db;
    background: #f9fafb;
  }
  .event-chip-bloqueo i { color: #6b7280; }
  .calendar-cell.is-holiday {
    background: #fff3e0;
    border-color: #ff9800;
  }
  .calendar-cell.is-holiday.is-today {
    background: linear-gradient(135deg, #fff3cd 0%, #ff9800 100%);
    border-color: #ff9800;
  }
  .holiday-indicator {
    position: absolute;
    top: 0.2rem;
    right: 0.2rem;
    font-size: 0.7rem;
    line-height: 1;
  }

  /* Estilos para modo oscuro */
  [data-theme="dark"] .calendar-grid-agenda,
  [data-theme="dark"] .calendar-grid {
    background: #1e293b;
  }

  [data-theme="dark"] .calendar-weekday {
    color: #94a3b8;
  }

  [data-theme="dark"] .calendar-cell {
    background: #1e293b;
    border-color: #334155;
  }

  [data-theme="dark"] .calendar-cell .day {
    color: #e2e8f0;
  }

  [data-theme="dark"] .calendar-cell.is-today {
    border-color: #fbbf24;
    background: #fbbf24;
  }

  [data-theme="dark"] .calendar-cell.is-today .day {
    color: #1f2937;
    font-weight: 700;
  }

  [data-theme="dark"] .calendar-cell.is-holiday {
    background: #fb923c;
    border-color: #f97316;
  }

  [data-theme="dark"] .calendar-cell.is-holiday .day {
    color: #ffffff;
    font-weight: 600;
  }

  [data-theme="dark"] .calendar-cell.is-holiday.is-today {
    background: linear-gradient(135deg, #fbbf24 0%, #fb923c 100%);
    border-color: #f59e0b;
  }

  [data-theme="dark"] .calendar-cell.is-holiday.is-today .day {
    color: #1f2937;
    font-weight: 700;
  }

  [data-theme="dark"] .card {
    background: #1e293b;
    border-color: #334155;
  }

  [data-theme="dark"] .card-title {
    color: #e2e8f0;
  }

  [data-theme="dark"] .card-text.text-muted {
    color: #94a3b8;
  }

  [data-theme="dark"] .form-control,
  [data-theme="dark"] .form-select {
    background: #1e293b;
    border-color: #334155;
    color: #e2e8f0;
  }

  [data-theme="dark"] .form-control:focus,
  [data-theme="dark"] .form-select:focus {
    background: #1e293b;
    border-color: #dc3545;
    color: #e2e8f0;
  }

  [data-theme="dark"] .form-control::placeholder {
    color: #64748b;
  }

  [data-theme="dark"] .table {
    color: #e2e8f0;
    background: #1e293b;
  }

  [data-theme="dark"] .table thead {
    background: #334155;
  }

  [data-theme="dark"] .table thead th {
    color: #e2e8f0;
    border-bottom-color: #475569;
    background: #334155;
  }

  [data-theme="dark"] .table tbody {
    background: #1e293b;
  }

  [data-theme="dark"] .table tbody tr {
    background: #1e293b;
    border-bottom-color: #334155;
  }

  [data-theme="dark"] .table tbody tr:hover {
    background-color: #334155;
  }

  [data-theme="dark"] .table tbody td {
    background: transparent;
    color: #e2e8f0;
  }

  [data-theme="dark"] .table tbody td {
    background: transparent;
    color: #e2e8f0;
  }

  [data-theme="dark"] .table tbody td.text-center {
    background: transparent;
  }

  [data-theme="dark"] .table tbody td.text-center .text-muted {
    color: #94a3b8;
  }

  [data-theme="dark"] .table tbody td.text-center i {
    color: #64748b;
  }

  [data-theme="dark"] .table .text-muted {
    color: #94a3b8;
  }

  [data-theme="dark"] .table-responsive {
    background: #1e293b;
    border-radius: 8px;
  }

  [data-theme="dark"] .badge.bg-secondary-subtle {
    background: #475569 !important;
    color: #e2e8f0 !important;
  }

  [data-theme="dark"] .btn-outline-secondary {
    border-color: #475569;
    color: #cbd5e1;
  }

  [data-theme="dark"] .btn-outline-secondary:hover {
    background: #475569;
    border-color: #475569;
    color: #fff;
  }

  [data-theme="dark"] .modal-content {
    background: #1e293b;
    border-color: #334155;
  }

  [data-theme="dark"] .modal-header {
    background: #1e293b;
    border-bottom-color: #334155;
  }

  [data-theme="dark"] .modal-title {
    color: #e2e8f0;
  }

  [data-theme="dark"] .modal-body {
    background: #1e293b;
    color: #e2e8f0;
  }

  [data-theme="dark"] .modal-footer {
    background: #1e293b;
    border-top-color: #334155;
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const events = @json($eventosCalendario ?? []);
  const feriados = @json($feriados ?? []);

  const weekdayLabels = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
  
  // Calendario pequeño en la tarjeta
  const gridAgenda = document.getElementById('calendarGridAgenda');
  const eventsListAgenda = document.getElementById('calendarEventsListAgenda');
  const monthLabelAgenda = document.getElementById('calendarMonthLabelAgenda');
  const prevBtnAgenda = document.getElementById('prevMonthBtnAgenda');
  const nextBtnAgenda = document.getElementById('nextMonthBtnAgenda');
  
  // Calendario en el modal
  const gridModal = document.getElementById('calendarGridModal');
  const eventsListModal = document.getElementById('calendarEventsListModal');
  const monthLabelModal = document.getElementById('calendarMonthLabelModal');
  const prevBtnModal = document.getElementById('prevMonthBtnModal');
  const nextBtnModal = document.getElementById('nextMonthBtnModal');

  const activeDateAgenda = events.length ? new Date(events[0].date + 'T00:00:00') : new Date();
  const activeDateModal = new Date(activeDateAgenda);

  function renderAgenda(date) {
    const year = date.getFullYear();
    const month = date.getMonth();
    monthLabelAgenda.textContent = date.toLocaleDateString('es-CL', { month: 'long', year: 'numeric' });
    gridAgenda.innerHTML = '';
    weekdayLabels.forEach(label => {
      const el = document.createElement('div');
      el.className = 'calendar-weekday';
      el.textContent = label;
      gridAgenda.appendChild(el);
    });

    const start = new Date(year, month, 1);
    const startOffset = (start.getDay() + 6) % 7;
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const todayStr = new Date().toISOString().slice(0, 10);

    for (let i = 0; i < startOffset; i++) {
      const empty = document.createElement('div');
      gridAgenda.appendChild(empty);
    }

    for (let day = 1; day <= daysInMonth; day++) {
      const cellDate = new Date(year, month, day);
      const dateStr = cellDate.toISOString().slice(0, 10);
      const cell = document.createElement('div');
      cell.className = 'calendar-cell';
      if (dateStr === todayStr) cell.classList.add('is-today');

      // Verificar si es feriado
      const isHoliday = feriados.hasOwnProperty(dateStr);
      const holidayName = isHoliday ? feriados[dateStr] : null;
      if (isHoliday) {
        cell.classList.add('is-holiday');
        cell.title = holidayName;
      }

      const dayLabel = document.createElement('div');
      dayLabel.className = 'day';
      dayLabel.textContent = day;
      cell.appendChild(dayLabel);
      
      if (isHoliday) {
        const holidayIndicator = document.createElement('div');
        holidayIndicator.className = 'holiday-indicator';
        holidayIndicator.textContent = '🎉';
        holidayIndicator.title = holidayName;
        cell.appendChild(holidayIndicator);
      }

      const dayEvents = events.filter(ev => ev.date === dateStr);
      if (dayEvents.length) {
        const entrevistas = dayEvents.filter(ev => ev.type === 'entrevista');
        const bloqueos = dayEvents.filter(ev => ev.type === 'bloqueo');
        
        if (entrevistas.length > 0) {
          // Separar entrevistas por modalidad
          const entrevistasVirtuales = entrevistas.filter(ev => ev.modalidad && ev.modalidad.toLowerCase() === 'virtual');
          const entrevistasPresenciales = entrevistas.filter(ev => ev.modalidad && ev.modalidad.toLowerCase() === 'presencial');
          const entrevistasSinModalidad = entrevistas.filter(ev => !ev.modalidad || (ev.modalidad.toLowerCase() !== 'virtual' && ev.modalidad.toLowerCase() !== 'presencial'));
          
          // En el mini calendario solo mostrar indicadores de color sin texto
          if (entrevistasVirtuales.length > 0) {
            const indicator = document.createElement('div');
            indicator.className = 'event-dot-indicator event-dot-indicator-virtual';
            indicator.title = `${entrevistasVirtuales.length} entrevista${entrevistasVirtuales.length > 1 ? 's' : ''} virtual${entrevistasVirtuales.length > 1 ? 'es' : ''}`;
            cell.appendChild(indicator);
          }
          
          if (entrevistasPresenciales.length > 0) {
            const indicator = document.createElement('div');
            indicator.className = 'event-dot-indicator event-dot-indicator-presencial';
            indicator.title = `${entrevistasPresenciales.length} entrevista${entrevistasPresenciales.length > 1 ? 's' : ''} presencial${entrevistasPresenciales.length > 1 ? 'es' : ''}`;
            cell.appendChild(indicator);
          }
          
          if (entrevistasSinModalidad.length > 0) {
            const indicator = document.createElement('div');
            indicator.className = 'event-dot-indicator event-dot-indicator-default';
            indicator.title = `${entrevistasSinModalidad.length} entrevista${entrevistasSinModalidad.length > 1 ? 's' : ''}`;
            cell.appendChild(indicator);
          }
        }
        
        if (bloqueos.length > 0) {
          // En el mini calendario solo mostrar indicador de color sin texto
          const indicator = document.createElement('div');
          indicator.className = 'event-dot-indicator event-dot-indicator-bloqueo';
          indicator.title = `${bloqueos.length} bloqueo${bloqueos.length > 1 ? 's' : ''}`;
          cell.appendChild(indicator);
        }
      }

      gridAgenda.appendChild(cell);
    }

    eventsListAgenda.innerHTML = '';
    const proximas = events.filter(ev => ev.date >= todayStr).slice(0, 5);
    proximas.forEach(ev => {
      const chip = document.createElement('span');
      chip.className = ev.type === 'bloqueo' ? 'event-chip event-chip-bloqueo' : 'event-chip';
      const icon = ev.type === 'bloqueo' ? 'fa-ban' : 'fa-calendar-day';
      const fechaFormateada = new Date(ev.date + 'T00:00:00');
      const dia = String(fechaFormateada.getDate()).padStart(2, '0');
      const mes = String(fechaFormateada.getMonth() + 1).padStart(2, '0');
      const año = fechaFormateada.getFullYear();
      const fechaStr = `${dia}/${mes}/${año}`;
      chip.innerHTML = `<i class="fas ${icon} me-1"></i>${fechaStr} · ${ev.full}`;
      eventsListAgenda.appendChild(chip);
    });
  }

  function renderModal(date) {
    const year = date.getFullYear();
    const month = date.getMonth();
    monthLabelModal.textContent = date.toLocaleDateString('es-CL', { month: 'long', year: 'numeric' });
    gridModal.innerHTML = '';
    weekdayLabels.forEach(label => {
      const el = document.createElement('div');
      el.className = 'calendar-weekday';
      el.textContent = label;
      gridModal.appendChild(el);
    });

    const start = new Date(year, month, 1);
    const startOffset = (start.getDay() + 6) % 7;
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const todayStr = new Date().toISOString().slice(0, 10);

    for (let i = 0; i < startOffset; i++) {
      const empty = document.createElement('div');
      gridModal.appendChild(empty);
    }

    for (let day = 1; day <= daysInMonth; day++) {
      const cellDate = new Date(year, month, day);
      const dateStr = cellDate.toISOString().slice(0, 10);
      const cell = document.createElement('div');
      cell.className = 'calendar-cell';
      if (dateStr === todayStr) cell.classList.add('is-today');
      
      // Verificar si es feriado
      const isHoliday = feriados.hasOwnProperty(dateStr);
      const holidayName = isHoliday ? feriados[dateStr] : null;
      if (isHoliday) {
        cell.classList.add('is-holiday');
        cell.title = holidayName;
      }

      const dayLabel = document.createElement('div');
      dayLabel.className = 'day';
      dayLabel.textContent = day;
      cell.appendChild(dayLabel);
      
      if (isHoliday) {
        const holidayIndicator = document.createElement('div');
        holidayIndicator.className = 'holiday-indicator';
        holidayIndicator.textContent = '🎉';
        holidayIndicator.title = holidayName;
        cell.appendChild(holidayIndicator);
      }

      const dayEvents = events.filter(ev => ev.date === dateStr);
      if (dayEvents.length) {
        const entrevistas = dayEvents.filter(ev => ev.type === 'entrevista');
        const bloqueos = dayEvents.filter(ev => ev.type === 'bloqueo');
        
        if (entrevistas.length > 0) {
          // Separar entrevistas por modalidad
          const entrevistasVirtuales = entrevistas.filter(ev => ev.modalidad && ev.modalidad.toLowerCase() === 'virtual');
          const entrevistasPresenciales = entrevistas.filter(ev => ev.modalidad && ev.modalidad.toLowerCase() === 'presencial');
          const entrevistasSinModalidad = entrevistas.filter(ev => !ev.modalidad || (ev.modalidad.toLowerCase() !== 'virtual' && ev.modalidad.toLowerCase() !== 'presencial'));
          
          // Mostrar entrevistas virtuales en celeste
          if (entrevistasVirtuales.length > 0) {
            const dot = document.createElement('div');
            dot.className = 'event-dot event-dot-entrevista-virtual';
            dot.textContent = `${entrevistasVirtuales.length} entrevista${entrevistasVirtuales.length > 1 ? 's' : ''} virtual${entrevistasVirtuales.length > 1 ? 'es' : ''}`;
            cell.appendChild(dot);
          }
          
          // Mostrar entrevistas presenciales en verde
          if (entrevistasPresenciales.length > 0) {
            const dot = document.createElement('div');
            dot.className = 'event-dot event-dot-entrevista-presencial';
            dot.textContent = `${entrevistasPresenciales.length} entrevista${entrevistasPresenciales.length > 1 ? 's' : ''} presencial${entrevistasPresenciales.length > 1 ? 'es' : ''}`;
            cell.appendChild(dot);
          }
          
          // Mostrar entrevistas sin modalidad definida en rojo (por defecto)
          if (entrevistasSinModalidad.length > 0) {
            const dot = document.createElement('div');
            dot.className = 'event-dot event-dot-entrevista';
            dot.textContent = `${entrevistasSinModalidad.length} entrevista${entrevistasSinModalidad.length > 1 ? 's' : ''}`;
            cell.appendChild(dot);
          }
        }
        
        if (bloqueos.length > 0) {
          const dot = document.createElement('div');
          dot.className = 'event-dot event-dot-bloqueo';
          dot.textContent = `${bloqueos.length} bloqueo${bloqueos.length > 1 ? 's' : ''}`;
          cell.appendChild(dot);
        }
      }

      gridModal.appendChild(cell);
    }

    eventsListModal.innerHTML = '';
    events.forEach(ev => {
      const chip = document.createElement('span');
      chip.className = ev.type === 'bloqueo' ? 'event-chip event-chip-bloqueo' : 'event-chip';
      const icon = ev.type === 'bloqueo' ? 'fa-ban' : 'fa-calendar-day';
      const fechaFormateada = new Date(ev.date + 'T00:00:00');
      const dia = String(fechaFormateada.getDate()).padStart(2, '0');
      const mes = String(fechaFormateada.getMonth() + 1).padStart(2, '0');
      const año = fechaFormateada.getFullYear();
      const fechaStr = `${dia}/${mes}/${año}`;
      chip.innerHTML = `<i class="fas ${icon} me-1"></i>${fechaStr} · ${ev.full}`;
      eventsListModal.appendChild(chip);
    });
  }

  if (prevBtnAgenda && nextBtnAgenda) {
    prevBtnAgenda.addEventListener('click', () => {
      activeDateAgenda.setMonth(activeDateAgenda.getMonth() - 1);
      renderAgenda(activeDateAgenda);
    });
    nextBtnAgenda.addEventListener('click', () => {
      activeDateAgenda.setMonth(activeDateAgenda.getMonth() + 1);
      renderAgenda(activeDateAgenda);
    });
  }

  if (prevBtnModal && nextBtnModal) {
    prevBtnModal.addEventListener('click', () => {
      activeDateModal.setMonth(activeDateModal.getMonth() - 1);
      renderModal(activeDateModal);
    });
    nextBtnModal.addEventListener('click', () => {
      activeDateModal.setMonth(activeDateModal.getMonth() + 1);
      renderModal(activeDateModal);
    });
  }

  // Inicializar calendarios
  if (gridAgenda) renderAgenda(activeDateAgenda);
  if (gridModal) renderModal(activeDateModal);

  // Funcionalidad de bloqueos rápidos
  const quickBlockButtons = document.querySelectorAll('.quick-block-btn');
  const bloqueoFecha = document.getElementById('bloqueoFecha');
  const bloqueoHoraInicio = document.getElementById('bloqueoHoraInicio');
  const bloqueoHoraFin = document.getElementById('bloqueoHoraFin');
  const bloqueoMotivo = document.getElementById('bloqueoMotivo');
  const clearQuickBlock = document.getElementById('clearQuickBlock');
  const bloqueoForm = document.getElementById('bloqueoForm');
  const addAnotherBlock = document.getElementById('addAnotherBlock');

  quickBlockButtons.forEach(btn => {
    btn.addEventListener('click', function() {
      bloqueoHoraInicio.value = this.getAttribute('data-hora-inicio');
      bloqueoHoraFin.value = this.getAttribute('data-hora-fin');
      bloqueoMotivo.value = this.getAttribute('data-motivo');
      // Si no hay fecha seleccionada, usar hoy
      if (!bloqueoFecha.value) {
        bloqueoFecha.value = new Date().toISOString().split('T')[0];
      }
      // Scroll suave al formulario
      bloqueoForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });
  });

  if (clearQuickBlock) {
    clearQuickBlock.addEventListener('click', function() {
      bloqueoHoraInicio.value = '';
      bloqueoHoraFin.value = '';
      bloqueoMotivo.value = '';
    });
  }

  // Funcionalidad "Agregar y continuar"
  if (addAnotherBlock) {
    addAnotherBlock.addEventListener('click', function(e) {
      e.preventDefault();
      const form = bloqueoForm;
      const formData = new FormData(form);
      
      fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
        }
      })
      .then(response => {
        if (response.ok) {
          // Limpiar solo los campos de hora y motivo, mantener la fecha
          bloqueoHoraInicio.value = '';
          bloqueoHoraFin.value = '';
          bloqueoMotivo.value = '';
          // Recargar la página para mostrar el nuevo bloqueo
          window.location.reload();
        } else {
          return response.text().then(html => {
            // Si hay errores, recargar la página para mostrar los mensajes
            window.location.reload();
          });
        }
      })
      .catch(error => {
        console.error('Error:', error);
        // En caso de error, enviar el formulario normalmente
        form.submit();
      });
    });
  }
});
</script>
@endsection
