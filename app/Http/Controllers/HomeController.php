<?php

namespace App\Http\Controllers;

use App\Models\Entrevista;
use App\Models\Evidencia;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();
        $estudiante = $user?->estudiante;

        $activeStatuses = ['pendiente', 'en_proceso', 'activo'];

        $solicitudes = $estudiante
            ? $estudiante->solicitudes()
                ->withCount(['ajustesRazonables', 'evidencias', 'entrevistas'])
                ->orderByDesc('fecha_solicitud')
                ->get()
            : collect();

        $solicitudesActivas = $solicitudes->whereIn('estado', $activeStatuses)->count();

        $ajustesActivos = $estudiante
            ? $estudiante->ajustesRazonables()
                ->whereIn('estado', $activeStatuses)
                ->orderByDesc('fecha_inicio')
                ->get()
            : collect();

        $proximasEntrevistas = $estudiante
            ? Entrevista::whereHas('solicitud', function ($query) use ($estudiante) {
                    $query->where('estudiante_id', $estudiante->id);
                })
                ->with('asesorPedagogico')
                ->whereDate('fecha', '>=', now()->startOfDay())
                ->orderBy('fecha')
                ->get()
            : collect();

        if ($estudiante) {
            $evidenciasQuery = Evidencia::whereHas('solicitud', function ($query) use ($estudiante) {
                $query->where('estudiante_id', $estudiante->id);
            });

            $evidenciasTotales = (clone $evidenciasQuery)->count();
            $evidenciasRecientes = (clone $evidenciasQuery)->latest()->take(3)->get();
        } else {
            $evidenciasTotales = 0;
            $evidenciasRecientes = collect();
        }

        $stats = [
            'solicitudes_activas' => $solicitudesActivas,
            'ajustes_activos' => $ajustesActivos->count(),
            'entrevistas_programadas' => $proximasEntrevistas->count(),
            'evidencias_enviadas' => $evidenciasTotales,
        ];

        return view('home', [
            'user' => $user,
            'estudiante' => $estudiante,
            'stats' => $stats,
            'solicitudesRecientes' => $solicitudes->take(3),
            'ajustesActivos' => $ajustesActivos->take(3),
            'proximasEntrevistas' => $proximasEntrevistas->take(3),
            'evidenciasRecientes' => $evidenciasRecientes,
        ]);
    }
}
