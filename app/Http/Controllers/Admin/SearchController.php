<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $customers = collect();
        $transaksi = collect();
        $produk = collect();

        if ($q !== '') {
            $customers = Customer::query()
                ->withCount(['transaksis as total_transaksi'])
                ->where(function ($query) use ($q) {
                    $query->where('nama', 'like', "%{$q}%")
                        ->orWhere('no_hp', 'like', "%{$q}%")
                        ->orWhere('kode_referral', 'like', "%{$q}%");
                })
                ->latest()
                ->limit(10)
                ->get();

            $produk = Produk::query()
                ->where('nama_produk', 'like', "%{$q}%")
                ->latest()
                ->limit(10)
                ->get();

            $transaksi = Transaksi::query()
                ->with('customer')
                ->whereHas('customer', function ($query) use ($q) {
                    $query->where('nama', 'like', "%{$q}%")
                        ->orWhere('no_hp', 'like', "%{$q}%");
                })
                ->latest('tanggal_transaksi')
                ->limit(10)
                ->get();
        }

        return view('admin.search', compact('customers', 'transaksi', 'produk', 'q'));
    }
}