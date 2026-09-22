<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReferralResource;
use App\Models\Referral;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReferralController extends Controller
{
    /**
     * Riwayat referral yang DIBERIKAN customer login.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return ReferralResource::collection(
            Referral::with(['referredCustomer'])
                ->where('customer_id', $request->user()->id)
                ->latest('tanggal')
                ->get(),
        );
    }

    /**
     * Detail satu referral (hanya milik customer login).
     */
    public function show(Request $request, Referral $referral): ReferralResource
    {
        abort_unless(
            $referral->customer_id === $request->user()->id,
            403,
        );

        return new ReferralResource(
            $referral->load(['referredCustomer']),
        );
    }
}
