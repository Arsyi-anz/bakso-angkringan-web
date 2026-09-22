<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $defaultEmail = env('ADMIN_EMAIL', 'admin@bakso.angkringan');
        $defaultPassword = env('ADMIN_PASSWORD', 'password123');

        // Model Admin me-cast password => hashed, jadi string plain di-hash otomatis.
        Admin::firstOrCreate(
            ['email' => $defaultEmail],
            [
                'nama' => env('ADMIN_NAME', 'Admin'),
                'password' => $defaultPassword,
            ],
        );
    }
}