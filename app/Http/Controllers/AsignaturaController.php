<?php

namespace App\Http\Controllers;

use App\Models\Asignatura;
use App\Models\Carrera;
use App\Models\Docente;
use Illuminate\Http\Request;

class AsignaturaController extends Controller
{
    public function index()
    {
        $asignaturas = Asignatura::with(['carrera', 'docente'])->orderBy('nombre')->get();

        return view('asignaturas.index', compact('asignaturas'));
    }

    public function create()
    {
        $carreras = Carrera::orderBy('nombre')->get();
        $docentes = Docente::orderBy('nombre')->orderBy('apellido')->get();

        return view('asignaturas.create', compact('carreras', 'docentes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'tipo' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'string', 'max:255'],
            'carrera_id' => ['required', 'exists:carreras,id'],
            'docente_id' => ['nullable', 'exists:docentes,id'],
        ]);

        Asignatura::create($validated);

        return redirect()->route('asignaturas.index')->with('success', 'Asignatura creada correctamente.');
    }

    public function show(Asignatura $asignatura)
    {
        $asignatura->load(['carrera', 'docente']);

        return view('asignaturas.show', compact('asignatura'));
    }

    public function edit(Asignatura $asignatura)
    {
        $carreras = Carrera::orderBy('nombre')->get();
        $docentes = Docente::orderBy('nombre')->orderBy('apellido')->get();

        return view('asignaturas.edit', compact('asignatura', 'carreras', 'docentes'));
    }

    public function update(Request $request, Asignatura $asignatura)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'tipo' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'string', 'max:255'],
            'carrera_id' => ['required', 'exists:carreras,id'],
            'docente_id' => ['nullable', 'exists:docentes,id'],
        ]);

        $asignatura->update($validated);

        return redirect()->route('asignaturas.index')->with('success', 'Asignatura actualizada correctamente.');
    }

    public function destroy(Asignatura $asignatura)
    {
        $asignatura->delete();

        return redirect()->route('asignaturas.index')->with('success', 'Asignatura eliminada correctamente.');
    }
}
