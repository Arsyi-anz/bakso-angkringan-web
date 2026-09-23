<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $defaultEmail = env('ADMIN_EMAIL', 'admin@bakso.angkringan');
        $defaultPassword = env('ADMIN_PASSWORD', 'admin123');

        // Model Admin me-cast password => hashed, jadi string plain di-hash otomatis.
        // updateOrCreate: bila admin sudah ada, nama & password tetap disinkronkan saat reseed.
        Admin::updateOrCreate(
            ['email' => $defaultEmail],
            [
                'nama' => env('ADMIN_NAME', 'Admin'),
                'password' => $defaultPassword,
            ],
        );
    }
}