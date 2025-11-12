<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AjusteRazonable;
use App\Models\Entrevista;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CoordinadoraDashboardController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $today = Carbon::now()->startOfDay();

        $entrevistasPendientes = Entrevista::where('asesor_id', $user->id)
            ->whereDate('fecha', '>=', $today)
            ->count();

        $entrevistasCompletadas = Entrevista::where('asesor_id', $user->id)
            ->whereDate('fecha', '<', $today)
            ->count();

        $casosRegistradosMes = Solicitud::whereYear('created_at', $today->year)
            ->whereMonth('created_at', $today->month)
            ->count();

        $casosEnProceso = AjusteRazonable::where(function ($query) {
            $query->whereNull('estado')
                ->orWhereNotIn('estado', ['Aprobado', 'Rechazado', 'Informado']);
        })->count();

        $proximasEntrevistas = Entrevista::with(['solicitud.estudiante'])
            ->where('asesor_id', $user->id)
            ->whereDate('fecha', '>=', $today)
            ->orderBy('fecha')
            ->take(5)
            ->get();

        $casosRecientes = Solicitud::with(['estudiante'])
            ->latest('created_at')
            ->take(5)
            ->get();

        $pipelineStats = [
            ['label' => 'Solicitud agendada', 'value' => Solicitud::count(), 'description' => 'Etapa inicial del caso'],
            ['label' => 'Entrevistas realizadas', 'value' => Entrevista::count(), 'description' => 'Casos con descripcion inicial'],
            ['label' => 'Ajustes formulados', 'value' => AjusteRazonable::count(), 'description' => 'En manos de la asesora tecnica'],
        ];

        $stats = [
            'entrevistasPendientes' => $entrevistasPendientes,
            'entrevistasCompletadas' => $entrevistasCompletadas,
            'casosRegistrados' => $casosRegistradosMes,
            'casosEnProceso' => $casosEnProceso,
        ];

        return view('coordinadora.dashboard.index', compact(
            'stats',
            'proximasEntrevistas',
            'casosRecientes',
            'pipelineStats'
        ));
    }
}
