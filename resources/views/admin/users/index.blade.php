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
  $modalHasErrors = $errors->any();
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
                  <th>Estado</th>
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
                      <div class="fw-semibold">{{ $usuario->name }}</div>
                      <div class="text-muted small">{{ $usuario->email }}</div>
                    </td>
                    <td>
                      <span class="badge-outline">{{ $usuario->rol->nombre ?? 'Sin rol' }}</span>
                      @if ($usuario->superuser)
                        <span class="badge rounded-pill bg-danger ms-1">Superuser</span>
                      @endif
                    </td>
                    <td>
                      <span class="badge {{ $activo ? 'badge-status-active' : 'badge-status-pending' }}">
                        {{ $activo ? 'Activo' : 'Pendiente' }}
                      </span>
                    </td>
                    <td>{{ $ultimoAcceso ?? 'N/D' }}</td>
                    <td class="text-end">
                      <a class="action-pill"
                         href="{{ route('users.roles.index', ['focus' => $usuario->id]) }}"
                         title="Gestionar roles">
                        <i class="fas fa-user-gear"></i>
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center text-muted py-4">
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

<div class="modal fade {{ $modalHasErrors ? 'show d-block' : '' }}"
     id="modalNuevoUsuario"
     tabindex="-1"
     aria-hidden="{{ $modalHasErrors ? 'false' : 'true' }}"
     @if($modalHasErrors) style="background: rgba(0,0,0,.5);" @endif>
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
          <div class="mb-3">
            <label class="form-label" for="modal_name">Nombre completo</label>
            <input type="text"
                   id="modal_name"
                   name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}"
                   required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label" for="modal_email">Correo electrónico</label>
            <input type="email"
                   id="modal_email"
                   name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}"
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
                <option value="{{ $rol->id }}" @selected(old('rol_id') == $rol->id)>{{ $rol->nombre }}</option>
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
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('modalNuevoUsuario');
    if (!modalEl) return;

    const cleanupManualModal = () => {
      modalEl.classList.remove('show', 'd-block');
      modalEl.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('modal-open');
      const manualBackdrop = document.querySelector('.modal-backdrop[data-manual-backdrop="true"]');
      if (manualBackdrop) manualBackdrop.remove();
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
      if (!document.querySelector('.modal-backdrop')) {
        const manualBackdrop = document.createElement('div');
        manualBackdrop.className = 'modal-backdrop fade show';
        manualBackdrop.setAttribute('data-manual-backdrop', 'true');
        document.body.appendChild(manualBackdrop);
      }
    };

    modalEl.addEventListener('hidden.bs.modal', cleanupManualModal);

    modalEl.querySelectorAll('[data-bs-dismiss="modal"]').forEach((trigger) => {
      trigger.addEventListener('click', cleanupManualModal);
    });

    const triggerBtn = document.getElementById('btnNuevoUsuario');
    if (triggerBtn) {
      triggerBtn.addEventListener('click', function (event) {
        event.preventDefault();
        showModal();
      });
    }

    @if($modalHasErrors)
      showModal();
    @endif
  });
</script>
@endpush
