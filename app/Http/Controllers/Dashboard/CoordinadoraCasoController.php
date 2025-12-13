<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CoordinadoraCasoController extends Controller
{
    public function index(Request $request)
    {
        // La Coordinadora solo ve casos en estado "Pendiente de entrevista"
        // y casos que ya informó pero aún están en proceso inicial
        $solicitudes = Solicitud::with(['estudiante.carrera', 'asesor', 'director', 'entrevistas', 'evidencias'])
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
     * Informa a ATP (Asesora Técnica Pedagógica) después de completar la anamnesis.
     * Cambia el estado de la solicitud a "Pendiente de formulación del caso".
     */
    public function informarACTP(Request $request, Solicitud $solicitud): RedirectResponse
    {
        // Verificar que el estado actual permita esta transición
        if (!in_array($solicitud->estado, ['Pendiente de entrevista', 'Pendiente de formulación del caso'])) {
            return back()->with('error', 'El estado actual de la solicitud no permite esta acción.');
        }

        $validated = $request->validate([
            'observaciones' => ['nullable', 'string'],
            'observaciones_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'], // Max 10MB
        ]);

        $updateData = [
            'estado' => 'Pendiente de formulación del caso',
        ];

        // Guardar el PDF si se subió
        if ($request->hasFile('observaciones_pdf')) {
            $file = $request->file('observaciones_pdf');
            $fileName = 'observaciones_' . $solicitud->id . '_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('observaciones', $fileName, 'public');
            $updateData['observaciones_pdf_ruta'] = $path;
        }

        $solicitud->update($updateData);

        // Guardar observaciones en la entrevista si existe
        if (!empty($validated['observaciones']) && $solicitud->entrevistas->isNotEmpty()) {
            $entrevista = $solicitud->entrevistas->first();
            $entrevista->update([
                'observaciones' => $validated['observaciones'],
            ]);
        }

        return back()->with('status', 'Solicitud informada a ATP (Asesora Técnica) para formulación del caso.');
    }
}
