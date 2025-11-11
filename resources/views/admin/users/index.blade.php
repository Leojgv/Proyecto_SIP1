{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.dashboard_admin.admin')

@section('title', 'Gestión de usuarios')

@push('styles')
<style>
  .user-management .card,
  .user-management .stat-card {
    border-radius: 1.25rem;
  }

  .user-management .stat-card {
    background: #ffffff;
    padding: 1.5rem;
    border: 1px solid rgba(15, 23, 42, 0.06);
  }

  .user-management .stat-card__icon {
    width: 3rem;
    height: 3rem;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
  }

  .user-management .stat-card__icon.bg-primary {
    background-color: rgba(239, 68, 68, 0.12) !important;
    color: var(--tone-600);
  }

  .user-management .stat-card__icon.bg-success {
    background-color: rgba(34, 197, 94, 0.15) !important;
    color: #15803d;
  }

  .user-management .stat-card__icon.bg-danger {
    background-color: rgba(239, 68, 68, 0.15) !important;
    color: var(--tone-700);
  }

  .user-management .stat-card__icon.bg-secondary {
    background-color: rgba(15, 23, 42, 0.1) !important;
    color: #0f172a;
  }

  .user-management .user-table thead th {
    text-transform: uppercase;
    font-size: .78rem;
    letter-spacing: .08em;
    color: #94a3b8;
    border-bottom: 1px solid #e2e8f0;
  }

  .user-management .user-table tbody td {
    border-bottom: 1px solid #f1f5f9;
  }

  .user-management .badge-outline {
    background-color: #fff;
    border: 1px solid rgba(15, 23, 42, 0.12);
    color: #0f172a;
    border-radius: 999px;
    padding: .35rem .85rem;
    font-size: .85rem;
  }

  .user-management .badge-status-active {
    background: rgba(34, 197, 94, 0.15);
    color: #15803d;
  }

  .user-management .badge-status-pending {
    background: rgba(248, 113, 22, 0.15);
    color: #b45309;
  }

  .user-management .action-pill {
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

  .user-management .action-pill:hover {
    border-color: var(--tone-600);
    color: #fff;
    background-color: var(--tone-600);
    box-shadow: 0 8px 20px rgba(220, 38, 38, 0.15);
  }

  .user-management .list-group-item {
    border: none;
    padding-left: 0;
    padding-right: 0;
  }

  .user-management .list-group-item + .list-group-item {
    border-top: 1px solid rgba(15, 23, 42, 0.08);
  }

  .user-management .modal-content {
    border-radius: 1rem;
  }
</style>
@endpush

@section('content')
@php
  $editUserId = session('edit_user_id');
  $createModalHasErrors = $errors->any() && ! $editUserId;
  $editModalHasErrors = $editUserId && $errors->getBag('editUser')->any();
  $editModalPrefill = $editModalHasErrors ? [
      'userId' => (string) $editUserId,
      'nombre' => old('nombre'),
      'apellido' => old('apellido'),
      'email' => old('email'),
      'rolId' => (string) old('rol_id'),
      'updateUrl' => route('admin.users.update', ['user' => $editUserId]),
  ] : null;
@endphp
<div class="container-fluid user-management">
  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      Corrige los errores del formulario y vuelve a intentar.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  @endif

  <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4">
    <div>
      <h1 class="h3 mb-1">Gestión de Usuarios</h1>
      <p class="text-muted mb-0">Administra el acceso, revisa el estado general y mantén control del sistema.</p>
    </div>
    <button type="button"
            id="btnNuevoUsuario"
            class="btn btn-primary mt-3 mt-lg-0"
            data-bs-toggle="modal"
            data-bs-target="#modalNuevoUsuario">
      <i class="fas fa-user-plus me-2"></i>Nuevo usuario
    </button>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-xxl-3 col-md-6">
      <div class="stat-card shadow-sm">
        <div class="d-flex align-items-center gap-3">
          <span class="stat-card__icon bg-primary"><i class="fas fa-users"></i></span>
          <div>
            <p class="text-muted mb-0 small">Total usuarios</p>
            <h4 class="mb-0">{{ number_format($stats['total']) }}</h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-3 col-md-6">
      <div class="stat-card shadow-sm">
        <div class="d-flex align-items-center gap-3">
          <span class="stat-card__icon bg-success"><i class="fas fa-user-check"></i></span>
          <div>
            <p class="text-muted mb-0 small">Usuarios activos</p>
            <h4 class="mb-0">{{ number_format($stats['activos']) }}</h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-3 col-md-6">
      <div class="stat-card shadow-sm">
        <div class="d-flex align-items-center gap-3">
          <span class="stat-card__icon bg-danger"><i class="fas fa-shield-halved"></i></span>
          <div>
            <p class="text-muted mb-0 small">Administradores</p>
            <h4 class="mb-0">{{ number_format($stats['administradores']) }}</h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-3 col-md-6">
      <div class="stat-card shadow-sm">
        <div class="d-flex align-items-center gap-3">
          <span class="stat-card__icon bg-secondary"><i class="fas fa-crown"></i></span>
          <div>
            <p class="text-muted mb-0 small">Superusuarios</p>
            <h4 class="mb-0">{{ number_format($stats['superusuarios']) }}</h4>
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
              <h5 class="mb-1">Usuarios del sistema</h5>
              <p class="text-muted mb-0 small">Lista general de cuentas registradas.</p>
            </div>
            <form action="{{ route('admin.users.index') }}" method="GET" class="w-100 w-xl-auto">
              <div class="row g-2">
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                      <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text"
                           name="q"
                           class="form-control border-start-0"
                           placeholder="Buscar usuario..."
                           value="{{ $search ?? '' }}">
                  </div>
                </div>
                <div class="col-md-4">
                  <select name="rol" class="form-select">
                    <option value="">Todos los roles</option>
                    @foreach ($roles as $rol)
                      <option value="{{ $rol->id }}" @selected((string) $rolId === (string) $rol->id)>
                        {{ $rol->nombre }} ({{ $rol->users_count }})
                      </option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                  <button type="submit" class="btn btn-outline-primary w-100">Filtrar</button>
                  @if($search || $rolId)
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light" title="Limpiar filtros">
                      <i class="fas fa-rotate-left"></i>
                    </a>
                  @endif
                </div>
              </div>
            </form>
          </div>

          <div class="table-responsive">
            <table class="table align-middle user-table mb-0">
              <thead>
                <tr>
                  <th>Usuario</th>
                  <th>Rol</th>
                  <th>Último acceso</th>
                  <th class="text-end">Acciones</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($usuarios as $usuario)
                  @php
                    $activo = ! is_null($usuario->email_verified_at);
                    $ultimoAcceso = optional($usuario->updated_at)->format('Y-m-d');
                  @endphp
                  <tr>
                    <td>
                      <div class="fw-semibold">{{ trim($usuario->nombre . ' ' . $usuario->apellido) }}</div>
                      <div class="text-muted small">{{ $usuario->email }}</div>
                    </td>
                    <td>
                      <span class="badge-outline">{{ $usuario->rol->nombre ?? 'Sin rol' }}</span>
                      @if ($usuario->superuser)
                        <span class="badge rounded-pill bg-danger ms-1">Superuser</span>
                      @endif
                    </td>
                    <td>{{ $ultimoAcceso ?? 'N/D' }}</td>
                    <td class="text-end">
                      <button type="button"
                              class="action-pill btn-edit-user"
                              data-edit-user="true"
                              data-user-id="{{ $usuario->id }}"
                              data-user-nombre="{{ $usuario->nombre }}"
                              data-user-apellido="{{ $usuario->apellido }}"
                              data-user-email="{{ $usuario->email }}"
                              data-user-rol-id="{{ $usuario->rol_id }}"
                              data-update-url="{{ route('admin.users.update', $usuario) }}"
                              title="Editar usuario">
                        <i class="fas fa-user-pen"></i>
                      </button>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center text-muted py-4">
                      No se encontraron usuarios con el criterio actual.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="mt-3">
            {{ $usuarios->links() }}
          </div>
        </div>
      </div>
    </div>

    <div class="col-xxl-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <h5 class="mb-3">Resumen por rol</h5>
          <ul class="list-group list-group-flush">
            @foreach ($roleBreakdown as $rol)
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>{{ $rol->nombre }}</span>
                <span class="badge bg-light text-dark border">{{ $rol->users_count }}</span>
              </li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade {{ $createModalHasErrors ? 'show d-block' : '' }}"
     id="modalNuevoUsuario"
     tabindex="-1"
     aria-hidden="{{ $createModalHasErrors ? 'false' : 'true' }}"
     @if($createModalHasErrors) style="background: rgba(0,0,0,.5);" @endif>
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-0">
        <div>
          <h5 class="modal-title">Crear nuevo usuario</h5>
          <p class="text-muted mb-0 small">Completa los datos para registrar una nueva cuenta.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label" for="modal_nombre">Nombre</label>
              <input type="text"
                     id="modal_nombre"
                     name="nombre"
                     class="form-control @error('nombre') is-invalid @enderror"
                     value="{{ $editUserId ? '' : old('nombre') }}"
                     required>
              @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label" for="modal_apellido">Apellido</label>
              <input type="text"
                     id="modal_apellido"
                     name="apellido"
                     class="form-control @error('apellido') is-invalid @enderror"
                     value="{{ $editUserId ? '' : old('apellido') }}"
                     required>
              @error('apellido')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label" for="modal_email">Correo electrónico</label>
            <input type="email"
                   id="modal_email"
                   name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ $editUserId ? '' : old('email') }}"
                   required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label" for="modal_rol">Rol principal</label>
            <select id="modal_rol"
                    name="rol_id"
                    class="form-select @error('rol_id') is-invalid @enderror"
                    required>
              <option value="">Selecciona un rol</option>
              @foreach ($roles as $rol)
                <option value="{{ $rol->id }}" @selected(! $editUserId && old('rol_id') == $rol->id)>{{ $rol->nombre }}</option>
              @endforeach
            </select>
            @error('rol_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label" for="modal_password">Contraseña</label>
              <input type="password"
                     id="modal_password"
                     name="password"
                     class="form-control @error('password') is-invalid @enderror"
                     required>
              @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label" for="modal_password_confirmation">Confirmar contraseña</label>
              <input type="password"
                     id="modal_password_confirmation"
                     name="password_confirmation"
                     class="form-control"
                     required>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Crear usuario</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade {{ $editModalHasErrors ? 'show d-block' : '' }}"
     id="modalEditarUsuario"
     tabindex="-1"
     aria-hidden="{{ $editModalHasErrors ? 'false' : 'true' }}"
     @if($editModalHasErrors) style="background: rgba(0,0,0,.5);" @endif>
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-0">
        <div>
          <h5 class="modal-title">Editar Usuario</h5>
          <p class="text-muted mb-0 small">Modifica la información del usuario.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="formEditarUsuario"
            method="POST"
            action="{{ $editModalHasErrors ? route('admin.users.update', ['user' => $editUserId]) : '#' }}">
        @csrf
        @method('PUT')
        <input type="hidden"
               id="edit_user_id_field"
               name="user_id"
               value="{{ $editModalHasErrors ? $editUserId : '' }}">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label" for="edit_nombre">Nombre</label>
              <input type="text"
                     id="edit_nombre"
                     name="nombre"
                     class="form-control @error('nombre', 'editUser') is-invalid @enderror"
                     value="{{ $editModalHasErrors ? old('nombre') : '' }}"
                     required>
              @error('nombre', 'editUser')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label" for="edit_apellido">Apellido</label>
              <input type="text"
                     id="edit_apellido"
                     name="apellido"
                     class="form-control @error('apellido', 'editUser') is-invalid @enderror"
                     value="{{ $editModalHasErrors ? old('apellido') : '' }}"
                     required>
              @error('apellido', 'editUser')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label" for="edit_email">Correo electrónico</label>
            <input type="email"
                   id="edit_email"
                   name="email"
                   class="form-control @error('email', 'editUser') is-invalid @enderror"
                   value="{{ $editModalHasErrors ? old('email') : '' }}"
                   required>
            @error('email', 'editUser')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label" for="edit_rol">Rol</label>
            <select id="edit_rol"
                    name="rol_id"
                    class="form-select @error('rol_id', 'editUser') is-invalid @enderror"
                    required>
              <option value="">Selecciona un rol</option>
              @foreach ($roles as $rol)
                <option value="{{ $rol->id }}" @if($editModalHasErrors) @selected(old('rol_id') == $rol->id) @endif>
                  {{ $rol->nombre }}
                </option>
              @endforeach
            </select>
            @error('rol_id', 'editUser')<div class="invalid-feedback">{{ $message }}</div>@enderror
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

    const nuevoUsuarioModal = setupModalController('modalNuevoUsuario');
    if (nuevoUsuarioModal) {
      const triggerBtn = document.getElementById('btnNuevoUsuario');
      if (triggerBtn) {
        triggerBtn.addEventListener('click', function (event) {
          event.preventDefault();
          nuevoUsuarioModal.show();
        });
      }

      if (@json($createModalHasErrors)) {
        nuevoUsuarioModal.show();
      }
    }

    const editarUsuarioModal = setupModalController('modalEditarUsuario');
    if (editarUsuarioModal) {
      const editForm = document.getElementById('formEditarUsuario');
      const editNombre = document.getElementById('edit_nombre');
      const editApellido = document.getElementById('edit_apellido');
      const editEmail = document.getElementById('edit_email');
      const editRol = document.getElementById('edit_rol');
      const editUserIdField = document.getElementById('edit_user_id_field');

      const populateAndShowEditModal = (data) => {
        if (!editForm) {
          return;
        }

        editForm.action = data.updateUrl || '#';
        if (editNombre) editNombre.value = data.nombre ?? '';
        if (editApellido) editApellido.value = data.apellido ?? '';
        if (editEmail) editEmail.value = data.email ?? '';
        if (editRol) editRol.value = data.rolId ?? '';
        if (editUserIdField) editUserIdField.value = data.userId ?? '';

        editarUsuarioModal.show();
      };

      document.querySelectorAll('[data-edit-user="true"]').forEach((button) => {
        button.addEventListener('click', () => {
          populateAndShowEditModal({
            userId: button.dataset.userId || '',
            nombre: button.dataset.userNombre || '',
            apellido: button.dataset.userApellido || '',
            email: button.dataset.userEmail || '',
            rolId: button.dataset.userRolId || '',
            updateUrl: button.dataset.updateUrl || '#',
          });
        });
      });

      const editModalPrefill = @json($editModalPrefill);

      if (editModalPrefill) {
        populateAndShowEditModal(editModalPrefill);
      }
    }
  });
</script>
@endpush
