@extends('layouts.dashboard_asesoratecnica.app')

@section('title', 'Casos')

@section('content')
<div class="dashboard-page">
  <div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h1 class="h4 mb-1">Casos asignados</h1>
      <p class="text-muted mb-0">Casos que requieren ajustes razonables.</p>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Estudiante</th>
              <th>Programa</th>
              <th>Fecha solicitud</th>
              <th>Estado</th>
              <th>Descripcion</th>
            </tr>
          </thead>
          <tbody>
            @forelse($solicitudes as $solicitud)
              <tr>
                <td>{{ $solicitud->estudiante->nombre ?? 'Estudiante' }} {{ $solicitud->estudiante->apellido ?? '' }}</td>
                <td>{{ $solicitud->estudiante->carrera->nombre ?? 'Sin carrera' }}</td>
                <td>{{ $solicitud->fecha_solicitud?->format('d/m/Y') ?? 's/f' }}</td>
                <td><span class="badge bg-warning text-dark">{{ $solicitud->estado ?? 'Pendiente' }}</span></td>
                <td class="text-muted small">{{ \Illuminate\Support\Str::limit($solicitud->descripcion, 60) }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-4">No tienes casos asignados actualmente.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-end mt-3">
        {{ $solicitudes->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
