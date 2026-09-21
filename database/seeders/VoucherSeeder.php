<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\HasilSpin;
use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $customer = Customer::firstOrCreate(
            ['no_hp' => '081111'],
            [
                'nama' => 'Person B',
                'password' => bcrypt('rahasia123'),
                'kode_referral' => 'ABC12345',
                'tanggal_daftar' => now(),
            ],
        );

        $hashSpin = HasilSpin::firstOrCreate(
            ['customer_id' => $customer->id, 'jenis_reward' => 'voucher'],
            ['tanggal_spin' => now()],
        );

        Voucher::firstOrCreate(
            ['kode_voucher' => 'SPIN-001'],
            [
                'hasil_spin_id' => $hashSpin->id,
                'customer_id' => $customer->id,
                'status' => 'aktif',
                'tanggal_kadaluarsa' => now()->addDays(7),
            ],
        );
    }
}
