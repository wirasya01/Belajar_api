<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BeritaController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user/profile', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::get('kategori', [kategoriController::class, 'index']);
// Route::post('kategori', [kategoriController::class, 'store']);
// Route::get('kategori/{$id}', [kategoriController::class, 'show']);
// Route::put('kategori/{$id}', [kategoriController::class, 'update']);
// Route::delete('kategori/{$id}', [kategoriController::class, 'destroy']);

//auth route
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::resource('kategori', kategoriController::class)->except(['edit', 'create']);
    Route::resource('tag', TagController::class)->except(['edit', 'create']);
    Route::resource('user', UserController::class)->except(['edit', 'create']);
    Route::resource('berita', BeritaController::class)->except(['edit', 'create']);

});
