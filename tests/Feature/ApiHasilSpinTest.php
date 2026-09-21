<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\BuktiIgStory;
use App\Models\Customer;
use App\Models\HasilSpin;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiHasilSpinTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected Customer $customer;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = Customer::create([
            'nama' => 'Via Customer',
            'no_hp' => '08120001',
            'password' => 'password',
            'kode_referral' => 'REF-VIA1',
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

    protected function makeTransaksi(int $total): Transaksi
    {
        $transaksi = Transaksi::create([
            'customer_id' => $this->customer->id,
            'admin_id' => null,
            'total_transaksi' => $total,
            'tanggal_transaksi' => now(),
        ]);

        $produk = Produk::first() ?? Produk::create([
            'nama_produk' => 'Bakso Jumbo',
            'harga' => 50000,
        ]);

        DetailTransaksi::create([
            'transaksi_id' => $transaksi->id,
            'produk_id' => $produk->id,
            'jumlah' => 1,
            'harga_satuan' => $produk->harga,
            'subtotal' => $produk->harga,
        ]);

        $transaksi->load('detailTransaksis');
        return $transaksi;
    }

    public function test_spin_ditolak_tanpa_sumber(): void
    {
        $response = $this->withHeaders($this->loginHeaders())
            ->postJson('/api/spin', []);

        $response->assertStatus(422);
    }

    public function test_spin_ditolak_pakai_transaksi_orang_lain(): void
    {
        $orangLain = Customer::create([
            'nama' => 'Orang Lain',
            'no_hp' => '08120002',
            'password' => 'password',
            'kode_referral' => 'REF-ORANG-LAIN',
            'tanggal_daftar' => now(),
        ]);
        $transaksiLain = $this->makeTransaksi(150000);
        $transaksiLain->update(['customer_id' => $orangLain->id]);

        $response = $this->withHeaders($this->loginHeaders())
            ->postJson('/api/spin', ['transaksi_id' => $transaksiLain->id]);

        $response->assertStatus(404);
    }

    public function test_spin_ditolak_transaksi_di_bawah_100rb(): void
    {
        $transaksi = $this->makeTransaksi(50000);

        $response = $this->withHeaders($this->loginHeaders())
            ->postJson('/api/spin', ['transaksi_id' => $transaksi->id]);

        $response->assertStatus(403)
            ->assertJson(['message' => 'Minimal transaksi 100.000 untuk spin.']);
    }

    public function test_spin_transaksi_100rb_memberi_voucher(): void
    {
        $transaksi = $this->makeTransaksi(100000);

        $response = $this->withHeaders($this->loginHeaders())
            ->postJson('/api/spin', ['transaksi_id' => $transaksi->id]);

        $response->assertStatus(201)
            ->assertJsonPath('data.jenis_reward', 'voucher')
            ->assertJsonStructure([
                'data' => [
                    'voucher' => [
                        'kode_voucher',
                        'status',
                        'tanggal_kadaluarsa',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('hasil_spins', [
            'customer_id' => $this->customer->id,
            'transaksi_id' => $transaksi->id,
        ]);
    }

    public function test_transaksi_hanya_bisa_di_spin_sekali(): void
    {
        $transaksi = $this->makeTransaksi(100000);

        $this->withHeaders($this->loginHeaders())
            ->postJson('/api/spin', ['transaksi_id' => $transaksi->id])
            ->assertStatus(201);

        $response = $this->withHeaders($this->loginHeaders())
            ->postJson('/api/spin', ['transaksi_id' => $transaksi->id]);

        $response->assertStatus(409);
    }

    public function test_spin_bukti_ig_story_belum_diterima_ditolak(): void
    {
        $bukti = BuktiIgStory::create([
            'customer_id' => $this->customer->id,
            'admin_id' => null,
            'url_bukti' => 'https://ig.com/story/abc',
            'status_verifikasi' => 'pending',
            'tanggal_kirim' => now(),
        ]);

        $response = $this->withHeaders($this->loginHeaders())
            ->postJson('/api/spin', ['bukti_ig_story_id' => $bukti->id]);

        $response->assertStatus(403)
            ->assertJsonPath('message', 'Bukti IG Story harus berstatus diterima.');
    }

    public function test_spin_bukti_ig_story_diterima_hari_yang_sama_ditolak(): void
    {
        $bukti = BuktiIgStory::create([
            'customer_id' => $this->customer->id,
            'admin_id' => null,
            'url_bukti' => 'https://ig.com/story/xyz',
            'status_verifikasi' => 'diterima',
            'tanggal_kirim' => now(),
        ]);

        $this->withHeaders($this->loginHeaders())
            ->postJson('/api/spin', ['bukti_ig_story_id' => $bukti->id])
            ->assertStatus(201);

        $response = $this->withHeaders($this->loginHeaders())
            ->postJson('/api/spin', ['bukti_ig_story_id' => $bukti->id]);

        $response->assertStatus(409);
    }

    public function test_customer_melihat_riwayat_spin_sendiri(): void
    {
        $transaksi = $this->makeTransaksi(100000);
        $transaksi->refresh();
        
        $this->withHeaders($this->loginHeaders())
            ->postJson('/api/spin', ['transaksi_id' => $transaksi->id])
            ->assertStatus(201);

        $response = $this->withHeaders($this->loginHeaders())
            ->getJson('/api/spin/riwayat');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'jenis_reward',
                        'tanggal_spin',
                        'voucher',
                    ],
                ],
            ]);
    }
}
