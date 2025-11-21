<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AsesoraPedagogicaCasoController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Mostrar casos que están en preaprobación o casos asignados a esta asesora
        $solicitudes = Solicitud::with(['estudiante.carrera', 'ajustesRazonables'])
            ->where(function ($query) use ($user) {
                $query->where('estado', 'Pendiente de preaprobación')
                    ->orWhere(function ($q) use ($user) {
                        $q->where('asesor_id', $user->id)
                            ->whereIn('estado', [
                                'Pendiente de formulación del caso',
                                'Pendiente de formulación de ajuste',
                                'Pendiente de Aprobación',
                                'Aprobado',
                                'Rechazado',
                            ]);
                    });
            })
            ->latest('fecha_solicitud')
            ->paginate(10);

        return view('asesora pedagogica.casos.index', [
            'solicitudes' => $solicitudes,
        ]);
    }

    public function show(Request $request, Solicitud $solicitud): View
    {
        $solicitud->load([
            'estudiante.carrera',
            'asesor',
            'director',
            'ajustesRazonables',
            'evidencias',
            'entrevistas.asesor',
        ]);

        return view('asesora pedagogica.casos.show', [
            'solicitud' => $solicitud,
        ]);
    }

    /**
     * Envía el caso a Dirección de Carrera para aprobación final.
     * Cambia el estado a "Pendiente de Aprobación".
     */
    public function enviarADirector(Request $request, Solicitud $solicitud): RedirectResponse
    {
        // Verificar que el estado actual permita esta transición
        if ($solicitud->estado !== 'Pendiente de preaprobación') {
            return back()->with('error', 'Solo se pueden enviar casos que estén en estado "Pendiente de preaprobación".');
        }

        // Verificar que haya ajustes razonables asociados
        if ($solicitud->ajustesRazonables()->count() === 0) {
            return back()->with('error', 'No se puede enviar un caso sin ajustes razonables asociados.');
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

        return redirect()
            ->route('asesora-pedagogica.casos.index')
            ->with('status', 'Solicitud enviada a Dirección de Carrera para aprobación final.');
    }

    /**
     * Devuelve el caso al Asesor Técnico Pedagógico para correcciones.
     * Cambia el estado a "Pendiente de formulación de ajuste".
     */
    public function devolverACTT(Request $request, Solicitud $solicitud): RedirectResponse
    {
        $validated = $request->validate([
            'motivo_devolucion' => ['required', 'string', 'min:10'],
        ]);

        // Verificar que el estado actual permita esta transición
        if ($solicitud->estado !== 'Pendiente de preaprobación') {
            return back()->with('error', 'Solo se pueden devolver casos que estén en estado "Pendiente de preaprobación".');
        }

        $solicitud->update([
            'estado' => 'Pendiente de formulación de ajuste',
            'motivo_rechazo' => $validated['motivo_devolucion'],
        ]);

        return redirect()
            ->route('asesora-pedagogica.casos.index')
            ->with('status', 'Caso devuelto al Asesor Técnico Pedagógico para correcciones.');
    }
}
