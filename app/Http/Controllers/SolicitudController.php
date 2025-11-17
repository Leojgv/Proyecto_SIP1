<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Http\Request;

class SolicitudController extends Controller
{
    public function index()
    {
        $solicitudes = Solicitud::with(['estudiante', 'asesor', 'director'])
            ->orderByDesc('fecha_solicitud')
            ->get();

        return view('solicitudes.index', compact('solicitudes'));
    }

    public function create()
    {
        $estudiantes = Estudiante::orderBy('nombre')->orderBy('apellido')->get();
        $asesores = $this->usuariosPorRol('Asesora Pedagogica');
        $directores = $this->usuariosPorRol('Director de carrera');

        return view('solicitudes.create', compact('estudiantes', 'asesores', 'directores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha_solicitud' => ['required', 'date'],
            'descripcion' => ['nullable', 'string'],
            'estado' => ['nullable', 'string', 'max:255'],
            'estudiante_id' => ['required', 'exists:estudiantes,id'],
            'asesor_id' => ['required', 'exists:users,id'],
            'director_id' => ['required', 'exists:users,id'],
        ]);

        Solicitud::create($validated);

        return redirect()->route('solicitudes.index')->with('success', 'Solicitud creada correctamente.');
    }

    public function show(Solicitud $solicitud)
    {
        $solicitud->load(['estudiante', 'asesor', 'director']);

        return view('solicitudes.show', compact('solicitud'));
    }

    public function edit(Solicitud $solicitud)
    {
        $estudiantes = Estudiante::orderBy('nombre')->orderBy('apellido')->get();
        $asesores = $this->usuariosPorRol('Asesora Pedagogica');
        $directores = $this->usuariosPorRol('Director de carrera');

        return view('solicitudes.edit', compact('solicitud', 'estudiantes', 'asesores', 'directores'));
    }

    public function update(Request $request, Solicitud $solicitud)
    {
        $validated = $request->validate([
            'fecha_solicitud' => ['required', 'date'],
            'descripcion' => ['nullable', 'string'],
            'estado' => ['nullable', 'string', 'max:255'],
            'estudiante_id' => ['required', 'exists:estudiantes,id'],
            'asesor_id' => ['required', 'exists:users,id'],
            'director_id' => ['required', 'exists:users,id'],
        ]);

        $solicitud->update($validated);

        return redirect()->route('solicitudes.index')->with('success', 'Solicitud actualizada correctamente.');
    }

    public function destroy(Solicitud $solicitud)
    {
        $solicitud->delete();

        return redirect()->route('solicitudes.index')->with('success', 'Solicitud eliminada correctamente.');
    }

    public function registrarCaso(Request $request, Solicitud $solicitud)
    {
        $solicitud->update([
            'estado' => 'Pendiente de formulación del caso',
            'coordinadora_id' => $request->user()->id,
        ]);

        return back()->with('success', 'Caso registrado y enviado a formulación.');
    }

    public function formularAjuste(Request $request, Solicitud $solicitud)
    {
        $solicitud->update([
            'estado' => 'Pendiente de formulación de ajuste',
            'asesor_tecnico_id' => $request->user()->id,
        ]);

        return back()->with('success', 'Caso enviado para formulación de ajuste.');
    }

    public function preaprobarCaso(Request $request, Solicitud $solicitud)
    {
        $solicitud->update([
            'estado' => 'Pendiente de Aprobación',
            'asesor_pedagogico_id' => $request->user()->id,
        ]);

        return back()->with('success', 'Caso enviado a aprobación del director.');
    }

    public function aprobarCaso(Solicitud $solicitud)
    {
        $solicitud->update(['estado' => 'Aprobado']);

        return back()->with('success', 'Caso aprobado.');
    }

    public function rechazarCaso(Request $request, Solicitud $solicitud)
    {
        $request->validate([
            'motivo_rechazo' => ['required', 'string'],
        ]);

        $solicitud->update([
            'estado' => 'Rechazado',
            'motivo_rechazo' => $request->input('motivo_rechazo'),
        ]);

        return back()->with('success', 'Caso rechazado.');
    }

    public function devolverACoordinadora(Solicitud $solicitud)
    {
        $solicitud->update(['estado' => 'Pendiente de formulación del caso']);

        return back()->with('success', 'Caso devuelto a la coordinadora.');
    }

    public function devolverAAsesorTecnico(Solicitud $solicitud)
    {
        $solicitud->update(['estado' => 'Pendiente de formulación de ajuste']);

        return back()->with('success', 'Caso devuelto a la asesora técnica.');
    }

    private function usuariosPorRol(string $rol)
    {
        return User::withRole($rol)
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->get();
    }
}
