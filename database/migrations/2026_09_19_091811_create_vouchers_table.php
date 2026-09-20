<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hasil_spin_id')->unique()->constrained('hasil_spins');
            $table->foreignId('customer_id')->constrained('customers');
            $table->string('kode_voucher', 30)->unique();
            $table->enum('status', ['aktif', 'terpakai', 'kedaluwarsa'])
                ->default('aktif');
            $table->dateTime('tanggal_kadaluarsa');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
