<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Http\Request;

class SolicitudController extends Controller
{
    public function index()
    {
        $solicitudes = Solicitud::with(['estudiante', 'asesor', 'director'])
            ->orderByDesc('fecha_solicitud')
            ->get();

        return view('solicitudes.index', compact('solicitudes'));
    }

    public function create()
    {
        $estudiantes = Estudiante::orderBy('nombre')->orderBy('apellido')->get();
        $asesores = $this->usuariosPorRol('Asesora Pedagogica');
        $directores = $this->usuariosPorRol('Director de carrera');

        return view('solicitudes.create', compact('estudiantes', 'asesores', 'directores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha_solicitud' => ['required', 'date'],
            'descripcion' => ['nullable', 'string'],
            'estudiante_id' => ['required', 'exists:estudiantes,id'],
            'asesor_id' => ['required', 'exists:users,id'],
            'director_id' => ['required', 'exists:users,id'],
        ]);

        Solicitud::create($validated + [
            'estado' => 'Pendiente de entrevista',
        ]);

        return redirect()->route('solicitudes.index')->with('success', 'Solicitud creada correctamente.');
    }

    public function show(Solicitud $solicitud)
    {
        $solicitud->load(['estudiante', 'asesor', 'director']);

        return view('solicitudes.show', compact('solicitud'));
    }

    public function edit(Solicitud $solicitud)
    {
        $estudiantes = Estudiante::orderBy('nombre')->orderBy('apellido')->get();
        $asesores = $this->usuariosPorRol('Asesora Pedagogica');
        $directores = $this->usuariosPorRol('Director de carrera');

        return view('solicitudes.edit', compact('solicitud', 'estudiantes', 'asesores', 'directores'));
    }

    public function update(Request $request, Solicitud $solicitud)
    {
        $validated = $request->validate([
            'fecha_solicitud' => ['required', 'date'],
            'descripcion' => ['nullable', 'string'],
            'estudiante_id' => ['required', 'exists:estudiantes,id'],
            'asesor_id' => ['required', 'exists:users,id'],
            'director_id' => ['required', 'exists:users,id'],
        ]);

        $solicitud->update($validated);

        return redirect()->route('solicitudes.index')->with('success', 'Solicitud actualizada correctamente.');
    }

    public function destroy(Solicitud $solicitud)
    {
        $solicitud->delete();

        return redirect()->route('solicitudes.index')->with('success', 'Solicitud eliminada correctamente.');
    }

    private function usuariosPorRol(string $rol)
    {
        return User::withRole($rol)
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->get();
    }
}

