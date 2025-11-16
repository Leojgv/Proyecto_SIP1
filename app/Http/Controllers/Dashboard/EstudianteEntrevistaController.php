<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\BloqueoAgenda;
use App\Models\Estudiante;
use App\Models\Entrevista;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class EstudianteEntrevistaController extends Controller
{
    public function create()
    {
        $estudiante = $this->resolveEstudiante();

        if (! $estudiante) {
            return redirect()
                ->route('estudiantes.dashboard')
                ->with('status', 'Primero completa tu perfil de estudiante para solicitar una entrevista.');
        }

        $estudiante->loadMissing('carrera');

        $coordinadora = $this->resolveCoordinadora();
        $cupos = $coordinadora
            ? $this->calcularCuposDisponibles($coordinadora, now()->startOfDay(), now()->copy()->addWeeks(2)->endOfDay())
            : collect();

        return view('estudiantes.Dashboard.solicitar-entrevista', [
            'estudiante' => $estudiante,
            'cuposDisponibles' => $cupos,
        ]);
    }

    public function store(Request $request)
    {
        $estudiante = $this->resolveEstudiante();

        if (! $estudiante) {
            return redirect()
                ->route('estudiantes.dashboard')
                ->with('status', 'Primero completa tu perfil de estudiante para solicitar una entrevista.');
        }

        $validated = $request->validate([
            'telefono' => ['required', 'string', 'max:255'],
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string'],
            'cupo' => ['required', 'date'],
            'autorizacion' => ['accepted'],
        ]);

        if ($estudiante->telefono !== $validated['telefono']) {
            $estudiante->update(['telefono' => $validated['telefono']]);
        }

        $coordinadora = $this->resolveCoordinadora();
        $cuposDisponibles = $coordinadora
            ? $this->calcularCuposDisponibles($coordinadora, now()->startOfDay(), now()->copy()->addWeeks(2)->endOfDay())
            : collect();

        $cupoSeleccionado = $cuposDisponibles->firstWhere('valor', Carbon::parse($validated['cupo'])->format('Y-m-d H:i'));

        if (! $cupoSeleccionado) {
            return back()
                ->withErrors(['cupo' => 'El cupo seleccionado ya no esta disponible. Por favor elige otro.'])
                ->withInput();
        }

        $inicioEntrevista = $cupoSeleccionado['inicio']->copy();
        $finEntrevista = $cupoSeleccionado['fin']->copy();

        $descripcion = '['.$validated['titulo'].'] '.trim($validated['descripcion']);

        $solicitud = Solicitud::create([
            'fecha_solicitud' => now()->toDateString(),
            'descripcion' => $descripcion,
            'estudiante_id' => $estudiante->id,
            'estado' => 'Pendiente de entrevista',
            // Asignacion de asesor y director sera posterior (nullable en BD)
            'asesor_id' => null,
            'director_id' => null,
        ]);

        Entrevista::create([
            'fecha' => $inicioEntrevista->toDateString(),
            'fecha_hora_inicio' => $inicioEntrevista,
            'fecha_hora_fin' => $finEntrevista,
            'solicitud_id' => $solicitud->id,
            'asesor_id' => $coordinadora?->id,
        ]);

        return redirect()
            ->route('estudiantes.dashboard')
            ->with('status', 'Solicitud de entrevista enviada correctamente.');
    }

    private function resolveEstudiante(): ?Estudiante
    {
        $user = Auth::user();

        if (! $user) {
            return null;
        }

        $estudiante = $user->estudiante;

        if (! $estudiante && $user->email) {
            $coincidencia = Estudiante::where('email', $user->email)->first();

            if ($coincidencia) {
                $coincidencia->user()->associate($user);
                $coincidencia->save();
                $estudiante = $coincidencia;
            }
        }

        return $estudiante;
    }

    private function resolveCoordinadora(): ?User
    {
        return User::withRole('Coordinadora de inclusion')
            ->orderBy('id')
            ->first();
    }

    private function calcularCuposDisponibles(User $coordinadora, Carbon $desde, Carbon $hasta): Collection
    {
        $bloqueos = BloqueoAgenda::where('user_id', $coordinadora->id)
            ->whereBetween('fecha', [$desde->toDateString(), $hasta->toDateString()])
            ->get()
            ->map(fn ($bloqueo) => [
                'inicio' => Carbon::parse($bloqueo->fecha->format('Y-m-d').' '.$bloqueo->hora_inicio),
                'fin' => Carbon::parse($bloqueo->fecha->format('Y-m-d').' '.$bloqueo->hora_fin),
            ]);

        $entrevistas = Entrevista::where('asesor_id', $coordinadora->id)
            ->where(function ($query) use ($desde, $hasta) {
                $query->whereBetween('fecha_hora_inicio', [$desde, $hasta])
                    ->orWhereBetween('fecha_hora_fin', [$desde, $hasta])
                    ->orWhereBetween('fecha', [$desde->toDateString(), $hasta->toDateString()]);
            })
            ->get()
            ->map(function (Entrevista $entrevista) {
                $inicio = $entrevista->fecha_hora_inicio;
                $fin = $entrevista->fecha_hora_fin;

                if (! $inicio && $entrevista->fecha) {
                    $inicio = Carbon::parse($entrevista->fecha)->startOfDay();
                }

                if (! $fin && $inicio) {
                    $fin = $inicio->copy()->addHour();
                }

                return [
                    'inicio' => $inicio,
                    'fin' => $fin,
                ];
            })
            ->filter(fn ($intervalo) => $intervalo['inicio'] && $intervalo['fin']);

        $ocupaciones = $bloqueos->concat($entrevistas);
        $cupos = collect();
        $horaInicioJornada = '07:00';
        $horaFinJornada = '21:00';

        for ($fecha = $desde->copy(); $fecha->lessThanOrEqualTo($hasta); $fecha->addDay()) {
            if ($fecha->isWeekend()) {
                continue;
            }

            $inicioDia = $fecha->copy()->setTimeFromTimeString($horaInicioJornada);
            $finDia = $fecha->copy()->setTimeFromTimeString($horaFinJornada);

            $cupoInicio = $inicioDia->copy();
            while ($cupoInicio->copy()->addMinutes(45)->lte($finDia)) {
                $cupoFin = $cupoInicio->copy()->addMinutes(45);

                if (! $this->estaOcupado($cupoInicio, $cupoFin, $ocupaciones)) {
                    $inicioParaLabel = $cupoInicio->copy()->locale('es');

                    $cupos->push([
                        'valor' => $cupoInicio->format('Y-m-d H:i'),
                        'inicio' => $cupoInicio->copy(),
                        'fin' => $cupoFin->copy(),
                        'label' => $inicioParaLabel->translatedFormat('l j \\de F \\a \\las H:i'),
                    ]);
                }

                $cupoInicio->addHour(); // 45 mins entrevista + 15 mins de descanso
            }
        }

        return $cupos;
    }

    private function estaOcupado(Carbon $inicio, Carbon $fin, Collection $ocupaciones): bool
    {
        return $ocupaciones->contains(function ($intervalo) use ($inicio, $fin) {
            return $inicio->lt($intervalo['fin']) && $fin->gt($intervalo['inicio']);
        });
    }
}
