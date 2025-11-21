@extends('layouts.Dashboard_director.app')

@section('title', 'Estudiantes')

@section('content')
<div class="dashboard-page">
  @if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="page-header mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h1 class="h4 mb-1">Estudiantes de tu carrera</h1>
      <p class="text-muted mb-0">Listado de estudiantes asociados a las carreras que diriges.</p>
    </div>
    <a href="{{ route('director.estudiantes.import.form') }}" class="btn btn-danger">
      <i class="fas fa-file-excel me-2"></i>Cargar desde Excel
    </a>
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
              <th>Teléfono</th>
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
                <td colspan="5" class="text-center text-muted py-4">No hay estudiantes registrados en tus carreras.</td>
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
