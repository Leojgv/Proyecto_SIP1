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
      ['title' => 'Entrevistas pendientes', 'value' => $stats['entrevistasPendientes'] ?? 0, 'sub' => '+0 esta semana', 'icon' => 'fa-calendar-check', 'bg' => '#d62828'],
      ['title' => 'Entrevistas completadas', 'value' => $stats['entrevistasCompletadas'] ?? 0, 'sub' => 'Ver casos', 'icon' => 'fa-user-check', 'bg' => '#b51b1b'],
      ['title' => 'Casos registrados', 'value' => $stats['casosRegistrados'] ?? 0, 'sub' => '0 este mes', 'icon' => 'fa-folder-open', 'bg' => '#951010'],
      ['title' => 'Casos en proceso', 'value' => $stats['casosEnProceso'] ?? 0, 'sub' => 'Requieren revision', 'icon' => 'fa-hourglass-half', 'bg' => '#f4a5a5'],
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

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <h5 class="card-title mb-3">Acciones rapidas</h5>
      <div class="row g-3">
        <div class="col-md-3">
          <a href="{{ route('admin.users.index') }}" class="quick-link">
            <span>Gestion de Usuarios</span>
            <small>Administra permisos y cuentas</small>
          </a>
        </div>
        <div class="col-md-3">
          <a href="{{ route('estudiantes.create') }}" class="quick-link">
            <span>Nuevo Estudiante</span>
            <small>Registrar nuevo estudiante</small>
          </a>
        </div>
        <div class="col-md-3">
          <a href="{{ route('entrevistas.index') }}" class="quick-link">
            <span>Ver Reportes</span>
            <small>Ultimos casos y metricas</small>
          </a>
        </div>
        <div class="col-md-3">
          <a href="{{ route('home') }}" class="quick-link">
            <span>Configuracion</span>
            <small>Catalogos y carreras</small>
          </a>
        </div>
      </div>
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
            <a href="{{ route('entrevistas.index') }}" class="btn btn-sm btn-outline-danger">Ver agenda</a>
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
</style>
@endsection

