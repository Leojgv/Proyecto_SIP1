<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\BloqueoAgenda;
use App\Models\Estudiante;
use App\Models\Entrevista;
use App\Models\Evidencia;
use App\Models\Solicitud;
use App\Models\User;
use App\Notifications\DashboardNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

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

        return view('estudiantes.dashboard.solicitar-entrevista', [
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
            'modalidad' => ['required', 'string', 'in:Virtual,Presencial'],
            'cupo' => ['required', 'date_format:Y-m-d H:i'],
            'autorizacion' => ['accepted'],
            'archivos.*' => ['nullable', 'file', 'mimes:pdf', 'max:10240'], // Máximo 10MB por archivo
            'tiene_acompanante' => ['nullable', 'boolean'],
            'acompanante_rut' => ['nullable', 'required_if:tiene_acompanante,1', 'string', 'max:20'],
            'acompanante_nombre' => ['nullable', 'required_if:tiene_acompanante,1', 'string', 'max:255'],
            'acompanante_telefono' => ['nullable', 'required_if:tiene_acompanante,1', 'string', 'max:20'],
        ], [
            'archivos.*.mimes' => 'Solo se permiten archivos PDF.',
            'archivos.*.max' => 'Cada archivo no puede exceder 10MB.',
            'acompanante_rut.required_if' => 'El RUT del acompañante es requerido cuando se indica que hay acompañante.',
            'acompanante_nombre.required_if' => 'El nombre del acompañante es requerido cuando se indica que hay acompañante.',
            'acompanante_telefono.required_if' => 'El teléfono del acompañante es requerido cuando se indica que hay acompañante.',
        ]);

        // Validar cantidad máxima de archivos
        if ($request->hasFile('archivos')) {
            $archivos = $request->file('archivos');
            if (count($archivos) > 5) {
                return back()
                    ->withErrors(['archivos' => 'No puedes adjuntar más de 5 archivos.'])
                    ->withInput();
            }
        }

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

        // Obtener el director automáticamente según la carrera del estudiante
        $estudiante->load('carrera');
        $directorId = $estudiante->carrera?->director_id;

        if (!$directorId) {
            return back()
                ->withErrors(['cupo' => 'No se ha asignado un Director de Carrera para tu carrera. Por favor contacta con administración.'])
                ->withInput();
        }

        // Obtener una asesora pedagógica disponible automáticamente
        $asesoraPedagogica = $this->resolveAsesoraPedagogica();

        if (!$asesoraPedagogica) {
            return back()
                ->withErrors(['cupo' => 'No hay Asesoras Pedagógicas disponibles en el sistema. Por favor contacta con administración.'])
                ->withInput();
        }

        $solicitud = Solicitud::create([
            'fecha_solicitud' => now()->toDateString(),
            'titulo' => $validated['titulo'],
            'descripcion' => trim($validated['descripcion']),
            'estudiante_id' => $estudiante->id,
            'estado' => 'Pendiente de entrevista',
            'asesor_id' => $asesoraPedagogica->id, // Asignado automáticamente
            'director_id' => $directorId, // Asignado automáticamente según la carrera
        ]);

        Entrevista::create([
            'fecha' => $inicioEntrevista->toDateString(),
            'fecha_hora_inicio' => $inicioEntrevista,
            'fecha_hora_fin' => $finEntrevista,
            'modalidad' => $validated['modalidad'],
            'solicitud_id' => $solicitud->id,
            'asesor_id' => $coordinadora?->id,
            'tiene_acompanante' => $validated['tiene_acompanante'] ?? false,
            'acompanante_rut' => ($validated['tiene_acompanante'] ?? false) ? ($validated['acompanante_rut'] ?? null) : null,
            'acompanante_nombre' => ($validated['tiene_acompanante'] ?? false) ? ($validated['acompanante_nombre'] ?? null) : null,
            'acompanante_telefono' => ($validated['tiene_acompanante'] ?? false) ? ($validated['acompanante_telefono'] ?? null) : null,
        ]);

        // Guardar archivos adjuntos si existen
        if ($request->hasFile('archivos')) {
            foreach ($request->file('archivos') as $archivo) {
                // Generar nombre único para el archivo
                $nombreArchivo = time() . '_' . uniqid() . '_' . $archivo->getClientOriginalName();
                
                // Guardar el archivo en storage/app/public/evidencias
                $rutaArchivo = $archivo->storeAs('evidencias', $nombreArchivo, 'public');
                
                // Crear registro de evidencia
                Evidencia::create([
                    'tipo' => 'Documentos Adicionales',
                    'descripcion' => 'Archivo adjunto en solicitud de entrevista: ' . $archivo->getClientOriginalName(),
                    'ruta_archivo' => $rutaArchivo,
                    'solicitud_id' => $solicitud->id,
                ]);
            }
        }

        // Notificar al estudiante que su solicitud fue enviada
        $estudianteUser = $estudiante->user;
        if ($estudianteUser) {
            Notification::send(
                $estudianteUser,
                new DashboardNotification(
                    'Solicitud Enviada',
                    'Tu solicitud de entrevista ha sido enviada correctamente. El equipo de asesoría pedagógica la revisará pronto.',
                    route('estudiantes.dashboard'),
                    'Ver mi dashboard'
                )
            );
        }

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

    private function resolveAsesoraPedagogica(): ?User
    {
        return User::withRole('Asesora Pedagogica')
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

    /**
     * Obtiene los horarios disponibles para un día específico (AJAX)
     */
    public function getHorariosPorFecha(Request $request)
    {
        $request->validate([
            'fecha' => ['required', 'date'],
        ]);

        $fecha = Carbon::parse($request->fecha);
        $coordinadora = $this->resolveCoordinadora();

        if (!$coordinadora) {
            return response()->json([
                'success' => false,
                'message' => 'No hay coordinadora disponible.',
                'horarios' => [],
            ]);
        }

        // Calcular horarios disponibles solo para ese día
        $inicioDia = $fecha->copy()->startOfDay();
        $finDia = $fecha->copy()->endOfDay();

        $cupos = $this->calcularCuposDisponibles($coordinadora, $inicioDia, $finDia);

        // Formatear horarios para el frontend
        $horarios = $cupos->map(function ($cupo) {
            return [
                'valor' => $cupo['valor'],
                'hora' => $cupo['inicio']->format('H:i'),
                'label' => $cupo['inicio']->format('H:i') . ' - ' . $cupo['fin']->format('H:i'),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'fecha' => $fecha->format('Y-m-d'),
            'fecha_formateada' => $fecha->locale('es')->translatedFormat('l j \\de F \\de Y'),
            'horarios' => $horarios,
        ]);
    }

    /**
     * Obtiene los días con disponibilidad para el calendario (AJAX)
     */
    public function getDiasDisponibles(Request $request)
    {
        $coordinadora = $this->resolveCoordinadora();

        if (!$coordinadora) {
            return response()->json([
                'success' => false,
                'message' => 'No hay coordinadora disponible.',
                'dias' => [],
            ]);
        }

        // Calcular cupos disponibles para las próximas 4 semanas
        $desde = now()->startOfDay();
        $hasta = now()->copy()->addWeeks(4)->endOfDay();

        $cupos = $this->calcularCuposDisponibles($coordinadora, $desde, $hasta);

        // Agrupar por fecha y obtener días con disponibilidad
        $diasDisponibles = $cupos->groupBy(function ($cupo) {
            return $cupo['inicio']->format('Y-m-d');
        })->map(function ($cuposDia, $fecha) {
            return [
                'fecha' => $fecha,
                'cantidad' => $cuposDia->count(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'dias' => $diasDisponibles,
        ]);
    }
}
