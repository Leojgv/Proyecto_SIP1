<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Support\Carbon;

class CoordinadoraEstudianteController extends Controller
{
    public function index()
    {
        $total = Estudiante::count();
        $activos = $total; // no hay estado, asumimos todos activos
        $conCasos = Estudiante::whereHas('solicitudes')->count();
        $nuevosMes = Estudiante::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        $estudiantesCollection = Estudiante::with(['carrera', 'solicitudes'])
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

        return view('coordinadora.estudiantes.index', compact(
            'total',
            'activos',
            'conCasos',
            'nuevosMes',
            'estudiantes',
            'estudiantesOptions',
            'asesores',
            'directores'
        ));
    }
}

