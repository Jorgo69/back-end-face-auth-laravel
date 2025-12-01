<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompareController;
use App\Http\Controllers\Api\PersonController;
use App\Http\Controllers\Api\StatisticsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes API (JSON)
|--------------------------------------------------------------------------
| Préfixe automatique : /api
| Middleware : api
*/

// Routes publiques
Route::prefix('v1')->group(function () {
    
    // Authentification
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/login/face', [AuthController::class, 'loginWithFace']);
    
    // Utilitaires publics
    Route::post('/compare', [CompareController::class, 'compare']);
    Route::get('/health', [CompareController::class, 'health']);
});

// Routes protégées (nécessitent authentification Sanctum)
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    
    // Profil utilisateur
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/face/register', [AuthController::class, 'registerFace']);
    
    // Personnes (CRUD)
    Route::apiResource('persons', PersonController::class);
    Route::post('/persons/{person}/verify', [PersonController::class, 'verify']);
    
    // Extraction (debug/test)
    Route::post('/extract', [CompareController::class, 'extract']);
    
    // Statistiques
    Route::prefix('statistics')->group(function () {
        Route::get('/dashboard', [StatisticsController::class, 'dashboard']);
        Route::get('/verifications', [StatisticsController::class, 'verifications']);
        Route::get('/verifications/by-day', [StatisticsController::class, 'verificationsByDay']);
        Route::get('/verifications/by-type', [StatisticsController::class, 'verificationsByType']);
    });
});