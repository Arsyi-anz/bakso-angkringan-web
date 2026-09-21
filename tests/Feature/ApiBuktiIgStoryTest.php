<?php

namespace Tests\Feature;

use App\Models\BuktiIgStory;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiBuktiIgStoryTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $customer;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = Customer::create([
            'nama' => 'Via Customer',
            'no_hp' => '08130001',
            'password' => 'password',
            'kode_referral' => 'REF-BUKTI1',
            'tanggal_daftar' => now(),
        ]);

        $this->token = $this->customer->createToken('test')->plainTextToken;
    }

    protected function loginHeaders(): array
    {
        return [
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ];
    }

    protected function makeBukti(string $url, string $status = 'pending'): BuktiIgStory
    {
        return BuktiIgStory::create([
            'customer_id' => $this->customer->id,
            'admin_id' => null,
            'url_bukti' => $url,
            'status_verifikasi' => $status,
            'tanggal_kirim' => now(),
        ]);
    }

    public function test_customer_bisa_upload_link_ig_story(): void
    {
        $response = $this->withHeaders($this->loginHeaders())
            ->postJson('/api/bukti-ig-story', [
                'url_bukti' => 'https://ig.com/story/abc123',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.status_verifikasi', 'pending')
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'url_bukti',
                    'status_verifikasi',
                    'tanggal_kirim',
                    'masih_valid',
                ],
            ]);
    }

    public function test_upload_tanpa_url_ditolak(): void
    {
        $response = $this->withHeaders($this->loginHeaders())
            ->postJson('/api/bukti-ig-story', []);

        $response->assertStatus(422);
    }

    public function test_upload_url_tidak_valid_ditolak(): void
    {
        $response = $this->withHeaders($this->loginHeaders())
            ->postJson('/api/bukti-ig-story', [
                'url_bukti' => 'bukan-url',
            ]);

        $response->assertStatus(422);
    }

    public function test_hanya_bisa_upload_satu_link_per_hari(): void
    {
        $this->makeBukti('https://ig.com/story/pertama');

        $response = $this->withHeaders($this->loginHeaders())
            ->postJson('/api/bukti-ig-story', [
                'url_bukti' => 'https://ig.com/story/kedua',
            ]);

        $response->assertStatus(409)
            ->assertJsonPath('message', 'Maksimal 1 upload link IG per hari.');
    }

    public function test_bisa_upload_lagi_keesokan_hari(): void
    {
        BuktiIgStory::create([
            'customer_id' => $this->customer->id,
            'admin_id' => null,
            'url_bukti' => 'https://ig.com/story/kemarin',
            'status_verifikasi' => 'pending',
            'tanggal_kirim' => now()->subDay(),
        ]);

        $response = $this->withHeaders($this->loginHeaders())
            ->postJson('/api/bukti-ig-story', [
                'url_bukti' => 'https://ig.com/story/hari-ini',
            ]);

        $response->assertCreated();
    }

    public function test_customer_melihat_daftar_upload_sendiri(): void
    {
        $this->makeBukti('https://ig.com/story/satu', 'diterima');
        $this->makeBukti('https://ig.com/story/dua');

        $customerLain = Customer::create([
            'nama' => 'Customer Lain',
            'no_hp' => '08130002',
            'password' => 'password',
            'kode_referral' => 'REF-BUKTI-2',
            'tanggal_daftar' => now(),
        ]);

        BuktiIgStory::create([
            'customer_id' => $customerLain->id,
            'admin_id' => null,
            'url_bukti' => 'https://ig.com/story/ora',
            'status_verifikasi' => 'pending',
            'tanggal_kirim' => now(),
        ]);

        $response = $this->withHeaders($this->loginHeaders())
            ->getJson('/api/bukti-ig-story');

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonMissingExact(['url_bukti' => 'https://ig.com/story/ora']);
    }
}