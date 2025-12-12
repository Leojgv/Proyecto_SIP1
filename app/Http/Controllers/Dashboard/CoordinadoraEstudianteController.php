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

        $estudiantesCollection = $estudiantesQuery
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->get();

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
                'semestre' => '-',
                'tipo_discapacidad' => 'No definido',
                'estado' => $casosActivos > 0 ? 'Activo' : 'Sin casos',
                'casos' => $casosActivos,
                'id' => $estudiante->id,
                'solicitudes' => $solicitudes,
            ];
        });

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
