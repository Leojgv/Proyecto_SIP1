@extends('layouts.app')

@section('title', 'Editar solicitud')

@section('content')
<div class="container-fluid">
  @include('partials.alerts')

  @php
    $solicitudModel = $solicitud ?? request()->route('solicitud') ?? request()->route('solicitude');
  @endphp

  @if (! $solicitudModel instanceof \App\Models\Solicitud)
    <div class="alert alert-danger">No se encontro la solicitud solicitada.</div>
  @else
  <div class="card">
    <div class="card-header">Editar solicitud</div>
    <div class="card-body">
      <form action="{{ route('solicitudes.update', ['solicitude' => $solicitudModel->getKey()]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-3">
          <div class="col-md-4">
            <label for="fecha_solicitud" class="form-label">Fecha</label>
            <input type="date" id="fecha_solicitud" name="fecha_solicitud" value="{{ old('fecha_solicitud', optional($solicitudModel->fecha_solicitud)->format('Y-m-d')) }}" class="form-control @error('fecha_solicitud') is-invalid @enderror" required>
            @error('fecha_solicitud')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          @php
            $estadoOptions = ['Pendiente', 'En proceso', 'Terminado'];
          @endphp
          <div class="col-md-4">
            <label for="estado" class="form-label">Estado</label>
            <select id="estado" name="estado" class="form-select @error('estado') is-invalid @enderror">
              <option value="">Selecciona un estado</option>
              @foreach ($estadoOptions as $estadoOption)
                <option value="{{ $estadoOption }}" @selected(old('estado', $solicitudModel->estado ?? 'Pendiente') === $estadoOption)>{{ $estadoOption }}</option>
              @endforeach
            </select>
            @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="estudiante_id" class="form-label">Estudiante</label>
            <select id="estudiante_id" name="estudiante_id" class="form-select @error('estudiante_id') is-invalid @enderror" required>
              @foreach($estudiantes as $estudiante)
                <option value="{{ $estudiante->id }}" @selected(old('estudiante_id', $solicitudModel->estudiante_id) == $estudiante->id)>{{ $estudiante->nombre }} {{ $estudiante->apellido }}</option>
              @endforeach
            </select>
            @error('estudiante_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="asesor_id" class="form-label">Asesora pedagogica</label>
            <select id="asesor_id" name="asesor_id" class="form-select @error('asesor_id') is-invalid @enderror" required>
              @foreach($asesores as $asesor)
                <option value="{{ $asesor->id }}" @selected(old('asesor_id', $solicitudModel->asesor_id) == $asesor->id)>{{ $asesor->nombre }} {{ $asesor->apellido }}</option>
              @endforeach
            </select>
            @error('asesor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="director_id" class="form-label">Director de carrera</label>
            <select id="director_id" name="director_id" class="form-select @error('director_id') is-invalid @enderror" required>
              @foreach($directores as $director)
                <option value="{{ $director->id }}" @selected(old('director_id', $solicitudModel->director_id) == $director->id)>{{ $director->nombre }} {{ $director->apellido }}</option>
              @endforeach
            </select>
            @error('director_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label for="descripcion" class="form-label">Descripcion</label>
            <textarea id="descripcion" name="descripcion" rows="4" class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion', $solicitudModel->descripcion) }}</textarea>
            @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="mt-4 d-flex justify-content-end gap-2">
          <a href="{{ route('solicitudes.index') }}" class="btn btn-secondary">Cancelar</a>
          <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
      </form>
    </div>
  </div>
  @endif
</div>
@endsection
