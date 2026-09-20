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
        Schema::create('hasil_spins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('transaksi_id')->nullable()->constrained('transaksis');
            $table->foreignId('bukti_ig_story_id')->nullable()->constrained('bukti_ig_stories');
            $table->string('jenis_reward', 100);
            $table->dateTime('tanggal_spin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_spins');
    }
};
