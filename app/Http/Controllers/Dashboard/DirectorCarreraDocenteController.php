<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Imports\DocentesImport;
use App\Models\Carrera;
use App\Models\Docente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class DirectorCarreraDocenteController extends Controller
{
    public function index(Request $request): View
    {
        $directorId = $request->user()->id;

        $docentes = Docente::with(['carrera', 'user'])
            ->whereHas('carrera', fn ($query) => $query->where('director_id', $directorId))
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->paginate(15);

        // Obtener solo las carreras del director para el modal de edición
        $carreras = Carrera::where('director_id', $directorId)
            ->orderBy('nombre')
            ->get();

        return view('DirectorCarrera.docentes.index', [
            'docentes' => $docentes,
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
}

