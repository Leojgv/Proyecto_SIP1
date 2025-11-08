@extends('layouts.app')

@section('content')
<div class="container">
  <div class="mb-4">
    <h1 class="h3 mb-1">Nueva notificación</h1>
    <p class="text-muted mb-0">Envía un mensaje a uno o varios usuarios.</p>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <form action="{{ route('notificaciones.store') }}" method="POST">
        @csrf

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label for="titulo" class="form-label">Título *</label>
            <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo" value="{{ old('titulo') }}" required>
            @error('titulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="texto_boton" class="form-label">Texto del botón (opcional)</label>
            <input type="text" class="form-control @error('texto_boton') is-invalid @enderror" id="texto_boton" name="texto_boton" value="{{ old('texto_boton') }}" placeholder="Ver detalle">
            @error('texto_boton')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="mb-3">
          <label for="mensaje" class="form-label">Mensaje *</label>
          <textarea class="form-control @error('mensaje') is-invalid @enderror" id="mensaje" name="mensaje" rows="4" required>{{ old('mensaje') }}</textarea>
          @error('mensaje')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
          <label for="url" class="form-label">URL destino (opcional)</label>
          <input type="url" class="form-control @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url') }}" placeholder="https://ejemplo.com/pagina">
          @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
          <label class="form-label d-block">Audiencia *</label>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="audiencia" id="audiencia_todos" value="todos" {{ old('audiencia', 'todos') === 'todos' ? 'checked' : '' }}>
            <label class="form-check-label" for="audiencia_todos">Todos los usuarios</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="audiencia" id="audiencia_rol" value="rol" {{ old('audiencia') === 'rol' ? 'checked' : '' }}>
            <label class="form-check-label" for="audiencia_rol">Por rol</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="audiencia" id="audiencia_usuarios" value="usuarios" {{ old('audiencia') === 'usuarios' ? 'checked' : '' }}>
            <label class="form-check-label" for="audiencia_usuarios">Usuarios específicos</label>
          </div>
          @error('audiencia')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label for="rol_id" class="form-label">Rol destino</label>
            <select class="form-select @error('rol_id') is-invalid @enderror" id="rol_id" name="rol_id">
              <option value="">Selecciona un rol</option>
              @foreach ($roles as $rol)
                <option value="{{ $rol->id }}" @selected(old('rol_id') == $rol->id)>{{ $rol->nombre }}</option>
              @endforeach
            </select>
            <div class="form-text">Obligatorio si eliges "Por rol".</div>
            @error('rol_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="user_ids" class="form-label">Usuarios destino</label>
            <select multiple class="form-select @error('user_ids') is-invalid @enderror" id="user_ids" name="user_ids[]" size="5">
              @foreach ($usuarios as $usuario)
                <option value="{{ $usuario->id }}" @selected(collect(old('user_ids', []))->contains($usuario->id))>
                  {{ $usuario->name ?? $usuario->email }}
                </option>
              @endforeach
            </select>
            <div class="form-text">Obligatorio si eliges "Usuarios específicos".</div>
            @error('user_ids')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
          <a href="{{ route('notificaciones.index') }}" class="btn btn-outline-secondary">Cancelar</a>
          <button type="submit" class="btn btn-primary">Enviar notificación</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
