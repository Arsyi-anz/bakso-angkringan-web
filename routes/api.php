<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProdukController;
use App\Http\Controllers\Api\VoucherController;
use App\Http\Controllers\Api\TransaksiController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // B2 - Home: produk unggulan
    Route::get('/produk', [ProdukController::class, 'index']);
    Route::get('/produk/{produk}', [ProdukController::class, 'show']);

    // B6 - Voucher
    Route::get('/voucher', [VoucherController::class, 'index'])
        ->missing(fn () => response()->json(['message' => 'Voucher tidak ditemukan.'], 404));
    Route::get('/voucher/{voucher}', [VoucherController::class, 'show']);

    // B4 - Transaksi (self-checkout customer + riwayat)
    Route::get('/transaksi', [TransaksiController::class, 'index']);
    Route::get('/transaksi/{transaksi}', [TransaksiController::class, 'show']);
    Route::post('/transaksi', [TransaksiController::class, 'store']);
});
