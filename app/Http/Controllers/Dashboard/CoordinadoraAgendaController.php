<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\BloqueoAgenda;
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

        return view('coordinadora.agenda.index', [
            'horarioLaboral' => [
                'inicio' => $this->horaInicioJornada,
                'fin' => $this->horaFinJornada,
            ],
            'bloqueos' => $bloqueos,
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
