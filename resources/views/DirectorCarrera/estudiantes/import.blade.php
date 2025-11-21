@extends('layouts.Dashboard_director.app')

@section('title', 'Cargar Estudiantes desde Excel')

@section('content')
<div class="dashboard-page">
  <div class="page-header mb-4">
    <div>
      <p class="text-danger text-uppercase fw-semibold small mb-1">Gestión de estudiantes</p>
      <h1 class="h4 mb-1">Carga de Estudiantes</h1>
      <p class="text-muted mb-0">Importa múltiples estudiantes desde un archivo Excel.</p>
    </div>
  </div>

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

  @if(session('import_errors'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
      <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Errores en la importación</h6>
      <ul class="mb-0">
        @foreach(session('import_errors') as $failure)
          <li>Fila {{ $failure->row() }}: {{ $failure->errors()[0] ?? 'Error desconocido' }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="row">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
          <h5 class="card-title mb-3">Formulario de Carga</h5>
          
          <form action="{{ route('director.estudiantes.import') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
              <label for="archivo" class="form-label">
                Archivo Excel <span class="text-danger">*</span>
              </label>
              <input 
                type="file" 
                class="form-control @error('archivo') is-invalid @enderror" 
                id="archivo" 
                name="archivo" 
                accept=".xlsx,.xls"
                required
              >
              @error('archivo')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="form-text text-muted">
                Formatos permitidos: .xlsx, .xls (máximo 10MB)
              </small>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-danger">
                <i class="fas fa-upload me-2"></i>Cargar Estudiantes
              </button>
              <a href="{{ route('director.estudiantes') }}" class="btn btn-outline-secondary">
                <i class="fas fa-times me-2"></i>Cancelar
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h6 class="card-title mb-3">
            <i class="fas fa-info-circle text-danger me-2"></i>Instrucciones
          </h6>
          
          <div class="mb-3">
            <strong class="d-block mb-2">Formato del Excel:</strong>
            <p class="small text-muted mb-2">
              El archivo debe tener una fila de encabezados en la primera fila con las siguientes columnas:
            </p>
            <ul class="small mb-0">
              <li><strong>nombre</strong> o <strong>name</strong> (requerido)</li>
              <li><strong>apellido</strong> o <strong>lastname</strong> (requerido)</li>
              <li><strong>email</strong> o <strong>correo</strong> (requerido)</li>
              <li><strong>rut</strong> (opcional)</li>
              <li><strong>telefono</strong> o <strong>phone</strong> (opcional)</li>
            </ul>
          </div>

          <div class="mb-3">
            <strong class="d-block mb-2">Notas importantes:</strong>
            <ul class="small mb-0">
              <li>Los estudiantes se asignarán automáticamente a tu carrera.</li>
              <li>Si un estudiante ya existe (por email), se actualizará su información.</li>
              <li>Se creará un usuario con contraseña por defecto: <code>password123</code></li>
              <li>Se asignará automáticamente el rol "Estudiante".</li>
            </ul>
          </div>

          <div class="alert alert-info small mb-0">
            <strong>Ejemplo de estructura:</strong><br>
            <table class="table table-sm table-bordered mt-2 mb-0">
              <thead>
                <tr>
                  <th>nombre</th>
                  <th>apellido</th>
                  <th>email</th>
                  <th>rut</th>
                  <th>telefono</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Juan</td>
                  <td>Pérez</td>
                  <td>juan.perez@email.com</td>
                  <td>12345678-9</td>
                  <td>912345678</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

