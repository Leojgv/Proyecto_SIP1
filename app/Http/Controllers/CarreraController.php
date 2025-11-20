<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\User;
use Illuminate\Http\Request;

class CarreraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $carreras = Carrera::orderBy('nombre')->get();

        return view('carreras.index', compact('carreras'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Obtener solo usuarios con el rol "Director de carrera"
        $directores = User::where(function ($query) {
            $query->whereHas('roles', function ($q) {
                $q->where('nombre', 'Director de carrera');
            })->orWhereHas('rol', function ($q) {
                $q->where('nombre', 'Director de carrera');
            });
        })->orderBy('nombre')->orderBy('apellido')->get();

        return view('carreras.create', compact('directores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'jornada' => ['nullable', 'string', 'max:255'],
            'grado' => ['nullable', 'string', 'max:255'],
            'director_id' => ['nullable', 'exists:users,id'],
        ]);

        // Validar que el director tenga el rol correcto si se proporciona
        if (isset($validated['director_id'])) {
            $director = User::with(['rol', 'roles'])->find($validated['director_id']);
            $tieneRol = collect([$director->rol?->nombre])
                ->merge($director->roles->pluck('nombre') ?? [])
                ->map(fn ($rol) => mb_strtolower($rol ?? ''))
                ->contains(mb_strtolower('Director de carrera'));

            if (!$tieneRol) {
                return back()
                    ->withErrors(['director_id' => 'El usuario seleccionado debe tener el rol "Director de carrera".'])
                    ->withInput();
            }
        }

        Carrera::create($validated);

        return redirect()->route('carreras.index')->with('success', 'Carrera creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Carrera $carrera)
    {
        return view('carreras.show', compact('carrera'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Carrera $carrera)
    {
        // Obtener solo usuarios con el rol "Director de carrera"
        $directores = User::where(function ($query) {
            $query->whereHas('roles', function ($q) {
                $q->where('nombre', 'Director de carrera');
            })->orWhereHas('rol', function ($q) {
                $q->where('nombre', 'Director de carrera');
            });
        })->orderBy('nombre')->orderBy('apellido')->get();

        return view('carreras.edit', compact('carrera', 'directores'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Carrera $carrera)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'jornada' => ['nullable', 'string', 'max:255'],
            'grado' => ['nullable', 'string', 'max:255'],
            'director_id' => ['nullable', 'exists:users,id'],
        ]);

        // Validar que el director tenga el rol correcto si se proporciona
        if (isset($validated['director_id'])) {
            $director = User::with(['rol', 'roles'])->find($validated['director_id']);
            $tieneRol = collect([$director->rol?->nombre])
                ->merge($director->roles->pluck('nombre') ?? [])
                ->map(fn ($rol) => mb_strtolower($rol ?? ''))
                ->contains(mb_strtolower('Director de carrera'));

            if (!$tieneRol) {
                return back()
                    ->withErrors(['director_id' => 'El usuario seleccionado debe tener el rol "Director de carrera".'])
                    ->withInput();
            }
        }

        $carrera->update($validated);

        return redirect()->route('carreras.index')->with('success', 'Carrera actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Carrera $carrera)
    {
        $carrera->delete();

        return redirect()->route('carreras.index')->with('success', 'Carrera eliminada correctamente.');
    }
}
