<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AjusteRazonable;
use App\Models\Entrevista;
use App\Models\Estudiante;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
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

        // Obtener estudiantes para el modal de registro de solicitud
        $estudiantes = Estudiante::with('carrera')
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->get();

        return view('coordinadora.dashboard.index', compact(
            'stats',
            'proximasEntrevistas',
            'casosRecientes',
            'pipelineStats',
            'estudiantes'
        ));
    }

    /**
     * Guarda una nueva solicitud desde el dashboard de Coordinadora.
     */
    public function storeSolicitud(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Verificar que el usuario esté autenticado
        if (!$user) {
            return back()
                ->withErrors(['error' => 'Debes estar autenticado para registrar una solicitud.'])
                ->withInput();
        }

        $validated = $request->validate([
            'estudiante_id' => ['required', 'exists:estudiantes,id'],
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string', 'min:10'],
        ]);

        // Obtener el estudiante para determinar su carrera y director
        $estudiante = Estudiante::with('carrera')->findOrFail($validated['estudiante_id']);
        $directorId = $estudiante->carrera?->director_id;

        if (!$directorId) {
            return back()
                ->withErrors(['estudiante_id' => 'El estudiante no tiene una carrera asignada o la carrera no tiene un director asignado.'])
                ->withInput();
        }

        // Obtener una asesora pedagógica disponible automáticamente
        $asesoraPedagogica = User::withRole('Asesora Pedagogica')
            ->orderBy('id')
            ->first();

        if (!$asesoraPedagogica) {
            return back()
                ->withErrors(['error' => 'No hay Asesoras Pedagógicas disponibles en el sistema. Por favor contacta con administración.'])
                ->withInput();
        }

        // Crear la solicitud con la fecha actual y asignar automáticamente la asesora pedagógica
        $solicitud = new Solicitud();
        $solicitud->fecha_solicitud = now()->toDateString();
        $solicitud->titulo = $validated['titulo'];
        $solicitud->descripcion = $validated['descripcion'];
        $solicitud->estudiante_id = $validated['estudiante_id'];
        $solicitud->estado = 'Pendiente de entrevista';
        $solicitud->asesor_id = $asesoraPedagogica->id; // Asignar automáticamente una asesora pedagógica
        $solicitud->director_id = $directorId; // Asignado automáticamente según la carrera
        $solicitud->save();

        return redirect()
            ->route('coordinadora.dashboard')
            ->with('status', 'Solicitud registrada correctamente.');
    }
}
