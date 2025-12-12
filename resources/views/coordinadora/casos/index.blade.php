@extends('layouts.dashboard_coordinadora.app')

@section('title', 'Casos')

@section('content')
<div class="container-fluid casos-page">
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
      <h1 class="h4 mb-1">Casos de solicitudes</h1>
      <p class="text-muted mb-0">Listado de solicitudes vinculadas a los estudiantes.</p>
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
      <div class="accordion" id="casosAccordion">
        @forelse($solicitudes as $solicitud)
          @php
            $collapseId = 'caso-' . $solicitud->id;
            $headingId = 'heading-' . $solicitud->id;
            $estado = $solicitud->estado ?? 'Pendiente';
            $badgeClass = match($estado) {
              'Pendiente de entrevista' => 'bg-warning text-dark',
              'Pendiente de formulacion del caso' => 'bg-info text-dark',
              'Informado a CTP' => 'bg-primary',
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
                  <span class="badge {{ $badgeClass }}">{{ $estado }}</span>
                  <div class="text-muted small text-nowrap ms-auto">
                    <i class="far fa-calendar me-1"></i>{{ $solicitud->fecha_solicitud?->format('d/m/Y') ?? 's/f' }}
                  </div>
                </div>
              </button>
            </h2>
            <div id="{{ $collapseId }}" class="accordion-collapse collapse" aria-labelledby="{{ $headingId }}" data-bs-parent="#casosAccordion">
              <div class="accordion-body case-body">
                <div class="row g-3 mb-3">
                  <div class="col-12">
                    <div class="border rounded p-3 bg-light">
                      @if($solicitud->titulo)
                      <small class="text-muted d-block mb-1">
                        <i class="fas fa-heading me-1"></i><strong>Título</strong>
                      </small>
                      <div class="fw-semibold mb-3">{{ $solicitud->titulo }}</div>
                      @endif
                      <small class="text-muted d-block mb-2">
                        <i class="fas fa-align-left me-1"></i><strong>Descripción</strong>
                      </small>
                      <div class="text-muted" style="line-height: 1.6;">{{ $solicitud->descripcion ?: 'Sin descripción registrada.' }}</div>
                    </div>
                  </div>
                  @if($solicitud->entrevistas->isNotEmpty())
                    @php
                      $entrevista = $solicitud->entrevistas->first();
                      $modalidad = $entrevista->modalidad ?? null;
                    @endphp
                    @if($modalidad)
                    <div class="col-12">
                      <div class="border rounded p-3 bg-light">
                        <small class="text-muted d-block mb-1">
                          <i class="fas fa-laptop me-1"></i><strong>Modalidad</strong>
                        </small>
                        <div class="fw-semibold">
                          <span class="badge {{ $modalidad === 'Virtual' ? 'bg-info' : 'bg-success' }}">{{ $modalidad }}</span>
                        </div>
                      </div>
                    </div>
                    @endif
                  @endif
                </div>
                <div class="d-flex flex-wrap align-items-center gap-3">
                  <div class="text-muted small">Solicitado el {{ $solicitud->fecha_solicitud?->format('d/m/Y') ?? 's/f' }}</div>
                  <div class="ms-auto">
                    @if($solicitud->estado === 'Pendiente de entrevista')
                      <button
                        type="button"
                        class="btn btn-sm btn-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#informarCtpModal"
                        data-action="{{ route('coordinadora.casos.informar-ctp', $solicitud) }}"
                        data-estudiante="{{ trim(($solicitud->estudiante->nombre ?? 'Estudiante').' '.($solicitud->estudiante->apellido ?? '')) }}"
                      >
                        <i class="fas fa-paper-plane me-1"></i>Informar a CTP
                      </button>
                    @elseif($solicitud->estado === 'Pendiente de formulacion del caso')
                      <span class="text-muted small">Informado a CTP</span>
                    @else
                      <span class="text-muted small">En proceso</span>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
        @empty
          <p class="text-center text-muted py-4 mb-0">No hay casos registrados.</p>
@endforelse
      </div>
      <div class="d-flex justify-content-end mt-3">
        {{ $solicitudes->links() }}
      </div>
    </div>
  </div>
</div>

{{-- Modal para informar a CTP con observaciones y adjunto --}}
<div class="modal fade" id="informarCtpModal" tabindex="-1" aria-labelledby="informarCtpModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <div>
            <h5 class="modal-title" id="informarCtpModalLabel">Informar a CTP</h5>
            <small class="text-muted">Confirma el envío e incluye las observaciones de la entrevista.</small>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted small mb-3">
            Se informará el caso de <span class="fw-semibold" data-estudiante-name></span> al CTP.
          </p>
          <div class="mb-3">
            <label class="form-label">Observaciones de la entrevista</label>
            <textarea class="form-control" name="observaciones" rows="4" placeholder="Notas relevantes, acuerdos o hallazgos de la entrevista."></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Adjuntar PDF con observaciones (opcional)</label>
            <input type="file" class="form-control" name="observaciones_pdf" accept="application/pdf">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger">
            <i class="fas fa-paper-plane me-1"></i>Informar y enviar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var modalEl = document.getElementById('informarCtpModal');
    if (!modalEl) return;

    modalEl.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      if (!button) return;

      var action = button.getAttribute('data-action');
      var estudiante = button.getAttribute('data-estudiante') || 'este estudiante';
      var form = modalEl.querySelector('form');
      var nameHolder = modalEl.querySelector('[data-estudiante-name]');

      if (form && action) {
        form.setAttribute('action', action);
        form.reset();
      }
      if (nameHolder) {
        nameHolder.textContent = estudiante.trim();
      }
    });
  });
</script>
@endsection

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
