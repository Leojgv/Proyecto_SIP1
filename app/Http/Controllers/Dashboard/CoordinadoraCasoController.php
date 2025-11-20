<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CoordinadoraCasoController extends Controller
{
    public function index(Request $request)
    {
        // La Coordinadora solo ve casos en estado "Pendiente de entrevista"
        // y casos que ya informó pero aún están en proceso inicial
        $solicitudes = Solicitud::with(['estudiante.carrera', 'asesor', 'director'])
            ->whereIn('estado', [
                'Pendiente de entrevista',
                'Pendiente de formulación del caso'
            ])
            ->latest('fecha_solicitud')
            ->paginate(12);

        return view('coordinadora.casos.index', [
            'solicitudes' => $solicitudes,
        ]);
    }

    /**
     * Informa a CTP (Coordinador Técnico Pedagógico) después de completar la anamnesis.
     * Cambia el estado de la solicitud a "Pendiente de formulación del caso".
     */
    public function informarACTP(Request $request, Solicitud $solicitud): RedirectResponse
    {
        // Verificar que el estado actual permita esta transición
        if (!in_array($solicitud->estado, ['Pendiente de entrevista', 'Pendiente de formulación del caso'])) {
            return back()->with('error', 'El estado actual de la solicitud no permite esta acción.');
        }

        $solicitud->update([
            'estado' => 'Pendiente de formulación del caso',
        ]);

        return back()->with('status', 'Solicitud informada a CTP para formulación del caso.');
    }
}
