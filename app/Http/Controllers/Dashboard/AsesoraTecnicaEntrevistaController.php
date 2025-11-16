<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Entrevista;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AsesoraTecnicaEntrevistaController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $baseQuery = Entrevista::with(['solicitud.estudiante.carrera'])
            ->when($user, fn ($query) => $query->where('asesor_id', $user->id));

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'proximas' => (clone $baseQuery)->whereDate('fecha', '>=', now()->toDateString())->count(),
            'hoy' => (clone $baseQuery)->whereDate('fecha', now()->toDateString())->count(),
        ];

        $entrevistas = (clone $baseQuery)
            ->orderByDesc('fecha_hora_inicio')
            ->orderByDesc('fecha')
            ->simplePaginate(10);

        $historialEstudiantes = (clone $baseQuery)->get()
            ->filter(fn ($entrevista) => $entrevista->solicitud?->estudiante)
            ->groupBy(fn ($entrevista) => $entrevista->solicitud->estudiante->id)
            ->map(function ($grupo) {
                $estudiante = $grupo->first()->solicitud->estudiante;

                $ultima = $grupo
                    ->sortByDesc(function (Entrevista $entrevista) {
                        $inicio = $entrevista->fecha_hora_inicio ?? ($entrevista->fecha ? Carbon::parse($entrevista->fecha)->startOfDay() : null);
                        return $inicio ?? now()->startOfDay();
                    })
                    ->first();

                return [
                    'estudiante' => $estudiante,
                    'total' => $grupo->count(),
                    'ultima' => $ultima,
                ];
            })
            ->values();

        return view('asesora tecnica.entrevistas.index', [
            'entrevistas' => $entrevistas,
            'historialEstudiantes' => $historialEstudiantes,
            'stats' => $stats,
        ]);
    }
}
