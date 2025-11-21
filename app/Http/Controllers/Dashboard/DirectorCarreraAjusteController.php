<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\Estudiante;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DirectorCarreraAjusteController extends Controller
{
    /**
     * Muestra la lista de estudiantes con sus ajustes aplicados
     */
    public function index(Request $request): View
    {
        $directorId = $request->user()->id;

        // Obtener todas las carreras dirigidas por este director
        $carreras = Carrera::where('director_id', $directorId)->pluck('id');

        // Obtener estudiantes con sus ajustes, solo de las carreras del director
        // Filtrar solo estudiantes que tengan al menos un ajuste aprobado o activo
        $estudiantes = Estudiante::with([
                'carrera', 
                'ajustesRazonables' => function ($query) {
                    $query->whereIn('estado', [
                        'Pendiente de formulación de ajuste',
                        'Pendiente de preaprobación',
                        'Pendiente de Aprobación',
                        'Aprobado',
                    ])->orderByDesc('fecha_solicitud');
                },
                'ajustesRazonables.solicitud'
            ])
            ->whereIn('carrera_id', $carreras)
            ->whereHas('ajustesRazonables', function ($query) {
                // Solo mostrar estudiantes con ajustes en estados activos/aprobados
                $query->whereIn('estado', [
                    'Pendiente de formulación de ajuste',
                    'Pendiente de preaprobación',
                    'Pendiente de Aprobación',
                    'Aprobado',
                ]);
            })
            ->withCount(['ajustesRazonables' => function ($query) {
                $query->whereIn('estado', [
                    'Pendiente de formulación de ajuste',
                    'Pendiente de preaprobación',
                    'Pendiente de Aprobación',
                    'Aprobado',
                ]);
            }])
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->get()
            ->map(function ($estudiante) {
                // Filtrar solo ajustes aprobados o activos
                $ajustesAplicados = $estudiante->ajustesRazonables->filter(function ($ajuste) {
                    return in_array($ajuste->estado, [
                        'Pendiente de formulación de ajuste',
                        'Pendiente de preaprobación',
                        'Pendiente de Aprobación',
                        'Aprobado',
                    ]);
                });
                
                return [
                    'id' => $estudiante->id,
                    'nombre' => $estudiante->nombre,
                    'apellido' => $estudiante->apellido,
                    'rut' => $estudiante->rut,
                    'email' => $estudiante->email,
                    'telefono' => $estudiante->telefono,
                    'carrera' => $estudiante->carrera->nombre ?? 'Sin carrera',
                    'ajustes_count' => $ajustesAplicados->count(),
                    'ajustes' => $ajustesAplicados->map(function ($ajuste) {
                        return [
                            'id' => $ajuste->id,
                            'nombre' => $ajuste->nombre,
                            'estado' => $ajuste->estado,
                            'fecha_solicitud' => $ajuste->fecha_solicitud,
                            'solicitud_estado' => $ajuste->solicitud->estado ?? null,
                        ];
                    }),
                ];
            });

        return view('DirectorCarrera.ajustes.index', [
            'estudiantes' => $estudiantes,
        ]);
    }
}

