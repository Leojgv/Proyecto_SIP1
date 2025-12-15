<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Imports\EstudiantesImport;
use App\Models\Carrera;
use App\Models\Estudiante;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class DirectorCarreraEstudianteController extends Controller
{
    public function index(Request $request)
    {
        $directorId = $request->user()->id;

        // Obtener estudiantes con carrera asignada que pertenecen al director
        $estudiantesConCarrera = Estudiante::with('carrera')
            ->whereHas('carrera', fn ($query) => $query->where('director_id', $directorId))
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->get();

        // Obtener IDs de usuarios que ya tienen registro en estudiantes con carrera asignada
        $userIdsConCarrera = $estudiantesConCarrera->pluck('user_id')->filter()->unique()->toArray();

        // Obtener usuarios con rol Estudiante que no están en la lista de estudiantes con carrera
        $rolEstudiante = Rol::where('nombre', 'Estudiante')->first();
        $usuariosSinCarrera = collect();
        
        if ($rolEstudiante) {
            $usuariosSinCarrera = User::where(function ($query) use ($rolEstudiante) {
                $query->where('rol_id', $rolEstudiante->id)
                    ->orWhereHas('roles', fn ($q) => $q->where('roles.id', $rolEstudiante->id));
            })
            ->whereNotIn('id', $userIdsConCarrera) // Excluir usuarios que ya están en la lista de con carrera
            ->where(function ($query) use ($directorId) {
                // Usuarios sin registro en estudiantes
                $query->whereDoesntHave('estudiante')
                    // O usuarios con registro en estudiantes pero sin carrera válida
                    ->orWhereHas('estudiante', function ($q) use ($directorId) {
                        $q->where(function ($subQ) use ($directorId) {
                            $subQ->whereNull('carrera_id')
                                ->orWhereDoesntHave('carrera')
                                ->orWhereHas('carrera', fn ($carreraQ) => $carreraQ->where('director_id', '!=', $directorId));
                        });
                    });
            })
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->get();
        }

        // Combinar ambas colecciones y paginar
        $todosLosEstudiantes = $estudiantesConCarrera->concat($usuariosSinCarrera->map(function ($user) {
            return (object) [
                'id' => 'user_' . $user->id,
                'nombre' => $user->nombre,
                'apellido' => $user->apellido,
                'email' => $user->email,
                'rut' => null,
                'telefono' => null,
                'carrera' => null,
                'carrera_id' => null,
                'user' => $user,
                'user_id' => $user->id,
                'es_usuario_sin_carrera' => true,
            ];
        }));

        // Paginar manualmente
        $currentPage = request()->get('page', 1);
        $perPage = 12;
        $items = $todosLosEstudiantes->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $todosLosEstudiantes->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Obtener solo las carreras del director para el modal de edición
        $carreras = Carrera::where('director_id', $directorId)
            ->orderBy('nombre')
            ->get();

        return view('DirectorCarrera.estudiantes.index', [
            'estudiantes' => $paginated,
            'carreras' => $carreras,
        ]);
    }

    /**
     * Muestra el formulario para cargar el archivo Excel
     */
    public function showImportForm(Request $request): View
    {
        return view('DirectorCarrera.estudiantes.import');
    }

    /**
     * Procesa la carga masiva de estudiantes desde un archivo Excel
     */
    public function import(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'archivo' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'], // Maximo 10MB
        ]);

        try {
            $directorId = $request->user()->id;

            // Ejecutar la importacion pasando el ID del director
            $import = new EstudiantesImport($directorId);
            Excel::import($import, $request->file('archivo'));

            // Notificar a docentes después de la importación
            $import->notifyTeachersAfterImport();

            return redirect()
                ->route('director.estudiantes')
                ->with('status', 'Estudiantes cargados exitosamente desde el archivo Excel.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            return redirect()
                ->back()
                ->withErrors(['archivo' => 'Error en la validacion del archivo. Revisa los datos.'])
                ->with('import_errors', $failures)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al procesar el archivo: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Muestra el formulario para editar un estudiante
     */
    public function edit(Request $request, Estudiante $estudiante): View
    {
        $directorId = $request->user()->id;

        // Verificar que el estudiante pertenezca a una carrera del director
        if (!$estudiante->carrera || $estudiante->carrera->director_id !== $directorId) {
            abort(403, 'No tienes permiso para editar este estudiante.');
        }

        $carreras = Carrera::where('director_id', $directorId)
            ->orderBy('nombre')
            ->get();

        return view('DirectorCarrera.estudiantes.edit', compact('estudiante', 'carreras'));
    }

    /**
     * Actualiza un estudiante
     */
    public function update(Request $request, Estudiante $estudiante): RedirectResponse
    {
        $directorId = $request->user()->id;

        // Verificar que el estudiante pertenezca a una carrera del director
        if (!$estudiante->carrera || $estudiante->carrera->director_id !== $directorId) {
            abort(403, 'No tienes permiso para editar este estudiante.');
        }

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:estudiantes,email,' . $estudiante->id],
            'telefono' => ['nullable', 'string', 'max:255'],
            'carrera_id' => ['required', 'exists:carreras,id'],
        ]);

        // Verificar que la carrera pertenezca al director
        $carrera = Carrera::where('id', $validated['carrera_id'])
            ->where('director_id', $directorId)
            ->first();

        if (!$carrera) {
            return redirect()
                ->back()
                ->withErrors(['carrera_id' => 'La carrera seleccionada no pertenece a tu área de dirección.'])
                ->withInput();
        }

        $estudiante->update($validated);

        return redirect()
            ->route('director.estudiantes')
            ->with('status', 'Estudiante actualizado correctamente.');
    }

    /**
     * Elimina un estudiante
     */
    public function destroy(Request $request, Estudiante $estudiante): RedirectResponse
    {
        $directorId = $request->user()->id;

        // Verificar que el estudiante pertenezca a una carrera del director
        if (!$estudiante->carrera || $estudiante->carrera->director_id !== $directorId) {
            abort(403, 'No tienes permiso para eliminar este estudiante.');
        }

        $estudiante->delete();

        return redirect()
            ->route('director.estudiantes')
            ->with('status', 'Estudiante eliminado correctamente.');
    }

    /**
     * Asigna una carrera a un usuario con rol Estudiante (crea registro en estudiantes)
     */
    public function store(Request $request): RedirectResponse
    {
        $directorId = $request->user()->id;

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'carrera_id' => ['required', 'exists:carreras,id'],
            'rut' => ['nullable', 'string', 'max:20'],
            'telefono' => ['nullable', 'string', 'max:20'],
        ]);

        // Verificar que la carrera pertenezca al director
        $carrera = Carrera::where('id', $validated['carrera_id'])
            ->where('director_id', $directorId)
            ->first();

        if (!$carrera) {
            return redirect()
                ->back()
                ->withErrors(['carrera_id' => 'La carrera seleccionada no pertenece a tu área de dirección.'])
                ->withInput();
        }

        // Verificar que el usuario tenga rol Estudiante
        $user = User::with(['rol', 'roles'])->findOrFail($validated['user_id']);
        $rolEstudiante = Rol::where('nombre', 'Estudiante')->first();
        
        $tieneRolEstudiante = false;
        if ($rolEstudiante) {
            $tieneRolEstudiante = ($user->rol_id === $rolEstudiante->id) || 
                                  $user->roles->contains($rolEstudiante->id);
        }

        if (!$tieneRolEstudiante) {
            return redirect()
                ->back()
                ->withErrors(['user_id' => 'El usuario seleccionado no tiene el rol de Estudiante.'])
                ->withInput();
        }

        // Si el usuario ya tiene un registro en estudiantes, actualizarlo
        // Si no, crear uno nuevo
        if ($user->estudiante) {
            $user->estudiante->update([
                'carrera_id' => $validated['carrera_id'],
                'rut' => $validated['rut'] ?? $user->estudiante->rut,
                'telefono' => $validated['telefono'] ?? $user->estudiante->telefono,
                'nombre' => $user->nombre,
                'apellido' => $user->apellido,
                'email' => $user->email,
            ]);
        } else {
            // Crear registro en estudiantes
            Estudiante::create([
                'user_id' => $user->id,
                'nombre' => $user->nombre,
                'apellido' => $user->apellido,
                'email' => $user->email,
                'carrera_id' => $validated['carrera_id'],
                'rut' => $validated['rut'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
            ]);
        }

        return redirect()
            ->route('director.estudiantes')
            ->with('status', 'Carrera asignada correctamente al estudiante.');
    }

    /**
     * Elimina un usuario pendiente (sin carrera asignada)
     */
    public function destroyPendingUser(Request $request, User $user): RedirectResponse
    {
        $directorId = $request->user()->id;

        // Verificar que el usuario tenga rol Estudiante
        $rolEstudiante = Rol::where('nombre', 'Estudiante')->first();
        $tieneRolEstudiante = false;
        if ($rolEstudiante) {
            $tieneRolEstudiante = ($user->rol_id === $rolEstudiante->id) || 
                                  $user->roles->contains($rolEstudiante->id);
        }

        if (!$tieneRolEstudiante) {
            abort(403, 'El usuario no tiene el rol de Estudiante.');
        }

        // Verificar que el usuario no tenga carrera asignada del director
        if ($user->estudiante && $user->estudiante->carrera && $user->estudiante->carrera->director_id === $directorId) {
            return redirect()
                ->route('director.estudiantes')
                ->with('error', 'No se puede eliminar un estudiante que tiene carrera asignada.');
        }

        // Si tiene registro en estudiantes pero sin carrera o con carrera de otro director, eliminarlo
        if ($user->estudiante) {
            $user->estudiante->delete();
        }

        // Eliminar el usuario
        $user->delete();

        return redirect()
            ->route('director.estudiantes')
            ->with('status', 'Usuario pendiente eliminado correctamente.');
    }
}

