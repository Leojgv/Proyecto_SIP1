{{-- resources/views/solicitudes/index.blade.php --}}
@extends('layouts.dashboard_admin.admin')

@section('title', 'Reportes y Solicitudes')

@push('styles')
<style>
  .reports-management .card,
  .reports-management .stat-card {
    border-radius: 1.25rem;
  }

  .reports-management .stat-card {
    background: #ffffff;
    padding: 1.5rem;
    border: 1px solid rgba(15, 23, 42, 0.06);
  }

  .reports-management .stat-card__icon {
    width: 3rem;
    height: 3rem;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
  }

  .reports-management .stat-card__icon.bg-primary {
    background-color: rgba(239, 68, 68, 0.12) !important;
    color: var(--tone-600);
  }

  .reports-management .stat-card__icon.bg-success {
    background-color: rgba(34, 197, 94, 0.15) !important;
    color: #15803d;
  }

  .reports-management .stat-card__icon.bg-danger {
    background-color: rgba(239, 68, 68, 0.15) !important;
    color: var(--tone-700);
  }

  .reports-management .stat-card__icon.bg-info {
    background-color: rgba(59, 130, 246, 0.15) !important;
    color: #1e40af;
  }

  .reports-management .stat-card__icon.bg-warning {
    background-color: rgba(251, 191, 36, 0.15) !important;
    color: #b45309;
  }

  .reports-management .reports-table thead th {
    text-transform: uppercase;
    font-size: .78rem;
    letter-spacing: .08em;
    color: #94a3b8;
    border-bottom: 1px solid #e2e8f0;
  }

  .reports-management .reports-table tbody td {
    border-bottom: 1px solid #f1f5f9;
  }

  .reports-management .badge-outline {
    background-color: #fff;
    border: 1px solid rgba(15, 23, 42, 0.12);
    color: #0f172a;
    border-radius: 999px;
    padding: .35rem .85rem;
    font-size: .85rem;
  }

  .reports-management .badge-status {
    padding: .35rem .85rem;
    border-radius: 999px;
    font-size: .85rem;
    font-weight: 500;
  }

  .reports-management .badge-status-pendiente {
    background-color: rgba(251, 191, 36, 0.15);
    color: #b45309;
  }

  .reports-management .badge-status-aprobado {
    background-color: rgba(34, 197, 94, 0.15);
    color: #15803d;
  }

  .reports-management .badge-status-rechazado {
    background-color: rgba(239, 68, 68, 0.15);
    color: #dc2626;
  }

  .reports-management .badge-status-en-proceso {
    background-color: rgba(59, 130, 246, 0.15);
    color: #1e40af;
  }

  .reports-management .action-pill {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: .75rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(15, 23, 42, 0.08);
    color: var(--tone-700);
    background-color: #fff;
    transition: all .15s ease;
    text-decoration: none;
  }

  .reports-management .action-pill:hover {
    border-color: var(--tone-600);
    color: #fff;
    background-color: var(--tone-600);
    box-shadow: 0 8px 20px rgba(220, 38, 38, 0.15);
  }

  .reports-management .action-pill.btn-danger:hover {
    background-color: #dc2626;
    border-color: #dc2626;
  }

  .reports-management .modal-content {
    border-radius: 1rem;
  }
</style>
@endpush

@section('content')
<div class="container-fluid reports-management">
  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  @endif

  <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4">
    <div>
      <h1 class="h3 mb-1">Reportes y Solicitudes</h1>
      <p class="text-muted mb-0">Gestiona y monitorea todas las solicitudes y casos del sistema.</p>
    </div>
    <a href="{{ route('solicitudes.create') }}" class="btn btn-primary mt-3 mt-lg-0">
      <i class="fas fa-plus me-2"></i>Nueva solicitud
    </a>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-xxl-3 col-lg-3 col-md-6">
      <div class="stat-card shadow-sm">
        <div class="d-flex align-items-center gap-3">
          <span class="stat-card__icon bg-primary"><i class="fas fa-clipboard-list"></i></span>
          <div>
            <p class="text-muted mb-0 small">Total solicitudes</p>
            <h4 class="mb-0">{{ number_format($totalSolicitudes) }}</h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-3 col-lg-3 col-md-6">
      <div class="stat-card shadow-sm">
        <div class="d-flex align-items-center gap-3">
          <span class="stat-card__icon bg-info"><i class="fas fa-hourglass-half"></i></span>
          <div>
            <p class="text-muted mb-0 small">Solicitudes activas</p>
            <h4 class="mb-0">{{ number_format($solicitudesActivas) }}</h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-3 col-lg-3 col-md-6">
      <div class="stat-card shadow-sm">
        <div class="d-flex align-items-center gap-3">
          <span class="stat-card__icon bg-success"><i class="fas fa-check-circle"></i></span>
          <div>
            <p class="text-muted mb-0 small">Aprobadas</p>
            <h4 class="mb-0">{{ number_format($solicitudesAprobadas) }}</h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-3 col-lg-3 col-md-6">
      <div class="stat-card shadow-sm">
        <div class="d-flex align-items-center gap-3">
          <span class="stat-card__icon bg-danger"><i class="fas fa-times-circle"></i></span>
          <div>
            <p class="text-muted mb-0 small">Rechazadas</p>
            <h4 class="mb-0">{{ number_format($solicitudesRechazadas) }}</h4>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-xxl-9">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex flex-column flex-xl-row gap-3 justify-content-between align-items-xl-end mb-4">
            <div>
              <h5 class="mb-1">Solicitudes del sistema</h5>
              <p class="text-muted mb-0 small">Lista general de solicitudes y casos registrados.</p>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table align-middle reports-table mb-0">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Estudiante</th>
                  <th>Asesora pedagógica</th>
                  <th>Director de carrera</th>
                  <th>Estado</th>
                  <th class="text-end">Acciones</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($solicitudes as $solicitud)
                  @php
                    $badgeClass = match($solicitud->estado) {
                      'Aprobado' => 'badge-status-aprobado',
                      'Rechazado' => 'badge-status-rechazado',
                      'Pendiente de entrevista', 'Pendiente de formulación del caso', 'Pendiente de formulación de ajuste', 'Pendiente de preaprobación', 'Pendiente de Aprobacion' => 'badge-status-pendiente',
                      default => 'badge-status-en-proceso'
                    };
                  @endphp
                  <tr>
                    <td>
                      <div class="fw-semibold">{{ $solicitud->fecha_solicitud?->format('d/m/Y') ?? 'N/A' }}</div>
                      <div class="text-muted small">{{ $solicitud->created_at->format('H:i') }}</div>
                    </td>
                    <td>
                      <div class="fw-semibold">{{ $solicitud->estudiante->nombre ?? '—' }} {{ $solicitud->estudiante->apellido ?? '' }}</div>
                      <div class="text-muted small">{{ $solicitud->estudiante->rut ?? 'Sin RUT' }}</div>
                    </td>
                    <td>
                      <div class="text-muted small">
                        {{ optional($solicitud->asesor)->nombre ? $solicitud->asesor->nombre . ' ' . $solicitud->asesor->apellido : '—' }}
                      </div>
                    </td>
                    <td>
                      <div class="text-muted small">
                        {{ optional($solicitud->director)->nombre ? $solicitud->director->nombre . ' ' . $solicitud->director->apellido : '—' }}
                      </div>
                    </td>
                    <td>
                      <span class="badge-status {{ $badgeClass }}">
                        {{ $solicitud->estado ?? 'Sin estado' }}
                      </span>
                    </td>
                    <td class="text-end">
                      <div class="d-flex gap-2 justify-content-end">
                        <button type="button"
                                class="action-pill btn-ver-detalles"
                                data-ver-detalles="true"
                                data-solicitud-id="{{ $solicitud->id }}"
                                data-estudiante-nombre="{{ $solicitud->estudiante->nombre ?? '' }}"
                                data-estudiante-apellido="{{ $solicitud->estudiante->apellido ?? '' }}"
                                data-estudiante-rut="{{ $solicitud->estudiante->rut ?? '' }}"
                                data-estudiante-email="{{ $solicitud->estudiante->email ?? '' }}"
                                data-estudiante-telefono="{{ $solicitud->estudiante->telefono ?? '' }}"
                                data-estudiante-carrera="{{ $solicitud->estudiante->carrera->nombre ?? 'Sin carrera' }}"
                                data-estudiante-discapacidad="{{ $solicitud->estudiante->tipo_discapacidad ?? 'Otros' }}"
                                data-director-nombre="{{ $solicitud->director->nombre ?? '' }}"
                                data-director-apellido="{{ $solicitud->director->apellido ?? '' }}"
                                data-director-email="{{ $solicitud->director->email ?? '' }}"
                                data-entrevistas="{{ json_encode($solicitud->entrevistas->map(function($e) { 
                                  return [
                                    'fecha' => $e->fecha ? $e->fecha->format('d/m/Y') : 'N/A',
                                    'hora_inicio' => $e->fecha_hora_inicio ? $e->fecha_hora_inicio->format('H:i') : 'N/A',
                                    'hora_fin' => $e->fecha_hora_fin ? $e->fecha_hora_fin->format('H:i') : 'N/A',
                                    'modalidad' => $e->modalidad ?? 'N/A',
                                    'asesor_nombre' => $e->asesor->nombre ?? '',
                                    'asesor_apellido' => $e->asesor->apellido ?? '',
                                    'asesor_email' => $e->asesor->email ?? '',
                                    'tiene_acompanante' => $e->tiene_acompanante ?? false,
                                    'acompanante_rut' => $e->acompanante_rut ?? null,
                                    'acompanante_nombre' => $e->acompanante_nombre ?? null,
                                    'acompanante_telefono' => $e->acompanante_telefono ?? null,
                                  ];
                                })) }}"
                                data-ajustes="{{ json_encode($solicitud->ajustesRazonables->map(function($a) {
                                  return [
                                    'nombre' => $a->nombre ?? 'Sin nombre',
                                    'descripcion' => $a->descripcion ?? 'Sin descripción',
                                    'estado' => $a->estado ?? 'Sin estado'
                                  ];
                                })) }}"
                                data-evidencias="{{ json_encode($solicitud->evidencias->map(function($e) {
                                  return [
                                    'id' => $e->id,
                                    'tipo' => $e->tipo ?? 'Documento',
                                    'descripcion' => $e->descripcion ?? '',
                                    'ruta_archivo' => $e->ruta_archivo ?? '',
                                  ];
                                })) }}"
                                title="Ver detalles">
                          <i class="fas fa-eye"></i>
                        </button>
                        <form action="{{ route('solicitudes.destroy', $solicitud) }}" 
                              method="POST" 
                              class="d-inline" 
                              onsubmit="return confirm('¿Estás seguro de eliminar esta solicitud?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="action-pill btn-danger" title="Eliminar solicitud">
                            <i class="fas fa-trash"></i>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                      No hay solicitudes registradas en el sistema.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="mt-3">
            {{ $solicitudes->links() }}
          </div>
        </div>
      </div>
    </div>

    <div class="col-xxl-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <h5 class="mb-3">Distribución por estado</h5>
          <ul class="list-group list-group-flush">
            @forelse($solicitudesPorEstado as $item)
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <span class="small">{{ Str::limit($item['estado'], 25) }}</span>
                <span class="badge bg-light text-dark border">{{ $item['cantidad'] }}</span>
              </li>
            @empty
              <li class="list-group-item px-0 text-muted">No hay datos disponibles.</li>
            @endforelse
          </ul>
          <div class="mt-3 pt-3 border-top">
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-muted small">Nuevas este mes</span>
              <span class="badge bg-primary">{{ $nuevasEsteMes }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Modal Ver Detalles --}}
<div class="modal fade"
     id="modalVerDetalles"
     tabindex="-1"
     aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-0">
        <div>
          <h5 class="modal-title">Detalles de la Solicitud</h5>
          <p class="text-muted mb-0 small">Información completa del caso y participantes.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          {{-- Datos del Estudiante --}}
          <div class="col-12">
            <h6 class="text-muted mb-3">
              <i class="fas fa-user-graduate me-2"></i>Datos del Estudiante
            </h6>
            <div class="row g-3">
              <div class="col-md-6">
                <div class="border rounded p-3 bg-light">
                  <small class="text-muted d-block mb-1">
                    <i class="fas fa-user me-1"></i><strong>Nombre completo</strong>
                  </small>
                  <div class="fw-semibold" id="modal_estudiante_nombre"></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="border rounded p-3 bg-light">
                  <small class="text-muted d-block mb-1">
                    <i class="fas fa-id-card me-1"></i><strong>RUT</strong>
                  </small>
                  <div class="fw-semibold" id="modal_estudiante_rut"></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="border rounded p-3 bg-light">
                  <small class="text-muted d-block mb-1">
                    <i class="fas fa-envelope me-1"></i><strong>Correo electrónico</strong>
                  </small>
                  <div class="fw-semibold" id="modal_estudiante_email"></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="border rounded p-3 bg-light">
                  <small class="text-muted d-block mb-1">
                    <i class="fas fa-phone me-1"></i><strong>Teléfono</strong>
                  </small>
                  <div class="fw-semibold" id="modal_estudiante_telefono"></div>
                </div>
              </div>
              <div class="col-12">
                <div class="border rounded p-3 bg-light">
                  <small class="text-muted d-block mb-1">
                    <i class="fas fa-school me-1"></i><strong>Carrera</strong>
                  </small>
                  <div class="fw-semibold" id="modal_estudiante_carrera"></div>
                </div>
              </div>
            </div>
          </div>

          {{-- Entrevista --}}
          <div class="col-12">
            <hr>
            <h6 class="text-muted mb-3">
              <i class="fas fa-calendar-check me-2"></i>Entrevista
            </h6>
            <div id="modal_entrevista_content">
              <p class="text-muted mb-0">No hay entrevistas registradas.</p>
            </div>
          </div>

          {{-- Archivos Adjuntos --}}
          <div class="col-12">
            <hr>
            <h6 class="text-muted mb-3">
              <i class="fas fa-file-pdf me-2"></i>Archivos Adjuntos
            </h6>
            <div id="modal_evidencias_content">
              <p class="text-muted mb-0">No hay archivos adjuntos.</p>
            </div>
          </div>

          {{-- Ajustes Razonables --}}
          <div class="col-12">
            <hr>
            <h6 class="text-muted mb-3">
              <i class="fas fa-sliders-h me-2"></i>Ajustes Razonables Aplicados
            </h6>
            <div id="modal_ajustes_content">
              <p class="text-muted mb-0">No hay ajustes razonables registrados.</p>
            </div>
          </div>

          {{-- Directora a Cargo --}}
          <div class="col-12">
            <hr>
            <h6 class="text-muted mb-3">
              <i class="fas fa-user-tie me-2"></i>Directora a Cargo
            </h6>
            <div class="row g-3">
              <div class="col-md-6">
                <div class="border rounded p-3 bg-light">
                  <small class="text-muted d-block mb-1">
                    <i class="fas fa-user me-1"></i><strong>Nombre completo</strong>
                  </small>
                  <div class="fw-semibold" id="modal_director_nombre"></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="border rounded p-3 bg-light">
                  <small class="text-muted d-block mb-1">
                    <i class="fas fa-envelope me-1"></i><strong>Correo electrónico</strong>
                  </small>
                  <div class="fw-semibold" id="modal_director_email"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const setupModalController = (modalId) => {
      const modalEl = document.getElementById(modalId);
      if (!modalEl) {
        return null;
      }

      const cleanupManualModal = () => {
        modalEl.classList.remove('show', 'd-block');
        modalEl.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
        const manualBackdrop = document.querySelector(`.modal-backdrop[data-manual-backdrop="${modalId}"]`);
        if (manualBackdrop) {
          manualBackdrop.remove();
        }
      };

      const showModal = () => {
        if (window.bootstrap && window.bootstrap.Modal) {
          window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
          return;
        }

        if (window.$ && typeof window.$.fn.modal === 'function') {
          window.$(modalEl).modal('show');
          return;
        }

        modalEl.classList.add('show', 'd-block');
        modalEl.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
        if (!document.querySelector(`.modal-backdrop[data-manual-backdrop="${modalId}"]`)) {
          const manualBackdrop = document.createElement('div');
          manualBackdrop.className = 'modal-backdrop fade show';
          manualBackdrop.setAttribute('data-manual-backdrop', modalId);
          document.body.appendChild(manualBackdrop);
        }
      };

      modalEl.addEventListener('hidden.bs.modal', cleanupManualModal);

      modalEl.querySelectorAll('[data-bs-dismiss="modal"]').forEach((trigger) => {
        trigger.addEventListener('click', cleanupManualModal);
      });

      return { show: showModal };
    };

    const verDetallesModal = setupModalController('modalVerDetalles');
    if (verDetallesModal) {
      const estudianteNombre = document.getElementById('modal_estudiante_nombre');
      const estudianteRut = document.getElementById('modal_estudiante_rut');
      const estudianteEmail = document.getElementById('modal_estudiante_email');
      const estudianteTelefono = document.getElementById('modal_estudiante_telefono');
      const estudianteCarrera = document.getElementById('modal_estudiante_carrera');
      const directorNombre = document.getElementById('modal_director_nombre');
      const directorEmail = document.getElementById('modal_director_email');
      const entrevistaContent = document.getElementById('modal_entrevista_content');
      const ajustesContent = document.getElementById('modal_ajustes_content');

      const formatRut = (rut) => {
        if (!rut) return 'Sin RUT';
        const rutLimpio = rut.replace(/[\.\-]/g, '');
        if (rutLimpio.length >= 7) {
          const rutFormateado = rutLimpio.slice(0, -1).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
          return rutFormateado + '-' + rutLimpio.slice(-1);
        }
        return rut;
      };

      const populateAndShowModal = (data) => {
        // Datos del estudiante
        const nombreCompleto = (data.estudianteNombre || '') + ' ' + (data.estudianteApellido || '');
        if (estudianteNombre) estudianteNombre.textContent = nombreCompleto.trim() || 'Sin nombre';
        if (estudianteRut) estudianteRut.textContent = formatRut(data.estudianteRut);
        if (estudianteEmail) estudianteEmail.textContent = data.estudianteEmail || 'Sin email';
        if (estudianteTelefono) estudianteTelefono.textContent = data.estudianteTelefono || 'Sin teléfono';
        if (estudianteCarrera) estudianteCarrera.textContent = data.estudianteCarrera || 'Sin carrera';

        // Directora a cargo
        const directorNombreCompleto = (data.directorNombre || '') + ' ' + (data.directorApellido || '');
        if (directorNombre) directorNombre.textContent = directorNombreCompleto.trim() || 'No asignado';
        if (directorEmail) directorEmail.textContent = data.directorEmail || 'Sin email';

        // Evidencias (PDFs adjuntos)
        if (evidenciasContent) {
          try {
            const evidencias = JSON.parse(data.evidencias || '[]');
            if (evidencias && evidencias.length > 0) {
              let html = '<div class="d-flex flex-column gap-2">';
              evidencias.forEach(evidencia => {
                const url = (evidencia.id && evidencia.ruta_archivo) ? `/evidencias/${evidencia.id}/download` : '#';
                const nombreArchivo = evidencia.ruta_archivo ? evidencia.ruta_archivo.split('/').pop() : 'Sin nombre';
                html += `
                  <div class="border rounded p-3 bg-light">
                    <div class="d-flex align-items-center justify-content-between">
                      <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-file-pdf text-danger" style="font-size: 1.5rem;"></i>
                        <div>
                          <div class="fw-semibold">${nombreArchivo}</div>
                          ${evidencia.descripcion ? `<div class="text-muted small">${evidencia.descripcion}</div>` : ''}
                        </div>
                      </div>
                      ${url !== '#' ? `
                      <a href="${url}" target="_blank" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-download me-1"></i>Descargar
                      </a>
                      ` : ''}
                    </div>
                  </div>
                `;
              });
              html += '</div>';
              evidenciasContent.innerHTML = html;
            } else {
              evidenciasContent.innerHTML = '<p class="text-muted mb-0">No hay archivos adjuntos.</p>';
            }
          } catch (e) {
            console.error('Error parsing evidencias:', e);
            evidenciasContent.innerHTML = '<p class="text-muted mb-0">Error al cargar archivos adjuntos.</p>';
          }
        }

        // Entrevistas
        if (entrevistaContent) {
          try {
            const entrevistas = JSON.parse(data.entrevistas || '[]');
            if (entrevistas.length > 0) {
              let html = '<div class="row g-3">';
              entrevistas.forEach((entrevista, index) => {
                const asesorNombre = (entrevista.asesor_nombre || '') + ' ' + (entrevista.asesor_apellido || '');
                html += `
                  <div class="col-md-6">
                    <div class="border rounded p-3 bg-light">
                      <small class="text-muted d-block mb-1">
                        <i class="fas fa-calendar-alt me-1"></i><strong>Fecha y hora</strong>
                      </small>
                      <div class="fw-semibold">${entrevista.fecha} de ${entrevista.hora_inicio} a ${entrevista.hora_fin}</div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="border rounded p-3 bg-light">
                      <small class="text-muted d-block mb-1">
                        <i class="fas fa-laptop me-1"></i><strong>Modalidad</strong>
                      </small>
                      <div class="fw-semibold">${entrevista.modalidad || 'N/A'}</div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="border rounded p-3 bg-light">
                      <small class="text-muted d-block mb-1">
                        <i class="fas fa-user me-1"></i><strong>Atendido por</strong>
                      </small>
                      <div class="fw-semibold">${asesorNombre.trim() || 'Sin asignar'}</div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="border rounded p-3 bg-light">
                      <small class="text-muted d-block mb-1">
                        <i class="fas fa-envelope me-1"></i><strong>Email del asesor</strong>
                      </small>
                      <div class="fw-semibold">${entrevista.asesor_email || 'Sin email'}</div>
                    </div>
                  </div>
                  ${entrevista.modalidad && entrevista.modalidad.toLowerCase() === 'presencial' ? `
                  <div class="col-12">
                    <div class="border rounded p-3 bg-light">
                      <small class="text-muted d-block mb-2">
                        <i class="fas fa-user-friends me-1"></i><strong>Info de Acompañante/Tutor:</strong>
                      </small>
                      ${entrevista.tiene_acompanante && entrevista.acompanante_nombre ? `
                        <div class="border rounded p-2 bg-white">
                          <div class="small mb-1"><strong>Nombre:</strong> ${entrevista.acompanante_nombre}</div>
                          ${entrevista.acompanante_rut ? `<div class="small mb-1"><strong>RUT:</strong> ${entrevista.acompanante_rut}</div>` : ''}
                          ${entrevista.acompanante_telefono ? `<div class="small"><strong>Teléfono:</strong> ${entrevista.acompanante_telefono}</div>` : ''}
                        </div>
                      ` : `
                        <div class="small text-muted">No hay acompañante adicional</div>
                      `}
                    </div>
                  </div>
                  ` : ''}
                `;
              });
              html += '</div>';
              entrevistaContent.innerHTML = html;
            } else {
              entrevistaContent.innerHTML = '<p class="text-muted mb-0">No hay entrevistas registradas.</p>';
            }
          } catch (e) {
            entrevistaContent.innerHTML = '<p class="text-muted mb-0">No hay entrevistas registradas.</p>';
          }
        }

        // Evidencias (PDFs adjuntos)
        if (evidenciasContent) {
          try {
            const evidencias = JSON.parse(data.evidencias || '[]');
            if (evidencias && evidencias.length > 0) {
              let html = '<div class="d-flex flex-column gap-2">';
              evidencias.forEach(evidencia => {
                const url = (evidencia.id && evidencia.ruta_archivo) ? `/evidencias/${evidencia.id}/download` : '#';
                const nombreArchivo = evidencia.ruta_archivo ? evidencia.ruta_archivo.split('/').pop() : 'Sin nombre';
                html += `
                  <div class="border rounded p-3 bg-light">
                    <div class="d-flex align-items-center justify-content-between">
                      <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-file-pdf text-danger" style="font-size: 1.5rem;"></i>
                        <div>
                          <div class="fw-semibold">${nombreArchivo}</div>
                          ${evidencia.descripcion ? `<div class="text-muted small">${evidencia.descripcion}</div>` : ''}
                        </div>
                      </div>
                      ${url !== '#' ? `
                      <a href="${url}" target="_blank" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-download me-1"></i>Descargar
                      </a>
                      ` : ''}
                    </div>
                  </div>
                `;
              });
              html += '</div>';
              evidenciasContent.innerHTML = html;
            } else {
              evidenciasContent.innerHTML = '<p class="text-muted mb-0">No hay archivos adjuntos.</p>';
            }
          } catch (e) {
            console.error('Error parsing evidencias:', e);
            evidenciasContent.innerHTML = '<p class="text-muted mb-0">Error al cargar archivos adjuntos.</p>';
          }
        }

        // Ajustes Razonables
        if (ajustesContent) {
          try {
            const ajustes = JSON.parse(data.ajustes || '[]');
            const discapacidad = data.estudianteDiscapacidad || 'Otros';
            if (ajustes.length > 0) {
              let html = '<div class="row g-3">';
              ajustes.forEach((ajuste, index) => {
                html += `
                  <div class="col-12">
                    <div class="border rounded p-3 bg-light">
                      <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                          <small class="text-muted d-block mb-1">
                            <i class="fas fa-tag me-1"></i><strong>Tipo de Discapacidad</strong>
                          </small>
                          <div class="fw-semibold mb-2">${discapacidad}</div>
                          <small class="text-muted d-block mb-1">
                            <strong>Título del Ajuste</strong>
                          </small>
                          <div class="fw-semibold mb-2">${ajuste.nombre || 'Sin nombre'}</div>
                        </div>
                        <span class="badge bg-secondary">${ajuste.estado || 'Sin estado'}</span>
                      </div>
                      <small class="text-muted d-block mb-1">
                        <strong>Descripción</strong>
                      </small>
                      <div class="text-break">${ajuste.descripcion || 'Sin descripción'}</div>
                    </div>
                  </div>
                `;
              });
              html += '</div>';
              ajustesContent.innerHTML = html;
            } else {
              ajustesContent.innerHTML = '<p class="text-muted mb-0">No hay ajustes razonables registrados para esta solicitud.</p>';
            }
          } catch (e) {
            ajustesContent.innerHTML = '<p class="text-muted mb-0">No hay ajustes razonables registrados para esta solicitud.</p>';
          }
        }

        verDetallesModal.show();
      };

      document.querySelectorAll('[data-ver-detalles="true"]').forEach((button) => {
        button.addEventListener('click', () => {
          populateAndShowModal({
            estudianteNombre: button.dataset.estudianteNombre || '',
            estudianteApellido: button.dataset.estudianteApellido || '',
            estudianteRut: button.dataset.estudianteRut || '',
            estudianteEmail: button.dataset.estudianteEmail || '',
            estudianteTelefono: button.dataset.estudianteTelefono || '',
            estudianteCarrera: button.dataset.estudianteCarrera || '',
            estudianteDiscapacidad: button.dataset.estudianteDiscapacidad || '',
            directorNombre: button.dataset.directorNombre || '',
            directorApellido: button.dataset.directorApellido || '',
            directorEmail: button.dataset.directorEmail || '',
            entrevistas: button.dataset.entrevistas || '[]',
            ajustes: button.dataset.ajustes || '[]',
            evidencias: button.dataset.evidencias || '[]',
          });
        });
      });
    }
  });
</script>
@endpush
