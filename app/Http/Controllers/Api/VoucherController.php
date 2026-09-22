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
     * Daftar voucher milik customer login.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return VoucherResource::collection(
            Voucher::with('hasilSpin')
                ->where('customer_id', $request->user()->id)
                ->where('status', 'aktif')
                ->whereDate('tanggal_kadaluarsa', '>', now())
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

