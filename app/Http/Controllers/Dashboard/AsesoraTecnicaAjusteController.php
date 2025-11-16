<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AjusteRazonable;
use App\Models\Estudiante;
use App\Models\Solicitud;
use Illuminate\Http\Request;

class AsesoraTecnicaAjusteController extends Controller
{
    public function create()
    {
        $solicitudes = Solicitud::with('estudiante')
            ->orderByDesc('fecha_solicitud')
            ->get();

        $estudiantes = Estudiante::orderBy('nombre')
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
            'estado' => ['nullable', 'string', 'max:255'],
            'porcentaje_avance' => ['nullable', 'integer', 'between:0,100'],
            'solicitud_id' => ['required', 'exists:solicitudes,id'],
            'estudiante_id' => ['required', 'exists:estudiantes,id'],
        ]);

        // Si no se aporta estado, asumimos que queda enviado para visibilidad inmediata.
        if (! $validated['estado'] ?? true) {
            $validated['estado'] = 'Enviado';
        }

        AjusteRazonable::create($validated);

        return redirect()
            ->route('asesora-tecnica.dashboard')
            ->with('status', 'Ajuste registrado correctamente.');
    }
}
