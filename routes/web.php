<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\CarreraController;
use App\Http\Controllers\AsesorPedagogicoController;
use App\Http\Controllers\AjusteRazonableController;
use App\Http\Controllers\EntrevistaController;
use App\Http\Controllers\EvidenciaController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\AsignaturaController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\DirectorCarreraController;
use App\Http\Controllers\DocenteAsignaturaController;

// Dashboard Controller
use App\Http\Controllers\Dashboard\EstudianteDashboardController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('estudiantes', EstudianteController::class)->whereNumber('estudiante');
Route::resource('carreras', CarreraController::class);
Route::resource('asesores-pedagogicos', AsesorPedagogicoController::class);
Route::resource('ajustes-razonables', AjusteRazonableController::class);
Route::resource('entrevistas', EntrevistaController::class);
Route::resource('evidencias', EvidenciaController::class);
Route::resource('docentes', DocenteController::class);
Route::resource('solicitudes', SolicitudController::class);
Route::resource('asignaturas', AsignaturaController::class);
Route::resource('roles', RolController::class);
Route::resource('directores-carrera', DirectorCarreraController::class);
Route::resource('docente-asignaturas', DocenteAsignaturaController::class);

// Dashboard Routes

Route::middleware('auth')->group(function () {
    Route::get('estudiantes/dashboard', [EstudianteDashboardController::class, 'show'])->name('estudiantes.dashboard');
    Route::post('estudiantes/dashboard/perfil', [EstudianteDashboardController::class, 'storeProfile'])->name('estudiantes.dashboard.store-profile');
    Route::put('estudiantes/dashboard/configuracion', [EstudianteDashboardController::class, 'updateSettings'])->name('estudiantes.dashboard.update-settings');
});
