@extends('layouts.dashboard_estudiante.estudiante')

@section('title', 'Solicitar Entrevista')

@section('content')
<div class="container-fluid">
  <div class="row align-items-center mb-4">
    <div class="col-lg-8">
      <h1 class="h3 mb-1">Solicitud de Entrevista</h1>
      <p class="text-muted mb-0">Solicita una entrevista con el equipo de asesoria pedagogica.</p>
    </div>
  </div>

  <div class="row g-4 justify-content-center">
    <div class="col-12 col-xl-10 col-xxl-8">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h5 class="card-title mb-2">Formulario de Solicitud</h5>
          <p class="card-text text-muted mb-4">Selecciona un cupo disponible y completa los campos para enviar tu solicitud.</p>

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
              <label class="form-label">Correo Electronico</label>
              <input type="email" class="form-control" value="{{ $estudiante->email }}" disabled>
            </div>
            <div class="col-md-6">
              <label for="telefono" class="form-label">Telefono de Contacto</label>
              <input type="text" name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono', $estudiante->telefono) }}" required>
              @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
              <label for="titulo" class="form-label">Titulo de la Solicitud</label>
              <input type="text" name="titulo" id="titulo" class="form-control @error('titulo') is-invalid @enderror" placeholder="Breve descripcion del motivo de la entrevista" value="{{ old('titulo') }}" required>
              @error('titulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
              <label for="descripcion" class="form-label">Descripcion</label>
              <textarea name="descripcion" id="descripcion" rows="5" class="form-control @error('descripcion') is-invalid @enderror" placeholder="Describe detalladamente el motivo de tu solicitud de entrevista..." required>{{ old('descripcion') }}</textarea>
              @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
              <label for="cupo" class="form-label">Selecciona un cupo disponible</label>
              @if($lista_de_cupos->isEmpty())
                <div class="alert alert-warning mb-0">
                  No hay cupos disponibles en las proximas semanas. Vuelve a intentarlo mas tarde o contacta a la coordinadora.
                </div>
              @else
                <select name="cupo" id="cupo" class="form-select @error('cupo') is-invalid @enderror" required>
                  <option value="" disabled {{ old('cupo') ? '' : 'selected' }}>Elige un horario</option>
                  @foreach($lista_de_cupos as $cupo)
                    <option value="{{ $cupo['valor'] }}" {{ old('cupo') === $cupo['valor'] ? 'selected' : '' }}>
                      {{ \Illuminate\Support\Str::of($cupo['label'])->ucfirst() }}
                    </option>
                  @endforeach
                </select>
                <div class="form-text">Cada cupo considera 45 minutos de entrevista y 15 minutos de descanso para la coordinadora.</div>
                @error('cupo')<div class="invalid-feedback">{{ $message }}</div>@enderror
              @endif
            </div>

            <div class="col-12">
              <div class="form-check p-3 bg-light rounded border">
                <input class="form-check-input" type="checkbox" value="1" id="autorizacion" name="autorizacion" {{ old('autorizacion') ? 'checked' : '' }} required>
                <label class="form-check-label" for="autorizacion">
                  Autorizo el tratamiento de mis datos personales para fines academicos y de asesoria pedagogica.
                </label>
              </div>
              @error('autorizacion')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="col-12 d-flex justify-content-end gap-2 mt-2">
              <a href="{{ route('estudiantes.dashboard') }}" class="btn btn-outline-danger">Cancelar</a>
              <button type="submit" class="btn btn-danger" {{ $lista_de_cupos->isEmpty() ? 'disabled' : '' }}>Enviar Solicitud</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
