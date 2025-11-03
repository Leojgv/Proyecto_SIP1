@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Alumno</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('alumnos.update', $alumno) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>RUT</label>
            <input type="text" name="rut" class="form-control" value="{{ old('rut', $alumno->rut) }}" required>
        </div>
        <div class="mb-3">
            <label>Nombre</label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $alumno->nombre) }}" required>
        </div>
        <div class="mb-3">
            <label>Apellido</label>
            <input type="text" name="apellido" class="form-control" value="{{ old('apellido', $alumno->apellido) }}" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $alumno->email) }}" required>
        </div>
        <div class="mb-3">
            <label>Teléfono</label>
            <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $alumno->telefono) }}">
        </div>
        <button type="submit" class="btn btn-success">Actualizar</button>
        <a href="{{ route('alumnos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
