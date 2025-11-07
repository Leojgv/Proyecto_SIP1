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
                if ($this->usersTableHasEstudianteId()) {
                    $user->estudiante()->associate($estudianteRelacionado);
                    $user->save();
                }

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
        $focus = $request->query('focus');

        $solicitudesActivas = $estudiante->solicitudes()
            ->where(function ($query) {
                $query->whereNull('estado')
                    ->orWhereIn('estado', ['pendiente', 'en_proceso', 'en proceso', 'activa']);
            })
            ->count();

        $solicitudesPendientes = $estudiante->solicitudes()
            ->where(fn ($query) => $query->whereNull('estado')->orWhere('estado', 'pendiente'))
            ->count();

        $ajustesActivos = $estudiante->ajustesRazonables()
            ->where(function ($query) {
                $query->whereNull('estado')
                    ->orWhereIn('estado', ['pendiente', 'activo', 'en curso']);
            })
            ->count();

        $cursosConAjustes = $estudiante->ajustesRazonables()
            ->whereNotNull('nombre')
            ->distinct('nombre')
            ->count('nombre');

        $proximasEntrevistas = Entrevista::with(['solicitud', 'asesorPedagogico'])
            ->whereHas('solicitud', function ($query) use ($estudiante) {
                $query->where('estudiante_id', $estudiante->id);
            })
            ->whereDate('fecha', '>=', $hoy)
            ->orderBy('fecha')
            ->take(5)
            ->get();

        $misSolicitudes = $estudiante->solicitudes()
            ->with(['asesorPedagogico', 'directorCarrera'])
            ->orderByDesc('fecha_solicitud')
            ->take(5)
            ->get();

        $misAjustes = $estudiante->ajustesRazonables()
            ->with('solicitud')
            ->orderByDesc('fecha_solicitud')
            ->take(5)
            ->get();

        $notificaciones = collect();
        $notificacionesSinLeer = 0;

        if ($user && $this->notificationsTableExists()) {
            $notificaciones = $user->notifications()
                ->latest()
                ->take(6)
                ->get();

            $notificacionesSinLeer = $user->unreadNotifications()->count();
        }

        return view('estudiantes.dashboard.index', [
            'estudiante' => $estudiante,
            'stats' => [
                'solicitudes_activas' => $solicitudesActivas,
                'ajustes_activos' => $ajustesActivos,
                'entrevistas_programadas' => $proximasEntrevistas->count(),
                'cursos_con_ajustes' => $cursosConAjustes,
                'problemas_detectados' => $solicitudesPendientes,
            ],
            'proximasEntrevistas' => $proximasEntrevistas,
            'misSolicitudes' => $misSolicitudes,
            'misAjustes' => $misAjustes,
            'hoy' => $hoy,
            'notificaciones' => $notificaciones,
            'notificacionesSinLeer' => $notificacionesSinLeer,
            'focus' => $focus,
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
            if ($this->usersTableHasEstudianteId()) {
                $user->estudiante()->associate($estudianteRelacionado);
                $user->save();
            }

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
            ]);

            if ($this->usersTableHasEstudianteId()) {
                $user->estudiante()->associate($estudiante);
                $user->save();
            }
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

    private function usersTableHasEstudianteId(): bool
    {
        static $cache;

        if ($cache === null) {
            $cache = \Illuminate\Support\Facades\Schema::hasColumn('users', 'estudiante_id');
        }

        return $cache;
    }

    private function notificationsTableExists(): bool
    {
        static $cache;

        if ($cache === null) {
            $cache = \Illuminate\Support\Facades\Schema::hasTable('notifications');
        }

        return $cache;
    }
}