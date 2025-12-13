<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\BloqueoAgenda;
use App\Models\Entrevista;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CoordinadoraAgendaController extends Controller
{
    private string $horaInicioJornada = '07:00';
    private string $horaFinJornada = '21:00';

    public function index(Request $request): View
    {
        $user = $request->user();

        $bloqueos = BloqueoAgenda::where('user_id', $user->id)
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->get();

        // Obtener todas las entrevistas de los estudiantes para esta coordinadora
        $entrevistas = Entrevista::with(['solicitud.estudiante'])
            ->where('asesor_id', $user->id)
            ->orderBy('fecha')
            ->orderBy('fecha_hora_inicio')
            ->get();

        // Preparar datos de entrevistas para el calendario
        $entrevistasCalendario = $entrevistas->map(function ($entrevista) {
            $fecha = optional($entrevista->fecha_hora_inicio ?? $entrevista->fecha)->format('Y-m-d');
            $nombreEstudiante = $entrevista->solicitud->estudiante->nombre ?? 'Estudiante';
            $apellidoEstudiante = $entrevista->solicitud->estudiante->apellido ?? '';
            $nombre = trim($nombreEstudiante . ' ' . $apellidoEstudiante);
            $hora = $entrevista->fecha_hora_inicio ? $entrevista->fecha_hora_inicio->format('H:i') : '';
            $modalidad = $entrevista->modalidad ?? '';
            $full = $hora ? $nombre . ' - ' . $hora : $nombre;
            if ($modalidad) {
                $full .= ' (' . $modalidad . ')';
            }
            
            return [
                'date' => $fecha,
                'label' => $nombre,
                'hora' => $hora,
                'full' => $full,
                'modalidad' => $modalidad,
                'type' => 'entrevista',
            ];
        });

        // Preparar datos de bloqueos para el calendario
        $bloqueosCalendario = $bloqueos->map(function ($bloqueo) {
            $fecha = $bloqueo->fecha->format('Y-m-d');
            $motivo = $bloqueo->motivo ?? 'Bloqueo';
            // Formatear horas sin segundos para consistencia
            $horaInicio = \Carbon\Carbon::parse($bloqueo->hora_inicio)->format('H:i');
            $horaFin = \Carbon\Carbon::parse($bloqueo->hora_fin)->format('H:i');
            $horario = $horaInicio . ' - ' . $horaFin;
            $full = $motivo . ' (' . $horario . ')';
            
            return [
                'date' => $fecha,
                'label' => $motivo,
                'hora' => $horario,
                'full' => $full,
                'type' => 'bloqueo',
            ];
        });

        // Combinar entrevistas y bloqueos
        $eventosCalendario = $entrevistasCalendario->concat($bloqueosCalendario);

        return view('coordinadora.agenda.index', [
            'horarioLaboral' => [
                'inicio' => $this->horaInicioJornada,
                'fin' => $this->horaFinJornada,
            ],
            'bloqueos' => $bloqueos,
            'entrevistas' => $entrevistas,
            'eventosCalendario' => $eventosCalendario,
        ]);
    }

    public function storeBloqueo(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fecha' => ['required', 'date'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'motivo' => ['nullable', 'string', 'max:255'],
        ]);

        BloqueoAgenda::create([
            'user_id' => $request->user()->id,
            'fecha' => $validated['fecha'],
            'hora_inicio' => $validated['hora_inicio'],
            'hora_fin' => $validated['hora_fin'],
            'motivo' => $validated['motivo'] ?? null,
        ]);

        return back()->with('status', 'Bloqueo agregado a la agenda.');
    }

    public function destroyBloqueo(Request $request, BloqueoAgenda $bloqueo): RedirectResponse
    {
        if ($bloqueo->user_id !== $request->user()->id) {
            abort(403);
        }

        $bloqueo->delete();

        return back()->with('status', 'Bloqueo eliminado.');
    }
}
