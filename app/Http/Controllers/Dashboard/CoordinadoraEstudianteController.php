<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CoordinadoraEstudianteController extends Controller
{
    public function index(Request $request)
    {
        $search = (string) $request->input('search', '');
        $carreraId = $request->input('carrera_id');
        $estado = $request->input('estado');
        $ordenarPor = $request->input('ordenar_por', 'nombre');

        $total = Estudiante::count();
        $activos = $total; // no hay estado, asumimos todos activos
        $conCasos = Estudiante::whereHas('solicitudes')->count();
        $nuevosMes = Estudiante::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        $estudiantesQuery = Estudiante::with(['carrera', 'solicitudes']);

        if ($search !== '') {
            $estudiantesQuery->where(function ($q) use ($search) {
                $q->where('nombre', 'like', '%' . $search . '%')
                    ->orWhere('apellido', 'like', '%' . $search . '%')
                    ->orWhere('rut', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($carreraId) {
            $estudiantesQuery->where('carrera_id', $carreraId);
        }

        if ($estado === 'activo') {
            $estudiantesQuery->whereHas('solicitudes');
        } elseif ($estado === 'sin_casos') {
            $estudiantesQuery->whereDoesntHave('solicitudes');
        }

        // Aplicar ordenamiento según el filtro seleccionado
        switch ($ordenarPor) {
            case 'nombre_desc':
                $estudiantesQuery->orderBy('nombre', 'desc')->orderBy('apellido', 'desc');
                break;
            case 'carrera':
                $estudiantesQuery->orderBy('carrera_id')->orderBy('nombre');
                break;
            case 'carrera_desc':
                $estudiantesQuery->orderBy('carrera_id', 'desc')->orderBy('nombre', 'desc');
                break;
            case 'casos':
            case 'casos_desc':
                // Se ordenará después de obtener los datos
                break;
            case 'fecha_asc':
                $estudiantesQuery->orderBy('created_at', 'asc');
                break;
            case 'fecha_desc':
                $estudiantesQuery->orderBy('created_at', 'desc');
                break;
            case 'nombre':
            default:
                $estudiantesQuery->orderBy('nombre')->orderBy('apellido');
                break;
        }

        $estudiantesCollection = $estudiantesQuery->get();

        $estudiantes = $estudiantesCollection->map(function ($estudiante) {
            $casosActivos = $estudiante->solicitudes->count();
            
            // Cargar solicitudes con relaciones necesarias
            $solicitudes = $estudiante->solicitudes()
                ->with(['asesor', 'director', 'entrevistas.asesor'])
                ->orderBy('fecha_solicitud', 'desc')
                ->get()
                ->map(function ($solicitud) {
                    return [
                        'id' => $solicitud->id,
                        'titulo' => $solicitud->titulo ?? null,
                        'fecha_solicitud' => $solicitud->fecha_solicitud?->format('d/m/Y') ?? 's/f',
                        'estado' => $solicitud->estado ?? 'Sin estado',
                        'descripcion' => $solicitud->descripcion ?? 'Sin descripción',
                        'motivo_rechazo' => $solicitud->motivo_rechazo ?? null,
                        'coordinadora' => $solicitud->asesor ? $solicitud->asesor->nombre . ' ' . $solicitud->asesor->apellido : 'Sin asignar',
                        'director' => $solicitud->director ? $solicitud->director->nombre . ' ' . $solicitud->director->apellido : 'No asignado',
                        'entrevistas' => $solicitud->entrevistas->map(function ($entrevista) {
                            return [
                                'fecha' => $entrevista->fecha?->format('d/m/Y') ?? 's/f',
                                'hora_inicio' => $entrevista->fecha_hora_inicio?->format('H:i') ?? '--',
                                'hora_fin' => $entrevista->fecha_hora_fin?->format('H:i') ?? '--',
                                'modalidad' => $entrevista->modalidad ?? 'N/A',
                                'asesor' => $entrevista->asesor ? $entrevista->asesor->nombre . ' ' . $entrevista->asesor->apellido : 'Sin asignar',
                            ];
                        })->toArray(),
                    ];
                })->toArray();

            return [
                'nombre' => $estudiante->nombre,
                'apellido' => $estudiante->apellido,
                'rut' => $estudiante->rut,
                'email' => $estudiante->email,
                'carrera' => $estudiante->carrera?->nombre,
                'carrera_id' => $estudiante->carrera_id,
                'semestre' => '-',
                'tipo_discapacidad' => 'No definido',
                'estado' => $casosActivos > 0 ? 'Activo' : 'Sin casos',
                'casos' => $casosActivos,
                'id' => $estudiante->id,
                'created_at' => $estudiante->created_at,
                'solicitudes' => $solicitudes,
            ];
        });

        // Ordenar por casos si es necesario (después de obtener los datos)
        if ($ordenarPor === 'casos') {
            $estudiantes = $estudiantes->sortBy('casos')->values();
        } elseif ($ordenarPor === 'casos_desc') {
            $estudiantes = $estudiantes->sortByDesc('casos')->values();
        }

        $estudiantesOptions = $estudiantesCollection;
        $asesores = User::withRole('Asesora Pedagogica')
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->get();
        $directores = User::withRole('Director de carrera')
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->get();
        $carreras = Carrera::orderBy('nombre')->get();
        $filters = [
            'search' => $search,
            'carrera_id' => $carreraId,
            'estado' => $estado,
            'ordenar_por' => $ordenarPor,
        ];

        return view('coordinadora.estudiantes.index', compact(
            'total',
            'activos',
            'conCasos',
            'nuevosMes',
            'estudiantes',
            'estudiantesOptions',
            'asesores',
            'directores',
            'carreras',
            'filters'
        ));
    }
}
