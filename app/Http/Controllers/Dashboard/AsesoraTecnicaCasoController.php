<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use Illuminate\Http\Request;

class AsesoraTecnicaCasoController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $solicitudes = Solicitud::with(['estudiante.carrera', 'ajustesRazonables'])
            ->when($user, fn ($query) => $query->where('asesor_id', $user->id))
            ->latest('fecha_solicitud')
            ->paginate(10);

        return view('asesora tecnica.casos.index', [
            'solicitudes' => $solicitudes,
        ]);
    }
}
