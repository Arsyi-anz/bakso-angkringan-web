<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hamper;
use App\Models\Referral;
use App\Models\Transaksi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RewardController extends Controller
{
    /**
     * Kelayakan customer mendapatkan hampers bulanan.
     * Hitung bulan berjalan -> otomatis reset tiap awal bulan.
     * Syarat: minimal 8 transaksi & 3 referral valid dalam sebulan.
     */
    public function hampers(Request $request): JsonResponse
    {
        $customerId = $request->user()->id;

        $awalBulan = Carbon::now()->startOfMonth();
        $akhirBulan = Carbon::now()->endOfMonth();
        $periode = Carbon::now()->format('Y-m');

        $jumlahTransaksi = Transaksi::where('customer_id', $customerId)
            ->whereBetween('tanggal_transaksi', [$awalBulan, $akhirBulan])
            ->count();

        $totalTransaksi = (float) Transaksi::where('customer_id', $customerId)
            ->whereBetween('tanggal_transaksi', [$awalBulan, $akhirBulan])
            ->sum('total_transaksi');

        $jumlahReferral = Referral::where('customer_id', $customerId)
            ->where('status_valid', 'valid')
            ->whereBetween('tanggal', [$awalBulan, $akhirBulan])
            ->count();

        $syaratTransaksi = 8;
        $syaratReferral = 3;
        $layak = $jumlahTransaksi >= $syaratTransaksi && $jumlahReferral >= $syaratReferral;

        Hamper::updateOrCreate(
            [
                'customer_id' => $customerId,
                'periode' => $periode,
            ],
            [
                'jumlah_repeat_order' => $jumlahTransaksi,
                'jumlah_referral' => $jumlahReferral,
                'status_kelayakan' => $layak ? 'memenuhi' : 'belum memenuhi',
            ],
        );

        $pesan = $layak
            ? 'Selamat, kamu layak mendapatkan hampers bulanan. Klaim hampers pada akhir bulan dengan menunjukkan bukti kelayakan ke admin secara offline.'
            : 'Hampers bulanan belum layak. Penuhi ketentuan yang berlaku: minimal '.$syaratTransaksi.' transaksi dan '.$syaratReferral.' referral baru dalam bulan berjalan.';

        return response()->json([
            'data' => [
                'periode' => $periode,
                'layak' => $layak,
                'jumlah_transaksi_bulan' => $jumlahTransaksi,
                'total_transaksi_bulan' => $totalTransaksi,
                'jumlah_referral_bulan' => $jumlahReferral,
                'syarat_transaksi' => $syaratTransaksi,
                'syarat_referral' => $syaratReferral,
                'pesan' => $pesan,
            ],
        ]);
    }
}