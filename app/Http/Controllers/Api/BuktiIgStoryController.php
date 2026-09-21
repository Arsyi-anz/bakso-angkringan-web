<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BuktiIgStoryResource;
use App\Models\BuktiIgStory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class BuktiIgStoryController extends Controller
{
    /**
     * Daftar upload bukti IG story customer login.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return BuktiIgStoryResource::collection(
            BuktiIgStory::where('customer_id', $request->user()->id)
                ->latest('tanggal_kirim')
                ->get(),
        );
    }

    /**
     * Upload link bukti IG story. Maksimal 1 link per hari.
     */
    public function store(Request $request): JsonResponse|JsonResource
    {
        $validated = $request->validate([
            'url_bukti' => ['required', 'url'],
        ]);

        $customerId = $request->user()->id;

        $sudahUploadHariIni = BuktiIgStory::where('customer_id', $customerId)
            ->where('tanggal_kirim', '>=', now()->startOfDay())
            ->exists();

        if ($sudahUploadHariIni) {
            return response()->json([
                'message' => 'Maksimal 1 upload link IG per hari.',
            ], 409);
        }

        $bukti = BuktiIgStory::create([
            'customer_id' => $customerId,
            'admin_id' => null,
            'url_bukti' => $validated['url_bukti'],
            'status_verifikasi' => 'pending',
            'tanggal_kirim' => now(),
        ]);

        return (new BuktiIgStoryResource($bukti))->response()->setStatusCode(201);
    }
}