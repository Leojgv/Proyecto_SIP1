@extends('layouts.app')

@section('title', 'Carreras')

@section('content')
<div class="container-fluid">
  @include('partials.alerts')

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Carreras</h1>
    <a href="{{ route('carreras.create') }}" class="btn btn-primary">
      <i class="fas fa-plus"></i> Nueva carrera
    </a>
  </div>

  <div class="card">
    <div class="card-body table-responsive p-0">
      <table class="table table-striped mb-0">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Jornada</th>
            <th>Grado</th>
          </tr>
        </thead>
        <tbody>
          @forelse($carreras as $carrera)
            <tr>
              <td>{{ $carrera->nombre }}</td>
              <td>{{ $carrera->jornada ?? '�"' }}</td>
              <td>{{ $carrera->grado ?? '�"' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="text-center py-4">No hay carreras registradas.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
