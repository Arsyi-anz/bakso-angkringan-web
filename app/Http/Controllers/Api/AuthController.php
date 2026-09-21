<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\Referral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * A2 - Auth (register, login, logout)
 */
class AuthController extends Controller
{
    /**
     * Register customer baru + buat rujukan jika kode_referral diberikan.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'no_hp' => ['required', 'string', 'unique:customers,no_hp'],
            'password' => ['required', 'string', 'min:6'],
            'kode_referral' => ['nullable', 'string', 'exists:customers,kode_referral'],
            'tanggal_daftar' => ['nullable', 'date'],
        ]);

        // Referral: kalau ada kode_referral, maka customer ini direferensikan oleh pemilik kode tsb.
        $referralPemberi = null;
        if ($validated['kode_referral'] ?? null) {
            $referralPemberi = Customer::where('kode_referral', $validated['kode_referral'])->first();
        }

        $customer = DB::transaction(function () use ($validated, $referralPemberi): Customer {
            $customer = Customer::create([
                'nama' => $validated['nama'],
                'no_hp' => $validated['no_hp'],
                'password' => $validated['password'],
                'kode_referral' => $this->generateKodeReferral(),
                'tanggal_daftar' => $validated['tanggal_daftar'] ?? now(),
            ]);

            if ($referralPemberi) {
                Referral::create([
                    'customer_id' => $referralPemberi->id,
                    'referred_customer_id' => $customer->id,
                    'kode_referral' => $customer->kode_referral,
                    'status_valid' => 'valid',
                    'tanggal' => now(),
                ]);
            }

            return $customer;
        });

        $token = $customer->createToken('flutter-app')->plainTextToken;

        return (new CustomerResource($customer))
            ->additional(['token' => $token])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Login: no_hp + password -> token sanctum.
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'no_hp' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $customer = Customer::where('no_hp', $validated['no_hp'])->first();

        if (! $customer || ! Hash::check($validated['password'], $customer->password)) {
            return response()->json([
                'message' => 'Kredensial salah.',
            ], 401);
        }

        $token = $customer->createToken('flutter-app')->plainTextToken;

        return (new CustomerResource($customer))
            ->additional(['token' => $token])
            ->response();
    }

    /**
     * Logout: hapus token aktif.
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Berhasil logout.',
        ]);
    }

    /**
     * Kode referral unik untuk customer baru.
     */
    private function generateKodeReferral(): string
    {
        do {
            $kode = 'REF-'.strtoupper(Str::random(8));
        } while (Customer::where('kode_referral', $kode)->exists());

        return $kode;
    }
}
