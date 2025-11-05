@extends('layouts.app')

@section('title', 'Nueva asignación docente-asignatura')

@section('content')
<div class="container-fluid">
  @include('partials.alerts')

  <div class="card">
    <div class="card-header">Registrar asignación</div>
    <div class="card-body">
      <form action="{{ route('docente-asignaturas.store') }}" method="POST">
        @csrf
        <div class="row g-3">
          <div class="col-md-4">
            <label for="docente_id" class="form-label">Docente</label>
            <select id="docente_id" name="docente_id" class="form-select @error('docente_id') is-invalid @enderror" required>
              <option value="">Selecciona un docente</option>
              @foreach($docentes as $docente)
                <option value="{{ $docente->id }}" @selected(old('docente_id') == $docente->id)>{{ $docente->nombre }} {{ $docente->apellido }}</option>
              @endforeach
            </select>
            @error('docente_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="asignatura_id" class="form-label">Asignatura</label>
            <select id="asignatura_id" name="asignatura_id" class="form-select @error('asignatura_id') is-invalid @enderror" required>
              <option value="">Selecciona una asignatura</option>
              @foreach($asignaturas as $asignatura)
                <option value="{{ $asignatura->id }}" @selected(old('asignatura_id') == $asignatura->id)>{{ $asignatura->nombre }}</option>
              @endforeach
            </select>
            @error('asignatura_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="carrera_id" class="form-label">Carrera</label>
            <select id="carrera_id" name="carrera_id" class="form-select @error('carrera_id') is-invalid @enderror" required>
              <option value="">Selecciona una carrera</option>
              @foreach($carreras as $carrera)
                <option value="{{ $carrera->id }}" @selected(old('carrera_id') == $carrera->id)>{{ $carrera->nombre }}</option>
              @endforeach
            </select>
            @error('carrera_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="mt-4 d-flex justify-content-end gap-2">
          <a href="{{ route('docente-asignaturas.index') }}" class="btn btn-secondary">Cancelar</a>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
