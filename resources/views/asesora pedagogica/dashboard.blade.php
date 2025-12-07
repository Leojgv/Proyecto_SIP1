@extends('layouts.dashboard_asesorapedagogica.app')

@php use Illuminate\Support\Str; @endphp

@section('title', 'Dashboard Asesora Pedagogica')

@section('content')
<div class="dashboard-page">
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

  <div class="page-header mb-4">
    <h1 class="h4 mb-1">Dashboard Asesora Pedagogica</h1>
    <p class="text-muted mb-0">Resumen de casos, autorizaciones y accesos rapidos para la gestion institucional.</p>
  </div>

  <div class="row g-3 mb-4">
    @foreach ($metrics as $metric)
      <div class="col-12 col-md-6 col-xl-3">
        <div class="stats-card" style="background: #dc2626;">
          <div class="stats-card__value">{{ $metric['value'] }}</div>
          <div class="stats-card__icon"><i class="fas {{ $metric['icon'] ?? 'fa-circle' }}"></i></div>
          <p class="stats-card__title">{{ $metric['label'] }}</p>
          <small class="stats-card__sub">{{ $metric['helper'] }}</small>
        </div>
      </div>
    @endforeach
  </div>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body d-flex flex-wrap justify-content-between align-items-center">
      <div>
        <h5 class="mb-1">Necesitas registrar un nuevo caso?</h5>
        <p class="text-muted mb-0">Centraliza desde aqui la creacion de solicitudes y su seguimiento.</p>
      </div>
      <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalRegistrarSolicitud">
        <i class="fas fa-plus me-2"></i>Registrar solicitud
      </button>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-xl-7">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h5 class="card-title mb-0">Casos para revision</h5>
              <small class="text-muted">Solicitudes que requieren tu autorizacion</small>
            </div>
            <a href="{{ route('asesora-pedagogica.casos.index') }}" class="btn btn-sm btn-outline-danger">Ver todos</a>
          </div>
          @forelse ($casesForReview as $case)
            <div class="case-item">
              <div>
                <strong>{{ $case['student'] }}</strong>
                <p class="text-muted mb-1">{{ $case['program'] }}</p>
                <p class="text-muted small mb-0">{{ $case['proposed_adjustment'] }}</p>
              </div>
              <div class="text-end">
                <span class="badge priority-badge priority-{{ Str::slug(strtolower($case['priority'])) }}">{{ $case['priority'] }}</span>
                <span class="badge status-badge">{{ $case['status'] }}</span>
                <p class="text-muted small mb-0">Recibido: {{ $case['received_at'] }}</p>
                <div class="mt-2 d-flex gap-2 justify-content-end flex-wrap">
                  <a href="{{ $case['detail_url'] ?? route('asesora-pedagogica.casos.show', $case['case_id']) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-eye me-1"></i>Ver detalles
                  </a>
                  @if (!empty($case['send_url']) && $case['status'] === 'Pendiente de preaprobación')
                    <form action="{{ $case['send_url'] }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Enviar este caso a Dirección de Carrera para aprobación final?');">
                        <i class="fas fa-paper-plane me-1"></i>Enviar a Dirección
                      </button>
                    </form>
                    <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalDevolverACTT{{ $case['case_id'] }}">
                      <i class="fas fa-arrow-left me-1"></i>Devolver a A. Técnico
                    </button>
                  @endif
                </div>
              </div>
            </div>
          @empty
            <p class="text-muted mb-0">No tienes casos pendientes actualmente.</p>
          @endforelse
        </div>
      </div>
    </div>
    <div class="col-xl-5">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h5 class="card-title mb-0">Casos autorizados</h5>
              <small class="text-muted">Enviados a Direccion para aprobacion final</small>
            </div>
          </div>
          @forelse ($authorizedCases as $case)
            <div class="timeline-item">
              <div>
                <strong>{{ $case['student'] }}</strong>
                <p class="text-muted mb-1">{{ $case['program'] }}</p>
                <p class="text-muted small mb-0">{{ $case['follow_up'] }}</p>
              </div>
              <div class="text-end">
                <span class="badge bg-success-subtle text-success">{{ $case['status'] }}</span>
                <p class="text-muted small mb-0">Autorizado: {{ $case['authorized_at'] }}</p>
              </div>
            </div>
          @empty
            <p class="text-muted mb-0">No hay casos autorizados recientemente.</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>

@push('styles')
<style>
  .dashboard-page {
    background: transparent;
    padding: 1rem;
    border-radius: 1.5rem;
  }
  .page-header h1 {
    font-weight: 600;
    color: #1f1f2d;
  }
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
    background: #fff;
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
    background: #fff;
    flex-wrap: wrap;
  }
  .priority-badge,
  .status-badge {
    border-radius: 999px;
    padding: .35rem .85rem;
    font-size: .75rem;
  }
  .priority-alta {
    background: #fee2e2;
    color: #b91c1c;
  }
  .priority-media {
    background: #fef3c7;
    color: #b45309;
  }
  .priority-baja {
    background: #d1fae5;
    color: #047857;
  }
  .status-badge {
    background: #eef2ff;
    color: #4338ca;
  }
  @media (max-width: 768px) {
    .stats-card {
      min-height: 160px;
    }
  }
</style>
@endpush

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
      <form action="{{ route('asesora-pedagogica.solicitud.store') }}" method="POST">
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

<!-- Modales para Devolver a A. Técnico -->
@foreach ($casesForReview as $case)
  @if ($case['status'] === 'Pendiente de preaprobación')
    <div class="modal fade" id="modalDevolverACTT{{ $case['case_id'] }}" tabindex="-1" aria-labelledby="modalDevolverACTTLabel{{ $case['case_id'] }}" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-warning text-dark">
            <h5 class="modal-title" id="modalDevolverACTTLabel{{ $case['case_id'] }}">
              <i class="fas fa-arrow-left me-2"></i>Devolver a Asesora Técnica
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="{{ route('asesora-pedagogica.casos.devolver-actt', $case['case_id']) }}" method="POST">
            @csrf
            <div class="modal-body">
              <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Estudiante:</strong> {{ $case['student'] }}<br>
                <strong>Carrera:</strong> {{ $case['program'] }}
              </div>
              <div class="mb-3">
                <label for="motivo_devolucion{{ $case['case_id'] }}" class="form-label">
                  Motivo de devolución <span class="text-danger">*</span>
                </label>
                <textarea 
                  name="motivo_devolucion" 
                  id="motivo_devolucion{{ $case['case_id'] }}" 
                  rows="4" 
                  class="form-control @error('motivo_devolucion') is-invalid @enderror" 
                  placeholder="Describe los motivos por los que necesitas devolver el caso a la Asesora Técnica..." 
                  required
                  minlength="10"
                >{{ old('motivo_devolucion') }}</textarea>
                @error('motivo_devolucion')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Mínimo 10 caracteres</small>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="fas fa-times me-2"></i>Cancelar
              </button>
              <button type="submit" class="btn btn-warning">
                <i class="fas fa-arrow-left me-2"></i>Devolver a A. Técnico
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif
@endforeach
@endsection
