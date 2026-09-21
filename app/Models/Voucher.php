<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $hasil_spin_id
 * @property int $customer_id
 * @property string $kode_voucher
 * @property string $status
 * @property \Illuminate\Support\Carbon $tanggal_kadaluarsa
 */
class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'hasil_spin_id',
        'customer_id',
        'kode_voucher',
        'keterangan',
        'status',
        'tanggal_kadaluarsa',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kadaluarsa' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function hasilSpin(): BelongsTo
    {
        return $this->belongsTo(HasilSpin::class, 'hasil_spin_id');
    }
}
