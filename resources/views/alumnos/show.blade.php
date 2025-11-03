@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalles del Alumno</h1>

    <table class="table table-bordered">
        <tr>
            <th>RUT</th>
            <td>{{ $alumno->rut }}</td>
        </tr>
        <tr>
            <th>Nombre</th>
            <td>{{ $alumno->nombre }}</td>
        </tr>
        <tr>
            <th>Apellido</th>
            <td>{{ $alumno->apellido }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $alumno->email }}</td>
        </tr>
        <tr>
            <th>Teléfono</th>
            <td>{{ $alumno->telefono }}</td>
        </tr>
    </table>

    <a href="{{ route('alumnos.edit', $alumno) }}" class="btn btn-warning">Editar</a>
    <a href="{{ route('alumnos.index') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection
