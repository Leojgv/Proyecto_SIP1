{{-- resources/views/roles/index.blade.php --}}
@extends('layouts.dashboard_admin.admin')

@section('title', 'Gestión de Roles')

@push('styles')
<style>
  .roles-management .card,
  .roles-management .stat-card {
    border-radius: 1.25rem;
  }

  .roles-management .stat-card {
    background: #ffffff;
    padding: 1.5rem;
    border: 1px solid rgba(15, 23, 42, 0.06);
  }

  .roles-management .stat-card__icon {
    width: 3rem;
    height: 3rem;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
  }

  .roles-management .stat-card__icon.bg-primary {
    background-color: rgba(239, 68, 68, 0.12) !important;
    color: var(--tone-600);
  }

  .roles-management .stat-card__icon.bg-success {
    background-color: rgba(34, 197, 94, 0.15) !important;
    color: #15803d;
  }

  .roles-management .stat-card__icon.bg-info {
    background-color: rgba(59, 130, 246, 0.15) !important;
    color: #1e40af;
  }

  .roles-management .roles-table thead th {
    text-transform: uppercase;
    font-size: .78rem;
    letter-spacing: .08em;
    color: #94a3b8;
    border-bottom: 1px solid #e2e8f0;
  }

  .roles-management .roles-table tbody td {
    border-bottom: 1px solid #f1f5f9;
  }

  .roles-management .badge-outline {
    background-color: #fff;
    border: 1px solid rgba(15, 23, 42, 0.12);
    color: #0f172a;
    border-radius: 999px;
    padding: .35rem .85rem;
    font-size: .85rem;
  }

  .roles-management .action-pill {
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

  .roles-management .action-pill:hover {
    border-color: var(--tone-600);
    color: #fff;
    background-color: var(--tone-600);
    box-shadow: 0 8px 20px rgba(220, 38, 38, 0.15);
  }

  .roles-management .action-pill.btn-danger:hover {
    background-color: #dc2626;
    border-color: #dc2626;
  }

  .roles-management .modal-content {
    border-radius: 1rem;
  }
</style>
@endpush

@section('content')
<div class="container-fluid roles-management">
  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  @endif

  <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4">
    <div>
      <h1 class="h3 mb-1">Gestión de Roles</h1>
      <p class="text-muted mb-0">Administra los roles del sistema y sus permisos de acceso.</p>
    </div>
    <button type="button"
            id="btnNuevoRol"
            class="btn btn-primary mt-3 mt-lg-0"
            data-bs-toggle="modal"
            data-bs-target="#modalNuevoRol">
      <i class="fas fa-plus me-2"></i>Nuevo rol
    </button>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-xxl-4 col-lg-4 col-md-6">
      <div class="stat-card shadow-sm">
        <div class="d-flex align-items-center gap-3">
          <span class="stat-card__icon bg-primary"><i class="fas fa-id-badge"></i></span>
          <div>
            <p class="text-muted mb-0 small">Total roles</p>
            <h4 class="mb-0">{{ number_format($totalRoles) }}</h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-4 col-lg-4 col-md-6">
      <div class="stat-card shadow-sm">
        <div class="d-flex align-items-center gap-3">
          <span class="stat-card__icon bg-success"><i class="fas fa-users"></i></span>
          <div>
            <p class="text-muted mb-0 small">Roles con usuarios</p>
            <h4 class="mb-0">{{ number_format($rolesConUsuarios) }}</h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-4 col-lg-4 col-md-6">
      <div class="stat-card shadow-sm">
        <div class="d-flex align-items-center gap-3">
          <span class="stat-card__icon bg-info"><i class="fas fa-user-check"></i></span>
          <div>
            <p class="text-muted mb-0 small">Total usuarios</p>
            <h4 class="mb-0">{{ number_format($totalUsuarios) }}</h4>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="d-flex flex-column flex-xl-row gap-3 justify-content-between align-items-xl-end mb-4">
        <div>
          <h5 class="mb-1">Roles del sistema</h5>
          <p class="text-muted mb-0 small">Lista de todos los roles disponibles y su asignación.</p>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table align-middle roles-table mb-0">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Descripción</th>
              <th>Usuarios asignados</th>
              <th class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($roles as $rol)
              <tr>
                <td>
                  <div class="fw-semibold">{{ $rol->nombre }}</div>
                </td>
                <td>
                  <div class="text-muted small">{{ $rol->descripcion ?? 'Sin descripción' }}</div>
                </td>
                <td>
                  <span class="badge-outline">{{ $rol->users_count }} usuario{{ $rol->users_count !== 1 ? 's' : '' }}</span>
                </td>
                <td class="text-end">
                  <div class="d-flex gap-2 justify-content-end">
                    <button type="button"
                            class="action-pill btn-edit-rol"
                            data-edit-rol="true"
                            data-rol-id="{{ $rol->id }}"
                            data-rol-nombre="{{ $rol->nombre }}"
                            data-rol-descripcion="{{ $rol->descripcion ?? '' }}"
                            data-update-url="{{ route('roles.update', $rol) }}"
                            title="Editar rol">
                      <i class="fas fa-pen"></i>
                    </button>
                    @if($rol->users_count == 0)
                      <form action="{{ route('roles.destroy', $rol) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este rol?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-pill btn-danger" title="Eliminar rol">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
                    @else
                      <button type="button" class="action-pill" disabled title="No se puede eliminar: tiene usuarios asignados" style="opacity: 0.5; cursor: not-allowed;">
                        <i class="fas fa-trash"></i>
                      </button>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center text-muted py-4">
                  No hay roles registrados en el sistema.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- Modal Nuevo Rol --}}
<div class="modal fade"
     id="modalNuevoRol"
     tabindex="-1"
     aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-0">
        <div>
          <h5 class="modal-title">Crear nuevo rol</h5>
          <p class="text-muted mb-0 small">Completa los datos para registrar un nuevo rol.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form action="{{ route('roles.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label" for="modal_nombre">Nombre del rol</label>
            <input type="text"
                   id="modal_nombre"
                   name="nombre"
                   class="form-control @error('nombre') is-invalid @enderror"
                   value="{{ old('nombre') }}"
                   placeholder="Ej: Administrador"
                   required>
            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label" for="modal_descripcion">Descripción</label>
            <textarea id="modal_descripcion"
                      name="descripcion"
                      class="form-control @error('descripcion') is-invalid @enderror"
                      rows="3"
                      placeholder="Describe las funciones y permisos de este rol">{{ old('descripcion') }}</textarea>
            @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Crear rol</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Editar Rol --}}
<div class="modal fade"
     id="modalEditarRol"
     tabindex="-1"
     aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-0">
        <div>
          <h5 class="modal-title">Editar Rol</h5>
          <p class="text-muted mb-0 small">Modifica la información del rol.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="formEditarRol"
            method="POST"
            action="#">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label" for="edit_nombre">Nombre del rol</label>
            <input type="text"
                   id="edit_nombre"
                   name="nombre"
                   class="form-control @error('nombre') is-invalid @enderror"
                   required>
            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label" for="edit_descripcion">Descripción</label>
            <textarea id="edit_descripcion"
                      name="descripcion"
                      class="form-control @error('descripcion') is-invalid @enderror"
                      rows="3"></textarea>
            @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
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

    const nuevoRolModal = setupModalController('modalNuevoRol');
    if (nuevoRolModal) {
      const triggerBtn = document.getElementById('btnNuevoRol');
      if (triggerBtn) {
        triggerBtn.addEventListener('click', function (event) {
          event.preventDefault();
          nuevoRolModal.show();
        });
      }
    }

    const editarRolModal = setupModalController('modalEditarRol');
    if (editarRolModal) {
      const editForm = document.getElementById('formEditarRol');
      const editNombre = document.getElementById('edit_nombre');
      const editDescripcion = document.getElementById('edit_descripcion');

      const populateAndShowEditModal = (data) => {
        if (!editForm) {
          return;
        }

        editForm.action = data.updateUrl || '#';
        if (editNombre) editNombre.value = data.nombre ?? '';
        if (editDescripcion) editDescripcion.value = data.descripcion ?? '';

        editarRolModal.show();
      };

      document.querySelectorAll('[data-edit-rol="true"]').forEach((button) => {
        button.addEventListener('click', () => {
          populateAndShowEditModal({
            nombre: button.dataset.rolNombre || '',
            descripcion: button.dataset.rolDescripcion || '',
            updateUrl: button.dataset.updateUrl || '#',
          });
        });
      });
    }
  });
</script>
@endpush
