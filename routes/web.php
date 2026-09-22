<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LoyaltyController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\TransaksiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Auth Admin (session guard -> tabel admins)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Panel Admin
Route::middleware('auth')->group(function (): void {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Data Master - Customer
    Route::get('/admin/customer', [CustomerController::class, 'index'])->name('admin.customer.index');
    Route::put('/admin/customer/{customer}', [CustomerController::class, 'update'])->name('admin.customer.update');
    Route::delete('/admin/customer/{customer}', [CustomerController::class, 'destroy'])->name('admin.customer.destroy');

    // Data Master - Produk
    Route::get('/admin/produk', [ProdukController::class, 'index'])->name('admin.produk.index');
    Route::post('/admin/produk', [ProdukController::class, 'store'])->name('admin.produk.store');
    Route::put('/admin/produk/{produk}', [ProdukController::class, 'update'])->name('admin.produk.update');
    Route::delete('/admin/produk/{produk}', [ProdukController::class, 'destroy'])->name('admin.produk.destroy');

    // Transaksi
    Route::get('/admin/transaksi', [TransaksiController::class, 'index'])->name('admin.transaksi.index');
    Route::get('/admin/transaksi/create', [TransaksiController::class, 'create'])->name('admin.transaksi.create');
    Route::post('/admin/transaksi', [TransaksiController::class, 'store'])->name('admin.transaksi.store');
    Route::get('/admin/transaksi/{transaksi}', [TransaksiController::class, 'show'])->name('admin.transaksi.show');
    Route::delete('/admin/transaksi/{transaksi}', [TransaksiController::class, 'destroy'])->name('admin.transaksi.destroy');

    // Program Loyalty
    Route::get('/admin/loyalty/spin-voucher', [LoyaltyController::class, 'spinVoucher'])->name('admin.loyalty.spin-voucher');
    Route::get('/admin/loyalty/instagram-story', [LoyaltyController::class, 'instagramStory'])->name('admin.loyalty.instagram-story');
    Route::post('/admin/loyalty/instagram-story/{buktiIgStory}/validasi', [LoyaltyController::class, 'validasiStory'])->name('admin.loyalty.instagram-story.validasi');
    Route::post('/admin/loyalty/instagram-story/{buktiIgStory}/tolak', [LoyaltyController::class, 'tolakStory'])->name('admin.loyalty.instagram-story.tolak');
    Route::get('/admin/loyalty/referral', [LoyaltyController::class, 'referral'])->name('admin.loyalty.referral');
    Route::get('/admin/loyalty/hampers', [LoyaltyController::class, 'hampers'])->name('admin.loyalty.hampers');

    // Pencarian Global
    Route::get('/admin/search', [SearchController::class, 'index'])->name('admin.search');

    // Report & Export
    Route::get('/admin/report/customer', [ReportController::class, 'customer'])->name('admin.report.customer');
    Route::get('/admin/report/transaksi', [ReportController::class, 'transaksi'])->name('admin.report.transaksi');
    Route::get('/admin/report/loyalty', [ReportController::class, 'loyalty'])->name('admin.report.loyalty');

    Route::get('/admin/report/customer/export', [ReportController::class, 'exportCustomer'])->name('admin.report.customer.export');
    Route::get('/admin/report/transaksi/export', [ReportController::class, 'exportTransaksi'])->name('admin.report.transaksi.export');
    Route::get('/admin/report/loyalty/export', [ReportController::class, 'exportLoyalty'])->name('admin.report.loyalty.export');
});