@extends('layouts.dashboard_asesoratecnica.app')

@section('title', 'Casos')

@section('content')
<div class="container-fluid casos-page">
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
      <h1 class="h4 mb-1">Casos asignados</h1>
      <p class="text-muted mb-0">Casos que requieren ajustes razonables.</p>
    </div>
  </div>

  @if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('status') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  @endif

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="accordion" id="casosAsesoraAccordion">
        @forelse($solicitudes as $solicitud)
          @php
            $collapseId = 'caso-' . $solicitud->id;
            $headingId = 'heading-' . $solicitud->id;
            $ajustesCount = $solicitud->ajustesRazonables()->count();
            $estadosPermitidos = ['Listo para Enviar', 'Pendiente de formulación de ajuste'];
            $puedeEnviarAPreaprobacion = in_array($solicitud->estado, $estadosPermitidos) && $ajustesCount > 0;
            $badgeClass = match($solicitud->estado) {
              'Pendiente de formulación de ajuste' => 'bg-warning text-dark',
              'Listo para Enviar' => 'bg-info',
              'Pendiente de preaprobación' => 'bg-primary',
              'Aprobado' => 'bg-success',
              'Rechazado' => 'bg-danger',
              default => 'bg-secondary'
            };
          @endphp
          <div class="accordion-item case-item border-0 mb-3 shadow-sm">
            <h2 class="accordion-header" id="{{ $headingId }}">
              <button class="accordion-button case-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false" aria-controls="{{ $collapseId }}">
                <div class="d-flex align-items-center gap-3 w-100">
                  <div>
                    <div class="fw-semibold">{{ $solicitud->estudiante->nombre ?? 'Estudiante' }} {{ $solicitud->estudiante->apellido ?? '' }}</div>
                    <small class="text-muted">{{ $solicitud->estudiante->carrera->nombre ?? 'Sin carrera' }}</small>
                  </div>
                  <span class="badge {{ $badgeClass }}">{{ $solicitud->estado ?? 'Pendiente' }}</span>
                  <div class="text-muted small text-nowrap ms-auto">
                    <i class="far fa-calendar me-1"></i>{{ $solicitud->fecha_solicitud?->format('d/m/Y') ?? 's/f' }}
                  </div>
                </div>
              </button>
            </h2>
            <div id="{{ $collapseId }}" class="accordion-collapse collapse" aria-labelledby="{{ $headingId }}" data-bs-parent="#casosAsesoraAccordion">
              <div class="accordion-body case-body">
                <div class="row g-3 mb-3">
                  <div class="col-12">
                    <div class="border rounded p-3 bg-light">
                      @if($solicitud->titulo)
                        <small class="text-muted d-block mb-1">
                          <strong>Título</strong>
                        </small>
                        <div class="fw-semibold mb-3">{{ $solicitud->titulo }}</div>
                      @endif
                      <small class="text-muted d-block mb-2">
                        <strong>Descripción</strong>
                      </small>
                      <div class="text-muted" style="line-height: 1.6;">{{ $solicitud->descripcion ?: 'Sin descripción registrada.' }}</div>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="border rounded p-3 bg-light">
                      <div class="row g-3">
                        <div class="col-md-6">
                          <small class="text-muted d-block mb-1">
                            <i class="far fa-calendar-alt me-1"></i><strong>Fecha/Hora</strong>
                          </small>
                          <div class="small">
                            {{ $solicitud->fecha_solicitud?->format('d/m/Y') ?? 's/f' }}
                            @if($solicitud->created_at)
                              - {{ $solicitud->created_at->format('H:i') }}
                            @endif
                          </div>
                        </div>
                        <div class="col-md-6">
                          <small class="text-muted d-block mb-1">
                            <i class="fas fa-file-pdf me-1"></i><strong>Evidencia / Observacion Adjuntado</strong>
                          </small>
                          <div class="small">
                            @if($solicitud->observaciones_pdf_ruta)
                              @php
                                $pdfUrl = asset('storage/' . $solicitud->observaciones_pdf_ruta);
                              @endphp
                              <a href="{{ $pdfUrl }}" target="_blank" class="text-danger">
                                <i class="fas fa-file-pdf me-1"></i>Ver PDF
                              </a>
                            @else
                              <span class="text-muted">Nada adjunto</span>
                            @endif
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  @if($solicitud->ajustesRazonables->count() > 0)
                    <div class="col-12">
                      <div class="border rounded p-3 bg-light">
                        <small class="text-muted d-block mb-3">
                          <strong>Ajustes aplicados</strong>
                        </small>
                        <div class="row g-2">
                          @foreach($solicitud->ajustesRazonables as $ajuste)
                            <div class="col-12">
                              <div class="d-flex justify-content-between align-items-start p-2 border rounded bg-white">
                                <div class="flex-grow-1 d-flex align-items-start gap-2">
                                  @if(in_array($solicitud->estado, ['Pendiente de preaprobación', 'Pendiente de Aprobación', 'Aprobado']))
                                    <div class="text-success mt-1">
                                      <i class="fas fa-check-circle"></i>
                                    </div>
                                  @endif
                                  <div>
                                    <div class="fw-semibold mb-1">{{ $ajuste->nombre }}</div>
                                    @if($ajuste->descripcion)
                                      <div class="text-muted small" style="line-height: 1.5;">{{ $ajuste->descripcion }}</div>
                                    @endif
                                  </div>
                                </div>
                                @if(in_array($solicitud->estado, ['Pendiente de formulación del caso', 'Pendiente de formulación de ajuste', 'Listo para Enviar']))
                                  <form action="{{ route('asesora-tecnica.ajustes.destroy', $ajuste) }}" method="POST" class="d-inline ms-2" onsubmit="return confirm('¿Estás seguro de eliminar este ajuste?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                      <i class="fas fa-trash"></i>
                                    </button>
                                  </form>
                                @endif
                              </div>
                            </div>
                          @endforeach
                        </div>
                      </div>
                    </div>
                  @endif
                </div>
                
                <div class="d-flex flex-wrap align-items-center gap-3">
                  <div class="text-muted small">Solicitado el {{ $solicitud->fecha_solicitud?->format('d/m/Y') ?? 's/f' }}</div>
                  <div class="text-muted small">Ajustes: <span class="fw-semibold">{{ $ajustesCount }}</span></div>
                  <div class="ms-auto">
                    @if($puedeEnviarAPreaprobacion)
                      <form action="{{ route('asesora-tecnica.solicitudes.enviar-preaprobacion', $solicitud) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('(Seguridad) ¿Estás seguro de enviar a Preaprobación?');">
                          <i class="fas fa-paper-plane me-1"></i>Enviar a Preaprobación
                        </button>
                      </form>
                    @elseif($solicitud->estado === 'Pendiente de preaprobación')
                      <span class="badge bg-warning text-dark">En preaprobación</span>
                    @elseif(in_array($solicitud->estado, ['Pendiente de Aprobación', 'Aprobado', 'Rechazado']))
                      <span class="text-muted small">Enviado</span>
                    @else
                      <span class="text-muted small">Formular ajustes primero</span>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
        @empty
          <p class="text-center text-muted py-4 mb-0">No tienes casos asignados actualmente.</p>
        @endforelse
      </div>
      <div class="d-flex justify-content-end mt-3">
        {{ $solicitudes->links() }}
      </div>
    </div>
  </div>
</div>

@push('styles')
<style>
  .casos-page .case-item {
    border-radius: 14px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
  }
  .casos-page .case-button {
    background: #eef2ff;
    color: #1f2937;
  }
  .casos-page .case-button:focus {
    box-shadow: none;
  }
  .casos-page .case-body {
    background: #fff;
    color: #1f2937;
  }
  .casos-page .case-body .text-muted {
    color: #6b7280 !important;
  }
  .dark-mode .casos-page .case-item {
    border-color: #1e293b;
    box-shadow: 0 10px 30px rgba(3, 7, 18, .35);
  }
  .dark-mode .casos-page .case-button {
    background: #0f172a;
    color: #e5e7eb;
  }
  .dark-mode .casos-page .case-body {
    background: #0b1220;
    color: #e5e7eb;
  }
  .dark-mode .casos-page .case-body .text-muted {
    color: #9ca3af !important;
  }
</style>
@endpush
@endsection
