<?php
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;



use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

