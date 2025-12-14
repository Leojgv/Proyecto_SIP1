<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AjusteRazonable;
use App\Models\BloqueoAgenda;
use App\Models\Entrevista;
use App\Models\Estudiante;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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
            ->where(function($query) {
                $query->whereNull('estado')
                      ->orWhere('estado', '!=', 'Pospuesta');
            })
            ->orderBy('fecha')
            ->take(5)
            ->get();

        $casosRecientes = Solicitud::with(['estudiante'])
            ->latest('created_at')
            ->take(5)
            ->get();

        // Estadísticas adicionales
        $entrevistasEsteMes = Entrevista::where('asesor_id', $user->id)
            ->whereYear('fecha', $today->year)
            ->whereMonth('fecha', $today->month)
            ->count();

        $entrevistasPresenciales = Entrevista::where('asesor_id', $user->id)
            ->where('modalidad', 'Presencial')
            ->count();

        $entrevistasVirtuales = Entrevista::where('asesor_id', $user->id)
            ->where('modalidad', 'Virtual')
            ->count();

        $totalEntrevistas = $entrevistasPresenciales + $entrevistasVirtuales;
        $porcentajePresencial = $totalEntrevistas > 0 ? round(($entrevistasPresenciales / $totalEntrevistas) * 100) : 0;
        $porcentajeVirtual = $totalEntrevistas > 0 ? round(($entrevistasVirtuales / $totalEntrevistas) * 100) : 0;

        // Tasa de aprobación de ajustes
        $totalAjustes = AjusteRazonable::count();
        $ajustesAprobados = AjusteRazonable::where('estado', 'Aprobado')->count();
        $tasaAprobacion = $totalAjustes > 0 ? round(($ajustesAprobados / $totalAjustes) * 100) : 0;

        // Tiempo promedio de resolución (días entre creación y aprobación/rechazo)
        $casosResueltos = Solicitud::whereIn('estado', ['Aprobado', 'Rechazado'])
            ->whereNotNull('updated_at')
            ->get();
        $tiempoPromedio = 0;
        if ($casosResueltos->count() > 0) {
            $totalDias = $casosResueltos->sum(function ($solicitud) {
                return $solicitud->created_at->diffInDays($solicitud->updated_at);
            });
            $tiempoPromedio = round($totalDias / $casosResueltos->count());
        }

        // Estudiantes activos (con casos en proceso)
        $estudiantesActivos = Estudiante::whereHas('solicitudes', function ($query) {
            $query->whereIn('estado', [
                'Pendiente de entrevista',
                'Pendiente de formulación del caso',
                'Pendiente de formulación de ajuste',
                'Pendiente de preaprobación',
                'Pendiente de Aprobacion'
            ]);
        })->count();

        // Casos urgentes (pendientes más de 4 días)
        $fechaUrgente = Carbon::now()->subDays(4);
        $casosUrgentes = Solicitud::whereIn('estado', [
            'Pendiente de entrevista',
            'Pendiente de formulación del caso',
            'Pendiente de formulación de ajuste'
        ])
        ->where('created_at', '<=', $fechaUrgente)
        ->count();

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
            'entrevistasEsteMes' => $entrevistasEsteMes,
            'entrevistasPresenciales' => $entrevistasPresenciales,
            'entrevistasVirtuales' => $entrevistasVirtuales,
            'porcentajePresencial' => $porcentajePresencial,
            'porcentajeVirtual' => $porcentajeVirtual,
            'tasaAprobacion' => $tasaAprobacion,
            'tiempoPromedioResolucion' => $tiempoPromedio,
            'estudiantesActivos' => $estudiantesActivos,
            'casosUrgentes' => $casosUrgentes,
        ];

        // Obtener estudiantes para el modal de registro de solicitud
        $estudiantes = Estudiante::with('carrera')
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->get();

        // Obtener todas las entrevistas y bloqueos para el calendario
        $entrevistasCalendario = Entrevista::with(['solicitud.estudiante'])
            ->where('asesor_id', $user->id)
            ->orderBy('fecha')
            ->orderBy('fecha_hora_inicio')
            ->get()
            ->map(function ($entrevista) {
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

        $bloqueosCalendario = BloqueoAgenda::where('user_id', $user->id)
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->get()
            ->map(function ($bloqueo) {
                $fecha = $bloqueo->fecha->format('Y-m-d');
                $motivo = $bloqueo->motivo ?? 'Bloqueo';
                $horario = $bloqueo->hora_inicio . ' - ' . $bloqueo->hora_fin;
                $full = $motivo . ' (' . $horario . ')';
                
                return [
                    'date' => $fecha,
                    'label' => $motivo,
                    'hora' => $horario,
                    'full' => $full,
                    'type' => 'bloqueo',
                ];
            });

        $eventosCalendario = $entrevistasCalendario->concat($bloqueosCalendario);

        return view('coordinadora.dashboard.index', compact(
            'stats',
            'proximasEntrevistas',
            'casosRecientes',
            'pipelineStats',
            'estudiantes',
            'eventosCalendario'
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
