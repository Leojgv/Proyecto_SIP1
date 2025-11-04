<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use Illuminate\Http\Request;

class EstudianteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $estudiantes = Estudiante::all();
        return view('estudiantes.index', compact('estudiantes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('estudiantes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $request->validate([
            'rut' => 'required|unique:estudiantes',
            'nombre' => 'required',
            'apellido' => 'required',
            'email' => 'required|email|unique:estudiantes',
            'telefono' => 'nullable',
        ]);

        Estudiante::create($request->all());
        return redirect()->route('estudiantes.index')->with('success', 'Estudiante creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(estudiante $estudiante)
    {
       return view('estudiantes.show', compact('estudiante'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(estudiante $estudiante)
    {
       return view('estudiantes.edit', compact('estudiante'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, estudiante $estudiante)
    {
       $request->validate([
            'rut' => 'required|unique:estudiantes,rut,' . $estudiante->id,
            'nombre' => 'required',
            'apellido' => 'required',
            'email' => 'required|email|unique:estudiantes,email,' . $estudiante->id,
            'telefono' => 'nullable',
        ]);

        $estudiante->update($request->all());
        return redirect()->route('estudiantes.index')->with('success', 'Estudiante actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(estudiante $estudiante)
    {
        $estudiante->delete();
        return redirect()->route('estudiantes.index')->with('success', 'Estudiante eliminado correctamente.');

    }
}
