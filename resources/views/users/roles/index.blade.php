@extends('layouts.app')

@section('title', 'Asignar roles a usuarios')

@section('content')
@php
    $focusedUserId = old('_focused_user_id');
@endphp
<div class="container-fluid">
  @include('partials.alerts')

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div>
      <h1 class="h3 mb-1">Asignación de Roles</h1>
      <p class="text-muted mb-0">Define el rol principal y los roles adicionales que habilitan accesos especiales.</p>
    </div>
  </div>

  <div class="card">
    <div class="card-body table-responsive p-0">
      <table class="table align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width: 25%;">Usuario</th>
            <th style="width: 50%;">Configurar roles</th>
            <th style="width: 25%;">Resumen actual</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($usuarios as $usuario)
            @php
              $isFocused = (string) $focusedUserId === (string) $usuario->id;
              $selectedPrimary = $isFocused ? old('rol_id') : $usuario->rol_id;
              $selectedExtras = $isFocused
                ? collect(old('roles_secundarios', []))->filter()->map(fn ($id) => (int) $id)->values()->all()
                : $usuario->roles->pluck('id')->diff([$usuario->rol_id])->values()->all();
              $extrasActuales = $usuario->roles->where('id', '!=', $usuario->rol_id);
            @endphp
            <tr>
              <td>
                <strong>{{ trim($usuario->nombre . ' ' . $usuario->apellido) }}</strong>
                <p class="mb-0 text-muted small">{{ $usuario->email }}</p>
                @if ($usuario->estudiante)
                  <p class="mb-0 text-muted small">Estudiante asociado: {{ $usuario->estudiante->nombre }} {{ $usuario->estudiante->apellido }}</p>
                @endif
              </td>
              <td>
                <form action="{{ route('users.roles.update', $usuario) }}" method="POST">
                  @csrf
                  @method('PUT')

                  <div class="mb-0">
                    <label class="form-label">Rol principal</label>
                    <select name="rol_id" class="form-select @if($isFocused && $errors->has('rol_id')) is-invalid @endif" required>
                      <option value="" disabled {{ $selectedPrimary ? '' : 'selected' }}>Selecciona un rol</option>
                      @foreach ($roles as $rol)
                        <option value="{{ $rol->id }}" @selected((int) $selectedPrimary === $rol->id)>{{ $rol->nombre }}</option>
                      @endforeach
                    </select>
                    @if ($isFocused && $errors->has('rol_id'))
                      <div class="invalid-feedback">{{ $errors->first('rol_id') }}</div>
                    @endif
                  </div>
                  <div class="text-md-end mt-3">
                    <button type="submit" class="btn btn-primary">
                      <i class="fas fa-save me-1"></i> Guardar
                    </button>
                  </div>
                </form>
              </td>
              <td>
                <div class="mb-2">
                  <span class="text-muted small">Rol principal</span>
                  <div>
                    <span class="badge bg-primary">{{ $usuario->rol->nombre ?? 'Sin rol' }}</span>
                  </div>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="text-center py-4 text-muted">Aún no existen usuarios registrados.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
