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

        return view('solicitudes.create', compact('estudiantes', 'asesores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha_solicitud' => ['required', 'date'],
            'descripcion' => ['nullable', 'string'],
            'estudiante_id' => ['required', 'exists:estudiantes,id'],
            'asesor_id' => ['required', 'exists:users,id'],
        ]);

        // Obtener el estudiante para determinar su carrera y director
        $estudiante = Estudiante::with('carrera')->findOrFail($validated['estudiante_id']);
        $directorId = $estudiante->carrera?->director_id;

        if (!$directorId) {
            return back()
                ->withErrors(['estudiante_id' => 'El estudiante no tiene una carrera asignada o la carrera no tiene un director asignado.'])
                ->withInput();
        }

        $solicitud = Solicitud::create($validated + [
            'estado' => 'Pendiente de entrevista',
            'director_id' => $directorId,
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

        return view('solicitudes.edit', compact('solicitud', 'estudiantes', 'asesores'));
    }

    public function update(Request $request, Solicitud $solicitud)
    {
        $validated = $request->validate([
            'fecha_solicitud' => ['required', 'date'],
            'descripcion' => ['nullable', 'string'],
            'estudiante_id' => ['required', 'exists:estudiantes,id'],
            'asesor_id' => ['required', 'exists:users,id'],
        ]);

        // Obtener el director automáticamente según la carrera del estudiante
        $estudiante = Estudiante::with('carrera')->findOrFail($validated['estudiante_id']);
        $directorId = $estudiante->carrera?->director_id;

        if (!$directorId) {
            return back()
                ->withErrors(['estudiante_id' => 'El estudiante no tiene una carrera asignada o la carrera no tiene un director asignado.'])
                ->withInput();
        }

        // El estado NO se puede actualizar manualmente, se gestiona automáticamente
        // El director se asigna automáticamente según la carrera del estudiante
        $solicitud->update($validated + [
            'director_id' => $directorId,
        ]);

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

