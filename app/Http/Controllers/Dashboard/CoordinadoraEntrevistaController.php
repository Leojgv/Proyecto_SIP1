<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Entrevista;
use App\Models\Solicitud;
use App\Notifications\DashboardNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

class CoordinadoraEntrevistaController extends Controller
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

        return view('coordinadora.entrevistas.index', [
            'entrevistas' => $entrevistas,
            'historialEstudiantes' => $historialEstudiantes,
            'stats' => $stats,
        ]);
    }

    /**
     * Posponer/Devolver una entrevista con motivo
     */
    public function posponer(Request $request, Entrevista $entrevista): RedirectResponse
    {
        $user = $request->user();

        // Verificar que la entrevista pertenezca a la coordinadora
        if ($entrevista->asesor_id !== $user->id) {
            abort(403, 'No tienes permiso para posponer esta entrevista.');
        }

        $validated = $request->validate([
            'motivo_posposicion' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        // Actualizar la entrevista
        $entrevista->update([
            'estado' => 'Pospuesta',
            'motivo_posposicion' => $validated['motivo_posposicion'],
        ]);

        // Cambiar el estado de la solicitud para que el estudiante pueda reagendar
        $solicitud = $entrevista->solicitud;
        if ($solicitud && $solicitud->estado === 'Pendiente de entrevista') {
            // La solicitud ya está en el estado correcto para reagendar
            // No necesitamos cambiar el estado, solo notificar
        }

        // Notificar al estudiante
        $estudiante = $solicitud->estudiante ?? null;
        if ($estudiante && $estudiante->user) {
            $nombreEstudiante = trim(($estudiante->nombre ?? '') . ' ' . ($estudiante->apellido ?? ''));
            $fechaEntrevista = $entrevista->fecha?->format('d/m/Y') ?? 'fecha no especificada';
            $horaEntrevista = $entrevista->fecha_hora_inicio?->format('H:i') ?? 'hora no especificada';
            
            $mensaje = "Tu entrevista programada para el {$fechaEntrevista} a las {$horaEntrevista} ha sido pospuesta. ";
            $mensaje .= "Motivo: {$validated['motivo_posposicion']}. ";
            $mensaje .= "Por favor, solicita una nueva fecha para tu entrevista.";

            Notification::send(
                $estudiante->user,
                new DashboardNotification(
                    'Entrevista Pospuesta',
                    $mensaje,
                    route('estudiantes.dashboard'),
                    'Ver solicitudes'
                )
            );
        }

        return redirect()
            ->route('coordinadora.dashboard')
            ->with('status', 'Entrevista pospuesta correctamente. El estudiante ha sido notificado.');
    }
}
