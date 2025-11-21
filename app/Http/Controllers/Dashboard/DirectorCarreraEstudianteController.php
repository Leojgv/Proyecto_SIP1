<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Imports\EstudiantesImport;
use App\Models\Carrera;
use App\Models\Estudiante;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\View\View;

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

        return view('DirectorCarrera.estudiantes.index', [
            'estudiantes' => $estudiantes,
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
            'archivo' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'], // Máximo 10MB
        ]);

        try {
            // Obtener la carrera asociada al Director autenticado
            $carrera = Carrera::where('director_id', auth()->id())->firstOrFail();

            // Ejecutar la importación pasando el ID de la carrera
            Excel::import(new EstudiantesImport($carrera->id), $request->file('archivo'));

            return redirect()
                ->route('director.estudiantes')
                ->with('status', 'Estudiantes cargados exitosamente desde el archivo Excel.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            return redirect()
                ->back()
                ->withErrors(['archivo' => 'Error en la validación del archivo. Revisa los datos.'])
                ->with('import_errors', $failures)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al procesar el archivo: ' . $e->getMessage())
                ->withInput();
        }
    }
}
