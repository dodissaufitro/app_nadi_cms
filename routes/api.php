<?php

use App\Http\Controllers\Api\AdministrasiController;
use App\Http\Controllers\Api\BeritaController;
use App\Http\Controllers\Api\NagariController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/berita', [BeritaController::class, 'index']);
    Route::post('/berita', [BeritaController::class, 'store']);
    Route::get('/administrasi', [AdministrasiController::class, 'index']);
    Route::post('/administrasi', [AdministrasiController::class, 'store']);
    Route::get('/nagari', [NagariController::class, 'index']);
    Route::post('/nagari', [NagariController::class, 'store']);
});
