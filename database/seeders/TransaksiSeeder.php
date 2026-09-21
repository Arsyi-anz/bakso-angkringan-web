<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\Produk;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;

class TransaksiSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = Admin::first() ?? Admin::create([
            'nama' => 'Admin Kasir',
            'email' => 'kasir@angkringan.test',
            'password' => bcrypt('rahasia123'),
        ]);

        $customer = Customer::where('no_hp', '081111')->first()
            ?? Customer::create([
                'nama' => 'Person B',
                'no_hp' => '081111',
                'password' => bcrypt('rahasia123'),
                'kode_referral' => 'ABC12345',
                'tanggal_daftar' => now(),
            ]);

        // Perlu produk hasil seed sebelumnya
        $produks = Produk::limit(3)->get();
        if ($produks->count() < 3) {
            throw new \RuntimeException('Seeder transaksi butuh minimal 3 produk. Jalankan ProdukSeeder dulu.');
        }

        // Keranjang dummy -> total >= 100000 (syarat spin B5)
        $keranjang = [
            ['produk' => $produks[0], 'jumlah' => 3],
            ['produk' => $produks[1], 'jumlah' => 2],
            ['produk' => $produks[2], 'jumlah' => 2],
        ];

        $detailTransaksis = [];
        $totalTransaksi = 0;

        foreach ($keranjang as $item) {
            $hargaSatuan = $item['produk']->harga;
            $subtotal = $hargaSatuan * $item['jumlah'];

            $detailTransaksis[] = [
                'produk' => $item['produk'],
                'jumlah' => $item['jumlah'],
                'harga_satuan' => $hargaSatuan,
                'subtotal' => $subtotal,
            ];

            $totalTransaksi += $subtotal;
        }

        $transaksi = Transaksi::firstOrCreate(
            [
                'customer_id' => $customer->id,
                'tanggal_transaksi' => now()->subDay(),
            ],
            [
                'admin_id' => $admin->id,
                'total_transaksi' => $totalTransaksi,
            ],
        );

        foreach ($detailTransaksis as $item) {
            DetailTransaksi::firstOrCreate(
                [
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $item['produk']->id,
                ],
                [
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $item['subtotal'],
                ],
            );
        }
    }
}
