<?php

use Illuminate\Http\Request;
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

// ✅ NUEVA: últimos registros (para la tabla del frontend)
Route::get('/registros/ultimos', [ScannerController::class, 'ultimosRegistros']);

// --------------------------------------------
// RUTAS DE ADMINISTRACIÓN (PREFIJO /admin)
// --------------------------------------------
Route::prefix('admin')->group(function () {
Route::get('/usuarios', [AdminApiController::class, 'usuarios']);
Route::post('/usuarios', [AdminApiController::class, 'crearUsuario']);
Route::get('/registros', [AdminApiController::class, 'registros']);
});
