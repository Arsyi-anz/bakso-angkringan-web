<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class ApiTransaksiTest extends TestCase
{
    use RefreshDatabase;

    private function tokenPermintaan(string $noHp, string $password = 'rahasia123'): string
    {
        $response = $this->postJson('/api/login', [
            'no_hp' => $noHp,
            'password' => $password,
        ]);

        return $response->json('token');
    }

    public function test_customer_bisa_melihat_riwayat_transaksi(): void
    {
        $customer = Customer::where('no_hp', '081111')->firstOrFail();
        $token = $this->tokenPermintaan('081111');

        $response = $this->withToken($token)->getJson('/api/transaksi');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'customer_id',
                    'admin_id',
                    'total_transaksi' => '',
                    'tanggal_transaksi',
                    'detail_transaksis' => [
                        '*' => [
                            'id',
                            'transaksi_id',
                            'produk',
                            'jumlah',
                            'harga_satuan',
                            'subtotal',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertGreaterThan(0, count($response->json('data')));
    }

    public function test_customer_hanya_melihat_transaksi_miliknya(): void
    {
        // Customer kedua tanpa transaksi
        $lain = Customer::create([
            'nama' => 'Customer Lain',
            'no_hp' => '089999',
            'password' => 'rahasia123',
            'kode_referral' => 'ZZZ99999',
            'tanggal_daftar' => now(),
        ]);
        $token = $this->tokenPermintaan('089999');

        $response = $this->withToken($token)->getJson('/api/transaksi');

        $response->assertOk();
        $this->assertCount(0, $response->json('data'));
    }

    public function test_customer_tidak_bisa_lihat_transaksi_orang_lain(): void
    {
        $pemilik = Customer::where('no_hp', '081111')->firstOrFail();
        $orangLain = Customer::create([
            'nama' => 'Penyusup',
            'no_hp' => '088888',
            'password' => 'rahasia123',
            'kode_referral' => 'ZZZ88888',
            'tanggal_daftar' => now(),
        ]);
        $token = $this->tokenPermintaan('088888');

        $transaksiPemilik = $pemilik->transaksis()->first();

        $response = $this->withToken($token)->getJson("/api/transaksi/{$transaksiPemilik->id}");

        $response->assertForbidden(); // 403
    }

    public function test_customer_bisa_buat_transaksi_self_checkout(): void
    {
        $customer = Customer::where('no_hp', '081111')->firstOrFail();
        $token = $this->tokenPermintaan('081111');

        $produks = Produk::take(3)->get();
        $jumlah = [3, 2, 1];
        $harapkanTotal = 0;
        foreach ($produks as $i => $produk) {
            $harapkanTotal += $produk->harga * $jumlah[$i];
        }

        $response = $this->withToken($token)->postJson('/api/transaksi', [
            'produk_id' => $produks->pluck('id')->all(),
            'jumlah' => $jumlah,
        ]);

        $response->assertCreated();
        $this->assertEqualsWithDelta(
            (float) $harapkanTotal,
            (float) $response->json('data.total_transaksi'),
            0.01,
        );
        $this->assertCount(3, $response->json('data.detail_transaksis'));
    }

    public function test_buat_transaksi_dengan_produk_tidak_valid_ditolak(): void
    {
        $token = $this->tokenPermintaan('081111');

        $response = $this->withToken($token)->postJson('/api/transaksi', [
            'produk_id' => [999999],
            'jumlah' => [1],
        ]);

        $response->assertStatus(422);
    }
}
