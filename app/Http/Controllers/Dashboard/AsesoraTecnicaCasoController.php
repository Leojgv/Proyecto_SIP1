<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\Solicitud;
use Illuminate\Http\Request;

class AsesoraTecnicaCasoController extends Controller
{
    public function index(Request $request)
    {
        // La Asesora Técnica (ATP) solo ve casos que están en su fase del proceso:
        // - Pendiente de formulación del caso (cuando Coordinadora informa)
        // - Pendiente de formulación de ajuste (cuando está formulando ajustes)
        // - Listo para Enviar (cuando se ha agregado al menos un ajuste)
        // - Pendiente de preaprobación (si aplica)
        // También puede ver casos devueltos por el Director
        $query = Solicitud::with(['estudiante.carrera', 'ajustesRazonables', 'entrevistas', 'evidencias'])
            ->whereIn('estado', [
                'Pendiente de formulación del caso',
                'Pendiente de formulación de ajuste',
                'Listo para Enviar',
                'Pendiente de preaprobación',
            ]);

        // Filtro por búsqueda (nombre, apellido, carrera, RUT)
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('estudiante', function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('apellido', 'like', "%{$buscar}%")
                  ->orWhere('rut', 'like', "%{$buscar}%")
                  ->orWhereHas('carrera', function ($qc) use ($buscar) {
                      $qc->where('nombre', 'like', "%{$buscar}%");
                  });
            });
        }

        // Filtro por carrera
        if ($request->filled('carrera')) {
            $query->whereHas('estudiante', function ($q) use ($request) {
                $q->where('carrera_id', $request->carrera);
            });
        }

        $solicitudes = $query->latest('fecha_solicitud')->paginate(10)->withQueryString();

        // Obtener carreras para el filtro
        $carreras = Carrera::orderBy('nombre')->get();

        return view('asesora tecnica.casos.index', [
            'solicitudes' => $solicitudes,
            'carreras' => $carreras,
        ]);
    }

    public function show(Request $request, Solicitud $solicitud)
    {
        $solicitud->load([
            'estudiante.carrera',
            'asesor',
            'director',
            'ajustesRazonables',
            'evidencias',
            'entrevistas.asesor',
        ]);

        return view('asesora tecnica.casos.show', [
            'solicitud' => $solicitud,
        ]);
    }
}
