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

        $query = AjusteRazonable::with(['estudiante.carrera', 'solicitud'])
            ->whereHas('solicitud', function ($query) use ($user) {
                if ($user) {
                    $query->where('asesor_id', $user->id);
                }
            });

        // Filtro de búsqueda por nombre
        if ($request->filled('buscar')) {
            $buscar = $request->get('buscar');
            $query->whereHas('estudiante', function ($q) use ($buscar) {
                $q->whereRaw("CONCAT(nombre, ' ', apellido) LIKE ?", ["%{$buscar}%"]);
            });
        }

        $ajustes = $query->get();

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
                    'estudiante_id' => $estudiante->id ?? null,
                    'items' => $items->map(function (AjusteRazonable $ajuste) {
                        // En la vista de Asesora Pedagógica, los ajustes con estado "Pendiente de formulación de ajuste"
                        // se muestran como "Pendiente de preaprobación" porque ya fueron formulados por la Asesora Técnica
                        $estado = $ajuste->estado ?? 'Pendiente';
                        if ($estado === 'Pendiente de formulación de ajuste') {
                            $estado = 'Pendiente de preaprobación';
                        }
                        
                        return [
                            'nombre' => $ajuste->nombre ?? 'Ajuste sin nombre',
                            'estado' => $estado,
                            'solicitud_id' => $ajuste->solicitud_id,
                            'fecha' => optional($ajuste->fecha_solicitud ?? $ajuste->updated_at)?->format('d/m/Y') ?? 's/f',
                            'descripcion' => $ajuste->descripcion ?? null,
                            'motivo_rechazo' => $ajuste->motivo_rechazo ?? null,
                        ];
                    })->all(),
                ];
            })
            ->values();

        // Ordenar por
        $ordenarPor = $request->get('ordenar_por', 'nombre_asc');
        switch ($ordenarPor) {
            case 'nombre_asc':
                $ajustesPorEstudiante = $ajustesPorEstudiante->sortBy('student')->values();
                break;
            case 'nombre_desc':
                $ajustesPorEstudiante = $ajustesPorEstudiante->sortByDesc('student')->values();
                break;
            case 'ajustes_asc':
                $ajustesPorEstudiante = $ajustesPorEstudiante->sortBy(function ($item) {
                    return count($item['items']);
                })->values();
                break;
            case 'ajustes_desc':
                $ajustesPorEstudiante = $ajustesPorEstudiante->sortByDesc(function ($item) {
                    return count($item['items']);
                })->values();
                break;
        }

        return view('asesora pedagogica.ajustes.index', [
            'ajustesPorEstudiante' => $ajustesPorEstudiante,
            'buscar' => $request->get('buscar', ''),
            'ordenarPor' => $ordenarPor,
        ]);
    }
}
