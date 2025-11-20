<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use Illuminate\Http\Request;

class AsesoraTecnicaCasoController extends Controller
{
    public function index(Request $request)
    {
        // La Asesora Técnica (CTP) solo ve casos que están en su fase del proceso:
        // - Pendiente de formulación del caso (cuando Coordinadora informa)
        // - Pendiente de formulación de ajuste (cuando está formulando ajustes)
        // - Pendiente de preaprobación (si aplica)
        // También puede ver casos devueltos por el Director
        $solicitudes = Solicitud::with(['estudiante.carrera', 'ajustesRazonables'])
            ->whereIn('estado', [
                'Pendiente de formulación del caso',
                'Pendiente de formulación de ajuste',
                'Pendiente de preaprobación',
            ])
            ->latest('fecha_solicitud')
            ->paginate(10);

        return view('asesora tecnica.casos.index', [
            'solicitudes' => $solicitudes,
        ]);
    }
}
