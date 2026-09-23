<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VoucherResource;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VoucherController extends Controller
{
    /**
     * Daftar SEMUA voucher milik customer login (aktif, terpakai, kedaluwarsa).
     * Flutter memilah per tab (aktif/terpakai) lewat field status.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return VoucherResource::collection(
            Voucher::with('hasilSpin')
                ->where('customer_id', $request->user()->id)
                ->latest()
                ->get(),
        );
    }

    /**
     * Detail voucher milik customer login.
     */
    public function show(Request $request, int $id): VoucherResource
    {
        $voucher = Voucher::with('hasilSpin')
            ->where('id', $id)
            ->where('customer_id', $request->user()->id)
            ->firstOrFail();

        return new VoucherResource($voucher);
    }
}

