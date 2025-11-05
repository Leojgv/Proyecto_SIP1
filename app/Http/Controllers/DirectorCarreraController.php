<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\DirectorCarrera;
use Illuminate\Http\Request;

class DirectorCarreraController extends Controller
{
    public function index()
    {
        $directores = DirectorCarrera::with('carrera')->orderBy('nombre')->orderBy('apellido')->get();

        return view('directores_carrera.index', compact('directores'));
    }

    public function create()
    {
        $carreras = Carrera::orderBy('nombre')->get();

        return view('directores_carrera.create', compact('carreras'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:director_carreras,email'],
            'telefono' => ['nullable', 'string', 'max:255'],
            'carrera_id' => ['required', 'exists:carreras,id'],
        ]);

        DirectorCarrera::create($validated);

        return redirect()->route('directores-carrera.index')->with('success', 'Director de carrera creado correctamente.');
    }

    public function show(DirectorCarrera $directores_carrera)
    {
        $directores_carrera->load('carrera');

        return view('directores_carrera.show', ['director' => $directores_carrera]);
    }

    public function edit(DirectorCarrera $directores_carrera)
    {
        $carreras = Carrera::orderBy('nombre')->get();

        return view('directores_carrera.edit', ['director' => $directores_carrera, 'carreras' => $carreras]);
    }

    public function update(Request $request, DirectorCarrera $directores_carrera)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:director_carreras,email,' . $directores_carrera->id],
            'telefono' => ['nullable', 'string', 'max:255'],
            'carrera_id' => ['required', 'exists:carreras,id'],
        ]);

        $directores_carrera->update($validated);

        return redirect()->route('directores-carrera.index')->with('success', 'Director de carrera actualizado correctamente.');
    }

    public function destroy(DirectorCarrera $directores_carrera)
    {
        $directores_carrera->delete();

        return redirect()->route('directores-carrera.index')->with('success', 'Director de carrera eliminado correctamente.');
    }
}
