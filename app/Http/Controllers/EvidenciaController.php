<?php

namespace App\Http\Controllers;

use App\Models\Evidencia;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EvidenciaController extends Controller
{
    public function index()
    {
        $evidencias = Evidencia::with('solicitud.estudiante')->orderByDesc('created_at')->get();

        return view('evidencias.index', compact('evidencias'));
    }

    public function create()
    {
        $solicitudes = Solicitud::with('estudiante')->orderByDesc('fecha_solicitud')->get();

        return view('evidencias.create', compact('solicitudes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'ruta_archivo' => ['nullable', 'string', 'max:255'],
            'solicitud_id' => ['required', 'exists:solicitudes,id'],
        ]);

        Evidencia::create($validated);

        return redirect()->route('evidencias.index')->with('success', 'Evidencia creada correctamente.');
    }

    public function show(Evidencia $evidencia)
    {
        $evidencia->load('solicitud.estudiante');

        return view('evidencias.show', compact('evidencia'));
    }

    public function edit(Evidencia $evidencia)
    {
        $solicitudes = Solicitud::with('estudiante')->orderByDesc('fecha_solicitud')->get();

        return view('evidencias.edit', compact('evidencia', 'solicitudes'));
    }

    public function update(Request $request, Evidencia $evidencia)
    {
        $validated = $request->validate([
            'tipo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'ruta_archivo' => ['nullable', 'string', 'max:255'],
            'solicitud_id' => ['required', 'exists:solicitudes,id'],
        ]);

        $evidencia->update($validated);

        return redirect()->route('evidencias.index')->with('success', 'Evidencia actualizada correctamente.');
    }

    public function destroy(Evidencia $evidencia)
    {
        $evidencia->delete();

        return redirect()->route('evidencias.index')->with('success', 'Evidencia eliminada correctamente.');
    }

    /**
     * Descargar o visualizar un archivo PDF de evidencia
     */
    public function download(Evidencia $evidencia)
    {
        if (!$evidencia->ruta_archivo) {
            abort(404, 'Archivo no encontrado');
        }

        // Cargar relaciones necesarias
        $evidencia->load('solicitud.estudiante');

        // Verificar permisos: estudiantes solo pueden descargar evidencias de sus propias solicitudes
        $user = auth()->user();
        $userRoles = collect([$user->rol?->nombre])
            ->merge($user->roles->pluck('nombre') ?? [])
            ->map(fn($role) => mb_strtolower($role))
            ->unique();

        $isStudent = $userRoles->contains('estudiante');
        
        if ($isStudent && !$user->superuser) {
            $estudiante = $user->estudiante;
            
            // Verificar que la evidencia pertenece a una solicitud del estudiante autenticado
            if (!$estudiante || !$evidencia->solicitud || $evidencia->solicitud->estudiante_id !== $estudiante->id) {
                abort(403, 'No tienes permisos para acceder a esta evidencia.');
            }
        }

        // Intentar primero con el disco 'public'
        if (Storage::disk('public')->exists($evidencia->ruta_archivo)) {
            $filePath = Storage::disk('public')->path($evidencia->ruta_archivo);
            $fileName = basename($evidencia->ruta_archivo);
            
            return response()->file($filePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            ]);
        }

        // Si no está en 'public', intentar con 'local' o ruta absoluta
        $possiblePaths = [
            storage_path('app/public/' . $evidencia->ruta_archivo),
            storage_path('app/private/' . $evidencia->ruta_archivo),
            $evidencia->ruta_archivo, // Ruta absoluta
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path) && is_readable($path)) {
                $fileName = basename($path);
                
                return response()->file($path, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $fileName . '"',
                ]);
            }
        }

        abort(404, 'Archivo no encontrado');
    }
}
