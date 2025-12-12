<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AjusteRazonable;
use Illuminate\Http\Request;

class AsesoraPedagogicaAjusteController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $ajustes = AjusteRazonable::with(['estudiante.carrera', 'solicitud'])
            ->whereHas('solicitud', function ($query) use ($user) {
                if ($user) {
                    $query->where('asesor_id', $user->id);
                }
            })
            ->latest('updated_at')
            ->get();

        $ajustesPorEstudiante = $ajustes
            ->groupBy('estudiante_id')
            ->map(function ($items) {
                $primero = $items->first();
                $estudiante = $primero?->estudiante;
                $nombreEstudiante = trim(($estudiante->nombre ?? '') . ' ' . ($estudiante->apellido ?? '')) ?: 'Estudiante sin nombre';
                $programa = optional(optional($estudiante)->carrera)->nombre ?? 'Programa no asignado';

                return [
                    'student' => $nombreEstudiante,
                    'program' => $programa,
                    'solicitud_id' => $primero->solicitud_id ?? null,
                    'items' => $items->map(function (AjusteRazonable $ajuste) {
                        return [
                            'nombre' => $ajuste->nombre ?? 'Ajuste sin nombre',
                            'estado' => $ajuste->estado ?? 'Pendiente',
                            'solicitud_id' => $ajuste->solicitud_id,
                            'fecha' => optional($ajuste->fecha_solicitud ?? $ajuste->updated_at)?->format('d/m/Y') ?? 's/f',
                            'descripcion' => optional($ajuste->solicitud)->descripcion,
                        ];
                    })->all(),
                ];
            })
            ->values();

        return view('asesora pedagogica.ajustes.index', [
            'ajustesPorEstudiante' => $ajustesPorEstudiante,
        ]);
    }
}
