@extends('layouts.dashboard_asesoratecnica.app')

@section('title', 'Estudiantes')

@section('content')
<div class="dashboard-page">
  <div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h1 class="h4 mb-1">Estudiantes</h1>
      <p class="text-muted mb-0">Visor rapido de estudiantes y sus carreras.</p>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>RUT</th>
              <th>Correo</th>
              <th>Telefono</th>
              <th>Carrera</th>
            </tr>
          </thead>
          <tbody>
            @forelse($estudiantes as $estudiante)
              <tr>
                <td>{{ $estudiante->nombre }} {{ $estudiante->apellido }}</td>
                <td>{{ $estudiante->rut }}</td>
                <td>{{ $estudiante->email }}</td>
                <td>{{ $estudiante->telefono }}</td>
                <td>{{ $estudiante->carrera->nombre ?? 'Sin carrera' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-4">No hay estudiantes registrados.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-end mt-3">
        {{ $estudiantes->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
