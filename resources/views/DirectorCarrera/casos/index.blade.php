@extends('layouts.Dashboard_director.app')

@php use Illuminate\Support\Str; @endphp

@section('title', 'Casos')

@section('content')
<div class="dashboard-page">
  <div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h1 class="h4 mb-1">Casos vinculados a tu dirección</h1>
      <p class="text-muted mb-0">Solicitudes enviadas por el equipo o asociadas a tus carreras.</p>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="accordion casos-director-accordion" id="casosDirector">
        @forelse($solicitudesPorEstudiante as $grupo)
          @php
            $primer = $grupo->first();
            $collapseId = 'est-' . ($primer->estudiante_id ?? 'na');
            $headingId = 'head-est-' . ($primer->estudiante_id ?? 'na');
            $nombre = trim(($primer->estudiante->nombre ?? 'Estudiante') . ' ' . ($primer->estudiante->apellido ?? ''));
            $carrera = $primer->estudiante->carrera->nombre ?? 'Sin carrera';
          @endphp
          @php
            $estado = $primer->estado ?? 'Pendiente';
            $badgeClass = match(strtolower($estado)) {
              'aprobado', 'aprobada' => 'bg-success',
              'rechazado', 'rechazada' => 'bg-danger',
              default => 'bg-warning text-dark',
            };
          @endphp
          <div class="accordion-item caso-card border-0 mb-3 shadow-sm">
            <h2 class="accordion-header" id="{{ $headingId }}">
              <button class="accordion-button caso-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false" aria-controls="{{ $collapseId }}">
                <div class="d-flex align-items-center gap-3 w-100">
                  <div>
                    <div class="fw-semibold">{{ $nombre }}</div>
                    <small class="text-muted">{{ $carrera }}</small>
                  </div>
                  <span class="badge {{ $badgeClass }}">{{ $estado }}</span>
                  <div class="text-muted small text-nowrap ms-auto">
                    <i class="far fa-calendar me-1"></i>{{ $primer->fecha_solicitud?->format('d/m/Y') ?? 's/f' }}
                  </div>
                </div>
              </button>
            </h2>
            <div id="{{ $collapseId }}" class="accordion-collapse collapse" aria-labelledby="{{ $headingId }}" data-bs-parent="#casosDirector">
              <div class="accordion-body caso-body">
                @foreach ($grupo as $solicitud)
                  @php
                    $estadoItem = $solicitud->estado ?? 'Pendiente';
                    $badgeItem = match(strtolower($estadoItem)) {
                      'aprobado', 'aprobada' => 'bg-success',
                      'rechazado', 'rechazada' => 'bg-danger',
                      default => 'bg-warning text-dark',
                    };
                    $ajustesCount = $solicitud->ajustesRazonables->count();
                    $ajustesAprobados = $solicitud->ajustesRazonables->where('estado', 'Aprobado')->count();
                    $ajustesRechazados = $solicitud->ajustesRazonables->where('estado', 'Rechazado')->count();
                  @endphp
                  <div class="border rounded p-3 mb-3 bg-light">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="flex-grow-1">
                        @if($solicitud->titulo)
                          <h6 class="fw-semibold mb-2">{{ $solicitud->titulo }}</h6>
                        @endif
                        <p class="text-muted small mb-2">{{ $solicitud->descripcion ?? 'Sin descripción registrada.' }}</p>
                        
                        <div class="row g-2 mb-2">
                          <div class="col-md-6">
                            <small class="text-muted d-block">
                              <i class="fas fa-calendar-alt me-1"></i><strong>Fecha solicitud:</strong> {{ $solicitud->fecha_solicitud?->format('d/m/Y') ?? 's/f' }}
                            </small>
                          </div>
                          @if($solicitud->estudiante->rut)
                            <div class="col-md-6">
                              <small class="text-muted d-block">
                                <i class="fas fa-id-card me-1"></i><strong>RUT:</strong> {{ $solicitud->estudiante->rut }}
                              </small>
                            </div>
                          @endif
                          @if($solicitud->asesor)
                            <div class="col-md-6">
                              <small class="text-muted d-block">
                                <i class="fas fa-user-tie me-1"></i><strong>Asesora Pedagógica:</strong> {{ $solicitud->asesor->nombre }} {{ $solicitud->asesor->apellido }}
                              </small>
                            </div>
                          @endif
                          @if($solicitud->updated_at)
                            <div class="col-md-6">
                              <small class="text-muted d-block">
                                <i class="fas fa-clock me-1"></i><strong>Última actualización:</strong> {{ $solicitud->updated_at->format('d/m/Y H:i') }}
                              </small>
                            </div>
                          @endif
                        </div>

                        @if($ajustesCount > 0)
                          <div class="mt-2">
                            <small class="text-muted d-block mb-1">
                              <i class="fas fa-sliders me-1"></i><strong>Ajustes razonables:</strong>
                            </small>
                            <div class="d-flex flex-wrap gap-2">
                              <span class="badge bg-info text-dark">
                                Total: {{ $ajustesCount }}
                              </span>
                              @if($ajustesAprobados > 0)
                                <span class="badge bg-success">
                                  Aprobados: {{ $ajustesAprobados }}
                                </span>
                              @endif
                              @if($ajustesRechazados > 0)
                                <span class="badge bg-danger">
                                  Rechazados: {{ $ajustesRechazados }}
                                </span>
                              @endif
                            </div>
                          </div>
                        @endif

                        @if($solicitud->motivo_rechazo)
                          <div class="alert alert-warning small mt-2 mb-0">
                            <i class="fas fa-exclamation-triangle me-1"></i><strong>Motivo de rechazo:</strong> {{ $solicitud->motivo_rechazo }}
                          </div>
                        @endif
                    </div>
                      <div class="ms-3 d-flex flex-column align-items-end gap-2">
                    <span class="badge {{ $badgeItem }}">{{ $estadoItem }}</span>
                        <a href="{{ route('director.casos.show', $solicitud) }}" class="btn btn-sm btn-outline-danger">
                      <i class="fas fa-eye me-1"></i>Ver detalles
                    </a>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        @empty
          <p class="text-center text-muted py-4 mb-0">No hay casos registrados para tus carreras.</p>
        @endforelse
      </div>
      <div class="d-flex justify-content-end mt-3">
        {{-- Agrupado sin paginación para mantener casos del mismo estudiante juntos --}}
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  .casos-director-accordion .caso-card {
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    overflow: hidden;
  }
  .caso-btn {
    background: #eef2ff;
    color: #1f2937;
  }
  .caso-btn:focus { box-shadow: none; }
  .caso-body {
    background: #fff;
    color: #1f2937;
  }
  .caso-body .text-muted { color: #6b7280 !important; }
  .dark-mode .casos-director-accordion .caso-card {
    border-color: #1e293b;
    box-shadow: 0 10px 30px rgba(3, 7, 18, .35);
  }
  .dark-mode .caso-btn {
    background: #0f172a;
    color: #e5e7eb;
  }
  .dark-mode .caso-body {
    background: #0b1220;
    color: #e5e7eb;
  }
  .dark-mode .caso-body .text-muted { color: #9ca3af !important; }
</style>
@endpush
