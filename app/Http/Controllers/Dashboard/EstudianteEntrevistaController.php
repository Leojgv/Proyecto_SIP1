<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use App\Models\Solicitud;
use Illuminate\Http\Request;
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

        return view('estudiantes.Dashboard.solicitar-entrevista', [
            'estudiante' => $estudiante,
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
            'autorizacion' => ['accepted'],
        ]);

        if ($estudiante->telefono !== $validated['telefono']) {
            $estudiante->update(['telefono' => $validated['telefono']]);
        }

        $descripcion = '['.$validated['titulo'].'] '.trim($validated['descripcion']);

        Solicitud::create([
            'fecha_solicitud' => now()->toDateString(),
            'descripcion' => $descripcion,
            'estudiante_id' => $estudiante->id,
            // Asignación de asesor y director será posterior (nullable en BD)
            'asesor_id' => null,
            'director_id' => null,
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
}
