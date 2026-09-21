<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Referral;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Customer::create([
            'nama' => 'Pemberi',
            'no_hp' => '08150001',
            'password' => 'password',
            'kode_referral' => 'REF-GIVER1',
            'tanggal_daftar' => now(),
        ]);
    }

    public function test_register_pakai_kode_referral_membuat_referral_valid(): void
    {
        $response = $this->postJson('/api/register', [
            'nama' => 'Customer Baru',
            'no_hp' => '08150002',
            'password' => 'password',
            'kode_referral' => 'REF-GIVER1',
        ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'nama',
                    'no_hp',
                    'kode_referral',
                    'tanggal_daftar',
                ],
                'token',
            ]);

        $pemberi = Customer::where('kode_referral', 'REF-GIVER1')->firstOrFail();

        $this->assertDatabaseHas('referrals', [
            'customer_id' => $pemberi->id,
            'status_valid' => 'valid',
            'kode_referral' => $response->json('data.kode_referral'),
        ]);

        $this->assertSame(1, Referral::where('customer_id', $pemberi->id)->count());
    }
}