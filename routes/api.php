<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JadwalController;
use App\Http\Controllers\Api\TurnamenController;
use App\Http\Controllers\Api\PendaftaranController;
use App\Http\Controllers\Api\PertandinganController;
use App\Http\Controllers\Api\TimController;
use App\Http\Controllers\Api\PemainController;
use App\Http\Controllers\Api\PenggunaController;

// ==========================================
// 1. RUTE PUBLIK (Tanpa Auth/Token)
// ==========================================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Endpoint Informasi Publik Turnamen & Jadwal
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

    // Turnamen (Penyelenggara)
    Route::post('/turnamen', [TurnamenController::class, 'store']);

    // Verifikasi Pendaftaran Tim (Penyelenggara / Admin)
    Route::patch('/pendaftaran/{pendaftaran}/setujui', [PendaftaranController::class, 'setujui']);
    Route::patch('/pendaftaran/{pendaftaran}/tolak', [PendaftaranController::class, 'tolak']);

    // Update Skor Pertandingan
    Route::patch('/pertandingan/{pertandingan}/skor', [PertandinganController::class, 'updateSkor']);

    // Tim & Pemain (Siap Diisi Logic-nya Nanti)
    Route::apiResource('/tim', TimController::class);
    Route::apiResource('/pemain', PemainController::class);

    // Pengguna 
    Route::get('/pengguna', [PenggunaController::class, 'index']); 
    Route::get('/pengguna/{id}', [PenggunaController::class, 'show']);
    Route::post('/pengguna', [PenggunaController::class, 'store']);
    Route::put('/pengguna/{id}', [PenggunaController::class, 'update']);
    Route::delete('/pengguna/{id}', [PenggunaController::class, 'destroy']);
});