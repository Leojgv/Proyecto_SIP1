<?php

namespace App\Http\Controllers;

use App\Models\Evidencia;
use App\Models\Solicitud;
use Illuminate\Http\Request;

class EvidenciaController extends Controller
{
    public function index()
    {
        $evidencias = Evidencia::with('solicitud.estudiante')->orderByDesc('created_at')->get();

        return view('evidencias.index', compact('evidencias'));
    }

    public function create()
    {
        $solicitudes = Solicitud::with('estudiante')->orderByDesc('fecha_solicitud')->get();

        return view('evidencias.create', compact('solicitudes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'ruta_archivo' => ['nullable', 'string', 'max:255'],
            'solicitud_id' => ['required', 'exists:solicitudes,id'],
        ]);

        Evidencia::create($validated);

        return redirect()->route('evidencias.index')->with('success', 'Evidencia creada correctamente.');
    }

    public function show(Evidencia $evidencia)
    {
        $evidencia->load('solicitud.estudiante');

        return view('evidencias.show', compact('evidencia'));
    }

    public function edit(Evidencia $evidencia)
    {
        $solicitudes = Solicitud::with('estudiante')->orderByDesc('fecha_solicitud')->get();

        return view('evidencias.edit', compact('evidencia', 'solicitudes'));
    }

    public function update(Request $request, Evidencia $evidencia)
    {
        $validated = $request->validate([
            'tipo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'ruta_archivo' => ['nullable', 'string', 'max:255'],
            'solicitud_id' => ['required', 'exists:solicitudes,id'],
        ]);

        $evidencia->update($validated);

        return redirect()->route('evidencias.index')->with('success', 'Evidencia actualizada correctamente.');
    }

    public function destroy(Evidencia $evidencia)
    {
        $evidencia->delete();

        return redirect()->route('evidencias.index')->with('success', 'Evidencia eliminada correctamente.');
    }
}
