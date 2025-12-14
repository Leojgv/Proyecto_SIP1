<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Imports\EstudiantesImport;
use App\Models\Carrera;
use App\Models\Estudiante;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class DirectorCarreraEstudianteController extends Controller
{
    public function index(Request $request)
    {
        $directorId = $request->user()->id;

        $estudiantes = Estudiante::with('carrera')
            ->whereHas('carrera', fn ($query) => $query->where('director_id', $directorId))
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->paginate(12);

        // Obtener solo las carreras del director para el modal de edición
        $carreras = Carrera::where('director_id', $directorId)
            ->orderBy('nombre')
            ->get();

        return view('DirectorCarrera.estudiantes.index', [
            'estudiantes' => $estudiantes,
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
}

