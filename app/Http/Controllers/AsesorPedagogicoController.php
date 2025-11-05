<?php

namespace App\Http\Controllers;

use App\Models\AsesorPedagogico;
use Illuminate\Http\Request;

class AsesorPedagogicoController extends Controller
{
    public function index()
    {
        $asesores = AsesorPedagogico::orderBy('nombre')->orderBy('apellido')->get();

        return view('asesores_pedagogicos.index', compact('asesores'));
    }

    public function create()
    {
        return view('asesores_pedagogicos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:asesor_pedagogicos,email'],
            'telefono' => ['nullable', 'string', 'max:255'],
        ]);

        AsesorPedagogico::create($validated);

        return redirect()->route('asesores-pedagogicos.index')->with('success', 'Asesor pedagógico creado correctamente.');
    }

    public function show(AsesorPedagogico $asesores_pedagogico)
    {
        return view('asesores_pedagogicos.show', ['asesor' => $asesores_pedagogico]);
    }

    public function edit(AsesorPedagogico $asesores_pedagogico)
    {
        return view('asesores_pedagogicos.edit', ['asesor' => $asesores_pedagogico]);
    }

    public function update(Request $request, AsesorPedagogico $asesores_pedagogico)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:asesor_pedagogicos,email,' . $asesores_pedagogico->id],
            'telefono' => ['nullable', 'string', 'max:255'],
        ]);

        $asesores_pedagogico->update($validated);

        return redirect()->route('asesores-pedagogicos.index')->with('success', 'Asesor pedagógico actualizado correctamente.');
    }

    public function destroy(AsesorPedagogico $asesores_pedagogico)
    {
        $asesores_pedagogico->delete();

        return redirect()->route('asesores-pedagogicos.index')->with('success', 'Asesor pedagógico eliminado correctamente.');
    }
}
