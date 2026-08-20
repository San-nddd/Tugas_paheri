<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JadwalController;
use App\Http\Controllers\Api\PemainController;
use App\Http\Controllers\Api\PendaftaranController;
use App\Http\Controllers\Api\PenggunaController;
use App\Http\Controllers\Api\PertandinganController;
use App\Http\Controllers\Api\TimController;
use App\Http\Controllers\Api\TurnamenController;
use Illuminate\Support\Facades\Route;

// ==========================================
// 1. RUTE PUBLIK (Tanpa Auth/Token)
// ==========================================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/turnamen', [TurnamenController::class, 'index']);
Route::get('/jadwal', [JadwalController::class, 'index']);
Route::get('/jadwal/{id}', [JadwalController::class, 'show']);

// ==========================================
// 2. RUTE PROTECTED (Wajib Token Sanctum)
// ==========================================
Route::middleware(['auth:sanctum'])->group(function () {

    // Auth & Profil
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Turnamen (Penyelenggara / Admin)
    Route::middleware('role:penyelenggara,admin')->group(function () {
        Route::post('/turnamen', [TurnamenController::class, 'store']);
    });

    // Verifikasi Pendaftaran Tim (Penyelenggara / Admin)
    Route::middleware('role:penyelenggara,admin')->group(function () {
        Route::patch('/pendaftaran/{pendaftaran}/setujui', [PendaftaranController::class, 'setujui']);
        Route::patch('/pendaftaran/{pendaftaran}/tolak', [PendaftaranController::class, 'tolak']);
    });

    // Update Skor Pertandingan (authorization handled in UpdateSkorRequest)
    Route::patch('/pertandingan/{pertandingan}/skor', [PertandinganController::class, 'updateSkor']);

    // Tim & Pemain
    Route::apiResource('/tim', TimController::class);
    Route::apiResource('/pemain', PemainController::class);

    // Pengguna (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/pengguna', [PenggunaController::class, 'index']);
        Route::get('/pengguna/{id}', [PenggunaController::class, 'show']);
        Route::post('/pengguna', [PenggunaController::class, 'store']);
        Route::put('/pengguna/{id}', [PenggunaController::class, 'update']);
        Route::delete('/pengguna/{id}', [PenggunaController::class, 'destroy']);
    });
});
