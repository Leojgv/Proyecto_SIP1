@extends('layouts.dashboard_coordinadora.app')

@section('title', 'Dashboard Coordinadora')

@section('content')
<div class="dashboard-page">
  <div class="page-header mb-4">
    <h1 class="h4 mb-1">Dashboard Coordinadora de Inclusion</h1>
    <p class="text-muted mb-0">Resumen de entrevistas y accesos rapidos para la gestion institucional.</p>
  </div>

  @php
    $cards = [
      ['title' => 'Entrevistas pendientes', 'value' => $stats['entrevistasPendientes'] ?? 0, 'sub' => '+0 esta semana', 'icon' => 'fa-calendar-check', 'bg' => '#dc2626'],
      ['title' => 'Entrevistas completadas', 'value' => $stats['entrevistasCompletadas'] ?? 0, 'sub' => 'Ver casos', 'icon' => 'fa-user-check', 'bg' => '#dc2626'],
      ['title' => 'Casos registrados', 'value' => $stats['casosRegistrados'] ?? 0, 'sub' => '0 este mes', 'icon' => 'fa-folder-open', 'bg' => '#dc2626'],
      ['title' => 'Casos en proceso', 'value' => $stats['casosEnProceso'] ?? 0, 'sub' => 'Requieren revision', 'icon' => 'fa-hourglass-half', 'bg' => '#dc2626'],
    ];
  @endphp

  <div class="row g-3 mb-4">
    @foreach ($cards as $card)
      <div class="col-12 col-md-6 col-xl-3">
        <div class="stats-card" style="background: {{ $card['bg'] }};">
          <div class="stats-card__value">{{ $card['value'] }}</div>
          <div class="stats-card__icon"><i class="fas {{ $card['icon'] }}"></i></div>
          <p class="stats-card__title">{{ $card['title'] }}</p>
          <small class="stats-card__sub">{{ $card['sub'] }}</small>
        </div>
      </div>
    @endforeach
  </div>

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

  <div class="row g-4 mb-4">
    <div class="col-xl-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h5 class="card-title mb-0">Proximas Entrevistas</h5>
              <small class="text-muted">Revisa las citas para los proximos dias</small>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#agendaModal">
              Ver agenda
            </button>
          </div>
          @forelse ($proximasEntrevistas as $entrevista)
            <div class="timeline-item d-flex justify-content-between align-items-start">
              <div class="flex-grow-1">
                <strong>{{ $entrevista->solicitud->estudiante->nombre ?? 'Estudiante' }} {{ $entrevista->solicitud->estudiante->apellido ?? '' }}</strong>
                <p class="text-muted mb-0">
                  <i class="far fa-calendar me-1"></i>{{ $entrevista->fecha?->format('d/m/Y') }} · {{ $entrevista->fecha_hora_inicio?->format('H:i') ?? '--' }}
                  @if($entrevista->modalidad)
                    · <span class="badge {{ $entrevista->modalidad === 'Virtual' ? 'bg-info' : 'bg-success' }}">{{ $entrevista->modalidad }}</span>
                  @endif
                  @if($entrevista->estado === 'Pospuesta')
                    · <span class="badge bg-warning text-dark">Pospuesta</span>
                  @endif
                </p>
              </div>
              @if($entrevista->estado !== 'Pospuesta')
                <button type="button"
                        class="btn btn-sm btn-outline-warning ms-2"
                        data-bs-toggle="modal"
                        data-bs-target="#modalPosponerEntrevista"
                        data-entrevista-id="{{ $entrevista->id }}"
                        data-estudiante-nombre="{{ $entrevista->solicitud->estudiante->nombre ?? 'Estudiante' }} {{ $entrevista->solicitud->estudiante->apellido ?? '' }}"
                        data-fecha-entrevista="{{ $entrevista->fecha?->format('d/m/Y') }}"
                        data-hora-entrevista="{{ $entrevista->fecha_hora_inicio?->format('H:i') ?? '--' }}"
                        title="Posponer entrevista">
                  <i class="fas fa-clock"></i>
                </button>
              @endif
            </div>
          @empty
            <p class="text-muted mb-0">No hay entrevistas agendadas.</p>
          @endforelse
        </div>
      </div>
    </div>
    <div class="col-xl-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h5 class="card-title mb-0">Casos Registrados Recientemente</h5>
              <small class="text-muted">Seguimiento y estados</small>
            </div>
          </div>
          @forelse ($casosRecientes as $caso)
            <div class="case-item">
              <div>
                <strong>{{ $caso->estudiante->nombre ?? 'Estudiante' }} {{ $caso->estudiante->apellido ?? '' }}</strong>
                <p class="text-muted mb-0">Registrado: {{ $caso->created_at?->format('d/m/Y') ?? $caso->fecha_solicitud?->format('d/m/Y') }}</p>
              </div>
              <span class="badge bg-warning text-dark">{{ ucfirst($caso->estado ?? 'Pendiente') }}</span>
            </div>
          @empty
            <p class="text-muted mb-0">Aun no registras casos.</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-xl-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h5 class="card-title mb-0">
                <i class="fas fa-chart-line text-danger me-2"></i>Métricas de Rendimiento
              </h5>
              <small class="text-muted">Indicadores clave de gestión</small>
            </div>
          </div>
          <div class="row g-3">
            <div class="col-6">
              <div class="metric-card p-3 border rounded">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <i class="fas fa-clock text-info"></i>
                  <small class="text-muted">Tiempo promedio</small>
                </div>
                <div class="h4 mb-0">{{ $stats['tiempoPromedioResolucion'] ?? '0' }} días</div>
                <small class="text-muted">Resolución de casos</small>
              </div>
            </div>
            <div class="col-6">
              <div class="metric-card p-3 border rounded">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <i class="fas fa-percentage text-success"></i>
                  <small class="text-muted">Tasa de aprobación</small>
                </div>
                <div class="h4 mb-0">{{ $stats['tasaAprobacion'] ?? '0' }}%</div>
                <small class="text-muted">Ajustes aprobados</small>
              </div>
            </div>
            <div class="col-6">
              <div class="metric-card p-3 border rounded">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <i class="fas fa-users text-warning"></i>
                  <small class="text-muted">Estudiantes activos</small>
                </div>
                <div class="h4 mb-0">{{ $stats['estudiantesActivos'] ?? '0' }}</div>
                <small class="text-muted">Con casos en proceso</small>
              </div>
            </div>
            <div class="col-6">
              <div class="metric-card p-3 border rounded">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <i class="fas fa-calendar-check text-primary"></i>
                  <small class="text-muted">Entrevistas este mes</small>
                </div>
                <div class="h4 mb-0">{{ $stats['entrevistasEsteMes'] ?? '0' }}</div>
                <small class="text-muted">Total programadas</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h5 class="card-title mb-0">
                <i class="fas fa-chart-pie text-danger me-2"></i>Distribución de Modalidades
              </h5>
              <small class="text-muted">Análisis de preferencias de entrevistas</small>
            </div>
          </div>
          <div class="row g-3">
            <div class="col-12">
              <div class="d-flex align-items-center justify-content-between p-3 border rounded bg-light">
                <div class="d-flex align-items-center gap-3">
                  <div class="modalidad-indicator bg-success"></div>
                  <div>
                    <strong>Presencial</strong>
                    <p class="text-muted small mb-0">{{ $stats['entrevistasPresenciales'] ?? '0' }} entrevistas</p>
                  </div>
                </div>
                <div class="h5 mb-0">{{ $stats['porcentajePresencial'] ?? '0' }}%</div>
              </div>
            </div>
            <div class="col-12">
              <div class="d-flex align-items-center justify-content-between p-3 border rounded bg-light">
                <div class="d-flex align-items-center gap-3">
                  <div class="modalidad-indicator bg-info"></div>
                  <div>
                    <strong>Virtual</strong>
                    <p class="text-muted small mb-0">{{ $stats['entrevistasVirtuales'] ?? '0' }} entrevistas</p>
                  </div>
                </div>
                <div class="h5 mb-0">{{ $stats['porcentajeVirtual'] ?? '0' }}%</div>
              </div>
            </div>
            <div class="col-12">
              <div class="p-3 border rounded bg-light">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <i class="fas fa-exclamation-triangle text-warning"></i>
                  <strong>Casos que requieren atención</strong>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                  <span class="text-muted small">Casos pendientes más de 4 días</span>
                  <span class="badge bg-warning text-dark">{{ $stats['casosUrgentes'] ?? '0' }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Modal agenda mensual --}}
<div class="modal fade" id="agendaModal" tabindex="-1" aria-labelledby="agendaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow">
      <div class="modal-header border-0">
        <div>
          <h5 class="modal-title" id="agendaModalLabel">Agenda del mes</h5>
          <small class="text-muted">Entrevistas programadas</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body pt-0">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-outline-secondary" id="prevMonthBtn" type="button">
              <i class="fas fa-chevron-left"></i>
            </button>
            <div class="fw-bold" id="calendarMonthLabel"></div>
            <button class="btn btn-sm btn-outline-secondary" id="nextMonthBtn" type="button">
              <i class="fas fa-chevron-right"></i>
            </button>
          </div>
          <span class="badge bg-danger-subtle text-danger"><i class="fas fa-calendar-day me-1"></i>Entrevistas</span>
        </div>
        <div id="calendarGrid" class="calendar-grid rounded-3"></div>
        <div class="mt-3">
          <p class="small text-muted mb-2">Próximas entrevistas</p>
          <div class="d-flex flex-wrap gap-2" id="calendarEventsList"></div>
        </div>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<style>
  .stats-card {
    color: #fff;
    border-radius: 18px;
    padding: 1.25rem;
    position: relative;
  }
  .stats-card__value {
    font-size: 2rem;
    font-weight: 700;
  }
  .stats-card__title {
    font-size: .95rem;
    margin-bottom: 0;
    text-transform: capitalize;
  }
  .stats-card__icon {
    position: absolute;
    top: 1rem;
    right: 1rem;
    opacity: .25;
    font-size: 2.5rem;
  }
  .stats-card__sub {
    font-size: .85rem;
    color: rgba(255,255,255,.8);
  }
  .quick-link {
    display: block;
    border: 1px solid #f1b0b0;
    border-radius: 12px;
    padding: 1rem;
    text-decoration: none;
    transition: all .2s ease;
    color: inherit;
  }
  .quick-link span {
    display: block;
    font-weight: 600;
    color: #b51b1b;
  }
  .quick-link small {
    color: #6c6c7a;
  }
  .quick-link:hover {
    background: #fff2f2;
    border-color: #d62828;
  }
  .timeline-item,
  .case-item {
    border: 1px solid #f0f0f5;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
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
  }
  .calendar-cell {
    background: #fff;
    border: 1px solid #f0f0f5;
    border-radius: 12px;
    min-height: 70px;
    padding: .5rem;
    position: relative;
    box-shadow: 0 2px 6px rgba(0,0,0,.03);
  }
  .calendar-cell.is-today {
    border-color: #d62828;
    box-shadow: 0 6px 14px rgba(214, 40, 40, .18);
  }
  .calendar-cell .day {
    font-weight: 700;
    color: #444;
  }
  .calendar-cell .event-dot {
    position: absolute;
    bottom: .5rem;
    left: .5rem;
    right: .5rem;
    background: #d62828;
    color: #fff;
    border-radius: 4px;
    padding: .12rem .3rem;
    font-size: .6rem;
    text-align: center;
    margin-bottom: .15rem;
  }
  .calendar-cell .event-dot:first-of-type {
    bottom: .5rem;
  }
  .calendar-cell .event-dot:last-of-type {
    bottom: .2rem;
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
  .event-chip {
    border: 1px solid #f0f0f5;
    border-radius: 999px;
    padding: .4rem .75rem;
    background: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,.05);
  }
  .event-chip i { color: #d62828; }
  .event-chip-bloqueo {
    border-color: #d1d5db;
    background: #f9fafb;
  }
  .event-chip-bloqueo i { color: #6b7280; }
  .metric-card {
    transition: all 0.2s ease;
  }
  .metric-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,.1);
  }
  .modalidad-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
  }

  /* Estilos para modo oscuro */
  [data-theme="dark"] .metric-card {
    background: #1e293b;
    border-color: #334155;
  }

  [data-theme="dark"] .metric-card .text-muted {
    color: #94a3b8;
  }

  [data-theme="dark"] .metric-card .h4 {
    color: #e2e8f0;
  }

  [data-theme="dark"] .bg-light {
    background: #1e293b !important;
    border-color: #334155 !important;
  }

  [data-theme="dark"] .bg-light strong {
    color: #e2e8f0;
  }

  [data-theme="dark"] .bg-light .text-muted {
    color: #94a3b8;
  }

  [data-theme="dark"] .bg-light .h5 {
    color: #e2e8f0;
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const events = @json($eventosCalendario ?? []);

    const weekdayLabels = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
    const grid = document.getElementById('calendarGrid');
    const eventsList = document.getElementById('calendarEventsList');
    const monthLabel = document.getElementById('calendarMonthLabel');
    const prevBtn = document.getElementById('prevMonthBtn');
    const nextBtn = document.getElementById('nextMonthBtn');

    const activeDate = events.length ? new Date(events[0].date + 'T00:00:00') : new Date();

    function render(date) {
      const year = date.getFullYear();
      const month = date.getMonth();
      monthLabel.textContent = date.toLocaleDateString('es-CL', { month: 'long', year: 'numeric' });
      grid.innerHTML = '';
      weekdayLabels.forEach(label => {
        const el = document.createElement('div');
        el.className = 'calendar-weekday';
        el.textContent = label;
        grid.appendChild(el);
      });

      const start = new Date(year, month, 1);
      const startOffset = (start.getDay() + 6) % 7; // lunes como inicio
      const daysInMonth = new Date(year, month + 1, 0).getDate();
      const todayStr = new Date().toISOString().slice(0, 10);

      for (let i = 0; i < startOffset; i++) {
        const empty = document.createElement('div');
        grid.appendChild(empty);
      }

      for (let day = 1; day <= daysInMonth; day++) {
        const cellDate = new Date(year, month, day);
        const dateStr = cellDate.toISOString().slice(0, 10);
        const cell = document.createElement('div');
        cell.className = 'calendar-cell';
        if (dateStr === todayStr) cell.classList.add('is-today');

        const dayLabel = document.createElement('div');
        dayLabel.className = 'day';
        dayLabel.textContent = day;
        cell.appendChild(dayLabel);

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
              dot.className = 'event-dot';
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

        grid.appendChild(cell);
      }

      eventsList.innerHTML = '';
      events.forEach(ev => {
        const chip = document.createElement('span');
        if (ev.type === 'bloqueo') {
          chip.className = 'event-chip event-chip-bloqueo';
          chip.innerHTML = `<i class="fas fa-ban me-1"></i>${new Date(ev.date + 'T00:00:00').toLocaleDateString('es-CL')} · ${ev.full}`;
        } else {
          chip.className = 'event-chip';
          const iconColor = ev.modalidad && ev.modalidad.toLowerCase() === 'virtual' ? 'text-info' : 
                           ev.modalidad && ev.modalidad.toLowerCase() === 'presencial' ? 'text-success' : 
                           'text-danger';
          chip.innerHTML = `<i class="fas fa-calendar-day me-1 ${iconColor}"></i>${new Date(ev.date + 'T00:00:00').toLocaleDateString('es-CL')} · ${ev.full}`;
        }
        eventsList.appendChild(chip);
      });
    }

    prevBtn?.addEventListener('click', () => {
      activeDate.setMonth(activeDate.getMonth() - 1);
      render(activeDate);
    });
    nextBtn?.addEventListener('click', () => {
      activeDate.setMonth(activeDate.getMonth() + 1);
      render(activeDate);
    });

    render(activeDate);
  });
</script>

<!-- Modal Registrar Solicitud -->
<div class="modal fade" id="modalRegistrarSolicitud" tabindex="-1" aria-labelledby="modalRegistrarSolicitudLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="modalRegistrarSolicitudLabel">
          <i class="fas fa-plus-circle me-2"></i>Registrar Nueva Solicitud
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('coordinadora.solicitud.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          @if($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <div class="mb-3">
            <label for="estudiante_id" class="form-label">
              Estudiante <span class="text-danger">*</span>
            </label>
            <select 
              name="estudiante_id" 
              id="estudiante_id" 
              class="form-select @error('estudiante_id') is-invalid @enderror" 
              required
            >
              <option value="">Selecciona un estudiante</option>
              @foreach($estudiantes ?? [] as $estudiante)
                <option value="{{ $estudiante->id }}" @selected(old('estudiante_id') == $estudiante->id)>
                  {{ $estudiante->nombre }} {{ $estudiante->apellido }}
                  @if($estudiante->rut)
                    ({{ $estudiante->rut }})
                  @endif
                  @if($estudiante->carrera)
                    - {{ $estudiante->carrera->nombre }}
                  @endif
                </option>
              @endforeach
            </select>
            @error('estudiante_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="titulo" class="form-label">
              Título <span class="text-danger">*</span>
            </label>
            <input 
              type="text" 
              name="titulo" 
              id="titulo" 
              class="form-control @error('titulo') is-invalid @enderror" 
              placeholder="Ingresa un título para la solicitud" 
              value="{{ old('titulo') }}"
              required
            >
            @error('titulo')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="descripcion" class="form-label">
              Descripción para la Entrevista <span class="text-danger">*</span>
            </label>
            <textarea 
              name="descripcion" 
              id="descripcion" 
              rows="5" 
              class="form-control @error('descripcion') is-invalid @enderror" 
              placeholder="Describe el motivo de la solicitud de entrevista..." 
              required
            >{{ old('descripcion') }}</textarea>
            @error('descripcion')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Mínimo 10 caracteres.</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i>Cancelar
          </button>
          <button type="submit" class="btn btn-danger">
            <i class="fas fa-save me-2"></i>Registrar Solicitud
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
{{-- Modal Posponer Entrevista --}}
<div class="modal fade" id="modalPosponerEntrevista" tabindex="-1" aria-labelledby="modalPosponerEntrevistaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-0">
        <div>
          <h5 class="modal-title" id="modalPosponerEntrevistaLabel">Posponer Entrevista</h5>
          <p class="text-muted mb-0 small">Indica el motivo por el cual necesitas posponer esta entrevista.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="formPosponerEntrevista" method="POST" action="#">
        @csrf
        <div class="modal-body">
          <div class="alert alert-info mb-3">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Estudiante:</strong> <span id="estudiante-nombre-modal"></span><br>
            <strong>Fecha:</strong> <span id="fecha-entrevista-modal"></span> a las <span id="hora-entrevista-modal"></span>
          </div>
          <div class="mb-3">
            <label for="motivo_posposicion" class="form-label">
              Motivo de posposición <span class="text-danger">*</span>
            </label>
            <textarea 
              id="motivo_posposicion" 
              name="motivo_posposicion" 
              class="form-control @error('motivo_posposicion') is-invalid @enderror" 
              rows="4" 
              placeholder="Ejemplo: Tengo un inconveniente personal, necesito reagendar para otra fecha..."
              required
              minlength="10"
            >{{ old('motivo_posposicion') }}</textarea>
            @error('motivo_posposicion')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Mínimo 10 caracteres. El estudiante recibirá una notificación con este motivo.</small>
          </div>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-warning">
            <i class="fas fa-clock me-2"></i>Posponer Entrevista
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const modalPosponer = document.getElementById('modalPosponerEntrevista');
    const formPosponer = document.getElementById('formPosponerEntrevista');
    
    if (modalPosponer && formPosponer) {
      modalPosponer.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const entrevistaId = button.getAttribute('data-entrevista-id');
        const estudianteNombre = button.getAttribute('data-estudiante-nombre');
        const fechaEntrevista = button.getAttribute('data-fecha-entrevista');
        const horaEntrevista = button.getAttribute('data-hora-entrevista');
        
        // Actualizar información en el modal
        document.getElementById('estudiante-nombre-modal').textContent = estudianteNombre;
        document.getElementById('fecha-entrevista-modal').textContent = fechaEntrevista;
        document.getElementById('hora-entrevista-modal').textContent = horaEntrevista;
        
        // Actualizar acción del formulario
        formPosponer.action = '{{ route("coordinadora.entrevistas.posponer", ":id") }}'.replace(':id', entrevistaId);
        
        // Limpiar textarea
        document.getElementById('motivo_posposicion').value = '';
      });
    }
  });
</script>
@endpush

@endsection
