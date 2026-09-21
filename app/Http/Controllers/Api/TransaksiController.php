<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransaksiResource;
use App\Models\DetailTransaksi;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TransaksiController extends Controller
{
    /**
     * Riwayat transaksi customer login.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return TransaksiResource::collection(
            Transaksi::with(['detailTransaksis.produk'])
                ->where('customer_id', $request->user()->id)
                ->latest('tanggal_transaksi')
                ->get(),
        );
    }

    /**
     * Detail transaksi customer login.
     */
    public function show(Request $request, Transaksi $transaksi): TransaksiResource
    {
        abort_if($transaksi->customer_id !== $request->user()->id, 403);

        $transaksi->load(['detailTransaksis.produk']);

        return new TransaksiResource($transaksi);
    }

    /**
     * Buat transaksi baru (self-checkout dari Flutter).
     */
    public function store(Request $request): JsonResource|JsonResponse
    {
        $validated = $request->validate([
            'produk_id' => ['required', 'array', 'min:1'],
            'produk_id.*' => ['integer', 'distinct'],
            'jumlah' => ['required', 'array', 'min:1'],
            'jumlah.*' => ['required', 'integer', 'min:1'],
        ]);

        $produkIds = $validated['produk_id'];

        $produks = Produk::whereIn('id', $produkIds)->get()->keyBy('id');

        if ($produks->count() !== count($produkIds)) {
            return response()->json([
                'message' => 'Ada produk yang tidak ditemukan.',
            ], 422);
        }

        $totalTransaksi = 0;
        $detailTransaksis = [];

        foreach ($produkIds as $index => $produkId) {
            $produk = $produks[$produkId];
            $jumlah = $validated['jumlah'][$index];
            $hargaSatuDin = $produk->harga;
            $subtotal = $hargaSatuDin * $jumlah;

            $detailTransaksis[] = [
                'produk' => $produk,
                'jumlah' => $jumlah,
                'harga_satuan' => $hargaSatuDin,
                'subtotal' => $subtotal,
            ];

            $totalTransaksi += $subtotal;
        }

        $transaksi = DB::transaction(function () use ($request, $totalTransaksi, $detailTransaksis): Transaksi {
            $transaksi = Transaksi::create([
                'customer_id' => $request->user()->id,
                'admin_id' => null,
                'total_transaksi' => $totalTransaksi,
                'tanggal_transaksi' => now(),
            ]);

            foreach ($detailTransaksis as $item) {
                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $item['produk']->id,
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            return $transaksi;
        });

        $transaksi->load(['detailTransaksis.produk']);

        return (new TransaksiResource($transaksi))->response()->setStatusCode(201);
    }
}
