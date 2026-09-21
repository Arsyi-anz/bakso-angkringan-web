<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTransaksiTest extends TestCase
{
    use RefreshDatabase;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->token = $this->tokenCustomer('081111');
    }

    private function tokenCustomer(string $noHp): string
    {
        $customer = Customer::where('no_hp', $noHp)->firstOrFail();

        return $customer->createToken('test')->plainTextToken;
    }

    public function test_customer_bisa_lihat_riwayat_transaksi(): void
    {
        $this->withToken($this->token)
            ->getJson('/api/transaksi')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'customer_id',
                        'admin_id',
                        'total_transaksi',
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
    }

    public function test_customer_bisa_lihat_detail_transaksi_sendiri(): void
    {
        $transaksiId = Transaksi::where('customer_id', Customer::where('no_hp', '081111')->firstOrFail()->id)
            ->firstOrFail()->id;

        $this->withToken($this->token)
            ->getJson("/api/transaksi/{$transaksiId}")
            ->assertOk()
            ->assertJsonPath('data.id', $transaksiId);
    }

    public function test_customer_tidak_bisa_lihat_transaksi_orang_lain(): void
    {
        $customerLain = Customer::create([
            'nama' => 'Customer Lain',
            'no_hp' => '088888',
            'password' => 'rahasia123',
            'kode_referral' => 'ZZZ99999',
            'tanggal_daftar' => now(),
        ]);

        $transaksiOrangLain = $customerLain->transaksis()->create([
            'admin_id' => null,
            'total_transaksi' => 50000,
            'tanggal_transaksi' => now(),
        ]);

        $this->withToken($this->token)
            ->getJson("/api/transaksi/{$transaksiOrangLain->id}")
            ->assertForbidden();
    }

    public function test_customer_bisa_buat_transaksi_self_checkout(): void
    {
        $produks = Produk::take(3)->get();
        $jumlah = [3, 2, 1];

        $respon = $this->withToken($this->token)
            ->postJson('/api/transaksi', [
                'produk_id' => $produks->pluck('id')->all(),
                'jumlah' => $jumlah,
            ]);

        $respon->assertCreated();

        $totalHarapan = $produks->sum(
            fn ($p, $i) => $p->harga * $jumlah[$i],
        );

        $this->assertEqualsWithDelta(
            (float) $totalHarapan,
            (float) $respon->json('data.total_transaksi'),
            0.01,
        );
    }

    public function test_customer_harus_mengisi_produk_dan_jumlah(): void
    {
        $this->withToken($this->token)
            ->postJson('/api/transaksi', [])
            ->assertStatus(422);
    }

    public function test_customer_bisa_buat_transaksi_dengan_produk_tidak_ada(): void
    {
        $this->withToken($this->token)
            ->postJson('/api/transaksi', [
                'produk_id' => [999999],
                'jumlah' => [1],
            ])
            ->assertStatus(422);
    }
}
