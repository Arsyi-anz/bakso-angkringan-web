<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    /**
     * Tampilkan profil customer login.
     */
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'data' => new CustomerResource($request->user()),
        ]);
    }
}
