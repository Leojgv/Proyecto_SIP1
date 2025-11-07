@extends('layouts.dashboard_estudiante.student')

@section('title', 'Dashboard Estudiante')

@section('content')
<div class="container-fluid">
  <div class="row align-items-center mb-4">
    <div class="col-lg-8">
      <h1 class="h3 mb-1">Mi Dashboard</h1>
      <p class="text-muted mb-0">Gestiona tus solicitudes y ajustes académicos de forma centralizada.</p>
    </div>
    <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
      <span class="badge bg-light text-dark me-2"><i class="fas fa-calendar-day me-1"></i>{{ $hoy->translatedFormat('d \d\e F, Y') }}</span>
      <span class="badge bg-light text-dark"><i class="fas fa-user-graduate me-1"></i>{{ $estudiante->nombre }} {{ $estudiante->apellido }}</span>
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
          <h3>{{ $stats['solicitudes_activas'] }}</h3>
          <p>Solicitudes Activas</p>
        </div>
        <div class="icon"><i class="fas fa-file-alt"></i></div>
        <a href="{{ route('solicitudes.index') }}" class="small-box-footer">Ver solicitudes <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <div class="col-xxl-2 col-lg-3 col-md-4 col-sm-6">
      <div class="small-box bg-success h-100 shadow-sm">
        <div class="inner">
          <h3>{{ $stats['ajustes_activos'] }}</h3>
          <p>Ajustes Activos</p>
        </div>
        <div class="icon"><i class="fas fa-sliders-h"></i></div>
        <a href="{{ route('ajustes-razonables.index') }}" class="small-box-footer">Ver ajustes <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <div class="col-xxl-2 col-lg-3 col-md-4 col-sm-6">
      <div class="small-box bg-warning h-100 shadow-sm">
        <div class="inner">
          <h3>{{ $stats['problemas_detectados'] }}</h3>
          <p>Problemas Detectados</p>
        </div>
        <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
        <a href="{{ route('solicitudes.index') }}" class="small-box-footer">Revisar detalles <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <div class="col-xxl-2 col-lg-3 col-md-4 col-sm-6">
      <div class="small-box bg-info h-100 shadow-sm">
        <div class="inner">
          <h3>{{ $stats['entrevistas_programadas'] }}</h3>
          <p>Próximas Entrevistas</p>
        </div>
        <div class="icon"><i class="fas fa-comments"></i></div>
        <a href="{{ route('entrevistas.index') }}" class="small-box-footer">Ver agenda <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <div class="col-xxl-2 col-lg-3 col-md-4 col-sm-6">
      <div class="small-box bg-secondary h-100 shadow-sm">
        <div class="inner">
          <h3>{{ $stats['cursos_con_ajustes'] }}</h3>
          <p>Cursos con Ajustes</p>
        </div>
        <div class="icon"><i class="fas fa-book-open"></i></div>
        <a href="{{ route('ajustes-razonables.index') }}" class="small-box-footer">Gestionar cursos <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>
  </div>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body d-flex flex-column flex-lg-row align-items-lg-center justify-content-between">
      <div class="mb-3 mb-lg-0">
        <h5 class="card-title mb-1">¿Necesitas solicitar una entrevista?</h5>
        <p class="card-text text-muted mb-0">Coordina con el equipo de asesoría pedagógica para recibir apoyo personalizado.</p>
      </div>
      <a href="{{ route('entrevistas.create') }}" class="btn btn-primary btn-lg">
        <i class="fas fa-calendar-plus me-2"></i>Solicitar Entrevista
      </a>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-xl-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="card-title mb-0">Mis Solicitudes</h5>
            <a href="{{ route('solicitudes.index') }}" class="btn btn-sm btn-outline-primary">Ver todas</a>
          </div>
          <div class="list-group list-group-flush">
            @forelse ($misSolicitudes as $solicitud)
              <div class="list-group-item px-0 d-flex align-items-start justify-content-between">
                <div>
                  <h6 class="mb-1">
                    <span class="badge bg-light text-dark me-2">{{ $solicitud->fecha_solicitud?->format('d/m/Y') }}</span>
                    {{ \Illuminate\Support\Str::title($solicitud->estado ?? 'pendiente') }}
                  </h6>
                  @if ($solicitud->asesorPedagogico)
                    <small class="text-muted d-block"><i class="fas fa-user-tie me-1"></i>Asesor: {{ $solicitud->asesorPedagogico->nombre }}</small>
                  @endif
                  @if ($solicitud->directorCarrera)
                    <small class="text-muted d-block"><i class="fas fa-user-shield me-1"></i>Director: {{ $solicitud->directorCarrera->nombre }}</small>
                  @endif
                </div>
                <a href="{{ route('solicitudes.show', $solicitud) }}" class="btn btn-sm btn-outline-secondary">Ver detalles</a>
              </div>
            @empty
              <p class="text-muted text-center my-4">Aún no tienes solicitudes registradas.</p>
            @endforelse
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-6">
      <div class="card border-0 shadow-sm h-100 mb-4 mb-xl-0">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="card-title mb-0">Próximas Entrevistas</h5>
            <a href="{{ route('entrevistas.index') }}" class="btn btn-sm btn-outline-primary">Ver agenda</a>
          </div>
          <div class="list-group list-group-flush">
            @forelse ($proximasEntrevistas as $entrevista)
              <div class="list-group-item px-0">
                <div class="d-flex align-items-center justify-content-between">
                  <div>
                    <h6 class="mb-1"><i class="fas fa-calendar-alt me-2 text-info"></i>{{ $entrevista->fecha?->format('d/m/Y') }}</h6>
                    @if ($entrevista->asesorPedagogico)
                      <small class="text-muted"><i class="fas fa-user-friends me-1"></i>{{ $entrevista->asesorPedagogico->nombre }}</small>
                    @endif
                  </div>
                  <a href="{{ route('entrevistas.show', $entrevista) }}" class="btn btn-sm btn-outline-secondary">Ver detalles</a>
                </div>
              </div>
            @empty
              <p class="text-muted text-center my-4">No tienes entrevistas programadas próximamente.</p>
            @endforelse
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="card-title mb-0">Mis Ajustes Académicos</h5>
            <a href="{{ route('ajustes-razonables.index') }}" class="btn btn-sm btn-outline-primary">Gestionar</a>
          </div>
          <div class="list-group list-group-flush">
            @forelse ($misAjustes as $ajuste)
              <div class="list-group-item px-0">
                <div class="d-flex align-items-center justify-content-between">
                  <div>
                    <h6 class="mb-1">{{ $ajuste->nombre }}</h6>
                    <small class="text-muted">
                      <i class="fas fa-flag me-1"></i>{{ \Illuminate\Support\Str::title($ajuste->estado ?? 'pendiente') }}
                      @if ($ajuste->porcentaje_avance)
                        · <i class="fas fa-chart-line ms-1 me-1"></i>{{ $ajuste->porcentaje_avance }}%
                      @endif
                    </small>
                  </div>
                  <a href="{{ route('ajustes-razonables.show', $ajuste) }}" class="btn btn-sm btn-outline-secondary">Ver seguimiento</a>
                </div>
              </div>
            @empty
              <p class="text-muted text-center my-4">Aún no tienes ajustes académicos registrados.</p>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

