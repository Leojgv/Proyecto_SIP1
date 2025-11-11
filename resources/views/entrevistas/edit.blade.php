@extends('layouts.app')

@section('title', 'Editar entrevista')

@section('content')
<div class="container-fluid">
  @include('partials.alerts')

  <div class="card">
    <div class="card-header">Editar entrevista</div>
    <div class="card-body">
      <form action="{{ route('entrevistas.update', $entrevista) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-3">
          <div class="col-md-4">
            <label for="fecha" class="form-label">Fecha</label>
            <input type="date" id="fecha" name="fecha" value="{{ old('fecha', optional($entrevista->fecha)->format('Y-m-d')) }}" class="form-control @error('fecha') is-invalid @enderror" required>
            @error('fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="solicitud_id" class="form-label">Solicitud</label>
            <select id="solicitud_id" name="solicitud_id" class="form-select @error('solicitud_id') is-invalid @enderror" required>
              @foreach($solicitudes as $solicitud)
                <option value="{{ $solicitud->id }}" @selected(old('solicitud_id', $entrevista->solicitud_id) == $solicitud->id)>
                  {{ $solicitud->fecha_solicitud?->format('d/m/Y') }} - {{ $solicitud->estudiante->nombre ?? '' }} {{ $solicitud->estudiante->apellido ?? '' }}
                </option>
              @endforeach
            </select>
            @error('solicitud_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label for="asesor_id" class="form-label">Asesor pedagógico</label>
            <select id="asesor_id" name="asesor_id" class="form-select @error('asesor_id') is-invalid @enderror" required>
              @foreach($asesores as $asesor)
                <option value="{{ $asesor->id }}" @selected(old('asesor_id', $entrevista->asesor_id) == $asesor->id)>{{ $asesor->nombre }} {{ $asesor->apellido }}</option>
              @endforeach
            </select>
            @error('asesor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label for="observaciones" class="form-label">Observaciones</label>
            <textarea id="observaciones" name="observaciones" rows="4" class="form-control @error('observaciones') is-invalid @enderror">{{ old('observaciones', $entrevista->observaciones) }}</textarea>
            @error('observaciones')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="mt-4 d-flex justify-content-end gap-2">
          <a href="{{ route('entrevistas.index') }}" class="btn btn-secondary">Cancelar</a>
          <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
