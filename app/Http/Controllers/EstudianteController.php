<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Docente;
use App\Models\Estudiante;
use App\Models\Rol;
use App\Models\User;
use App\Notifications\DashboardNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class EstudianteController extends Controller
{
    public function index()
    {
        $estudiantes = Estudiante::with('carrera')->orderBy('nombre')->orderBy('apellido')->paginate(10);
        $carreras = Carrera::orderBy('nombre')->get();
        
        $totalEstudiantes = Estudiante::count();
        $nuevosEsteMes = Estudiante::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $estudiantesPorCarrera = Estudiante::with('carrera')
            ->get()
            ->groupBy('carrera_id')
            ->map(function ($group) {
                return [
                    'nombre' => $group->first()->carrera->nombre ?? 'Sin carrera',
                    'cantidad' => $group->count()
                ];
            })
            ->values();

        return view('estudiantes.index', compact(
            'estudiantes',
            'carreras',
            'totalEstudiantes',
            'nuevosEsteMes',
            'estudiantesPorCarrera'
        ));
    }

    public function create()
    {
        $carreras = Carrera::orderBy('nombre')->get();

        return view('estudiantes.create', compact('carreras'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rut' => ['required', 'string', 'max:20', 'unique:estudiantes,rut'],
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:estudiantes,email'],
            'telefono' => ['nullable', 'string', 'max:255'],
            'carrera_id' => ['required', 'exists:carreras,id'],
        ]);

        // Verificar si ya existe un usuario con este email
        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            // Crear nuevo usuario para el estudiante
            $password = Hash::make('password123'); // Contraseña por defecto
            $user = User::create([
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'email' => $validated['email'],
                'password' => $password,
            ]);

            // Asignar rol de Estudiante
            $rolEstudiante = Rol::where('nombre', 'Estudiante')->first();
            if ($rolEstudiante) {
                $user->rol_id = $rolEstudiante->id;
                $user->save();
                if (!$user->roles->contains($rolEstudiante->id)) {
                    $user->roles()->attach($rolEstudiante->id);
                }
            }
        }

        // Crear el estudiante con el user_id
        $estudiante = Estudiante::create($validated + [
            'user_id' => $user->id,
        ]);

        // Notificar a docentes de la carrera sobre el nuevo estudiante
        $this->notifyTeachersNewStudent($estudiante);

        return redirect()->route('estudiantes.index')->with('success', 'Estudiante creado correctamente.');
    }

    /**
     * Notifica a los docentes de la carrera cuando se crea un nuevo estudiante
     */
    protected function notifyTeachersNewStudent(Estudiante $estudiante): void
    {
        if (!$estudiante->carrera_id) {
            return;
        }

        // Obtener docentes de la misma carrera
        $docentes = Docente::where('carrera_id', $estudiante->carrera_id)
            ->with('user')
            ->get()
            ->pluck('user')
            ->filter();

        if ($docentes->isEmpty()) {
            return;
        }

        $nombreEstudiante = trim(($estudiante->nombre ?? '') . ' ' . ($estudiante->apellido ?? ''));
        $rutEstudiante = $estudiante->rut ?? '';

        $mensaje = "Se ha registrado un nuevo estudiante en tu carrera. ";
        $mensaje .= "Estudiante: {$nombreEstudiante}";
        if ($rutEstudiante) {
            $mensaje .= " (RUT: {$rutEstudiante})";
        }
        $mensaje .= ". Por favor, revisa tu lista de estudiantes.";

        Notification::send(
            $docentes,
            new DashboardNotification(
                'Nuevo Estudiante',
                $mensaje,
                route('docente.estudiantes'),
                'Ver estudiantes',
                $estudiante->carrera_id // Incluir carrera_id para filtrar notificaciones
            )
        );
    }

    public function show(Estudiante $estudiante)
    {
        $estudiante->load('carrera');

        return view('estudiantes.show', compact('estudiante'));
    }

    public function edit(Estudiante $estudiante)
    {
        $carreras = Carrera::orderBy('nombre')->get();

        return view('estudiantes.edit', compact('estudiante', 'carreras'));
    }

    public function update(Request $request, Estudiante $estudiante)
    {
        $validated = $request->validate([
            'rut' => ['required', 'string', 'max:20', 'unique:estudiantes,rut,' . $estudiante->id],
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:estudiantes,email,' . $estudiante->id],
            'telefono' => ['nullable', 'string', 'max:255'],
            'carrera_id' => ['required', 'exists:carreras,id'],
        ]);

        $estudiante->update($validated);

        return redirect()->route('estudiantes.index')->with('success', 'Estudiante actualizado correctamente.');
    }

    public function destroy(Estudiante $estudiante)
    {
        $estudiante->delete();

        return redirect()->route('estudiantes.index')->with('success', 'Estudiante eliminado correctamente.');
    }
}
