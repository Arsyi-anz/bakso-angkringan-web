<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BuktiIgStoryController;
use App\Http\Controllers\Api\HasilSpinController;
use App\Http\Controllers\Api\ProfilController;
use App\Http\Controllers\Api\ProdukController;
use App\Http\Controllers\Api\RewardController;
use App\Http\Controllers\Api\TransaksiController;
use App\Http\Controllers\Api\VoucherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (bakso angkringan)
|--------------------------------------------------------------------------
| Auth via Sanctum. Semua modul customer di bawah auth:sanctum.
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);

    // B3 - Profil customer
    Route::get('/profil', [ProfilController::class, 'show']);
    
    // B2 - Home: produk
    Route::get('/produk', [ProdukController::class, 'index']);
    Route::get('/produk/{produk}', [ProdukController::class, 'show']);

    // B4 - Transaksi (self-checkout + riwayat)
    Route::get('/transaksi', [TransaksiController::class, 'index']);
    Route::get('/transaksi/{transaksi}', [TransaksiController::class, 'show']);
    Route::post('/transaksi', [TransaksiController::class, 'store']);

    // B5 - Spin & Win
    Route::post('/spin', [HasilSpinController::class, 'store']);
    Route::get('/spin/riwayat', [HasilSpinController::class, 'index']);
    Route::get('/spin/status', [HasilSpinController::class, 'status']);

    // B6 - Voucher
    Route::get('/voucher', [VoucherController::class, 'index']);
    Route::get('/voucher/{voucher}', [VoucherController::class, 'show']);

    // B7 - Bukti IG Story
    Route::post('/bukti-ig-story', [BuktiIgStoryController::class, 'store']);
    Route::get('/bukti-ig-story', [BuktiIgStoryController::class, 'index']);

    // B8 - Reward & kelayakan hampers bulanan
    Route::get('/reward', [RewardController::class, 'hampers']);
});
