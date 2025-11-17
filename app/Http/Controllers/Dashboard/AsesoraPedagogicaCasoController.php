<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AsesoraPedagogicaCasoController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $solicitudes = Solicitud::with(['estudiante.carrera'])
            ->when($user, fn ($query) => $query->where('asesor_id', $user->id))
            ->latest('fecha_solicitud')
            ->paginate(10);

        return view('asesora pedagogica.casos.index', [
            'solicitudes' => $solicitudes,
        ]);
    }

    public function sendToDirector(Request $request, Solicitud $solicitud): RedirectResponse
    {
        $estudiante = $solicitud->estudiante;
        $directorId = $estudiante?->carrera?->director_id ?? $solicitud->director_id;

        $solicitud->update([
            'estado' => 'Enviado a Direccion',
            'director_id' => $directorId,
        ]);

        return back()->with('status', 'Solicitud enviada a Dirección de Carrera.');
    }
}
