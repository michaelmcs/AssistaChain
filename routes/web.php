<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\AsistenciaEmpleadoController;

// Rutas públicas
Route::get('/', function () {
    if (session('authenticated')) {
        return redirect('/dashboard');
    }
    return view('auth.login');
})->name('login.form');

Route::get('/login', function () {
    if (session('authenticated')) {
        return redirect('/dashboard');
    }
    return view('auth.login');
})->name('login.show');

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Ruta para Dashboard
Route::get('/dashboard', function () {
    if (!session('authenticated')) {
        return redirect('/login')->with('error', 'Debes iniciar sesión');
    }
    return view('dashboard');
})->name('dashboard');

// Rutas de empleados
Route::get('/empleados', [EmpleadoController::class, 'index'])->name('empleados.index');
Route::get('/empleados/create', [EmpleadoController::class, 'create'])->name('empleados.create');
Route::post('/empleados', [EmpleadoController::class, 'store'])->name('empleados.store');
Route::get('/empleados/{id}', [EmpleadoController::class, 'show'])->name('empleados.show');
Route::get('/empleados/{id}/edit', [EmpleadoController::class, 'edit'])->name('empleados.edit');
Route::put('/empleados/{id}', [EmpleadoController::class, 'update'])->name('empleados.update');
Route::delete('/empleados/{id}', [EmpleadoController::class, 'destroy'])->name('empleados.destroy');

// Rutas de asistencia
Route::get('/registrar-asistencia', [AsistenciaEmpleadoController::class, 'create'])
    ->name('registrar.asistencia.form');

Route::post('/registrar-asistencia', [AsistenciaEmpleadoController::class, 'registrarAsistencia'])
    ->name('registrar.asistencia');

Route::get('/asistencias', [AsistenciaEmpleadoController::class, 'index'])->name('asistencias.index');
Route::get('/asistencias/{id}', [AsistenciaEmpleadoController::class, 'show'])->name('asistencias.show');

// Ruta para marcar ausentes manualmente
Route::post('/empleados/{id}/marcar-ausente', [AsistenciaEmpleadoController::class, 'marcarAusenteManualmente'])
    ->name('empleados.marcar-ausente');



// CORRECTO: Es una ruta de visualización, debe ser GET
Route::get('/blockchain/verify', function () {
    $blockchainService = app(App\Services\BlockchainService::class);
    $results = $blockchainService->verifyChainIntegrity();
    return view('blockchain.verify', compact('results'));
})->name('blockchain.verify');