<?php

namespace App\Http\Controllers;

use App\Models\AjusteRazonable;
use App\Models\Estudiante;
use App\Models\Solicitud;
use Illuminate\Http\Request;

class AjusteRazonableController extends Controller
{
    public function index()
    {
        $ajustes = AjusteRazonable::with(['solicitud', 'estudiante'])->orderByDesc('fecha_solicitud')->get();

        return view('ajustes_razonables.index', compact('ajustes'));
    }

    public function create()
    {
        $solicitudes = Solicitud::with('estudiante')->orderByDesc('fecha_solicitud')->get();
        $estudiantes = Estudiante::orderBy('nombre')->orderBy('apellido')->get();

        return view('ajustes_razonables.create', compact('solicitudes', 'estudiantes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'fecha_solicitud' => ['required', 'date'],
            'solicitud_id' => ['required', 'exists:solicitudes,id'],
            'estudiante_id' => ['required', 'exists:estudiantes,id'],
        ]);

        $solicitud = Solicitud::find($validated['solicitud_id']);
        if ($solicitud && $solicitud->estado === 'Pendiente de formulación del caso') {
            $solicitud->update(['estado' => 'Pendiente de formulación de ajuste']);
        }

        AjusteRazonable::create($validated + [
            'estado' => 'Pendiente de formulación de ajuste',
        ]);

        return redirect()->route('ajustes-razonables.index')->with('success', 'Ajuste razonable creado correctamente.');
    }

    public function show(AjusteRazonable $ajustes_razonable)
    {
        $ajustes_razonable->load(['solicitud', 'estudiante']);

        return view('ajustes_razonables.show', ['ajuste' => $ajustes_razonable]);
    }

    public function edit(AjusteRazonable $ajustes_razonable)
    {
        $solicitudes = Solicitud::with('estudiante')->orderByDesc('fecha_solicitud')->get();
        $estudiantes = Estudiante::orderBy('nombre')->orderBy('apellido')->get();

        return view('ajustes_razonables.edit', [
            'ajuste' => $ajustes_razonable,
            'solicitudes' => $solicitudes,
            'estudiantes' => $estudiantes,
        ]);
    }

    public function update(Request $request, AjusteRazonable $ajustes_razonable)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'fecha_solicitud' => ['required', 'date'],
            'solicitud_id' => ['required', 'exists:solicitudes,id'],
            'estudiante_id' => ['required', 'exists:estudiantes,id'],
        ]);

        $ajustes_razonable->update($validated);

        return redirect()->route('ajustes-razonables.index')->with('success', 'Ajuste razonable actualizado correctamente.');
    }

    public function destroy(AjusteRazonable $ajustes_razonable)
    {
        $ajustes_razonable->delete();

        return redirect()->route('ajustes-razonables.index')->with('success', 'Ajuste razonable eliminado correctamente.');
    }
}
