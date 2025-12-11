{{-- resources/views/estudiantes/index.blade.php --}}
@extends('layouts.dashboard_admin.admin')

@section('title', 'Gestión de Estudiantes')

@push('styles')
<style>
  .students-management .card,
  .students-management .stat-card {
    border-radius: 1.25rem;
  }

  .students-management .stat-card {
    background: #ffffff;
    padding: 1.5rem;
    border: 1px solid rgba(15, 23, 42, 0.06);
  }

  .students-management .stat-card__icon {
    width: 3rem;
    height: 3rem;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
  }

  .students-management .stat-card__icon.bg-primary {
    background-color: rgba(239, 68, 68, 0.12) !important;
    color: var(--tone-600);
  }

  .students-management .stat-card__icon.bg-success {
    background-color: rgba(34, 197, 94, 0.15) !important;
    color: #15803d;
  }

  .students-management .stat-card__icon.bg-info {
    background-color: rgba(59, 130, 246, 0.15) !important;
    color: #1e40af;
  }

  .students-management .stat-card__icon.bg-warning {
    background-color: rgba(251, 191, 36, 0.15) !important;
    color: #b45309;
  }

  .students-management .students-table thead th {
    text-transform: uppercase;
    font-size: .78rem;
    letter-spacing: .08em;
    color: #94a3b8;
    border-bottom: 2px solid #e2e8f0;
    padding: 1rem;
    font-weight: 600;
  }

  .students-management .students-table tbody td {
    border-bottom: 1px solid #f1f5f9;
    padding: 1rem;
    vertical-align: middle;
  }

  .students-management .students-table tbody tr:hover {
    background-color: #f8f9fa;
  }

  .students-management .badge-outline {
    background-color: #f8f9fa;
    border: 1px solid rgba(15, 23, 42, 0.08);
    color: #0f172a;
    border-radius: 6px;
    padding: .4rem .75rem;
    font-size: .875rem;
    font-weight: 500;
    display: inline-block;
  }

  .students-management .student-name {
    font-weight: 600;
    color: #0f172a;
    font-size: .95rem;
    margin-bottom: 0;
  }

  .students-management .student-contact {
    color: #64748b;
    font-size: .875rem;
    line-height: 1.5;
  }

  .students-management .student-contact-item {
    display: flex;
    align-items: center;
    gap: .5rem;
    margin-bottom: .25rem;
  }

  .students-management .student-contact-item:last-child {
    margin-bottom: 0;
  }

  .students-management .student-contact-item i {
    color: #94a3b8;
    font-size: .75rem;
    width: 16px;
  }

  .students-management .rut-display {
    font-family: 'Courier New', monospace;
    font-weight: 500;
    color: #334155;
  }

  .students-management .action-pill {
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

  .students-management .action-pill:hover {
    border-color: var(--tone-600);
    color: #fff;
    background-color: var(--tone-600);
    box-shadow: 0 8px 20px rgba(220, 38, 38, 0.15);
  }

  .students-management .action-pill.btn-danger:hover {
    background-color: #dc2626;
    border-color: #dc2626;
  }

  .students-management .modal-content {
    border-radius: 1rem;
  }
</style>
@endpush

@section('content')
<div class="container-fluid students-management">
  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  @endif

  <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4">
    <div>
      <h1 class="h3 mb-1">Gestión de Estudiantes</h1>
      <p class="text-muted mb-0">Administra el registro de estudiantes y su información académica.</p>
    </div>
    <a href="{{ route('estudiantes.create') }}" class="btn btn-primary mt-3 mt-lg-0">
      <i class="fas fa-user-plus me-2"></i>Nuevo estudiante
    </a>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-xxl-3 col-lg-3 col-md-6">
      <div class="stat-card shadow-sm">
        <div class="d-flex align-items-center gap-3">
          <span class="stat-card__icon bg-primary"><i class="fas fa-user-graduate"></i></span>
          <div>
            <p class="text-muted mb-0 small">Total estudiantes</p>
            <h4 class="mb-0">{{ number_format($totalEstudiantes) }}</h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-3 col-lg-3 col-md-6">
      <div class="stat-card shadow-sm">
        <div class="d-flex align-items-center gap-3">
          <span class="stat-card__icon bg-success"><i class="fas fa-clipboard-list"></i></span>
          <div>
            <p class="text-muted mb-0 small">Con casos activos</p>
            <h4 class="mb-0">{{ number_format($estudiantesConCasos) }}</h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-3 col-lg-3 col-md-6">
      <div class="stat-card shadow-sm">
        <div class="d-flex align-items-center gap-3">
          <span class="stat-card__icon bg-info"><i class="fas fa-calendar-plus"></i></span>
          <div>
            <p class="text-muted mb-0 small">Nuevos este mes</p>
            <h4 class="mb-0">{{ number_format($nuevosEsteMes) }}</h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-3 col-lg-3 col-md-6">
      <div class="stat-card shadow-sm">
        <div class="d-flex align-items-center gap-3">
          <span class="stat-card__icon bg-warning"><i class="fas fa-school"></i></span>
          <div>
            <p class="text-muted mb-0 small">Carreras activas</p>
            <h4 class="mb-0">{{ number_format($estudiantesPorCarrera->count()) }}</h4>
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
              <h5 class="mb-1">Estudiantes del sistema</h5>
              <p class="text-muted mb-0 small">Lista general de estudiantes registrados.</p>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table align-middle students-table mb-0">
              <thead>
                <tr>
                  <th>Estudiante</th>
                  <th>RUT</th>
                  <th>Contacto</th>
                  <th>Carrera</th>
                  <th class="text-end">Acciones</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($estudiantes as $estudiante)
                  @php
                    // Formatear RUT chileno
                    $rut = $estudiante->rut ?? '';
                    $rutFormateado = $rut;
                    if ($rut && strlen($rut) > 0) {
                      // Remover puntos y guiones existentes
                      $rutLimpio = str_replace(['.', '-'], '', $rut);
                      if (strlen($rutLimpio) >= 7) {
                        // Formatear como XX.XXX.XXX-X
                        $rutFormateado = substr($rutLimpio, 0, -1);
                        $rutFormateado = number_format((int)$rutFormateado, 0, '', '.');
                        $rutFormateado .= '-' . substr($rutLimpio, -1);
                      }
                    }
                  @endphp
                  <tr>
                    <td>
                      <p class="student-name mb-0">{{ $estudiante->nombre }} {{ $estudiante->apellido }}</p>
                    </td>
                    <td>
                      <span class="badge-outline rut-display">{{ $rutFormateado ?: 'Sin RUT' }}</span>
                    </td>
                    <td>
                      <div class="student-contact">
                        @if($estudiante->email)
                          <div class="student-contact-item">
                            <i class="fas fa-envelope"></i>
                            <span>{{ $estudiante->email }}</span>
                          </div>
                        @endif
                        @if($estudiante->telefono)
                          <div class="student-contact-item">
                            <i class="fas fa-phone"></i>
                            <span>{{ $estudiante->telefono }}</span>
                          </div>
                        @endif
                        @if(!$estudiante->email && !$estudiante->telefono)
                          <span class="text-muted">Sin información de contacto</span>
                        @endif
                      </div>
                    </td>
                    <td>
                      <span class="badge-outline">{{ $estudiante->carrera->nombre ?? 'Sin carrera' }}</span>
                    </td>
                    <td class="text-end">
                      <div class="d-flex gap-2 justify-content-end">
                        <button type="button"
                                class="action-pill btn-edit-estudiante"
                                data-edit-estudiante="true"
                                data-estudiante-id="{{ $estudiante->id }}"
                                data-estudiante-rut="{{ $estudiante->rut }}"
                                data-estudiante-nombre="{{ $estudiante->nombre }}"
                                data-estudiante-apellido="{{ $estudiante->apellido }}"
                                data-estudiante-email="{{ $estudiante->email }}"
                                data-estudiante-telefono="{{ $estudiante->telefono ?? '' }}"
                                data-estudiante-carrera-id="{{ $estudiante->carrera_id }}"
                                data-update-url="{{ route('estudiantes.update', $estudiante) }}"
                                title="Editar estudiante">
                          <i class="fas fa-pen"></i>
                        </button>
                        <form action="{{ route('estudiantes.destroy', $estudiante) }}" 
                              method="POST" 
                              class="d-inline" 
                              onsubmit="return confirm('¿Estás seguro de eliminar este estudiante?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="action-pill btn-danger" title="Eliminar estudiante">
                            <i class="fas fa-trash"></i>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center text-muted py-5">
                      <i class="fas fa-user-graduate fa-2x mb-3 d-block text-muted"></i>
                      <p class="mb-0">No hay estudiantes registrados en el sistema.</p>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="mt-3">
            {{ $estudiantes->links() }}
          </div>
        </div>
      </div>
    </div>

    <div class="col-xxl-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <h5 class="mb-3">Distribución por carrera</h5>
          <ul class="list-group list-group-flush">
            @forelse($estudiantesPorCarrera->take(5) as $carrera)
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <span>{{ $carrera['nombre'] }}</span>
                <span class="badge bg-light text-dark border">{{ $carrera['cantidad'] }}</span>
              </li>
            @empty
              <li class="list-group-item px-0 text-muted">No hay datos disponibles.</li>
            @endforelse
            @if($estudiantesPorCarrera->count() > 5)
              <li class="list-group-item px-0 text-muted small">
                Y {{ $estudiantesPorCarrera->count() - 5 }} más...
              </li>
            @endif
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Modal Editar Estudiante --}}
<div class="modal fade"
     id="modalEditarEstudiante"
     tabindex="-1"
     aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-0">
        <div>
          <h5 class="modal-title">Editar Estudiante</h5>
          <p class="text-muted mb-0 small">Modifica la información del estudiante.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="formEditarEstudiante"
            method="POST"
            action="#">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label" for="edit_rut">RUT <span class="text-danger">*</span></label>
              <input type="text"
                     id="edit_rut"
                     name="rut"
                     class="form-control @error('rut') is-invalid @enderror"
                     placeholder="12.345.678-9"
                     required>
              @error('rut')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
              <label class="form-label" for="edit_nombre">Nombre <span class="text-danger">*</span></label>
              <input type="text"
                     id="edit_nombre"
                     name="nombre"
                     class="form-control @error('nombre') is-invalid @enderror"
                     required>
              @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
              <label class="form-label" for="edit_apellido">Apellido <span class="text-danger">*</span></label>
              <input type="text"
                     id="edit_apellido"
                     name="apellido"
                     class="form-control @error('apellido') is-invalid @enderror"
                     required>
              @error('apellido')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label" for="edit_email">Correo electrónico <span class="text-danger">*</span></label>
              <input type="email"
                     id="edit_email"
                     name="email"
                     class="form-control @error('email') is-invalid @enderror"
                     required>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label" for="edit_telefono">Teléfono</label>
              <input type="text"
                     id="edit_telefono"
                     name="telefono"
                     class="form-control @error('telefono') is-invalid @enderror"
                     placeholder="+569 1234 5678">
              @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-12">
              <label class="form-label" for="edit_carrera_id">Carrera <span class="text-danger">*</span></label>
              <select id="edit_carrera_id"
                      name="carrera_id"
                      class="form-select @error('carrera_id') is-invalid @enderror"
                      required>
                <option value="">Selecciona una carrera</option>
                @foreach ($carreras as $carrera)
                  <option value="{{ $carrera->id }}">{{ $carrera->nombre }}</option>
                @endforeach
              </select>
              @error('carrera_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
      </form>
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

    const editarEstudianteModal = setupModalController('modalEditarEstudiante');
    if (editarEstudianteModal) {
      const editForm = document.getElementById('formEditarEstudiante');
      const editRut = document.getElementById('edit_rut');
      const editNombre = document.getElementById('edit_nombre');
      const editApellido = document.getElementById('edit_apellido');
      const editEmail = document.getElementById('edit_email');
      const editTelefono = document.getElementById('edit_telefono');
      const editCarreraId = document.getElementById('edit_carrera_id');

      const populateAndShowEditModal = (data) => {
        if (!editForm) {
          return;
        }

        editForm.action = data.updateUrl || '#';
        if (editRut) editRut.value = data.rut ?? '';
        if (editNombre) editNombre.value = data.nombre ?? '';
        if (editApellido) editApellido.value = data.apellido ?? '';
        if (editEmail) editEmail.value = data.email ?? '';
        if (editTelefono) editTelefono.value = data.telefono ?? '';
        if (editCarreraId) editCarreraId.value = data.carreraId ?? '';

        editarEstudianteModal.show();
      };

      document.querySelectorAll('[data-edit-estudiante="true"]').forEach((button) => {
        button.addEventListener('click', () => {
          populateAndShowEditModal({
            rut: button.dataset.estudianteRut || '',
            nombre: button.dataset.estudianteNombre || '',
            apellido: button.dataset.estudianteApellido || '',
            email: button.dataset.estudianteEmail || '',
            telefono: button.dataset.estudianteTelefono || '',
            carreraId: button.dataset.estudianteCarreraId || '',
            updateUrl: button.dataset.updateUrl || '#',
          });
        });
      });
    }
  });
</script>
@endpush
