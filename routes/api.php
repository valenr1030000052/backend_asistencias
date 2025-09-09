<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\AdminApiController;


// --------------------------------------------
// RUTAS API GENERALES (CRUD)
// --------------------------------------------

// Usuarios
Route::apiResource('usuarios', UsuarioController::class);

// Registros
Route::apiResource('registros', RegistroController::class);

// --------------------------------------------
// RUTA PARA SCANNER
// --------------------------------------------
Route::post('/scan', [ScannerController::class, 'apiScan']);

// --------------------------------------------
// RUTAS DE ADMINISTRACIÓN (PREFIJO /admin)
// --------------------------------------------
Route::prefix('admin')->group(function () {
    // Listar usuarios
    Route::get('/usuarios', [AdminApiController::class, 'usuarios']);

    // Crear usuario
    Route::post('/usuarios', [AdminApiController::class, 'crearUsuario']);

    // Listar registros con filtros
    Route::get('/registros', [AdminApiController::class, 'registros']);
});