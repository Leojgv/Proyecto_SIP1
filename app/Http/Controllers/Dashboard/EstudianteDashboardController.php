<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\Entrevista;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EstudianteDashboardController extends Controller
{
    /**
     * Display the dashboard for the authenticated student.
     */
    public function show(Request $request)
    {
        $user = Auth::user();
        $estudiante = $user?->estudiante;

        if (! $estudiante && $user) {
            $estudianteRelacionado = Estudiante::where('email', $user->email)->first();

            if ($estudianteRelacionado) {
                $estudianteRelacionado->user()->associate($user);
                $estudianteRelacionado->save();
                $estudiante = $estudianteRelacionado;
            }
        }

        if (! $estudiante) {
            return view('estudiantes.dashboard.crear-perfil', [
                'user' => $user,
                'carreras' => Carrera::orderBy('nombre')->get(),
            ]);
        }

        $estudiante->loadMissing('carrera');

        $hoy = Carbon::today();

        $solicitudesActivas = $estudiante->solicitudes()
            ->where(function ($query) {
                $query->whereNull('estado')
                    ->orWhereIn('estado', ['pendiente', 'en_proceso', 'en proceso', 'activa']);
            })
            ->count();

        $solicitudesPendientes = $estudiante->solicitudes()
            ->where(fn ($query) => $query->whereNull('estado')->orWhere('estado', 'pendiente'))
            ->count();

        // Contar ajustes rechazados para sumarlos a problemas detectados
        $ajustesRechazados = $estudiante->ajustesRazonables()
            ->where('estado', 'Rechazado')
            ->count();

        $ajustesActivos = $estudiante->ajustesRazonables()
            ->where(function ($query) {
                $query->whereNull('estado')
                    ->orWhereIn('estado', ['pendiente', 'activo', 'en curso', 'Aprobado']);
            })
            ->count();

        $cursosConAjustes = $estudiante->ajustesRazonables()
            ->whereNotNull('nombre')
            ->distinct('nombre')
            ->count('nombre');

        $solicitudesRealizadas = $estudiante->solicitudes()->count();

        $proximasEntrevistas = Entrevista::with(['solicitud', 'asesor'])
            ->whereHas('solicitud', function ($query) use ($estudiante) {
                $query->where('estudiante_id', $estudiante->id);
            })
            ->whereDate('fecha', '>=', $hoy)
            ->orderBy('fecha')
            ->take(5)
            ->get();

        $misSolicitudes = $estudiante->solicitudes()
            ->with(['asesor', 'director', 'ajustesRazonables', 'entrevistas.asesor'])
            ->orderByDesc('fecha_solicitud')
            ->take(5)
            ->get();

        // Solo ajustes aprobados y activos (no rechazados)
        $misAjustes = $estudiante->ajustesRazonables()
            ->with('solicitud')
            ->whereHas('solicitud', function ($query) {
                $query->where('estado', '!=', 'Rechazado');
            })
            ->where('estado', '!=', 'Rechazado')
            ->orderByDesc('fecha_solicitud')
            ->take(5)
            ->get();

        // Ajustes rechazados (para mostrar en problemas detectados)
        // Mostrar todos los ajustes rechazados, tengan o no motivo
        $ajustesRechazadosList = $estudiante->ajustesRazonables()
            ->with('solicitud')
            ->where('estado', 'Rechazado')
            ->orderByDesc('updated_at')
            ->get();

        return view('estudiantes.dashboard.index', [
            'estudiante' => $estudiante,
            'stats' => [
                'solicitudes_activas' => $solicitudesActivas,
                'ajustes_activos' => $ajustesActivos,
                'entrevistas_programadas' => $proximasEntrevistas->count(),
                'cursos_con_ajustes' => $cursosConAjustes,
                'problemas_detectados' => $solicitudesPendientes + $ajustesRechazados, // Sumar ajustes rechazados a problemas detectados
                'solicitudes_realizadas' => $solicitudesRealizadas,
            ],
            'proximasEntrevistas' => $proximasEntrevistas,
            'misSolicitudes' => $misSolicitudes,
            'misAjustes' => $misAjustes,
            'ajustesRechazados' => $ajustesRechazadosList,
            'hoy' => $hoy,
        ]);
    }

    /**
     * Permite crear un perfil de estudiante vinculado al usuario autenticado.
     */
    public function storeProfile(Request $request)
    {
        $user = Auth::user();

        if (! $user) {
            abort(403);
        }

        if ($user->estudiante) {
            return redirect()->route('estudiantes.dashboard');
        }

        $estudianteRelacionado = Estudiante::where('email', $user->email)->first();

        if ($estudianteRelacionado) {
            $estudianteRelacionado->user()->associate($user);
            $estudianteRelacionado->save();

            return redirect()->route('estudiantes.dashboard');
        }

        $validated = $request->validate([
            'rut' => ['required', 'string', 'max:20', 'unique:estudiantes,rut'],
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:255'],
            'carrera_id' => ['required', 'exists:carreras,id'],
        ]);

        DB::transaction(function () use ($validated, $user) {
            $estudiante = Estudiante::create([
                'rut' => $validated['rut'],
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'telefono' => $validated['telefono'] ?? null,
                'carrera_id' => $validated['carrera_id'],
                'email' => $user->email,
                'user_id' => $user->id,
            ]);
        });

        return redirect()
            ->route('estudiantes.dashboard')
            ->with('status', 'Tu perfil de estudiante fue creado correctamente.');
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        $estudiante = $user?->estudiante;

        if (! $user || ! $estudiante) {
            abort(403);
        }

        $validated = $request->validate([
            'telefono' => ['nullable', 'string', 'max:255'],
        ]);

        $estudiante->update([
            'telefono' => $validated['telefono'] ?? null,
        ]);

        return redirect()
            ->route('estudiantes.dashboard', ['focus' => 'configuracion'])
            ->with('status', 'Se guardaron tus datos de contacto.');
    }

    protected function countNotifications($user): int
    {
        if (!$user) {
            return 0;
        }

        return \App\Models\Notificacion::query()
            ->where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->count();
    }

    protected function getRecentNotifications($user): array
    {
        if (!$user) {
            return [];
        }

        return \App\Models\Notificacion::query()
            ->where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->latest('created_at')
            ->take(5)
            ->get()
            ->map(function (\App\Models\Notificacion $notification) {
                $data = $notification->data ?? [];

                return [
                    'id' => $notification->id,
                    'title' => $data['titulo'] ?? ($data['title'] ?? ($data['subject'] ?? 'Notificación')),
                    'message' => $data['mensaje'] ?? ($data['message'] ?? ($data['body'] ?? 'Nueva actualización disponible.')),
                    'url' => $data['url'] ?? null,
                    'button_text' => $data['texto_boton'] ?? ($data['textoBoton'] ?? null),
                    'time' => optional($notification->created_at)->diffForHumans() ?? 'hace instantes',
                    'read_at' => $notification->read_at,
                ];
            })
            ->values()
            ->all();
    }
}
