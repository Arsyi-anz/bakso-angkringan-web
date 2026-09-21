<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Produk;
use App\Models\Referral;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiRewardTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $customer;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = Customer::create([
            'nama' => 'Via Customer',
            'no_hp' => '08140001',
            'password' => 'password',
            'kode_referral' => 'REF-REWARD1',
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

    protected function makeTransaksi(int $total, ?\DateTimeInterface $tanggal = null): Transaksi
    {
        return Transaksi::create([
            'customer_id' => $this->customer->id,
            'admin_id' => null,
            'total_transaksi' => $total,
            'tanggal_transaksi' => $tanggal ?? now(),
        ]);
    }

    protected function makeReferral(int $jumlah, ?\DateTimeInterface $tanggal = null, string $status = 'valid'): void
    {
        for ($i = 0; $i < $jumlah; $i++) {
            $referred = Customer::create([
                'nama' => 'Referred '.$i,
                'no_hp' => '08140002'.$i,
                'password' => 'password',
                'kode_referral' => 'REF-DOWN'.($this->customer->id).($i + 1),
                'tanggal_daftar' => $tanggal ?? now(),
            ]);

            Referral::create([
                'customer_id' => $this->customer->id,
                'referred_customer_id' => $referred->id,
                'kode_referral' => $this->customer->kode_referral,
                'status_valid' => $status,
                'tanggal' => $tanggal ?? now(),
            ]);
        }
    }

    public function test_reward_awal_belum_layak(): void
    {
        $this->makeTransaksi(120000);

        $response = $this->withHeaders($this->loginHeaders())
            ->getJson('/api/reward');

        $response->assertOk()
            ->assertJsonPath('data.layak', false)
            ->assertJsonPath('data.jumlah_transaksi_bulan', 1)
            ->assertJsonPath('data.total_transaksi_bulan', 120000)
            ->assertJsonPath('data.jumlah_referral_bulan', 0)
            ->assertJsonPath('data.syarat_transaksi', 8)
            ->assertJsonPath('data.syarat_referral', 3)
            ->assertJsonPath('data.pesan', 'Hampers bulanan belum layak. Penuhi ketentuan yang berlaku: minimal 8 transaksi dan 3 referral baru dalam bulan berjalan.');
    }

    public function test_reward_layak_dengan_8_transaksi_dan_3_referral(): void
    {
        for ($i = 0; $i < 8; $i++) {
            $this->makeTransaksi(100000 + ($i * 10000));
        }
        $this->makeReferral(3);

        $response = $this->withHeaders($this->loginHeaders())
            ->getJson('/api/reward');

        $response->assertOk()
            ->assertJsonPath('data.layak', true)
            ->assertJsonPath('data.jumlah_transaksi_bulan', 8)
            ->assertJsonPath('data.total_transaksi_bulan', 8 * 100000 + 28 * 10000)
            ->assertJsonPath('data.jumlah_referral_bulan', 3);
    }

    public function test_transaksi_bulan_lalu_tidak_dihitung(): void
    {
        for ($i = 0; $i < 7; $i++) {
            $this->makeTransaksi(100000);
        }
        $this->makeTransaksi(100000, now()->subMonth());
        $this->makeReferral(3, now()->subMonth());

        $response = $this->withHeaders($this->loginHeaders())
            ->getJson('/api/reward');

        $response->assertOk()
            ->assertJsonPath('data.jumlah_transaksi_bulan', 7)
            ->assertJsonPath('data.jumlah_referral_bulan', 0)
            ->assertJsonPath('data.layak', false);
    }

    public function test_referral_tidak_valid_tidak_dihitung(): void
    {
        for ($i = 0; $i < 8; $i++) {
            $this->makeTransaksi(100000);
        }
        $this->makeReferral(3, null, 'pending');

        $response = $this->withHeaders($this->loginHeaders())
            ->getJson('/api/reward');

        $response->assertOk()
            ->assertJsonPath('data.jumlah_referral_bulan', 0)
            ->assertJsonPath('data.layak', false);
    }

    public function test_reward_merekam_hamper_bulanan(): void
    {
        for ($i = 0; $i < 8; $i++) {
            $this->makeTransaksi(100000);
        }
        $this->makeReferral(3);

        $this->withHeaders($this->loginHeaders())
            ->getJson('/api/reward')
            ->assertOk();

        $periode = now()->format('Y-m');
        $this->assertDatabaseHas('hampers', [
            'customer_id' => $this->customer->id,
            'periode' => $periode,
            'jumlah_repeat_order' => 8,
            'jumlah_referral' => 3,
            'status_kelayakan' => 'memenuhi',
        ]);
    }
}