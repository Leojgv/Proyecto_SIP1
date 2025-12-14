<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\Rol;
use App\Models\User;
use App\Notifications\DashboardNotification;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class NotificacionController extends Controller
{
    public function index()
    {
        $notificaciones = Notificacion::with('notifiable')
            ->latest()
            ->paginate(15);

        return view('notificaciones.index', compact('notificaciones'));
    }

    public function create()
    {
        $roles = Rol::orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();

        return view('notificaciones.create', compact('roles', 'usuarios'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'mensaje' => ['required', 'string'],
            'url' => ['nullable', 'url'],
            'texto_boton' => ['nullable', 'string', 'max:255'],
            'audiencia' => ['required', 'in:todos,rol,usuarios'],
            'rol_id' => ['required_if:audiencia,rol', 'nullable', 'exists:roles,id'],
            'user_ids' => ['required_if:audiencia,usuarios', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $destinatarios = collect();

        switch ($validated['audiencia']) {
            case 'rol':
                $destinatarios = User::where('rol_id', $validated['rol_id'])->get();
                break;
            case 'usuarios':
                $destinatarios = User::whereIn('id', $validated['user_ids'])->get();
                break;
            default:
                $destinatarios = User::all();
                break;
        }

        if ($destinatarios->isEmpty()) {
            return back()->withErrors([
                'audiencia' => 'No se encontraron destinatarios para la audiencia seleccionada.',
            ])->withInput();
        }

        NotificationFacade::send(
            $destinatarios,
            new DashboardNotification(
                $validated['titulo'],
                $validated['mensaje'],
                $validated['url'] ?? null,
                $validated['texto_boton'] ?? null,
            )
        );

        return redirect()
            ->route('notificaciones.index')
            ->with('success', 'Notificación enviada correctamente.');
    }

    public function show(Notificacion $notificacion)
    {
        $notificacion->load('notifiable');

        return view('notificaciones.show', compact('notificacion'));
    }

    public function destroy(Notificacion $notificacion): RedirectResponse
    {
        $notificacion->delete();

        return redirect()
            ->route('notificaciones.index')
            ->with('success', 'Notificación eliminada.');
    }

    /**
     * Obtener notificaciones del usuario autenticado vía AJAX
     */
    public function getNotifications(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['notifications' => [], 'count' => 0]);
        }

        // Si el usuario es un docente, filtrar notificaciones por su carrera actual
        $carreraId = null;
        if ($user->docente && $user->docente->carrera_id) {
            $carreraId = $user->docente->carrera_id;
        }

        $query = Notificacion::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id);

        $allNotifications = (clone $query)
            ->latest('created_at')
            ->take(50) // Obtener más para filtrar después
            ->get();

        // Filtrar por carrera si es docente
        // Para docentes, SOLO mostrar notificaciones de su carrera actual
        // No mostrar notificaciones sin carrera_id (estas son de otras carreras o antiguas)
        if ($carreraId !== null) {
            $allNotifications = $allNotifications->filter(function ($notification) use ($carreraId) {
                // Acceder a los datos de la notificación
                $data = is_array($notification->data) ? $notification->data : (is_object($notification->data) ? (array)$notification->data : []);
                
                // Obtener carrera_id de la notificación
                $notificacionCarreraId = $data['carrera_id'] ?? null;
                
                // Si la notificación no tiene carrera_id, es una notificación antigua o general
                // Para docentes, NO mostrar estas notificaciones (solo de su carrera actual)
                if ($notificacionCarreraId === null) {
                    return false;
                }
                
                // Solo mostrar notificaciones que tengan carrera_id Y que coincida con la carrera actual del docente
                return (int)$notificacionCarreraId === (int)$carreraId;
            });
        }

        $notificationsCount = $allNotifications
            ->where('read_at', null)
            ->count();

        $recentNotifications = $allNotifications
            ->take(10)
            ->values()
            ->map(function ($notification) {
                $data = $notification->data ?? [];
                
                // Formatear tiempo en español
                $time = 'hace instantes';
                if ($notification->created_at) {
                    $time = $notification->created_at->locale('es')->diffForHumans();
                }
                
                return [
                    'id' => $notification->id,
                    'title' => $data['titulo'] ?? ($data['title'] ?? ($data['subject'] ?? 'Notificación')),
                    'message' => $data['mensaje'] ?? ($data['message'] ?? ($data['body'] ?? 'Nueva actualización disponible.')),
                    'url' => $data['url'] ?? null,
                    'button_text' => $data['texto_boton'] ?? ($data['textoBoton'] ?? null),
                    'time' => $time,
                    'read_at' => $notification->read_at,
                ];
            })
            ->values()
            ->all();

        return response()->json([
            'notifications' => $recentNotifications,
            'count' => $notificationsCount,
        ]);
    }

    /**
     * Marcar una notificación como leída
     */
    public function markAsRead(Request $request, string $notificationId)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['success' => false], 401);
        }

        $notification = Notificacion::where('id', $notificationId)
            ->where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->first();

        if ($notification && !$notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        return response()->json(['success' => true]);
    }
}

