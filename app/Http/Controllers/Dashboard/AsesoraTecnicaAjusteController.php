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
        // Solo mostrar solicitudes que están en fase de CTP para crear ajustes
        $solicitudes = Solicitud::with('estudiante')
            ->whereIn('estado', [
                'Pendiente de formulación del caso',
                'Pendiente de formulación de ajuste',
                'Pendiente de preaprobación',
            ])
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
     * Envía los ajustes razonables a Asesoría Pedagógica para preaprobación.
     * Cambia el estado de la solicitud a "Pendiente de preaprobación".
     */
    public function enviarAPreaprobacion(Request $request, Solicitud $solicitud): RedirectResponse
    {
        // Verificar que haya ajustes razonables asociados
        if ($solicitud->ajustesRazonables()->count() === 0) {
            return back()->with('error', 'No hay ajustes razonables asociados a esta solicitud para enviar.');
        }

        // Verificar que el estado actual permita esta transición
        $estadosPermitidos = ['Pendiente de formulación de ajuste'];
        if (!in_array($solicitud->estado, $estadosPermitidos)) {
            return back()->with('error', 'El estado actual de la solicitud no permite enviar a preaprobación.');
        }

        // Obtener la asesora pedagógica asignada al caso
        $asesoraPedagogicaId = $solicitud->asesor_id;

        if (!$asesoraPedagogicaId) {
            return back()->with('error', 'No se ha asignado una Asesora Pedagógica para este caso. Por favor, verifica la asignación del caso.');
        }

        $solicitud->update([
            'estado' => 'Pendiente de preaprobación',
        ]);

        return back()->with('status', 'Ajustes razonables enviados a Asesoría Pedagógica para preaprobación.');
    }
}
