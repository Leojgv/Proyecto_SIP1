@extends('layouts.dashboard_admin.admin')

@section('title', 'Dashboard Administrador')

@php
    $adminUser = auth()->user();
@endphp

@section('content')
  <div class="container-fluid">
    <div class="row align-items-center mb-4">
      <div class="col-lg-8">
        <h1 class="h3 mb-1">Dashboard Administrador</h1>
        <p class="text-muted mb-0">Resumen del sistema y accesos rápidos para la gestión institucional.</p>
      </div>
      <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
        <span class="badge bg-light text-dark me-2">
          <i class="fas fa-calendar-day me-1"></i>{{ $hoy->translatedFormat('d \\d\\e F, Y') }}
        </span>
        <span class="badge bg-light text-dark">
          <i class="fas fa-user-shield me-1"></i>{{ $adminUser ? trim(($adminUser->nombre ?? '') . ' ' . ($adminUser->apellido ?? '')) : 'Administrador' }}
        </span>
      </div>
    </div>

    @if (session('status'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
      </div>
    @endif

    <div class="row g-3 mb-4">
      <div class="col-xxl-2 col-lg-3 col-md-4 col-sm-6">
        <div class="small-box bg-primary h-100 shadow-sm">
          <div class="inner">
            <h3>{{ number_format($stats['total_estudiantes']) }}</h3>
            <p>Total Estudiantes</p>
          </div>
          <div class="icon"><i class="fas fa-user-graduate"></i></div>
          <a href="{{ route('estudiantes.index') }}" class="small-box-footer">
            +{{ number_format($stats['nuevos_estudiantes_mes']) }} este mes <i class="fas fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
      <div class="col-xxl-2 col-lg-3 col-md-4 col-sm-6">
        <div class="small-box bg-secondary h-100 shadow-sm">
          <div class="inner">
            <h3>{{ number_format($stats['casos_activos']) }}</h3>
            <p>Casos Activos</p>
          </div>
          <div class="icon"><i class="fas fa-briefcase-medical"></i></div>
          <a href="{{ route('solicitudes.index') }}" class="small-box-footer">
            Ver casos <i class="fas fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
      <div class="col-xxl-2 col-lg-3 col-md-4 col-sm-6">
        <div class="small-box bg-success h-100 shadow-sm">
          <div class="inner">
            <h3>{{ number_format($stats['casos_cerrados']) }}</h3>
            <p>Casos Cerrados</p>
          </div>
          <div class="icon"><i class="fas fa-circle-check"></i></div>
          <a href="{{ route('solicitudes.index') }}" class="small-box-footer">
            {{ number_format($stats['casos_cerrados_mes']) }} este mes <i class="fas fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
      <div class="col-xxl-2 col-lg-3 col-md-4 col-sm-6">
        <div class="small-box bg-warning h-100 shadow-sm text-dark">
          <div class="inner">
            <h3>{{ number_format($stats['casos_pendientes']) }}</h3>
            <p>Casos Pendientes</p>
          </div>
          <div class="icon"><i class="fas fa-hourglass-half"></i></div>
          <a href="{{ route('solicitudes.index') }}" class="small-box-footer text-dark">
            Requieren revisión <i class="fas fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
      <div class="col-xxl-2 col-lg-3 col-md-4 col-sm-6">
        <div class="small-box bg-info h-100 shadow-sm text-dark">
          <div class="inner">
            <h3>{{ number_format($stats['pendientes_aprobacion']) }}</h3>
            <p>Pendientes Aprobación</p>
          </div>
          <div class="icon"><i class="fas fa-triangle-exclamation"></i></div>
          <a href="{{ route('ajustes-razonables.index') }}" class="small-box-footer text-dark">
            Ver ajustes <i class="fas fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
      <div class="card-body d-flex flex-column flex-lg-row align-items-lg-center">
        <div class="mb-3 mb-lg-0 flex-grow-1">
          <h5 class="card-title mb-1">¿Necesitas registrar un nuevo caso?</h5>
          <p class="card-text text-muted mb-0">Centraliza desde aquí la creación de solicitudes y su seguimiento.</p>
        </div>
        <a href="{{ route('solicitudes.create') }}" class="btn btn-primary btn-lg ms-lg-auto">
          <i class="fas fa-plus me-2"></i>Registrar solicitud
        </a>
      </div>
    </div>

    @if (! empty($accionesRapidas))
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="card-title mb-0">Acciones rápidas</h5>
            <span class="text-muted small">Atajos a las funciones principales</span>
          </div>
          <div class="row g-3">
            @foreach ($accionesRapidas as $accion)
              <div class="col-xl-3 col-md-6">
                <a href="{{ $accion['route'] }}" class="btn w-100 btn-outline-primary d-flex align-items-center justify-content-between">
                  <div class="text-start">
                    <strong>{{ $accion['label'] }}</strong>
                    <p class="mb-0 small text-muted">{{ $accion['description'] }}</p>
                  </div>
                  <i class="fas {{ $accion['icon'] }} fa-lg ms-3"></i>
                </a>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    @endif

    <div class="row g-4 mb-4">
      <div class="col-xl-7">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <h5 class="card-title mb-0">Casos por Carrera</h5>
                <p class="text-muted mb-0">Distribución de casos activos y cerrados</p>
              </div>
              <a href="{{ route('solicitudes.index') }}" class="btn btn-outline-primary btn-sm">Ver detalle</a>
            </div>
            @forelse ($casosPorCarrera as $item)
              @php
                $activosPercent = $item->total > 0 ? round(($item->activos / $item->total) * 100) : 0;
              @endphp
              <div class="pb-3 mb-3 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <strong>{{ $item->carrera }}</strong>
                    <p class="text-muted small mb-0">{{ $item->total }} casos totales</p>
                  </div>
                  <span class="badge bg-light text-dark">{{ $activosPercent }}% activos</span>
                </div>
                <div class="progress mt-2" style="height: 6px;">
                  <div class="progress-bar bg-success" role="progressbar" style="width: {{ $activosPercent }}%"></div>
                </div>
                <div class="d-flex justify-content-between text-muted small mt-2">
                  <span>{{ $item->activos }} activos</span>
                  <span>{{ $item->cerrados }} cerrados</span>
                </div>
              </div>
            @empty
              <p class="text-muted mb-0">Aún no hay datos suficientes para mostrar esta sección.</p>
            @endforelse
          </div>
        </div>
      </div>
      <div class="col-xl-5">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <h5 class="card-title mb-0">Tipos de Discapacidad</h5>
                <p class="text-muted mb-0">Distribución de estudiantes por acompañamiento</p>
              </div>
            </div>
            @forelse ($tiposDiscapacidad as $tipo)
              <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center">
                  <strong>{{ $tipo['tipo'] }}</strong>
                  <span class="text-muted small">{{ $tipo['total'] }} casos</span>
                </div>
                <div class="progress mt-2" style="height: 6px;">
                  <div class="progress-bar" role="progressbar"
                       style="width: {{ $tipo['porcentaje'] }}%; background-color: {{ $tipo['color'] }}"></div>
                </div>
                <small class="text-muted d-block mt-1">{{ $tipo['porcentaje'] }}% del total</small>
              </div>
            @empty
              <p class="text-muted mb-0">No hay información disponible aún.</p>
            @endforelse
          </div>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="card-title mb-0">Actividad Reciente</h5>
            <p class="text-muted mb-0">Últimos movimientos dentro del sistema</p>
          </div>
        </div>
        <div class="list-group list-group-flush">
          @forelse ($actividadReciente as $actividad)
            <div class="list-group-item px-0">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <strong>{{ $actividad['titulo'] }}</strong>
                  <p class="mb-1 text-muted">{{ $actividad['detalle'] }}</p>
                  <small class="text-muted">
                    <i class="fas fa-clock me-1"></i>{{ $actividad['hace'] }}
                  </small>
                </div>
                <span class="badge bg-{{ $actividad['estado_badge'] }}">{{ $actividad['estado'] }}</span>
              </div>
            </div>
          @empty
            <p class="text-muted mb-0">Todavía no hay movimientos registrados.</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>
@endsection
