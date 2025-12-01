<?php

use App\Http\Controllers\Web\CompareController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\FaceAuthController;
use App\Http\Controllers\Web\PersonController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes Web
|--------------------------------------------------------------------------
*/

// Page d'accueil publique
Route::get('/', function () {
    return view('welcome');
})->name('home');


        
// Routes d'authentification par visage (publiques)
Route::middleware('guest')->group(function () {
    Route::get('/login/face', [FaceAuthController::class, 'showLoginForm'])
        ->name('face-auth.login');
    Route::post('/login/face', [FaceAuthController::class, 'loginWithFace'])
        ->name('face-auth.login.submit');
});

// Routes protégées (nécessitent une authentification)
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    Route::get('/statistics', [DashboardController::class, 'statistics'])
        ->name('statistics');

    // Enregistrement du visage
    Route::prefix('face-auth')->name('face-auth.')->group(function () {
        Route::get('/register', [FaceAuthController::class, 'showRegisterForm'])
            ->name('register');
        Route::post('/register', [FaceAuthController::class, 'register'])
            ->name('register.submit');
        
        Route::get('/reregister', [FaceAuthController::class, 'reregisterForm'])
            ->name('reregister');
        Route::post('/reregister', [FaceAuthController::class, 'reregister'])
            ->name('reregister.submit');
    });

    // Gestion des personnes (CRUD complet)
    Route::resource('persons', PersonController::class);
    Route::post('/persons/{person}/verify', [PersonController::class, 'verify'])
        ->name('persons.verify');

    // Comparaison d'images
    Route::prefix('compare')->name('compare.')->group(function () {
        Route::get('/', [CompareController::class, 'index'])
            ->name('index');
        Route::post('/', [CompareController::class, 'compare'])
            ->name('submit');
    });

    // Profil utilisateur
    Route::get('/profile', function () {
        return view('profile.edit');
    })->name('profile.edit');
});

// Routes Breeze (si installé)
require __DIR__.'/auth.php';