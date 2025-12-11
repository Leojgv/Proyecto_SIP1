<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Estudiante;
use Illuminate\Http\Request;

class EstudianteController extends Controller
{
    public function index()
    {
        $estudiantes = Estudiante::with('carrera')->orderBy('nombre')->orderBy('apellido')->paginate(10);
        $carreras = Carrera::orderBy('nombre')->get();
        
        $totalEstudiantes = Estudiante::count();
        $nuevosEsteMes = Estudiante::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $estudiantesPorCarrera = Estudiante::with('carrera')
            ->get()
            ->groupBy('carrera_id')
            ->map(function ($group) {
                return [
                    'nombre' => $group->first()->carrera->nombre ?? 'Sin carrera',
                    'cantidad' => $group->count()
                ];
            })
            ->values();

        return view('estudiantes.index', compact(
            'estudiantes',
            'carreras',
            'totalEstudiantes',
            'nuevosEsteMes',
            'estudiantesPorCarrera'
        ));
    }

    public function create()
    {
        $carreras = Carrera::orderBy('nombre')->get();

        return view('estudiantes.create', compact('carreras'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rut' => ['required', 'string', 'max:20', 'unique:estudiantes,rut'],
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:estudiantes,email'],
            'telefono' => ['nullable', 'string', 'max:255'],
            'carrera_id' => ['required', 'exists:carreras,id'],
        ]);

        Estudiante::create($validated);

        return redirect()->route('estudiantes.index')->with('success', 'Estudiante creado correctamente.');
    }

    public function show(Estudiante $estudiante)
    {
        $estudiante->load('carrera');

        return view('estudiantes.show', compact('estudiante'));
    }

    public function edit(Estudiante $estudiante)
    {
        $carreras = Carrera::orderBy('nombre')->get();

        return view('estudiantes.edit', compact('estudiante', 'carreras'));
    }

    public function update(Request $request, Estudiante $estudiante)
    {
        $validated = $request->validate([
            'rut' => ['required', 'string', 'max:20', 'unique:estudiantes,rut,' . $estudiante->id],
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:estudiantes,email,' . $estudiante->id],
            'telefono' => ['nullable', 'string', 'max:255'],
            'carrera_id' => ['required', 'exists:carreras,id'],
        ]);

        $estudiante->update($validated);

        return redirect()->route('estudiantes.index')->with('success', 'Estudiante actualizado correctamente.');
    }

    public function destroy(Estudiante $estudiante)
    {
        $estudiante->delete();

        return redirect()->route('estudiantes.index')->with('success', 'Estudiante eliminado correctamente.');
    }
}
