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
// Dashboard Controller

use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\Dashboard\EstudianteDashboardController;
use App\Http\Controllers\Dashboard\EstudianteEntrevistaController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('estudiantes', EstudianteController::class)->whereNumber('estudiante');
Route::resource('carreras', CarreraController::class);
Route::resource('ajustes-razonables', AjusteRazonableController::class);
Route::resource('entrevistas', EntrevistaController::class);
Route::resource('evidencias', EvidenciaController::class);
Route::resource('solicitudes', SolicitudController::class);
Route::resource('asignaturas', AsignaturaController::class);
Route::resource('roles', RolController::class);

// Dashboard Routes

Route::middleware('auth')->group(function () {
    Route::get('estudiantes/dashboard', [EstudianteDashboardController::class, 'show'])->name('estudiantes.dashboard');
    Route::post('estudiantes/dashboard/perfil', [EstudianteDashboardController::class, 'storeProfile'])->name('estudiantes.dashboard.store-profile');
    Route::put('estudiantes/dashboard/configuracion', [EstudianteDashboardController::class, 'updateSettings'])->name('estudiantes.dashboard.update-settings');

    Route::get('admin/dashboard', [AdminDashboardController::class, 'show'])->name('admin.dashboard');
    Route::get('admin/usuarios', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::post('admin/usuarios', [UserManagementController::class, 'store'])->name('admin.users.store');
    Route::put('admin/usuarios/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');

    // Solicitar Entrevista (estudiante)
    Route::get('estudiantes/entrevistas/solicitar', [EstudianteEntrevistaController::class, 'create'])->name('estudiantes.entrevistas.create');
    Route::post('estudiantes/entrevistas', [EstudianteEntrevistaController::class, 'store'])->name('estudiantes.entrevistas.store');

    Route::resource('notificaciones', NotificacionController::class)
        ->only(['index', 'create', 'store', 'show', 'destroy']);

    Route::get('usuarios/roles', [UserRoleController::class, 'index'])->name('users.roles.index');
    Route::put('usuarios/{user}/roles', [UserRoleController::class, 'update'])->name('users.roles.update');
});
