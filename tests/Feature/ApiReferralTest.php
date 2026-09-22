<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Referral;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiReferralTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $pemberi;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pemberi = Customer::create([
            'nama' => 'Pemberi Kode',
            'no_hp' => '08140001',
            'password' => 'password',
            'kode_referral' => 'REF-PEMBERI',
            'tanggal_daftar' => now(),
        ]);

        $this->token = $this->pemberi->createToken('flutter-app')->plainTextToken;
    }

    protected function loginHeaders(): array
    {
        return [
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ];
    }

    public function test_pemberi_melihat_daftar_referral_sendiri(): void
    {
        $referred = Customer::create([
            'nama' => 'Anak Pemberi',
            'no_hp' => '08140002',
            'password' => 'password',
            'kode_referral' => 'REF-ANAK',
            'tanggal_daftar' => now(),
        ]);

        Referral::create([
            'customer_id' => $this->pemberi->id,
            'referred_customer_id' => $referred->id,
            'kode_referral' => 'REF-ANAK',
            'status_valid' => 'valid',
            'tanggal' => now(),
        ]);

        $this->withHeaders($this->loginHeaders())
            ->getJson('/api/referral')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.referred_customer.nama', 'Anak Pemberi');
    }

    public function test_customer_tanpa_login_tidak_bisa_lihat_referral(): void
    {
        $this->getJson('/api/referral')
            ->assertStatus(401);
    }

    public function test_tidak_bisa_lihat_referral_orang_lain(): void
    {
        $referred = Customer::create([
            'nama' => 'Orang Lain',
            'no_hp' => '08140003',
            'password' => 'password',
            'kode_referral' => 'REF-OL',
            'tanggal_daftar' => now(),
        ]);

        $referral = Referral::create([
            'customer_id' => $this->pemberi->id,
            'referred_customer_id' => $referred->id,
            'kode_referral' => 'REF-OL',
            'status_valid' => 'valid',
            'tanggal' => now(),
        ]);

        $this->withHeaders($this->loginHeaders())
            ->getJson('/api/referral/'.$referral->id)
            ->assertStatus(200)
            ->assertJsonPath('data.id', $referral->id);
    }

    public function test_customer_lain_tidak_bisa_buka_referral_pemberi_lain(): void
    {
        $referred = Customer::create([
            'nama' => 'Orang Ketiga',
            'no_hp' => '08140004',
            'password' => 'password',
            'kode_referral' => 'REF-OK',
            'tanggal_daftar' => now(),
        ]);

        $referral = Referral::create([
            'customer_id' => $this->pemberi->id,
            'referred_customer_id' => $referred->id,
            'kode_referral' => 'REF-OK',
            'status_valid' => 'valid',
            'tanggal' => now(),
        ]);

        $penyusup = Customer::create([
            'nama' => 'Penyusup',
            'no_hp' => '08140005',
            'password' => 'password',
            'kode_referral' => 'REF-PS',
            'tanggal_daftar' => now(),
        ]);

        $token = $penyusup->createToken('flutter-app')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])
            ->getJson('/api/referral/'.$referral->id)
            ->assertStatus(403);
    }
}
