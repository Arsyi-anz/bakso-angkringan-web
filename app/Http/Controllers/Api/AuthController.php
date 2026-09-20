<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'no_hp' => ['required', 'string', 'max:20', 'unique:customers,no_hp'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'kode_referral' => ['nullable', 'string', 'max:20'],
        ]);

        $customer = Customer::create([
            'nama' => $validated['nama'],
            'no_hp' => $validated['no_hp'],
            'password' => Hash::make($validated['password']),
            'kode_referral' => Str::upper(Str::random(8)),
            'tanggal_daftar' => now(),
        ]);

        // Referral: simpan sebagai pending bila kode referral diberikan
        if (! empty($validated['kode_referral'])) {
            $referrer = Customer::where('kode_referral', $validated['kode_referral'])->first();

            if ($referrer) {
                $customer->referralGiven()->create([
                    'referred_customer_id' => $customer->id,
                    'kode_referral' => $validated['kode_referral'],
                    'status_valid' => 'pending',
                    'tanggal' => now(),
                ]);
            }
        }

        $token = $customer->createToken('customer-token')->plainTextToken;

        return (new CustomerResource($customer))
            ->additional(['token' => $token])
            ->response()
            ->setStatusCode(201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'no_hp' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $customer = Customer::where('no_hp', $credentials['no_hp'])->first();

        if (! $customer || ! Hash::check($credentials['password'], $customer->password)) {
            return response()->json([
                'message' => 'No. HP atau password salah.',
            ], 401);
        }

        $token = $customer->createToken('customer-token')->plainTextToken;

        return (new CustomerResource($customer))
            ->additional(['token' => $token])
            ->response();
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Berhasil logout.',
        ]);
    }
}
