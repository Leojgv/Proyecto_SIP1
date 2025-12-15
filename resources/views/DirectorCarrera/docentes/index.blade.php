@extends('layouts.Dashboard_director.app')

@section('title', 'Docentes')

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

  <div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h1 class="h4 mb-1">Docentes de tu Area</h1>
      <p class="text-muted mb-0">Listado de docentes asociados a las areas que diriges.</p>
    </div>
    <a href="{{ route('director.docentes.import.form') }}" class="btn btn-danger">
      <i class="fas fa-file-excel me-2"></i>Cargar desde Excel
    </a>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-dark-mode align-middle">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>RUT</th>
              <th>Correo</th>
              <th>Carrera</th>
              <th class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($docentes as $docente)
              @php
                $esUsuarioSinCarrera = isset($docente->es_usuario_sin_carrera) && $docente->es_usuario_sin_carrera;
                
                if ($esUsuarioSinCarrera) {
                  // Es un usuario sin registro en docentes
                  $nombre = $docente->nombre ?? '';
                  $apellido = $docente->apellido ?? '';
                  $email = $docente->email ?? '';
                  $rutFormateado = 'Sin RUT';
                  $carreraNombre = 'Sin carrera asignada';
                  $carreraId = null;
                  $docenteId = null;
                  $userId = $docente->user_id ?? null;
                } else {
                  // Es un docente con registro
                  $rut = $docente->rut ?? '';
                  $rutFormateado = $rut;
                  if ($rut && strlen($rut) > 0) {
                    $rutLimpio = str_replace(['.', '-'], '', $rut);
                    if (strlen($rutLimpio) >= 7) {
                      $rutFormateado = substr($rutLimpio, 0, -1);
                      $rutFormateado = number_format((int)$rutFormateado, 0, '', '.');
                      $rutFormateado .= '-' . substr($rutLimpio, -1);
                    }
                  }
                  $email = $docente->user->email ?? ($docente->email ?? null);
                  $nombre = $docente->nombre ?? '';
                  $apellido = $docente->apellido ?? '';
                  $carreraNombre = $docente->carrera->nombre ?? 'Sin carrera';
                  $carreraId = $docente->carrera_id ?? null;
                  $docenteId = $docente->id ?? null;
                  $userId = $docente->user_id ?? null;
                }
              @endphp
              <tr class="{{ $esUsuarioSinCarrera ? 'table-warning' : '' }}">
                <td>
                  <p class="mb-0 fw-semibold">{{ $nombre }} {{ $apellido }}</p>
                  @if($esUsuarioSinCarrera)
                    <small class="text-muted">Usuario sin carrera asignada</small>
                  @endif
                </td>
                <td>
                  <span class="badge bg-light text-dark">{{ $rutFormateado ?: 'Sin RUT' }}</span>
                </td>
                <td>
                  @if($email)
                    <div class="d-flex align-items-center gap-2">
                      <i class="fas fa-envelope text-muted"></i>
                      <span>{{ $email }}</span>
                    </div>
                  @else
                    <span class="text-muted">Sin correo</span>
                  @endif
                </td>
                <td>
                  <span class="badge {{ $esUsuarioSinCarrera ? 'bg-warning text-dark' : 'bg-light text-dark' }}">
                    {{ $carreraNombre }}
                  </span>
                </td>
                <td class="text-end">
                  @if($esUsuarioSinCarrera)
                    <div class="d-flex gap-2 justify-content-end">
                      <button type="button"
                              class="btn btn-sm btn-success btn-asignar-carrera-docente"
                              data-asignar-docente="true"
                              data-user-id="{{ $userId }}"
                              data-user-nombre="{{ $nombre }}"
                              data-user-apellido="{{ $apellido }}"
                              data-user-email="{{ $email }}"
                              title="Asignar carrera">
                        <i class="fas fa-plus"></i> Asignar carrera
                      </button>
                      <button type="button"
                              class="btn btn-sm btn-danger btn-eliminar-usuario-pendiente"
                              data-eliminar-usuario="true"
                              data-user-id="{{ $userId }}"
                              data-user-nombre="{{ $nombre }}"
                              data-user-apellido="{{ $apellido }}"
                              data-delete-url="{{ route('director.docentes.pending.destroy', $userId) }}"
                              title="Eliminar usuario pendiente">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  @else
                    <button type="button"
                            class="btn btn-sm btn-outline-primary btn-edit-docente"
                            data-edit-docente="true"
                            data-docente-id="{{ $docenteId }}"
                            data-docente-nombre="{{ $nombre }}"
                            data-docente-apellido="{{ $apellido }}"
                            data-docente-email="{{ $email }}"
                            data-docente-carrera-id="{{ $carreraId }}"
                            data-update-url="{{ route('director.docentes.update', $docenteId) }}"
                            title="Editar docente">
                      <i class="fas fa-pen"></i>
                    </button>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-4">No hay docentes registrados en tus areas.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-end mt-3">
        {{ $docentes->links() }}
      </div>
    </div>
  </div>
</div>

{{-- Modal Editar Docente --}}
<div class="modal fade"
     id="modalEditarDocente"
     tabindex="-1"
     aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-0">
        <div>
          <h5 class="modal-title">Editar Docente</h5>
          <p class="text-muted mb-0 small">Modifica la información del docente.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="formEditarDocente"
            method="POST"
            action="#">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label" for="edit_docente_nombre">Nombre <span class="text-danger">*</span></label>
              <input type="text"
                     id="edit_docente_nombre"
                     name="nombre"
                     class="form-control @error('nombre') is-invalid @enderror"
                     required>
              @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label" for="edit_docente_apellido">Apellido <span class="text-danger">*</span></label>
              <input type="text"
                     id="edit_docente_apellido"
                     name="apellido"
                     class="form-control @error('apellido') is-invalid @enderror"
                     required>
              @error('apellido')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label" for="edit_docente_email">Correo electrónico <span class="text-danger">*</span></label>
              <input type="email"
                     id="edit_docente_email"
                     name="email"
                     class="form-control @error('email') is-invalid @enderror"
                     required>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label" for="edit_docente_carrera_id">Carrera <span class="text-danger">*</span></label>
              <select id="edit_docente_carrera_id"
                      name="carrera_id"
                      class="form-select @error('carrera_id') is-invalid @enderror"
                      required>
                <option value="">Seleccione una carrera</option>
                @foreach($carreras ?? [] as $carrera)
                  <option value="{{ $carrera->id }}">{{ $carrera->nombre }}</option>
                @endforeach
              </select>
              @error('carrera_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger">Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Asignar Carrera a Usuario Docente --}}
<div class="modal fade"
     id="modalAsignarCarreraDocente"
     tabindex="-1"
     aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-0">
        <div>
          <h5 class="modal-title">Asignar Carrera a Docente</h5>
          <p class="text-muted mb-0 small">Asigna una carrera al usuario docente.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="formAsignarCarreraDocente"
            method="POST"
            action="{{ route('director.docentes.store') }}">
        @csrf
        <input type="hidden" id="asignar_user_id" name="user_id">
        <div class="modal-body">
          <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Usuario:</strong> <span id="asignar_usuario_nombre"></span>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label" for="asignar_carrera_id">Carrera <span class="text-danger">*</span></label>
              <select id="asignar_carrera_id"
                      name="carrera_id"
                      class="form-select @error('carrera_id') is-invalid @enderror"
                      required>
                <option value="">Seleccione una carrera</option>
                @foreach($carreras ?? [] as $carrera)
                  <option value="{{ $carrera->id }}">{{ $carrera->nombre }}</option>
                @endforeach
              </select>
              @error('carrera_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label" for="asignar_rut">RUT (opcional)</label>
              <input type="text"
                     id="asignar_rut"
                     name="rut"
                     class="form-control @error('rut') is-invalid @enderror"
                     placeholder="12.345.678-9">
              @error('rut')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Asignar Carrera</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Confirmar Eliminación de Usuario Pendiente --}}
<div class="modal fade"
     id="modalConfirmarEliminarUsuario"
     tabindex="-1"
     aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-0">
        <div>
          <h5 class="modal-title text-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación
          </h5>
          <p class="text-muted mb-0 small">Esta acción no se puede deshacer.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="formEliminarUsuario"
            method="POST"
            action="#">
        @csrf
        @method('DELETE')
        <div class="modal-body">
          <p>¿Estás seguro de que deseas eliminar al usuario <strong id="usuario_a_eliminar"></strong>?</p>
          <p class="text-muted small mb-0">Se eliminará el usuario y todos sus datos asociados.</p>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger">Eliminar</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  .table-dark-mode thead {
    background: #f8fafc;
  }
  .table-dark-mode thead th {
    color: #1f2937;
    border-color: #e5e7eb;
  }
  .table-dark-mode tbody tr {
    background: #fff;
    border-color: #e5e7eb;
    color: #1f2937;
  }
  .table-dark-mode tbody tr:nth-of-type(odd) {
    background: #f8fafc;
  }
  .table-dark-mode td {
    border-color: #e5e7eb;
  }
  .table-dark-mode .text-muted {
    color: #6b7280 !important;
  }
  .dark-mode .table-dark-mode thead {
    background: #111827;
  }
  .dark-mode .table-dark-mode thead th {
    color: #e5e7eb;
    border-color: #1f2937;
  }
  .dark-mode .table-dark-mode tbody tr {
    background: #0f172a;
    border-color: #1f2937;
    color: #e5e7eb;
  }
  .dark-mode .table-dark-mode tbody tr:nth-of-type(odd) {
    background: #0b1220;
  }
  .dark-mode .table-dark-mode td {
    border-color: #1f2937;
  }
  .dark-mode .table-dark-mode .text-muted {
    color: #9ca3af !important;
  }
</style>
@endpush

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

    const editarDocenteModal = setupModalController('modalEditarDocente');
    if (editarDocenteModal) {
      const editForm = document.getElementById('formEditarDocente');
      const editNombre = document.getElementById('edit_docente_nombre');
      const editApellido = document.getElementById('edit_docente_apellido');
      const editEmail = document.getElementById('edit_docente_email');
      const editCarreraId = document.getElementById('edit_docente_carrera_id');

      const populateAndShowEditModal = (data) => {
        if (!editForm) {
          return;
        }

        if (editNombre) editNombre.value = data.nombre || '';
        if (editApellido) editApellido.value = data.apellido || '';
        if (editEmail) editEmail.value = data.email || '';
        if (editCarreraId) editCarreraId.value = data.carreraId || '';

        if (data.updateUrl) {
          editForm.action = data.updateUrl;
        }

        editarDocenteModal.show();
      };

      document.querySelectorAll('[data-edit-docente="true"]').forEach((btn) => {
        btn.addEventListener('click', function () {
          const data = {
            nombre: this.getAttribute('data-docente-nombre') || '',
            apellido: this.getAttribute('data-docente-apellido') || '',
            email: this.getAttribute('data-docente-email') || '',
            carreraId: this.getAttribute('data-docente-carrera-id') || '',
            updateUrl: this.getAttribute('data-update-url') || '',
          };
          populateAndShowEditModal(data);
        });
      });
    }

    // Modal para asignar carrera
    const asignarCarreraModal = setupModalController('modalAsignarCarreraDocente');
    if (asignarCarreraModal) {
      const asignarForm = document.getElementById('formAsignarCarreraDocente');
      const asignarUserId = document.getElementById('asignar_user_id');
      const asignarUsuarioNombre = document.getElementById('asignar_usuario_nombre');
      const asignarCarreraId = document.getElementById('asignar_carrera_id');

      document.querySelectorAll('[data-asignar-docente="true"]').forEach((btn) => {
        btn.addEventListener('click', function () {
          const userId = this.getAttribute('data-user-id') || '';
          const nombre = this.getAttribute('data-user-nombre') || '';
          const apellido = this.getAttribute('data-user-apellido') || '';
          
          if (asignarUserId) asignarUserId.value = userId;
          if (asignarUsuarioNombre) asignarUsuarioNombre.textContent = nombre + ' ' + apellido;
          if (asignarCarreraId) asignarCarreraId.value = '';
          
          asignarCarreraModal.show();
        });
      });
    }

    // Modal para confirmar eliminación de usuario pendiente
    const confirmarEliminarModal = setupModalController('modalConfirmarEliminarUsuario');
    if (confirmarEliminarModal) {
      const eliminarForm = document.getElementById('formEliminarUsuario');
      const usuarioAEliminar = document.getElementById('usuario_a_eliminar');

      document.querySelectorAll('[data-eliminar-usuario="true"]').forEach((btn) => {
        btn.addEventListener('click', function () {
          const userId = this.getAttribute('data-user-id') || '';
          const nombre = this.getAttribute('data-user-nombre') || '';
          const apellido = this.getAttribute('data-user-apellido') || '';
          const deleteUrl = this.getAttribute('data-delete-url') || '';
          
          if (eliminarForm) eliminarForm.action = deleteUrl;
          if (usuarioAEliminar) usuarioAEliminar.textContent = nombre + ' ' + apellido;
          
          confirmarEliminarModal.show();
        });
      });
    }
  });
</script>
@endpush
