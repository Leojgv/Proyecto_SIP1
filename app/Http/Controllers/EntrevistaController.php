<?php

namespace App\Http\Controllers;

use App\Models\AsesorPedagogico;
use App\Models\Entrevista;
use App\Models\Solicitud;
use Illuminate\Http\Request;

class EntrevistaController extends Controller
{
    public function index()
    {
        $entrevistas = Entrevista::with(['solicitud.estudiante', 'asesorPedagogico'])
            ->orderByDesc('fecha')
            ->get();

        return view('entrevistas.index', compact('entrevistas'));
    }

    public function create()
    {
        $solicitudes = Solicitud::with('estudiante')->orderByDesc('fecha_solicitud')->get();
        $asesores = AsesorPedagogico::orderBy('nombre')->orderBy('apellido')->get();

        return view('entrevistas.create', compact('solicitudes', 'asesores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha' => ['required', 'date'],
            'observaciones' => ['nullable', 'string'],
            'solicitud_id' => ['required', 'exists:solicitudes,id'],
            'asesor_pedagogico_id' => ['required', 'exists:asesor_pedagogicos,id'],
        ]);

        Entrevista::create($validated);

        return redirect()->route('entrevistas.index')->with('success', 'Entrevista creada correctamente.');
    }

    public function show(Entrevista $entrevista)
    {
        $entrevista->load(['solicitud.estudiante', 'asesorPedagogico']);

        return view('entrevistas.show', compact('entrevista'));
    }

    public function edit(Entrevista $entrevista)
    {
        $solicitudes = Solicitud::with('estudiante')->orderByDesc('fecha_solicitud')->get();
        $asesores = AsesorPedagogico::orderBy('nombre')->orderBy('apellido')->get();

        return view('entrevistas.edit', compact('entrevista', 'solicitudes', 'asesores'));
    }

    public function update(Request $request, Entrevista $entrevista)
    {
        $validated = $request->validate([
            'fecha' => ['required', 'date'],
            'observaciones' => ['nullable', 'string'],
            'solicitud_id' => ['required', 'exists:solicitudes,id'],
            'asesor_pedagogico_id' => ['required', 'exists:asesor_pedagogicos,id'],
        ]);

        $entrevista->update($validated);

        return redirect()->route('entrevistas.index')->with('success', 'Entrevista actualizada correctamente.');
    }

    public function destroy(Entrevista $entrevista)
    {
        $entrevista->delete();

        return redirect()->route('entrevistas.index')->with('success', 'Entrevista eliminada correctamente.');
    }
}
