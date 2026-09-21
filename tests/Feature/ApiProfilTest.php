<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiProfilTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected Customer $customer;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = Customer::create([
            'nama' => 'Via Profil',
            'no_hp' => '08130001',
            'password' => 'password',
            'kode_referral' => 'REF-PROFIL',
            'tanggal_daftar' => now(),
        ]);

        $this->token = $this->customer->createToken('flutter-app')->plainTextToken;
    }

    protected function loginHeaders(): array
    {
        return [
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ];
    }

    public function test_customer_lihat_profil_sendiri(): void
    {
        $this->withHeaders($this->loginHeaders())
            ->getJson('/api/profil')
            ->assertStatus(200)
            ->assertJsonPath('data.nama', 'Via Profil')
            ->assertJsonPath('data.no_hp', '08130001')
            ->assertJsonPath('data.kode_referral', 'REF-PROFIL')
            ->assertJsonMissingPath('data.password')
            ->assertJsonMissingPath('data.token');
    }

    public function test_customer_tanpa_login_tidak_bisa_lihat_profil(): void
    {
        $this->getJson('/api/profil')
            ->assertStatus(401);
    }
}
