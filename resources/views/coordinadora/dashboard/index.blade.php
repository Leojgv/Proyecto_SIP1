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

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body d-flex flex-wrap justify-content-between align-items-center">
      <div>
        <h5 class="mb-1">¿Necesitas registrar un nuevo caso?</h5>
        <p class="text-muted mb-0">Centraliza desde aqui la creacion de solicitudes y su seguimiento.</p>
      </div>
      <a href="{{ route('solicitudes.create') }}" class="btn btn-danger">+ Registrar solicitud</a>
    </div>
  </div>

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
            <div class="timeline-item">
              <div>
                <strong>{{ $entrevista->solicitud->estudiante->nombre ?? 'Estudiante' }} {{ $entrevista->solicitud->estudiante->apellido ?? '' }}</strong>
                <p class="text-muted mb-0"><i class="far fa-calendar me-1"></i>{{ $entrevista->fecha?->format('d/m/Y') }} · {{ $entrevista->fecha?->format('H:i') }}</p>
              </div>
              <a href="{{ route('entrevistas.show', $entrevista) }}" class="btn btn-sm btn-outline-secondary">Ver detalles</a>
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
            <a href="{{ route('solicitudes.index') }}" class="btn btn-sm btn-outline-danger">Ver todos</a>
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
          <h5 class="card-title mb-3">Casos por Carrera</h5>
          <p class="text-muted mb-0">Aun no hay datos suficientes para mostrar esta seccion.</p>
        </div>
      </div>
    </div>
    <div class="col-xl-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-title mb-3">Actividad Reciente</h5>
          <p class="text-muted mb-0">Todavia no hay movimientos registrados.</p>
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
    background: #d62828;
    color: #fff;
    border-radius: 999px;
    padding: .2rem .5rem;
    font-size: .75rem;
  }
  .event-chip {
    border: 1px solid #f0f0f5;
    border-radius: 999px;
    padding: .4rem .75rem;
    background: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,.05);
  }
  .event-chip i { color: #d62828; }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const events = @json(
      ($proximasEntrevistas ?? collect())->map(function ($entrevista) {
          return [
              'date' => optional($entrevista->fecha_hora_inicio ?? $entrevista->fecha)->format('Y-m-d'),
              'label' => trim(($entrevista->solicitud->estudiante->nombre ?? 'Estudiante') . ' ' . ($entrevista->solicitud->estudiante->apellido ?? '')),
          ];
      })
    );

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
          const dot = document.createElement('div');
          dot.className = 'event-dot';
          dot.textContent = `${dayEvents.length} entrevista${dayEvents.length > 1 ? 's' : ''}`;
          cell.appendChild(dot);
        }

        grid.appendChild(cell);
      }

      eventsList.innerHTML = '';
      events.forEach(ev => {
        const chip = document.createElement('span');
        chip.className = 'event-chip';
        chip.innerHTML = `<i class="fas fa-calendar-day me-1"></i>${new Date(ev.date + 'T00:00:00').toLocaleDateString('es-CL')} · ${ev.label}`;
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
@endsection

