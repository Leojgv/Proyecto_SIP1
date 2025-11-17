<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use Illuminate\Http\Request;

class DirectorCarreraEstudianteController extends Controller
{
    public function index(Request $request)
    {
        $directorId = $request->user()->id;

        $estudiantes = Estudiante::with('carrera')
            ->whereHas('carrera', fn ($query) => $query->where('director_id', $directorId))
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->paginate(12);

        return view('DirectorCarrera.estudiantes.index', [
            'estudiantes' => $estudiantes,
        ]);
    }
}
