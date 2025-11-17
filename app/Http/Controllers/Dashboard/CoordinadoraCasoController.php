<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use Illuminate\Http\Request;

class CoordinadoraCasoController extends Controller
{
    public function index(Request $request)
    {
        $solicitudes = Solicitud::with(['estudiante.carrera', 'asesor', 'director'])
            ->latest('fecha_solicitud')
            ->paginate(12);

        return view('coordinadora.casos.index', [
            'solicitudes' => $solicitudes,
        ]);
    }
}
