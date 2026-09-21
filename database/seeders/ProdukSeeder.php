<?php

namespace Database\Seeders;

use App\Models\Produk;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        $produks = [
            ['nama_produk' => 'Bakso Sapi Jumbo', 'harga' => 15000],
            ['nama_produk' => 'Bakso Urat', 'harga' => 13000],
            ['nama_produk' => 'Bakso Campur', 'harga' => 16000],
            ['nama_produk' => 'Bakso Telur', 'harga' => 14000],
            ['nama_produk' => 'Bakso Mercon', 'harga' => 18000],
            ['nama_produk' => 'Bakso Sapi Komplit', 'harga' => 20000],
        ];

        foreach ($produks as $produk) {
            Produk::firstOrCreate(['nama_produk' => $produk['nama_produk']], $produk);
        }
    }
}
