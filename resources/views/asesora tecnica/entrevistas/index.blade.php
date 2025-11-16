@extends('layouts.dashboard_asesoratecnica.app')

@section('title', 'Entrevistas')

@section('content')
<div class="dashboard-page">
  <div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h1 class="h4 mb-1">Entrevistas</h1>
      <p class="text-muted mb-0">Gestiona tus entrevistas y revisa el historial de estudiantes atendidos.</p>
    </div>
    <a href="{{ route('entrevistas.index') }}" class="btn btn-danger">Ver agenda completa</a>
  </div>

  <div class="row g-4">
    <div class="col-xl-7">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h5 class="card-title mb-0">Listado de entrevistas</h5>
              <small class="text-muted">Ordenadas de la mas reciente a la mas antigua</small>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th>Estudiante</th>
                  <th>Fecha</th>
                  <th>Hora</th>
                  <th>Solicitud</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @forelse($entrevistas as $entrevista)
                  <tr>
                    <td>
                      {{ $entrevista->solicitud->estudiante->nombre ?? 'Sin nombre' }} {{ $entrevista->solicitud->estudiante->apellido ?? '' }}
                      <div class="text-muted small">{{ $entrevista->solicitud->estudiante->carrera->nombre ?? 'Sin carrera' }}</div>
                    </td>
                    <td>{{ $entrevista->fecha?->format('d/m/Y') ?? 's/f' }}</td>
                    <td>{{ $entrevista->fecha_hora_inicio?->format('H:i') ?? '--' }}</td>
                    <td class="text-muted small">{{ \Illuminate\Support\Str::limit($entrevista->solicitud->descripcion, 40) }}</td>
                    <td class="text-end">
                      <a href="{{ route('entrevistas.show', $entrevista) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center text-muted py-4">Aun no registras entrevistas.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="d-flex justify-content-end">
            {{ $entrevistas->links() }}
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-5">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h5 class="card-title mb-0">Historial de estudiantes</h5>
              <small class="text-muted">Cuantas veces has atendido a cada estudiante</small>
            </div>
          </div>
          @forelse($historialEstudiantes as $registro)
            <div class="timeline-item">
              <div>
                <strong>{{ $registro['estudiante']->nombre ?? 'Sin nombre' }} {{ $registro['estudiante']->apellido ?? '' }}</strong>
                <p class="text-muted mb-1 small">{{ $registro['estudiante']->carrera->nombre ?? 'Sin carrera' }}</p>
                <p class="text-muted mb-0 small">Ultima: {{ $registro['ultima']?->fecha?->format('d/m/Y') ?? $registro['ultima']?->fecha_hora_inicio?->format('d/m/Y H:i') ?? 's/f' }}</p>
              </div>
              <span class="badge bg-danger-subtle text-danger">{{ $registro['total'] }} entrevista(s)</span>
            </div>
          @empty
            <p class="text-muted mb-0">Aun no hay historial de estudiantes.</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
