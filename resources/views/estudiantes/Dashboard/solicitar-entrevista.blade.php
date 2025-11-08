@extends('layouts.dashboard_estudiante.estudiante')

@section('title', 'Solicitar Entrevista')

@section('content')
<div class="container-fluid">
  <div class="row align-items-center mb-4">
    <div class="col-lg-8">
      <h1 class="h3 mb-1">Solicitud de Entrevista</h1>
      <p class="text-muted mb-0">Solicita una entrevista con el equipo de asesoría pedagógica.</p>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-12 col-xxl-10 col-xl-10">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h5 class="card-title mb-2">Formulario de Solicitud</h5>
          <p class="card-text text-muted mb-4">Completa los campos para enviar tu solicitud de entrevista</p>

          <form action="{{ route('estudiantes.entrevistas.store') }}" method="POST" class="row g-3">
            @csrf

            <div class="col-md-6">
              <label class="form-label">Nombres</label>
              <input type="text" class="form-control" value="{{ $estudiante->nombre }}" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label">Apellidos</label>
              <input type="text" class="form-control" value="{{ $estudiante->apellido }}" disabled>
            </div>

            <div class="col-md-6">
              <label class="form-label">RUT</label>
              <input type="text" class="form-control" value="{{ $estudiante->rut }}" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label">Tu Carrera</label>
              <input type="text" class="form-control" value="{{ $estudiante->carrera->nombre ?? 'Sin carrera' }}" disabled>
            </div>

            <div class="col-md-6">
              <label class="form-label">Correo Electrónico</label>
              <input type="email" class="form-control" value="{{ $estudiante->email }}" disabled>
            </div>
            <div class="col-md-6">
              <label for="telefono" class="form-label">Teléfono de Contacto</label>
              <input type="text" name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono', $estudiante->telefono) }}" required>
              @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
              <label for="titulo" class="form-label">Título de la Solicitud</label>
              <input type="text" name="titulo" id="titulo" class="form-control @error('titulo') is-invalid @enderror" placeholder="Breve descripción del motivo de la entrevista" value="{{ old('titulo') }}" required>
              @error('titulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
              <label for="descripcion" class="form-label">Descripción</label>
              <textarea name="descripcion" id="descripcion" rows="5" class="form-control @error('descripcion') is-invalid @enderror" placeholder="Describe detalladamente el motivo de tu solicitud de entrevista..." required>{{ old('descripcion') }}</textarea>
              @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <!-- Sin adjuntar archivo por solicitud del cliente -->

            <div class="col-12">
              <div class="form-check p-3 bg-light rounded border">
                <input class="form-check-input" type="checkbox" value="1" id="autorizacion" name="autorizacion" {{ old('autorizacion') ? 'checked' : '' }} required>
                <label class="form-check-label" for="autorizacion">
                  Autorizo el tratamiento de mis datos personales para fines académicos y de asesoría pedagógica.
                </label>
              </div>
              @error('autorizacion')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="col-12 d-flex justify-content-end gap-2 mt-2">
              <a href="{{ route('estudiantes.dashboard') }}" class="btn btn-outline-primary">Cancelar</a>
              <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

