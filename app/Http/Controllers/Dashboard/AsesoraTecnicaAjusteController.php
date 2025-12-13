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
        // Excluir solicitudes que ya están en preaprobación o estados posteriores
        $solicitudes = Solicitud::with('estudiante')
            ->whereIn('estado', [
                'Pendiente de formulación del caso',
                'Pendiente de formulación de ajuste',
                'Listo para Enviar',
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
            'solicitud_id' => ['required', 'exists:solicitudes,id'],
            'estudiante_id' => ['required', 'exists:estudiantes,id'],
            'descripcion' => ['required', 'string'],
        ]);

        $solicitud = Solicitud::findOrFail($validated['solicitud_id']);

        // Asignar fecha automáticamente con la fecha actual
        $validated['fecha_solicitud'] = now()->toDateString();
        
        // Siempre marcamos los ajustes como en formulación cuando se crean.
        $validated['estado'] = 'Pendiente de formulación de ajuste';

        // Cuando CTP crea un ajuste razonable, cambiar estado de solicitud a "Listo para Enviar"
        if (in_array($solicitud->estado, ['Pendiente de formulación del caso', 'Pendiente de formulación de ajuste'])) {
            $solicitud->update([
                'estado' => 'Listo para Enviar',
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
        $estadosPermitidos = ['Listo para Enviar', 'Pendiente de formulación de ajuste'];
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

    /**
     * Elimina un ajuste razonable.
     * Solo permite eliminar ajustes si la solicitud aún no está en preaprobación o estados posteriores.
     */
    public function destroy(Request $request, AjusteRazonable $ajuste): RedirectResponse
    {
        $solicitud = $ajuste->solicitud;
        
        // Solo permitir eliminar si la solicitud está en estados que permiten modificación
        $estadosPermitidos = [
            'Pendiente de formulación del caso',
            'Pendiente de formulación de ajuste',
            'Listo para Enviar',
        ];
        
        if (!in_array($solicitud->estado, $estadosPermitidos)) {
            return back()->with('error', 'No se puede eliminar ajustes de solicitudes que ya están en preaprobación o estados posteriores.');
        }

        $ajuste->delete();

        // Si ya no hay ajustes, actualizar el estado de la solicitud
        if ($solicitud->ajustesRazonables()->count() === 0) {
            $solicitud->update([
                'estado' => 'Pendiente de formulación del caso',
            ]);
        }

        return back()->with('status', 'Ajuste eliminado correctamente.');
    }
}
