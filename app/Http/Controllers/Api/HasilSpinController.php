<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HasilSpinResource;
use App\Http\Resources\VoucherResource;
use App\Models\BuktiIgStory;
use App\Models\HasilSpin;
use App\Models\Transaksi;
use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class HasilSpinController extends Controller
{
    /**
     * Riwayat spin customer login.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return HasilSpinResource::collection(
            HasilSpin::with(['voucher'])
                ->where('customer_id', $request->user()->id)
                ->latest('tanggal_spin')
                ->get(),
        );
    }

    /**
     * Lakukan spin <= elibilitas: transaksi >= 100000 ATAU bukti IG story diterima.
     */
    public function store(Request $request): JsonResponse|JsonResource
    {
        $validated = $request->validate([
            'transaksi_id' => ['nullable', 'integer', 'exists:transaksis,id'],
            'bukti_ig_story_id' => ['nullable', 'integer'],
        ]);

        $customerId = $request->user()->id;
        $transaksiId = $validated['transaksi_id'] ?? null;
        $buktiIgStoryId = $validated['bukti_ig_story_id'] ?? null;

        // --- Perisai 1: minimal SATU sumber ---
        if ($transaksiId === null && $buktiIgStoryId === null) {
            return response()->json([
                'message' => 'Sertakan transaksi_id atau bukti_ig_story_id.',
            ], 422);
        }

        $transaksi = null;
        $bukti = null;

        if ($transaksiId !== null) {
            $transaksi = Transaksi::where('customer_id', $customerId)
                ->find($transaksiId);

            if (! $transaksi) {
                return response()->json([
                    'message' => 'Transaksi tidak ditemukan.',
                ], 404);
            }

            if ($transaksi->total_transaksi < 100000) {
                return response()->json([
                    'message' => 'Minimal transaksi 100.000 untuk spin.',
                ], 403);
            }

            $sudah = HasilSpin::where('transaksi_id', $transaksi->id)->exists();
            if ($sudah) {
                return response()->json([
                    'message' => 'Transaksi ini sudah pernah di-spin.',
                ], 409);
            }
        }

        if ($buktiIgStoryId !== null) {
            $bukti = BuktiIgStory::where('customer_id', $customerId)
                ->find($buktiIgStoryId);

            if (! $bukti) {
                return response()->json([
                    'message' => 'Bukti IG Story tidak ditemukan.',
                ], 404);
            }

            if ($bukti->status_verifikasi !== 'diterima') {
                return response()->json([
                    'message' => 'Bukti IG Story harus berstatus diterima.',
                ], 403);
            }

            $sudah = HasilSpin::where('bukti_ig_story_id', $bukti->id)
                ->whereDate('tanggal_spin', now()->toDateString())
                ->exists();
            if ($sudah) {
                return response()->json([
                    'message' => 'Bukti IG ini sudah dipakai untuk spin hari ini.',
                ], 409);
            }
        }

        // --- Simpan spin + reward deterministic (selalu voucher) ---
        $hasilSpin = DB::transaction(function () use ($request, $customerId, $transaksi, $bukti): HasilSpin {
            $hasilSpin = HasilSpin::create([
                'customer_id' => $customerId,
                'transaksi_id' => $transaksi?->id,
                'bukti_ig_story_id' => $bukti?->id,
                'jenis_reward' => 'voucher',
                'tanggal_spin' => now(),
            ]);

            $hasilSpin->voucher()->create([
                'customer_id' => $customerId,
                'kode_voucher' => 'SPIN-'.strtoupper(Str::random(8)),
                'status' => 'aktif',
                'tanggal_kadaluarsa' => now()->addDays(7),
            ]);

            return $hasilSpin;
        });

        $hasilSpin->load(['voucher']);

        return (new HasilSpinResource($hasilSpin))
            ->additional(['voucher' => new VoucherResource($hasilSpin->voucher)])
            ->response()
            ->setStatusCode(201);
    }
}
