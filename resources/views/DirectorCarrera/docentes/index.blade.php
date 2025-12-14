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
                // Formatear RUT chileno
                $rut = $docente->rut ?? '';
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
                $email = $docente->user->email ?? ($docente->email ?? null);
              @endphp
              <tr>
                <td>
                  <p class="mb-0 fw-semibold">{{ $docente->nombre }} {{ $docente->apellido ?? '' }}</p>
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
                  <span class="badge bg-light text-dark">{{ $docente->carrera->nombre ?? 'Sin carrera' }}</span>
                </td>
                <td class="text-end">
                  <button type="button"
                          class="btn btn-sm btn-outline-primary btn-edit-docente"
                          data-edit-docente="true"
                          data-docente-id="{{ $docente->id }}"
                          data-docente-nombre="{{ $docente->nombre }}"
                          data-docente-apellido="{{ $docente->apellido }}"
                          data-docente-email="{{ $email }}"
                          data-docente-carrera-id="{{ $docente->carrera_id }}"
                          data-update-url="{{ route('director.docentes.update', $docente) }}"
                          title="Editar docente">
                    <i class="fas fa-pen"></i>
                  </button>
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
  });
</script>
@endpush
