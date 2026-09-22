<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Hamper;
use App\Models\Referral;
use App\Models\Transaksi;
use App\Models\Voucher;

class DashboardController extends Controller
{
    public function index()
    {
        Hamper::sinkronkanPeriode(now()->format('Y-m'));

        $stats = [
            ['label' => 'Total Customer', 'value' => Customer::count(), 'icon' => 'bi-people', 'suffix' => ''],
            ['label' => 'Total Transaksi', 'value' => Transaksi::count(), 'icon' => 'bi-receipt', 'suffix' => ''],
            ['label' => 'Total Voucher', 'value' => Voucher::count(), 'icon' => 'bi-ticket-perforated', 'suffix' => ''],
            ['label' => 'Total Referral Valid', 'value' => Referral::where('status_valid', 'valid')->count(), 'icon' => 'bi-share', 'suffix' => ''],
            ['label' => 'Customer Layak Hampers', 'value' => Hamper::where('status_kelayakan', 'memenuhi')->count(), 'icon' => 'bi-gift', 'suffix' => ''],
        ];

        return view('admin.dashboard', compact('stats'));
    }
}