<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AsesoraPedagogicaCasoController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $solicitudes = Solicitud::with(['estudiante.carrera'])
            ->when($user, fn ($query) => $query->where('asesor_id', $user->id))
            ->latest('fecha_solicitud')
            ->paginate(10);

        return view('asesora pedagogica.casos.index', [
            'solicitudes' => $solicitudes,
        ]);
    }

    public function sendToDirector(Request $request, Solicitud $solicitud): RedirectResponse
    {
        // Verificar que el estado actual permita esta transición
        $estadosPermitidos = ['Pendiente de formulación del caso', 'Pendiente de formulación de ajuste'];
        if (!in_array($solicitud->estado, $estadosPermitidos)) {
            return back()->with('error', 'El estado actual de la solicitud no permite enviar a Dirección.');
        }

        // Obtener el director automáticamente según la carrera del estudiante
        $estudiante = $solicitud->estudiante;
        $estudiante->load('carrera');
        $directorId = $estudiante?->carrera?->director_id;

        if (!$directorId) {
            return back()->with('error', 'No se ha asignado un Director de Carrera para la carrera del estudiante. Por favor, verifica que la carrera del estudiante tenga un director asignado.');
        }

        $solicitud->update([
            'estado' => 'Pendiente de Aprobación',
            'director_id' => $directorId,
        ]);

        return back()->with('status', 'Solicitud enviada a Dirección de Carrera para aprobación.');
    }
}
