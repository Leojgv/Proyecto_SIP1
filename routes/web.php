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

Route::get('/', function () {
    return view('welcome');
});

Route::view('/2', 'landing_admin')->name('landing.admin');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('estudiantes', EstudianteController::class);
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
