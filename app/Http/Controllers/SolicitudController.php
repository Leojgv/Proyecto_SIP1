<?php

namespace App\Http\Controllers;

use App\Models\AsesorPedagogico;
use App\Models\DirectorCarrera;
use App\Models\Estudiante;
use App\Models\Solicitud;
use Illuminate\Http\Request;

class SolicitudController extends Controller
{
    public function index()
    {
        $solicitudes = Solicitud::with(['estudiante', 'asesorPedagogico', 'directorCarrera'])
            ->orderByDesc('fecha_solicitud')
            ->get();

        return view('solicitudes.index', compact('solicitudes'));
    }

    public function create()
    {
        $estudiantes = Estudiante::orderBy('nombre')->orderBy('apellido')->get();
        $asesores = AsesorPedagogico::orderBy('nombre')->orderBy('apellido')->get();
        $directores = DirectorCarrera::orderBy('nombre')->orderBy('apellido')->get();

        return view('solicitudes.create', compact('estudiantes', 'asesores', 'directores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha_solicitud' => ['required', 'date'],
            'descripcion' => ['nullable', 'string'],
            'estado' => ['nullable', 'string', 'max:255'],
            'estudiante_id' => ['required', 'exists:estudiantes,id'],
            'asesor_pedagogico_id' => ['required', 'exists:asesor_pedagogicos,id'],
            'director_carrera_id' => ['required', 'exists:director_carreras,id'],
        ]);

        Solicitud::create($validated);

        return redirect()->route('solicitudes.index')->with('success', 'Solicitud creada correctamente.');
    }

    public function show(Solicitud $solicitud)
    {
        $solicitud->load(['estudiante', 'asesorPedagogico', 'directorCarrera']);

        return view('solicitudes.show', compact('solicitud'));
    }

    public function edit(Solicitud $solicitud)
    {
        $estudiantes = Estudiante::orderBy('nombre')->orderBy('apellido')->get();
        $asesores = AsesorPedagogico::orderBy('nombre')->orderBy('apellido')->get();
        $directores = DirectorCarrera::orderBy('nombre')->orderBy('apellido')->get();

        return view('solicitudes.edit', compact('solicitud', 'estudiantes', 'asesores', 'directores'));
    }

    public function update(Request $request, Solicitud $solicitud)
    {
        $validated = $request->validate([
            'fecha_solicitud' => ['required', 'date'],
            'descripcion' => ['nullable', 'string'],
            'estado' => ['nullable', 'string', 'max:255'],
            'estudiante_id' => ['required', 'exists:estudiantes,id'],
            'asesor_pedagogico_id' => ['required', 'exists:asesor_pedagogicos,id'],
            'director_carrera_id' => ['required', 'exists:director_carreras,id'],
        ]);

        $solicitud->update($validated);

        return redirect()->route('solicitudes.index')->with('success', 'Solicitud actualizada correctamente.');
    }

    public function destroy(Solicitud $solicitud)
    {
        $solicitud->delete();

        return redirect()->route('solicitudes.index')->with('success', 'Solicitud eliminada correctamente.');
    }
}
