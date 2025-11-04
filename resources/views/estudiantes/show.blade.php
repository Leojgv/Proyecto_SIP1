@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalles del Estudiante</h1>

    <table class="table table-bordered">
        <tr>
            <th>RUT</th>
            <td>{{ $estudiante->rut }}</td>
        </tr>
        <tr>
            <th>Nombre</th>
            <td>{{ $estudiante->nombre }}</td>
        </tr>
        <tr>
            <th>Apellido</th>
            <td>{{ $estudiante->apellido }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $estudiante->email }}</td>
        </tr>
        <tr>
            <th>Teléfono</th>
            <td>{{ $estudiante->telefono }}</td>
        </tr>
    </table>

    <a href="{{ route('estudiantes.edit', $estudiante) }}" class="btn btn-warning">Editar</a>
    <a href="{{ route('estudiantes.index') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection
