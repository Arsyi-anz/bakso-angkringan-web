<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\HasilSpin;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiVoucherTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $customer;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = Customer::create([
            'nama' => 'Via Voucher',
            'no_hp' => '08160001',
            'password' => 'password',
            'kode_referral' => 'REF-VOUCHER',
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

    protected function makeVoucher(string $status, ?int $daysToExpiry = 30): Voucher
    {
        $hasilSpin = HasilSpin::create([
            'customer_id' => $this->customer->id,
            'transaksi_id' => null,
            'bukti_ig_story_id' => null,
            'jenis_reward' => 'voucher',
            'tanggal_spin' => now(),
        ]);

        return Voucher::create([
            'hasil_spin_id' => $hasilSpin->id,
            'customer_id' => $this->customer->id,
            'kode_voucher' => 'V-'.strtoupper(uniqid()),
            'status' => $status,
            'tanggal_kadaluarsa' => now()->addDays($daysToExpiry),
        ]);
    }

    public function test_voucher_aktif_muncul(): void
    {
        $this->makeVoucher('aktif');

        $this->withHeaders($this->loginHeaders())
            ->getJson('/api/voucher')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', 'aktif');
    }

    public function test_voucher_terpakai_tetap_muncul(): void
    {
        $this->makeVoucher('terpakai');

        $this->withHeaders($this->loginHeaders())
            ->getJson('/api/voucher')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', 'terpakai');
    }

    public function test_mix_semua_status_tetap_terkirim(): void
    {
        $this->makeVoucher('aktif');
        $this->makeVoucher('terpakai');
        $this->makeVoucher('kedaluwarsa', -1);

        $response = $this->withHeaders($this->loginHeaders())
            ->getJson('/api/voucher')
            ->assertStatus(200)
            ->assertJsonCount(3, 'data');

        $statuses = collect($response->json('data'))->pluck('status');
        $this->assertTrue($statuses->contains('aktif'));
        $this->assertTrue($statuses->contains('terpakai'));
        $this->assertTrue($statuses->contains('kedaluwarsa'));
    }

    public function test_voucher_orang_lain_tidak_terkirim(): void
    {
        $orangLain = Customer::create([
            'nama' => 'Lainnya',
            'no_hp' => '08160002',
            'password' => 'password',
            'kode_referral' => 'REF-LAIN',
            'tanggal_daftar' => now(),
        ]);

        $hasilSpin = HasilSpin::create([
            'customer_id' => $orangLain->id,
            'transaksi_id' => null,
            'bukti_ig_story_id' => null,
            'jenis_reward' => 'voucher',
            'tanggal_spin' => now(),
        ]);

        Voucher::create([
            'hasil_spin_id' => $hasilSpin->id,
            'customer_id' => $orangLain->id,
            'kode_voucher' => 'V-ORANGLAIN',
            'status' => 'aktif',
            'tanggal_kadaluarsa' => now()->addDays(30),
        ]);

        $this->withHeaders($this->loginHeaders())
            ->getJson('/api/voucher')
            ->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    public function test_tanpa_login_tidak_bisa_akses_voucher(): void
    {
        $this->getJson('/api/voucher')
            ->assertStatus(401);
    }
}