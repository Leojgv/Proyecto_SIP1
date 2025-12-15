@extends('layouts.Dashboard_director.app')

@section('title', 'Estudiantes')

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
      <h1 class="h4 mb-1">Estudiantes de tu Area</h1>
      <p class="text-muted mb-0">Listado de estudiantes asociados a las areas que diriges.</p>
    </div>
    <a href="{{ route('director.estudiantes.import.form') }}" class="btn btn-danger">
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
              <th>Teléfono</th>
              <th>Carrera</th>
              <th class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($estudiantes as $estudiante)
              @php
                $esUsuarioSinCarrera = isset($estudiante->es_usuario_sin_carrera) && $estudiante->es_usuario_sin_carrera;
                
                if ($esUsuarioSinCarrera) {
                  // Es un usuario sin registro en estudiantes
                  $nombre = $estudiante->nombre ?? '';
                  $apellido = $estudiante->apellido ?? '';
                  $email = $estudiante->email ?? '';
                  $telefono = $estudiante->telefono ?? null;
                  $rutFormateado = 'Sin RUT';
                  $carreraNombre = 'Sin carrera asignada';
                  $carreraId = null;
                  $estudianteId = null;
                  $userId = $estudiante->user_id ?? null;
                } else {
                  // Es un estudiante con registro
                  $rut = $estudiante->rut ?? '';
                  $rutFormateado = $rut;
                  if ($rut && strlen($rut) > 0) {
                    $rutLimpio = str_replace(['.', '-'], '', $rut);
                    if (strlen($rutLimpio) >= 7) {
                      $rutFormateado = substr($rutLimpio, 0, -1);
                      $rutFormateado = number_format((int)$rutFormateado, 0, '', '.');
                      $rutFormateado .= '-' . substr($rutLimpio, -1);
                    }
                  }
                  $nombre = $estudiante->nombre ?? '';
                  $apellido = $estudiante->apellido ?? '';
                  $email = $estudiante->email ?? '';
                  $telefono = $estudiante->telefono ?? null;
                  $carreraNombre = $estudiante->carrera->nombre ?? 'Sin carrera';
                  $carreraId = $estudiante->carrera_id ?? null;
                  $estudianteId = $estudiante->id ?? null;
                  $userId = $estudiante->user_id ?? null;
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
                  @if($telefono)
                    <div class="d-flex align-items-center gap-2">
                      <i class="fas fa-phone text-muted"></i>
                      <span>{{ $telefono }}</span>
                    </div>
                  @else
                    <span class="text-muted">Sin teléfono</span>
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
                              class="btn btn-sm btn-success btn-asignar-carrera-estudiante"
                              data-asignar-estudiante="true"
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
                              data-delete-url="{{ route('director.estudiantes.pending.destroy', $userId) }}"
                              title="Eliminar usuario pendiente">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  @else
                    <button type="button"
                            class="btn btn-sm btn-outline-primary btn-edit-estudiante"
                            data-edit-estudiante="true"
                            data-estudiante-id="{{ $estudianteId }}"
                            data-estudiante-rut="{{ $estudiante->rut ?? '' }}"
                            data-estudiante-nombre="{{ $nombre }}"
                            data-estudiante-apellido="{{ $apellido }}"
                            data-estudiante-email="{{ $email }}"
                            data-estudiante-telefono="{{ $telefono ?? '' }}"
                            data-estudiante-carrera-id="{{ $carreraId }}"
                            data-update-url="{{ route('director.estudiantes.update', $estudianteId) }}"
                            title="Editar estudiante">
                      <i class="fas fa-pen"></i>
                    </button>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-4">No hay estudiantes registrados en tus areas.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-end mt-3">
        {{ $estudiantes->links() }}
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
            <div class="col-md-6">
              <label class="form-label" for="edit_nombre">Nombre <span class="text-danger">*</span></label>
              <input type="text"
                     id="edit_nombre"
                     name="nombre"
                     class="form-control @error('nombre') is-invalid @enderror"
                     required>
              @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
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
            <div class="col-md-6">
              <label class="form-label" for="edit_carrera_id">Carrera <span class="text-danger">*</span></label>
              <select id="edit_carrera_id"
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

{{-- Modal Asignar Carrera a Usuario Estudiante --}}
<div class="modal fade"
     id="modalAsignarCarreraEstudiante"
     tabindex="-1"
     aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-0">
        <div>
          <h5 class="modal-title">Asignar Carrera a Estudiante</h5>
          <p class="text-muted mb-0 small">Asigna una carrera al usuario estudiante.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="formAsignarCarreraEstudiante"
            method="POST"
            action="{{ route('director.estudiantes.store') }}">
        @csrf
        <input type="hidden" id="asignar_estudiante_user_id" name="user_id">
        <div class="modal-body">
          <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Usuario:</strong> <span id="asignar_estudiante_usuario_nombre"></span>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label" for="asignar_estudiante_carrera_id">Carrera <span class="text-danger">*</span></label>
              <select id="asignar_estudiante_carrera_id"
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
              <label class="form-label" for="asignar_estudiante_rut">RUT (opcional)</label>
              <input type="text"
                     id="asignar_estudiante_rut"
                     name="rut"
                     class="form-control @error('rut') is-invalid @enderror"
                     placeholder="12.345.678-9">
              @error('rut')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label" for="asignar_estudiante_telefono">Teléfono (opcional)</label>
              <input type="text"
                     id="asignar_estudiante_telefono"
                     name="telefono"
                     class="form-control @error('telefono') is-invalid @enderror"
                     placeholder="+569 1234 5678">
              @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
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

    const editarEstudianteModal = setupModalController('modalEditarEstudiante');
    if (editarEstudianteModal) {
      const editForm = document.getElementById('formEditarEstudiante');
      const editNombre = document.getElementById('edit_nombre');
      const editApellido = document.getElementById('edit_apellido');
      const editEmail = document.getElementById('edit_email');
      const editTelefono = document.getElementById('edit_telefono');
      const editCarreraId = document.getElementById('edit_carrera_id');

      const populateAndShowEditModal = (data) => {
        if (!editForm) {
          return;
        }

        if (editNombre) editNombre.value = data.nombre || '';
        if (editApellido) editApellido.value = data.apellido || '';
        if (editEmail) editEmail.value = data.email || '';
        if (editTelefono) editTelefono.value = data.telefono || '';
        if (editCarreraId) editCarreraId.value = data.carreraId || '';

        if (data.updateUrl) {
          editForm.action = data.updateUrl;
        }

        editarEstudianteModal.show();
      };

      document.querySelectorAll('[data-edit-estudiante="true"]').forEach((btn) => {
        btn.addEventListener('click', function () {
          const data = {
            rut: this.getAttribute('data-estudiante-rut') || '',
            nombre: this.getAttribute('data-estudiante-nombre') || '',
            apellido: this.getAttribute('data-estudiante-apellido') || '',
            email: this.getAttribute('data-estudiante-email') || '',
            telefono: this.getAttribute('data-estudiante-telefono') || '',
            carreraId: this.getAttribute('data-estudiante-carrera-id') || '',
            updateUrl: this.getAttribute('data-update-url') || '',
          };
          populateAndShowEditModal(data);
        });
      });
    }

    // Modal para asignar carrera a estudiante
    const asignarCarreraEstudianteModal = setupModalController('modalAsignarCarreraEstudiante');
    if (asignarCarreraEstudianteModal) {
      const asignarForm = document.getElementById('formAsignarCarreraEstudiante');
      const asignarUserId = document.getElementById('asignar_estudiante_user_id');
      const asignarUsuarioNombre = document.getElementById('asignar_estudiante_usuario_nombre');
      const asignarCarreraId = document.getElementById('asignar_estudiante_carrera_id');

      document.querySelectorAll('[data-asignar-estudiante="true"]').forEach((btn) => {
        btn.addEventListener('click', function () {
          const userId = this.getAttribute('data-user-id') || '';
          const nombre = this.getAttribute('data-user-nombre') || '';
          const apellido = this.getAttribute('data-user-apellido') || '';
          
          if (asignarUserId) asignarUserId.value = userId;
          if (asignarUsuarioNombre) asignarUsuarioNombre.textContent = nombre + ' ' + apellido;
          if (asignarCarreraId) asignarCarreraId.value = '';
          
          asignarCarreraEstudianteModal.show();
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
