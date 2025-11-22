<?php

use App\Http\Controllers\Api\AdministrasiController;
use App\Http\Controllers\Api\BeritaController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\NagariController;
use App\Http\Controllers\Api\TbAparaturNagariController;
use App\Http\Controllers\Api\TbBeritaController;
use App\Http\Controllers\Api\TbGaleryController;
use App\Http\Controllers\Api\TbIdentitasNagariController;
use App\Models\TbGalery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Authentication routes (no authentication required)
Route::prefix('auth')->group(function () {
    Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('/token-login', [\App\Http\Controllers\Api\AuthController::class, 'tokenLogin']);
    Route::post('/get-token', [\App\Http\Controllers\Api\AuthController::class, 'getToken']);
    Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
    Route::post('/register-no-token', [\App\Http\Controllers\Api\AuthController::class, 'registerWithoutToken']);
    Route::get('/check', [\App\Http\Controllers\Api\AuthController::class, 'check']);
    Route::get('/session-check', [\App\Http\Controllers\Api\AuthController::class, 'sessionCheck']);
});

// Public routes (no authentication required)
Route::prefix('public')->group(function () {
    Route::get('/berita', [TbBeritaController::class, 'index']);
    Route::get('/berita/{id}', [TbBeritaController::class, 'show']);
    Route::get('/galery', [TbGaleryController::class, 'index']);
    Route::get('/galery/{id}', [TbGaleryController::class, 'show']);
    Route::get('/aparatur', [TbAparaturNagariController::class, 'index']);
    Route::get('/nagari', [NagariController::class, 'index']);

    // Penduduk statistics - public access
    Route::get('/penduduk/summary', [\App\Http\Controllers\Api\TbPendudukController::class, 'summary']);
    Route::get('/penduduk/statistics', [\App\Http\Controllers\Api\TbPendudukController::class, 'statistics']);
});

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/berita', [BeritaController::class, 'index']);
    Route::post('/berita', [BeritaController::class, 'store']);
    Route::get('/administrasi', [AdministrasiController::class, 'index']);
    Route::post('/administrasi', [AdministrasiController::class, 'store']);
    Route::get('/nagari', [NagariController::class, 'index']);
    Route::post('/nagari', [NagariController::class, 'store']);
    Route::get('/laporan', [LaporanController::class, 'index']);
    Route::get('/aparatur', [TbAparaturNagariController::class, 'index']);
    Route::get('/tbberita', [TbBeritaController::class, 'index']);
    Route::get('/tbberita/{id}', [TbBeritaController::class, 'show']);
    Route::get('/tbgalery', [TbGaleryController::class, 'index']);
    Route::get('/identitas', [TbIdentitasNagariController::class, 'index']);
    Route::post('/laporan', [LaporanController::class, 'store']);

    // Authentication routes that require login
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
        Route::post('/session-logout', [\App\Http\Controllers\Api\AuthController::class, 'sessionLogout']);
        Route::post('/token-logout', [\App\Http\Controllers\Api\AuthController::class, 'tokenLogout']);
        Route::get('/me', [\App\Http\Controllers\Api\AuthController::class, 'me']);
        Route::post('/change-password', [\App\Http\Controllers\Api\AuthController::class, 'changePassword']);
        Route::post('/refresh-token', [\App\Http\Controllers\Api\AuthController::class, 'refreshToken']);
        Route::get('/tokens', [\App\Http\Controllers\Api\AuthController::class, 'tokens']);
        Route::post('/revoke-all-tokens', [\App\Http\Controllers\Api\AuthController::class, 'revokeAllTokens']);
        Route::get('/validate-session', [\App\Http\Controllers\Api\AuthController::class, 'validateSession']);
    });

    // Penduduk management
    Route::prefix('penduduk')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\TbPendudukController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\TbPendudukController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\Api\TbPendudukController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\Api\TbPendudukController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\TbPendudukController::class, 'destroy']);
        Route::get('/statistics/detailed', [\App\Http\Controllers\Api\TbPendudukController::class, 'statistics']);
    });
});
