<?php

namespace App\Http\Controllers;

use App\Models\Entrevista;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Http\Request;

class EntrevistaController extends Controller
{
    public function index()
    {
        $entrevistas = Entrevista::with(['solicitud.estudiante', 'asesor'])
            ->orderByDesc('fecha')
            ->get();

        return view('entrevistas.index', compact('entrevistas'));
    }

    public function create()
    {
        $solicitudes = Solicitud::with('estudiante')->orderByDesc('fecha_solicitud')->get();
        $asesores = $this->asesores();

        return view('entrevistas.create', compact('solicitudes', 'asesores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha' => ['required', 'date'],
            'observaciones' => ['nullable', 'string'],
            'solicitud_id' => ['required', 'exists:solicitudes,id'],
            'asesor_id' => ['required', 'exists:users,id'],
        ]);

        Entrevista::create($validated);

        return redirect()->route('entrevistas.index')->with('success', 'Entrevista creada correctamente.');
    }

    public function show(Entrevista $entrevista)
    {
        $entrevista->load(['solicitud.estudiante', 'asesor']);

        return view('entrevistas.show', compact('entrevista'));
    }

    public function edit(Entrevista $entrevista)
    {
        $solicitudes = Solicitud::with('estudiante')->orderByDesc('fecha_solicitud')->get();
        $asesores = $this->asesores();

        return view('entrevistas.edit', compact('entrevista', 'solicitudes', 'asesores'));
    }

    public function update(Request $request, Entrevista $entrevista)
    {
        $validated = $request->validate([
            'fecha' => ['required', 'date'],
            'observaciones' => ['nullable', 'string'],
            'solicitud_id' => ['required', 'exists:solicitudes,id'],
            'asesor_id' => ['required', 'exists:users,id'],
        ]);

        $entrevista->update($validated);

        return redirect()->route('entrevistas.index')->with('success', 'Entrevista actualizada correctamente.');
    }

    public function destroy(Entrevista $entrevista)
    {
        $entrevista->delete();

        return redirect()->route('entrevistas.index')->with('success', 'Entrevista eliminada correctamente.');
    }

    private function asesores()
    {
        return User::withRole('Asesor')
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->get();
    }
}
