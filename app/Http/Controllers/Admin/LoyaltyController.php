<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BuktiIgStory;
use App\Models\Hamper;
use App\Models\HasilSpin;
use App\Models\Referral;
use App\Models\Voucher;
use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    public function spinVoucher()
    {
        Voucher::where('status', 'aktif')
            ->where('tanggal_kadaluarsa', '<', now())
            ->update(['status' => 'kedaluwarsa']);

        $riwayatSpin = HasilSpin::with('customer')
            ->latest('tanggal_spin')
            ->limit(50)
            ->get();

        $voucher = Voucher::with('customer')
            ->latest()
            ->limit(50)
            ->get();

        return view('admin.loyalty.spin-voucher', compact('riwayatSpin', 'voucher'));
    }

    public function pakaiVoucher(Voucher $voucher)
    {
        $voucher->update([
            'status' => now()->greaterThanOrEqualTo($voucher->tanggal_kadaluarsa)
                ? 'kedaluwarsa'
                : 'terpakai',
        ]);

        return back()->with('success', 'Voucher '.$voucher->kode_voucher.' berhasil '.($voucher->status === 'terpakai' ? 'dipakai' : 'ditandai kedaluwarsa').'.');
    }

    public function instagramStory()
    {
        $buktiStory = BuktiIgStory::with('customer')
            ->latest('tanggal_kirim')
            ->paginate(15)
            ->withQueryString();

        return view('admin.loyalty.instagram-story', compact('buktiStory'));
    }

    public function validasiStory(BuktiIgStory $buktiIgStory)
    {
        $buktiIgStory->update([
            'status_verifikasi' => 'diterima',
            'admin_id' => auth()->id(),
        ]);

        return back()->with('success', 'Story berhasil divalidasi.');
    }

    public function tolakStory(BuktiIgStory $buktiIgStory)
    {
        $buktiIgStory->update([
            'status_verifikasi' => 'ditolak',
            'admin_id' => auth()->id(),
        ]);

        return back()->with('success', 'Story berhasil ditolak.');
    }

    public function referral()
    {
        $referral = Referral::with(['customer', 'referredCustomer'])
            ->latest('tanggal')
            ->paginate(15)
            ->withQueryString();

        return view('admin.loyalty.referral', compact('referral'));
    }

    public function hampers()
    {
        Hamper::sinkronkanPeriode(now()->format('Y-m'));

        $hampers = Hamper::with('customer')
            ->latest('periode')
            ->orderByDesc('jumlah_repeat_order')
            ->paginate(15)
            ->withQueryString();

        return view('admin.loyalty.hampers', compact('hampers'));
    }
}