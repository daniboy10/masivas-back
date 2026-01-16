<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CsvController;
use App\Http\Controllers\PersonaController;
// Rutas públicas (sin autenticación)
Route::get('/test', [TestController::class, 'test']);
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas (requieren autenticación con token)
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/formulario', [TestController::class, 'formulario']);
    Route::post('/upload-csv', [CsvController::class, 'upload']); 
    Route::get('/personas', [PersonaController::class, 'index']);
    Route::get('/personas/{id}', [PersonaController::class, 'show']);
});