<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AjusteRazonable;
use App\Models\Estudiante;
use App\Models\Solicitud;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AsesoraTecnicaAjusteController extends Controller
{
    public function create()
    {
        $solicitudes = Solicitud::with('estudiante')
            ->orderByDesc('fecha_solicitud')
            ->get();

        $estudiantes = Estudiante::with('carrera')
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->get();

        return view('asesora tecnica.ajustes.create', [
            'solicitudes' => $solicitudes,
            'estudiantes' => $estudiantes,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'fecha_solicitud' => ['required', 'date'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_termino' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'porcentaje_avance' => ['nullable', 'integer', 'between:0,100'],
            'solicitud_id' => ['required', 'exists:solicitudes,id'],
            'estudiante_id' => ['required', 'exists:estudiantes,id'],
        ]);

        $solicitud = Solicitud::findOrFail($validated['solicitud_id']);

        // Siempre marcamos los ajustes como en formulación cuando se crean.
        $validated['estado'] = 'Pendiente de formulación de ajuste';

        // Cuando CTP crea el primer ajuste razonable, cambiar estado de solicitud a "Pendiente de formulación de ajuste"
        if ($solicitud->estado === 'Pendiente de formulación del caso') {
            $solicitud->update([
                'estado' => 'Pendiente de formulación de ajuste',
            ]);
        }

        AjusteRazonable::create($validated);

        return redirect()
            ->route('asesora-tecnica.dashboard')
            ->with('status', 'Ajuste registrado correctamente.');
    }

    /**
     * Envía los ajustes razonables al Director de Carrera para validación.
     * Cambia el estado de la solicitud a "Pendiente de Aprobación".
     */
    public function enviarADirector(Request $request, Solicitud $solicitud): RedirectResponse
    {
        // Verificar que haya ajustes razonables asociados
        if ($solicitud->ajustesRazonables()->count() === 0) {
            return back()->with('error', 'No hay ajustes razonables asociados a esta solicitud para enviar.');
        }

        // Verificar que el estado actual permita esta transición
        $estadosPermitidos = ['Pendiente de formulación de ajuste', 'Pendiente de preaprobación'];
        if (!in_array($solicitud->estado, $estadosPermitidos)) {
            return back()->with('error', 'El estado actual de la solicitud no permite enviar a Dirección.');
        }

        // Obtener el director de la carrera del estudiante
        $estudiante = $solicitud->estudiante;
        $directorId = $estudiante?->carrera?->director_id ?? $solicitud->director_id;

        if (!$directorId) {
            return back()->with('error', 'No se ha asignado un Director de Carrera para este estudiante.');
        }

        $solicitud->update([
            'estado' => 'Pendiente de Aprobación',
            'director_id' => $directorId,
        ]);

        return back()->with('status', 'Ajustes razonables enviados al Director de Carrera para aprobación.');
    }
}
