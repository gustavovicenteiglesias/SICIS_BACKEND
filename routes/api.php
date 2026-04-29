<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;

// Rutas Públicas
Route::post('/login', [AuthController::class, 'login']);

// Rutas Protegidas (Solo podés entrar si pasás un Token válido en el Header Authorization: Bearer <token>)
Route::middleware('auth:sanctum')->group(function () {
    
    // Endpoint de prueba para ver quién está logueado
    Route::get('/perfil', function (Request $request) {
        // Carga el usuario con sus roles asociados
        return $request->user()->load('roles');
    });

});
