<?php
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;



use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/admin', function () {
    return view('admin'); 
});


// Y también la ruta del escáner:
Route::get('/escanear/{ciudad}/{sede}', function ($ciudad, $sede) {
    return view('escanear', compact('ciudad', 'sede'));
})->name('escanear');

Route::get('/escanear/{ciudad}/{sede}', [\App\Http\Controllers\ScannerController::class, 'view']);