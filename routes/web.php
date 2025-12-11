<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\CarreraController;
use App\Http\Controllers\AjusteRazonableController;
use App\Http\Controllers\EntrevistaController;
use App\Http\Controllers\EvidenciaController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\AsignaturaController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\Dashboard\AsesoraPedagogicaDashboardController;
use App\Http\Controllers\Dashboard\AsesoraPedagogicaEstudianteController;
use App\Http\Controllers\Dashboard\AsesoraPedagogicaCasoController;
use App\Http\Controllers\Dashboard\AsesoraTecnicaDashboardController;
use App\Http\Controllers\Dashboard\AsesoraTecnicaAjusteController;
use App\Http\Controllers\Dashboard\AsesoraTecnicaCasoController;
use App\Http\Controllers\Dashboard\AsesoraTecnicaEntrevistaController;
use App\Http\Controllers\Dashboard\AsesoraTecnicaEstudianteController;
use App\Http\Controllers\Dashboard\CoordinadoraDashboardController;
use App\Http\Controllers\Dashboard\CoordinadoraEstudianteController;
use App\Http\Controllers\Dashboard\CoordinadoraAgendaController;
use App\Http\Controllers\Dashboard\CoordinadoraEntrevistaController;
use App\Http\Controllers\Dashboard\CoordinadoraCasoController;
use App\Http\Controllers\Dashboard\DirectorCarreraDashboardController;
use App\Http\Controllers\Dashboard\DirectorCarreraEstudianteController;
use App\Http\Controllers\Dashboard\DirectorCarreraCasoController;
use App\Http\Controllers\Dashboard\DirectorCarreraDocenteController;
use App\Http\Controllers\Dashboard\DocenteDashboardController;
use App\Http\Controllers\Dashboard\EstudianteDashboardController;
use App\Http\Controllers\Dashboard\EstudianteEntrevistaController;

$staffRoles = implode(',', [
    'Admin',
    'Asesora Pedagogica',
    'Asesora Tecnica Pedagogica',
    'Coordinadora de inclusion',
    'Director de carrera',
    'Docente',
]);

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () use ($staffRoles) {
    Route::middleware("role:$staffRoles")->group(function () {
        Route::resource('estudiantes', EstudianteController::class)->whereNumber('estudiante');
        Route::resource('carreras', CarreraController::class);
        Route::resource('ajustes-razonables', AjusteRazonableController::class);
        Route::resource('entrevistas', EntrevistaController::class);
        Route::resource('evidencias', EvidenciaController::class);
        Route::resource('solicitudes', SolicitudController::class);
        Route::resource('asignaturas', AsignaturaController::class);

        Route::resource('notificaciones', NotificacionController::class)
            ->only(['index', 'create', 'store', 'show', 'destroy']);
    });

    Route::middleware('role:Admin')->group(function () {
        Route::resource('roles', RolController::class);

        Route::get('admin/dashboard', [AdminDashboardController::class, 'show'])->name('admin.dashboard');
        Route::get('admin/usuarios', [UserManagementController::class, 'index'])->name('admin.users.index');
        Route::post('admin/usuarios', [UserManagementController::class, 'store'])->name('admin.users.store');
        Route::put('admin/usuarios/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');

        Route::get('usuarios/roles', [UserRoleController::class, 'index'])->name('users.roles.index');
        Route::put('usuarios/{user}/roles', [UserRoleController::class, 'update'])->name('users.roles.update');
    });

    Route::middleware('role:Estudiante')->group(function () {
        Route::get('estudiantes/dashboard', [EstudianteDashboardController::class, 'show'])->name('estudiantes.dashboard');
        Route::post('estudiantes/dashboard/perfil', [EstudianteDashboardController::class, 'storeProfile'])->name('estudiantes.dashboard.store-profile');
        Route::put('estudiantes/dashboard/configuracion', [EstudianteDashboardController::class, 'updateSettings'])->name('estudiantes.dashboard.update-settings');

        Route::get('estudiantes/entrevistas/solicitar', [EstudianteEntrevistaController::class, 'create'])->name('estudiantes.entrevistas.create');
        Route::post('estudiantes/entrevistas', [EstudianteEntrevistaController::class, 'store'])->name('estudiantes.entrevistas.store');
        Route::get('estudiantes/entrevistas/dias-disponibles', [EstudianteEntrevistaController::class, 'getDiasDisponibles'])->name('estudiantes.entrevistas.dias-disponibles');
        Route::get('estudiantes/entrevistas/horarios-por-fecha', [EstudianteEntrevistaController::class, 'getHorariosPorFecha'])->name('estudiantes.entrevistas.horarios-por-fecha');
    });

    Route::middleware('role:Coordinadora de inclusion')->group(function () {
        Route::get('coordinadora/dashboard', [CoordinadoraDashboardController::class, 'show'])->name('coordinadora.dashboard');
        Route::post('coordinadora/solicitud', [CoordinadoraDashboardController::class, 'storeSolicitud'])
            ->name('coordinadora.solicitud.store');
        Route::get('coordinadora/estudiantes', [CoordinadoraEstudianteController::class, 'index'])->name('coordinadora.estudiantes');
        Route::get('coordinadora/agenda', [CoordinadoraAgendaController::class, 'index'])->name('coordinadora.agenda.index');
        Route::get('coordinadora/entrevistas', [CoordinadoraEntrevistaController::class, 'index'])->name('coordinadora.entrevistas.index');
        Route::get('coordinadora/casos', [CoordinadoraCasoController::class, 'index'])->name('coordinadora.casos.index');
        Route::post('coordinadora/casos/{solicitud}/informar-ctp', [CoordinadoraCasoController::class, 'informarACTP'])->name('coordinadora.casos.informar-ctp');
        Route::post('coordinadora/agenda/bloqueos', [CoordinadoraAgendaController::class, 'storeBloqueo'])->name('coordinadora.agenda.bloqueos.store');
        Route::delete('coordinadora/agenda/bloqueos/{bloqueo}', [CoordinadoraAgendaController::class, 'destroyBloqueo'])->name('coordinadora.agenda.bloqueos.destroy');
    });

    Route::middleware('role:Director de carrera')->group(function () {
        Route::get('director-carrera/dashboard', [DirectorCarreraDashboardController::class, 'show'])
            ->name('director.dashboard');
        Route::get('director-carrera/estudiantes', [DirectorCarreraEstudianteController::class, 'index'])
            ->name('director.estudiantes');
        Route::get('director-carrera/estudiantes/importar', [DirectorCarreraEstudianteController::class, 'showImportForm'])
            ->name('director.estudiantes.import.form');
        Route::post('director-carrera/estudiantes/importar', [DirectorCarreraEstudianteController::class, 'import'])
            ->name('director.estudiantes.import');
        Route::get('director-carrera/ajustes', [\App\Http\Controllers\Dashboard\DirectorCarreraAjusteController::class, 'index'])
            ->name('director.ajustes.index');
        Route::get('director-carrera/docentes', [DirectorCarreraDocenteController::class, 'index'])
            ->name('director.docentes');
        Route::get('director-carrera/docentes/importar', [DirectorCarreraDocenteController::class, 'showImportForm'])
            ->name('director.docentes.import.form');
        Route::post('director-carrera/docentes/importar', [DirectorCarreraDocenteController::class, 'import'])
            ->name('director.docentes.import');
        Route::get('director-carrera/casos', [DirectorCarreraCasoController::class, 'index'])
            ->name('director.casos');
        Route::get('director-carrera/casos/{solicitud}', [DirectorCarreraCasoController::class, 'show'])
            ->name('director.casos.show');
        Route::post('director-carrera/casos/{solicitud}/aprobar', [DirectorCarreraCasoController::class, 'approve'])
            ->name('director.casos.approve');
        Route::post('director-carrera/casos/{solicitud}/rechazar', [DirectorCarreraCasoController::class, 'reject'])
            ->name('director.casos.reject');
        Route::post('director-carrera/casos/{solicitud}/devolver-ctp', [DirectorCarreraCasoController::class, 'devolverACTP'])
            ->name('director.casos.devolver-ctp');
        Route::get('director-carrera/reporte-pdf', [DirectorCarreraDashboardController::class, 'generarReportePDF'])
            ->name('director.reporte.pdf');
    });

    Route::middleware('role:Asesora Pedagogica')->group(function () {
        Route::get('asesora-pedagogica/dashboard', [AsesoraPedagogicaDashboardController::class, 'show'])
            ->name('asesora-pedagogica.dashboard');
        Route::get('asesora-pedagogica/estudiantes', [AsesoraPedagogicaEstudianteController::class, 'index'])
            ->name('asesora-pedagogica.estudiantes');
        Route::get('asesora-pedagogica/casos', [AsesoraPedagogicaCasoController::class, 'index'])
            ->name('asesora-pedagogica.casos.index');
        Route::get('asesora-pedagogica/ajustes', [\App\Http\Controllers\Dashboard\AsesoraPedagogicaAjusteController::class, 'index'])
            ->name('asesora-pedagogica.ajustes.index');

        Route::get('asesora-pedagogica/casos/{solicitud}', [AsesoraPedagogicaCasoController::class, 'show'])
            ->name('asesora-pedagogica.casos.show');
        Route::post('asesora-pedagogica/casos/{solicitud}/enviar-director', [AsesoraPedagogicaCasoController::class, 'enviarADirector'])
            ->name('asesora-pedagogica.casos.enviar-director');
        Route::post('asesora-pedagogica/casos/{solicitud}/devolver-actt', [AsesoraPedagogicaCasoController::class, 'devolverACTT'])
            ->name('asesora-pedagogica.casos.devolver-actt');
        Route::post('asesora-pedagogica/solicitud', [AsesoraPedagogicaDashboardController::class, 'storeSolicitud'])
            ->name('asesora-pedagogica.solicitud.store');
    });

    Route::middleware('role:Asesora Tecnica Pedagogica')->group(function () {
        Route::get('asesora-tecnica/dashboard', [AsesoraTecnicaDashboardController::class, 'show'])
            ->name('asesora-tecnica.dashboard');
        Route::get('asesora-tecnica/estudiantes', [AsesoraTecnicaEstudianteController::class, 'index'])
            ->name('asesora-tecnica.estudiantes');
        Route::get('asesora-tecnica/entrevistas', [AsesoraTecnicaEntrevistaController::class, 'index'])
            ->name('asesora-tecnica.entrevistas.index');
        Route::get('asesora-tecnica/casos', [AsesoraTecnicaCasoController::class, 'index'])
            ->name('asesora-tecnica.casos.index');
        Route::get('asesora-tecnica/ajustes/formular', [AsesoraTecnicaAjusteController::class, 'create'])
            ->name('asesora-tecnica.ajustes.create');
        Route::post('asesora-tecnica/ajustes', [AsesoraTecnicaAjusteController::class, 'store'])
            ->name('asesora-tecnica.ajustes.store');
        Route::post('asesora-tecnica/solicitudes/{solicitud}/enviar-preaprobacion', [AsesoraTecnicaAjusteController::class, 'enviarAPreaprobacion'])
            ->name('asesora-tecnica.solicitudes.enviar-preaprobacion');
    });

    Route::middleware('role:Docente')->group(function () {
        Route::get('docente/dashboard', [DocenteDashboardController::class, 'show'])
            ->name('docente.dashboard');
        Route::get('docente/estudiantes', [DocenteDashboardController::class, 'students'])
            ->name('docente.estudiantes');
    });
});
