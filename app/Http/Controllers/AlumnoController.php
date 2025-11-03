<?php

namespace App\Http\Controllers;

use App\Models\alumno;
use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $alumnos = Alumno::all();
        return view('alumnos.index', compact('alumnos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('alumnos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $request->validate([
            'rut' => 'required|unique:alumnos',
            'nombre' => 'required',
            'apellido' => 'required',
            'email' => 'required|email|unique:alumnos',
            'telefono' => 'nullable',
        ]);

        Alumno::create($request->all());
        return redirect()->route('alumnos.index')->with('success', 'Alumno creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(alumno $alumno)
    {
       return view('alumnos.show', compact('alumno'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(alumno $alumno)
    {
       return view('alumnos.edit', compact('alumno'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, alumno $alumno)
    {
       $request->validate([
            'rut' => 'required|unique:alumnos,rut,' . $alumno->id,
            'nombre' => 'required',
            'apellido' => 'required',
            'email' => 'required|email|unique:alumnos,email,' . $alumno->id,
            'telefono' => 'nullable',
        ]);

        $alumno->update($request->all());
        return redirect()->route('alumnos.index')->with('success', 'Alumno actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(alumno $alumno)
    {
        $alumno->delete();
        return redirect()->route('alumnos.index')->with('success', 'Alumno eliminado correctamente.');
   
    }
}
