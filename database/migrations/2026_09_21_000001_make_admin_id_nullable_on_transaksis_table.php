<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            // SQLite tak mendukung drop constraint langsung; ubah tanpa foreign key check.
            Schema::table('transaksis', function (Blueprint $table) {
                $table->unsignedBigInteger('admin_id')->nullable()->change();
            });
        } else {
            Db::statement('ALTER TABLE transaksis MODIFY admin_id BIGINT UNSIGNED NULL');
            Db::statement('ALTER TABLE transaksis DROP FOREIGN KEY transaksis_admin_id_foreign');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            Schema::table('transaksis', function (Blueprint $table) {
                $table->unsignedBigInteger('admin_id')->nullable(false)->change();
            });
        } else {
            Db::statement('ALTER TABLE transaksis MODIFY admin_id BIGINT UNSIGNED NOT NULL');
        }
    }
};
