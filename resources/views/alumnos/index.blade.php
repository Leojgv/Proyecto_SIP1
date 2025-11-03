@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Lista de Alumnos</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('alumnos.create') }}" class="btn btn-primary mb-3">Nuevo Alumno</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>RUT</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($alumnos as $alumno)
                <tr>
                    <td>{{ $alumno->rut }}</td>
                    <td>{{ $alumno->nombre }}</td>
                    <td>{{ $alumno->apellido }}</td>
                    <td>{{ $alumno->email }}</td>
                    <td>{{ $alumno->telefono }}</td>
                    <td>
                        <a href="{{ route('alumnos.show', $alumno) }}" class="btn btn-info btn-sm">Ver</a>
                        <a href="{{ route('alumnos.edit', $alumno) }}" class="btn btn-warning btn-sm">Editar</a>
                        <form action="{{ route('alumnos.destroy', $alumno) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este alumno?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
