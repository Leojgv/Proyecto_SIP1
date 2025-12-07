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
