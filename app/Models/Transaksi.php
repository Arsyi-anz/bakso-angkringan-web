<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $customer_id
 * @property int $admin_id
 * @property float $total_transaksi
 * @property \Illuminate\Support\Carbon $tanggal_transaksi
 */
class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'admin_id',
        'total_transaksi',
        'tanggal_transaksi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_transaksi' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function detailTransaksis(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class);
    }

    public function hasilSpins(): HasMany
    {
        return $this->hasMany(HasilSpin::class);
    }
}
