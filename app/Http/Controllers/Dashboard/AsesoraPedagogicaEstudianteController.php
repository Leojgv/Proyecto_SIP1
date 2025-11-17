<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use Illuminate\Http\Request;

class AsesoraPedagogicaEstudianteController extends Controller
{
    public function index(Request $request)
    {
        $estudiantes = Estudiante::with('carrera')
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->paginate(10);

        return view('asesora pedagogica.estudiantes.index', [
            'estudiantes' => $estudiantes,
        ]);
    }
}
