@extends('layouts.dashboard_coordinadora.app')

@section('title', 'Agenda de atencion')

@section('content')
<div class="container-fluid">
  <div class="row align-items-center mb-4">
    <div class="col">
      <h1 class="h3 mb-1">Agenda de la Coordinadora</h1>
      <p class="text-muted mb-0">Tu horario laboral esta fijado de {{ $horarioLaboral['inicio'] }} a {{ $horarioLaboral['fin'] }} todos los dias. Solo necesitas bloquear las franjas en que no puedas atender.</p>
    </div>
  </div>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="row g-4">
    <div class="col-12 col-xl-4">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body">
          <h5 class="card-title mb-3">Horario laboral</h5>
          <p class="card-text text-muted">Tus cupos se generan automaticamente todos los dias entre {{ $horarioLaboral['inicio'] }} y {{ $horarioLaboral['fin'] }} (45 minutos de entrevista + 15 minutos de descanso).</p>
          <ul class="small text-muted mb-0">
            <li>Los estudiantes veran cupos en ese rango mientras no exista una entrevista o bloqueo.</li>
            <li>Usa los bloqueos para marcar vacaciones, reuniones o ausencias puntuales.</li>
          </ul>
        </div>
      </div>
    </div>

    <div class="col-12 col-xl-8">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body">
          <h5 class="card-title mb-3">Bloqueos puntuales</h5>
          <p class="card-text text-muted">Agenda eventos personales o descansos fuera de la regla 45/15.</p>

          <form action="{{ route('coordinadora.agenda.bloqueos.store') }}" method="POST" class="row g-3 mb-4">
            @csrf
            <div class="col-md-6">
              <label class="form-label">Fecha</label>
              <input type="date" name="fecha" class="form-control @error('fecha') is-invalid @enderror" value="{{ old('fecha') }}" required>
              @error('fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
              <label class="form-label">Inicio</label>
              <input type="time" name="hora_inicio" class="form-control @error('hora_inicio') is-invalid @enderror" value="{{ old('hora_inicio') }}" required>
              @error('hora_inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
              <label class="form-label">Fin</label>
              <input type="time" name="hora_fin" class="form-control @error('hora_fin') is-invalid @enderror" value="{{ old('hora_fin') }}" required>
              @error('hora_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
              <label class="form-label">Motivo (opcional)</label>
              <input type="text" name="motivo" class="form-control @error('motivo') is-invalid @enderror" placeholder="Ej: Almuerzo, reunion de equipo..." value="{{ old('motivo') }}">
              @error('motivo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 d-flex justify-content-end">
              <button class="btn btn-outline-danger" type="submit">Agregar bloqueo</button>
            </div>
          </form>

          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Horario</th>
                  <th>Motivo</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @forelse($bloqueos as $bloqueo)
                  <tr>
                    <td>{{ $bloqueo->fecha->format('d/m/Y') }}</td>
                    <td>{{ $bloqueo->hora_inicio }} - {{ $bloqueo->hora_fin }}</td>
                    <td>{{ $bloqueo->motivo ?? 'Sin detalle' }}</td>
                    <td class="text-end">
                      <form action="{{ route('coordinadora.agenda.bloqueos.destroy', $bloqueo) }}" method="POST" onsubmit="return confirm('Eliminar este bloqueo?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" type="submit"><i class="fas fa-trash"></i></button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center text-muted">Aun no tienes bloqueos registrados.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
