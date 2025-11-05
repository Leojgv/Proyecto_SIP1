<?php

namespace App\Http\Controllers;

use App\Models\Asignatura;
use App\Models\Carrera;
use App\Models\Docente;
use App\Models\DocenteAsignatura;
use Illuminate\Http\Request;

class DocenteAsignaturaController extends Controller
{
    public function index()
    {
        $asignaciones = DocenteAsignatura::with(['docente', 'asignatura', 'carrera'])->get();

        return view('docente_asignaturas.index', compact('asignaciones'));
    }

    public function create()
    {
        $docentes = Docente::orderBy('nombre')->orderBy('apellido')->get();
        $asignaturas = Asignatura::orderBy('nombre')->get();
        $carreras = Carrera::orderBy('nombre')->get();

        return view('docente_asignaturas.create', compact('docentes', 'asignaturas', 'carreras'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'docente_id' => ['required', 'exists:docentes,id'],
            'asignatura_id' => ['required', 'exists:asignaturas,id'],
            'carrera_id' => ['required', 'exists:carreras,id'],
        ]);

        DocenteAsignatura::create($validated);

        return redirect()->route('docente-asignaturas.index')->with('success', 'Asignación creada correctamente.');
    }

    public function show(DocenteAsignatura $docente_asignatura)
    {
        $docente_asignatura->load(['docente', 'asignatura', 'carrera']);

        return view('docente_asignaturas.show', ['asignacion' => $docente_asignatura]);
    }

    public function edit(DocenteAsignatura $docente_asignatura)
    {
        $docentes = Docente::orderBy('nombre')->orderBy('apellido')->get();
        $asignaturas = Asignatura::orderBy('nombre')->get();
        $carreras = Carrera::orderBy('nombre')->get();

        return view('docente_asignaturas.edit', [
            'asignacion' => $docente_asignatura,
            'docentes' => $docentes,
            'asignaturas' => $asignaturas,
            'carreras' => $carreras,
        ]);
    }

    public function update(Request $request, DocenteAsignatura $docente_asignatura)
    {
        $validated = $request->validate([
            'docente_id' => ['required', 'exists:docentes,id'],
            'asignatura_id' => ['required', 'exists:asignaturas,id'],
            'carrera_id' => ['required', 'exists:carreras,id'],
        ]);

        $docente_asignatura->update($validated);

        return redirect()->route('docente-asignaturas.index')->with('success', 'Asignación actualizada correctamente.');
    }

    public function destroy(DocenteAsignatura $docente_asignatura)
    {
        $docente_asignatura->delete();

        return redirect()->route('docente-asignaturas.index')->with('success', 'Asignación eliminada correctamente.');
    }
}
