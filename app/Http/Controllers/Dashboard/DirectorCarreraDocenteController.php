<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Imports\DocentesImport;
use App\Models\Carrera;
use App\Models\Docente;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class DirectorCarreraDocenteController extends Controller
{
    public function index(Request $request): View
    {
        $directorId = $request->user()->id;

        // Obtener docentes con carrera asignada que pertenecen al director
        $docentesConCarrera = Docente::with(['carrera', 'user'])
            ->whereHas('carrera', fn ($query) => $query->where('director_id', $directorId))
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->get();

        // Obtener IDs de usuarios que ya tienen registro en docentes con carrera asignada
        $userIdsConCarrera = $docentesConCarrera->pluck('user_id')->filter()->unique()->toArray();

        // Obtener usuarios con rol Docente que no están en la lista de docentes con carrera
        $rolDocente = Rol::where('nombre', 'Docente')->first();
        $usuariosSinCarrera = collect();
        
        if ($rolDocente) {
            $usuariosSinCarrera = User::where(function ($query) use ($rolDocente) {
                $query->where('rol_id', $rolDocente->id)
                    ->orWhereHas('roles', fn ($q) => $q->where('roles.id', $rolDocente->id));
            })
            ->whereNotIn('id', $userIdsConCarrera) // Excluir usuarios que ya están en la lista de con carrera
            ->where(function ($query) use ($directorId) {
                // Usuarios sin registro en docentes
                $query->whereDoesntHave('docente')
                    // O usuarios con registro en docentes pero sin carrera válida
                    ->orWhereHas('docente', function ($q) use ($directorId) {
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
        $todosLosDocentes = $docentesConCarrera->concat($usuariosSinCarrera->map(function ($user) {
            return (object) [
                'id' => 'user_' . $user->id,
                'nombre' => $user->nombre,
                'apellido' => $user->apellido,
                'email' => $user->email,
                'rut' => null,
                'carrera' => null,
                'carrera_id' => null,
                'user' => $user,
                'user_id' => $user->id,
                'es_usuario_sin_carrera' => true,
            ];
        }));

        // Paginar manualmente
        $currentPage = request()->get('page', 1);
        $perPage = 15;
        $items = $todosLosDocentes->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $todosLosDocentes->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Obtener solo las carreras del director para el modal de edición
        $carreras = Carrera::where('director_id', $directorId)
            ->orderBy('nombre')
            ->get();

        return view('DirectorCarrera.docentes.index', [
            'docentes' => $paginated,
            'carreras' => $carreras,
        ]);
    }

    /**
     * Muestra el formulario para cargar el archivo Excel
     */
    public function showImportForm(Request $request): View
    {
        return view('DirectorCarrera.docentes.import');
    }

    /**
     * Procesa la carga masiva de docentes desde un archivo Excel
     */
    public function import(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'archivo' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'], // Maximo 10MB
        ]);

        try {
            $directorId = $request->user()->id;

            // Ejecutar la importacion pasando el ID del director
            Excel::import(new DocentesImport($directorId), $request->file('archivo'));

            return redirect()
                ->route('director.docentes')
                ->with('status', 'Docentes cargados exitosamente desde el archivo Excel. Todos los docentes tienen la contraseña: Inacap.2030');
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
     * Actualiza un docente
     */
    public function update(Request $request, Docente $docente): RedirectResponse
    {
        $directorId = $request->user()->id;

        // Verificar que el docente pertenezca a una carrera del director
        if (!$docente->carrera || $docente->carrera->director_id !== $directorId) {
            abort(403, 'No tienes permiso para editar este docente.');
        }

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:docentes,email,' . $docente->id],
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

        $docente->update($validated);

        // Actualizar también el email en el usuario relacionado si existe
        if ($docente->user) {
            $docente->user->update(['email' => $validated['email']]);
        }

        return redirect()
            ->route('director.docentes')
            ->with('status', 'Docente actualizado correctamente.');
    }

    /**
     * Asigna una carrera a un usuario con rol Docente (crea registro en docentes)
     */
    public function store(Request $request): RedirectResponse
    {
        $directorId = $request->user()->id;

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'carrera_id' => ['required', 'exists:carreras,id'],
            'rut' => ['nullable', 'string', 'max:20'],
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

        // Verificar que el usuario tenga rol Docente
        $user = User::with(['rol', 'roles'])->findOrFail($validated['user_id']);
        $rolDocente = Rol::where('nombre', 'Docente')->first();
        
        $tieneRolDocente = false;
        if ($rolDocente) {
            $tieneRolDocente = ($user->rol_id === $rolDocente->id) || 
                               $user->roles->contains($rolDocente->id);
        }

        if (!$tieneRolDocente) {
            return redirect()
                ->back()
                ->withErrors(['user_id' => 'El usuario seleccionado no tiene el rol de Docente.'])
                ->withInput();
        }

        // Si el usuario ya tiene un registro en docentes, actualizarlo
        // Si no, crear uno nuevo
        if ($user->docente) {
            $user->docente->update([
                'carrera_id' => $validated['carrera_id'],
                'rut' => $validated['rut'] ?? $user->docente->rut,
                'nombre' => $user->nombre,
                'apellido' => $user->apellido,
                'email' => $user->email,
            ]);
        } else {
            // Crear registro en docentes
            Docente::create([
                'user_id' => $user->id,
                'nombre' => $user->nombre,
                'apellido' => $user->apellido,
                'email' => $user->email,
                'carrera_id' => $validated['carrera_id'],
                'rut' => $validated['rut'] ?? null,
            ]);
        }

        return redirect()
            ->route('director.docentes')
            ->with('status', 'Carrera asignada correctamente al docente.');
    }

    /**
     * Elimina un usuario pendiente (sin carrera asignada)
     */
    public function destroyPendingUser(Request $request, User $user): RedirectResponse
    {
        $directorId = $request->user()->id;

        // Verificar que el usuario tenga rol Docente
        $rolDocente = Rol::where('nombre', 'Docente')->first();
        $tieneRolDocente = false;
        if ($rolDocente) {
            $tieneRolDocente = ($user->rol_id === $rolDocente->id) || 
                               $user->roles->contains($rolDocente->id);
        }

        if (!$tieneRolDocente) {
            abort(403, 'El usuario no tiene el rol de Docente.');
        }

        // Verificar que el usuario no tenga carrera asignada del director
        if ($user->docente && $user->docente->carrera && $user->docente->carrera->director_id === $directorId) {
            return redirect()
                ->route('director.docentes')
                ->with('error', 'No se puede eliminar un docente que tiene carrera asignada.');
        }

        // Si tiene registro en docentes pero sin carrera o con carrera de otro director, eliminarlo
        if ($user->docente) {
            $user->docente->delete();
        }

        // Eliminar el usuario
        $user->delete();

        return redirect()
            ->route('director.docentes')
            ->with('status', 'Usuario pendiente eliminado correctamente.');
    }
}

