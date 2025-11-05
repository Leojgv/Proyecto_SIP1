@extends('layouts.app')

@section('title', 'Editar evidencia')

@section('content')
<div class="container-fluid">
  @include('partials.alerts')

  <div class="card">
    <div class="card-header">Editar evidencia</div>
    <div class="card-body">
      <form action="{{ route('evidencias.update', $evidencia) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-3">
          <div class="col-md-6">
            <label for="tipo" class="form-label">Tipo</label>
            <input type="text" id="tipo" name="tipo" value="{{ old('tipo', $evidencia->tipo) }}" class="form-control @error('tipo') is-invalid @enderror" required>
            @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label for="solicitud_id" class="form-label">Solicitud</label>
            <select id="solicitud_id" name="solicitud_id" class="form-select @error('solicitud_id') is-invalid @enderror" required>
              @foreach($solicitudes as $solicitud)
                <option value="{{ $solicitud->id }}" @selected(old('solicitud_id', $evidencia->solicitud_id) == $solicitud->id)>
                  {{ $solicitud->fecha_solicitud?->format('d/m/Y') }} - {{ $solicitud->estudiante->nombre ?? '' }} {{ $solicitud->estudiante->apellido ?? '' }}
                </option>
              @endforeach
            </select>
            @error('solicitud_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="3" class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion', $evidencia->descripcion) }}</textarea>
            @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label for="ruta_archivo" class="form-label">Ruta de archivo</label>
            <input type="text" id="ruta_archivo" name="ruta_archivo" value="{{ old('ruta_archivo', $evidencia->ruta_archivo) }}" class="form-control @error('ruta_archivo') is-invalid @enderror">
            @error('ruta_archivo')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="mt-4 d-flex justify-content-end gap-2">
          <a href="{{ route('evidencias.index') }}" class="btn btn-secondary">Cancelar</a>
          <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
