<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\Rol;
use App\Models\User;
use App\Notifications\DashboardNotification;
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
}

