@extends('layouts.app')

@section('title', 'Solicitar entrevista')

@push('styles')
<style>
  .interview-hero {
    background: linear-gradient(135deg, #f9fafb 0%, #eef2ff 100%);
    min-height: calc(100vh - 120px);
  }
  .interview-card {
    border-radius: 1.75rem;
  }
  .interview-card .form-label {
    font-weight: 600;
    color: #1f2937;
  }
  .interview-card .form-control,
  .interview-card .form-select {
    border-radius: 1rem;
    border: 1px solid #e5e7eb;
    padding: 0.75rem 1rem;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
  }
  .interview-card .form-control:focus,
  .interview-card .form-select:focus {
    border-color: #c8102e;
    box-shadow: 0 0 0 0.25rem rgba(200, 16, 46, 0.15);
  }
  .interview-card .input-hint {
    font-size: 0.85rem;
    color: #6b7280;
  }
  .badge-soft-danger {
    background: rgba(200, 16, 46, 0.12);
    color: #c8102e;
    font-weight: 600;
    border-radius: 999px;
    padding: 0.35rem 0.85rem;
  }
  .btn-inacap {
    background-color: #c8102e;
    border-color: #c8102e;
    color: #fff;
    border-radius: 0.9rem;
    padding: 0.75rem 1.75rem;
    font-weight: 600;
  }
  .btn-inacap:hover,
  .btn-inacap:focus {
    background-color: #a50d25;
    border-color: #a50d25;
    color: #fff;
  }
  .attachment-drop {
    border: 2px dashed #d1d5db;
    border-radius: 1rem;
    padding: 1.75rem;
    text-align: center;
    background: #f9fafb;
  }
  .attachment-drop i {
    font-size: 2rem;
    color: #c8102e;
  }
</style>
@endpush

@section('content')
<div class="interview-hero py-4 py-lg-5">
  <div class="container">
    @include('partials.alerts')

    <div class="row justify-content-center">
      <div class="col-12 col-xl-10">
        <div class="card interview-card shadow border-0">
          <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5">
              <div>
                <span class="badge-soft-danger text-uppercase">Gestión de entrevistas</span>
                <h1 class="mt-3 mb-2 fw-bold text-dark">Solicitud de Entrevista</h1>
                <p class="mb-0 text-secondary">Completa el formulario para coordinar una entrevista con el equipo de asesoría pedagógica. Nos contactaremos contigo para confirmar la cita.</p>
              </div>
              <div class="text-md-end">
                <a href="{{ route('entrevistas.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                  <i class="fas fa-arrow-left me-2"></i>Volver al listado
                </a>
              </div>
            </div>

            <form action="{{ route('entrevistas.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
              @csrf
              <div class="row g-4">
                <div class="col-md-6">
                  <label for="fecha" class="form-label">Fecha propuesta</label>
                  <input
                    type="date"
                    id="fecha"
                    name="fecha"
                    value="{{ old('fecha') }}"
                    class="form-control @error('fecha') is-invalid @enderror"
                    required>
                  @error('fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  <p class="input-hint mt-2">Selecciona la fecha tentativa para tu entrevista.</p>
                </div>

                <div class="col-md-6">
                  <label for="solicitud_id" class="form-label">Solicitud asociada</label>
                  <select
                    id="solicitud_id"
                    name="solicitud_id"
                    class="form-select @error('solicitud_id') is-invalid @enderror"
                    required>
                    <option value="">Selecciona una solicitud previa</option>
                    @foreach($solicitudes as $solicitud)
                      <option value="{{ $solicitud->id }}" @selected(old('solicitud_id') == $solicitud->id)>
                        {{ $solicitud->fecha_solicitud?->format('d/m/Y') }} &mdash; {{ $solicitud->estudiante->nombre ?? '' }} {{ $solicitud->estudiante->apellido ?? '' }}
                      </option>
                    @endforeach
                  </select>
                  @error('solicitud_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  <p class="input-hint mt-2">Escoge la solicitud que originó esta entrevista.</p>
                </div>

                <div class="col-md-6">
                  <label for="asesor_pedagogico_id" class="form-label">Asesor pedagógico</label>
                  <select
                    id="asesor_pedagogico_id"
                    name="asesor_pedagogico_id"
                    class="form-select @error('asesor_pedagogico_id') is-invalid @enderror"
                    required>
                    <option value="">Selecciona a tu asesor</option>
                    @foreach($asesores as $asesor)
                      <option value="{{ $asesor->id }}" @selected(old('asesor_pedagogico_id') == $asesor->id)>{{ $asesor->nombre }} {{ $asesor->apellido }}</option>
                    @endforeach
                  </select>
                  @error('asesor_pedagogico_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  <p class="input-hint mt-2">Elige el profesional que atenderá tu entrevista.</p>
                </div>

                <div class="col-12">
                  <label for="observaciones" class="form-label">Descripción o motivo</label>
                  <textarea
                    id="observaciones"
                    name="observaciones"
                    rows="4"
                    class="form-control @error('observaciones') is-invalid @enderror"
                    placeholder="Describe brevemente los temas que deseas tratar en la entrevista.">{{ old('observaciones') }}</textarea>
                  @error('observaciones')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                  <label class="form-label">Adjuntar archivo o imagen (opcional)</label>
                  <div class="attachment-drop">
                    <i class="fas fa-cloud-upload-alt mb-3"></i>
                    <p class="mb-2 fw-semibold">Arrastra y suelta o <label for="attachment" class="text-danger text-decoration-underline mb-0" style="cursor:pointer;">selecciona un archivo</label></p>
                    <p class="input-hint mb-0">Formatos permitidos: PDF, JPG o PNG (hasta 5 MB).</p>
                    <input type="file" id="attachment" name="attachment" class="d-none" accept=".pdf,.jpg,.jpeg,.png">
                  </div>
                </div>

                <div class="col-12">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="privacy_ack" required>
                    <label class="form-check-label" for="privacy_ack">
                      Autorizo el tratamiento de mis datos personales conforme a la Ley 19.628 y las políticas de privacidad de la Vicerrectoría.
                    </label>
                    <div class="invalid-feedback">Debes autorizar el tratamiento para continuar.</div>
                  </div>
                </div>
              </div>

              <div class="d-flex flex-column flex-md-row justify-content-md-end gap-3 mt-5">
                <button type="reset" class="btn btn-outline-secondary rounded-pill px-4">Limpiar formulario</button>
                <button type="submit" class="btn btn-inacap px-4">
                  <i class="fas fa-paper-plane me-2"></i>Enviar solicitud
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
