<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;

Route::get('/', function () {
    return view('welcome');
});

// Ruta de prueba API
Route::get('/api/test', [TestController::class, 'test']);