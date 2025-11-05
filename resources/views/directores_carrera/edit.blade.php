@extends('layouts.app')

@section('title', 'Editar director de carrera')

@section('content')
<div class="container-fluid">
  @include('partials.alerts')

  <div class="card">
    <div class="card-header">Editar director de carrera</div>
    <div class="card-body">
      <form action="{{ route('directores-carrera.update', $director) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-3">
          <div class="col-md-6">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $director->nombre) }}" class="form-control @error('nombre') is-invalid @enderror" required>
            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="apellido" class="form-label">Apellido</label>
            <input type="text" id="apellido" name="apellido" value="{{ old('apellido', $director->apellido) }}" class="form-control @error('apellido') is-invalid @enderror" required>
            @error('apellido')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="email" class="form-label">Correo</label>
            <input type="email" id="email" name="email" value="{{ old('email', $director->email) }}" class="form-control @error('email') is-invalid @enderror" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $director->telefono) }}" class="form-control @error('telefono') is-invalid @enderror">
            @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label for="carrera_id" class="form-label">Carrera</label>
            <select id="carrera_id" name="carrera_id" class="form-select @error('carrera_id') is-invalid @enderror" required>
              <option value="">Selecciona una carrera</option>
              @foreach($carreras as $carrera)
                <option value="{{ $carrera->id }}" @selected(old('carrera_id', $director->carrera_id) == $carrera->id)>{{ $carrera->nombre }}</option>
              @endforeach
            </select>
            @error('carrera_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="mt-4 d-flex justify-content-end gap-2">
          <a href="{{ route('directores-carrera.index') }}" class="btn btn-secondary">Cancelar</a>
          <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
