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
use App\Http\Controllers\Dashboard\CoordinadoraDashboardController;
use App\Http\Controllers\Dashboard\CoordinadoraEstudianteController;
use App\Http\Controllers\Dashboard\EstudianteDashboardController;
use App\Http\Controllers\Dashboard\EstudianteEntrevistaController;

$staffRoles = implode(',', [
    'Admin',
    'Asesora Pedagogica',
    'Asesora Tecnica Pedagogica',
    'Coordinadora de inclusion',
    'Director de carrera',
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
    });

    Route::middleware('role:Coordinadora de inclusion')->group(function () {
        Route::get('coordinadora/dashboard', [CoordinadoraDashboardController::class, 'show'])->name('coordinadora.dashboard');
        Route::get('coordinadora/estudiantes', [CoordinadoraEstudianteController::class, 'index'])->name('coordinadora.estudiantes');
    });
});
