<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $customer_id
 * @property int $referred_customer_id
 * @property string $kode_referral
 * @property string $status_valid
 * @property \Illuminate\Support\Carbon $tanggal
 */
class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'referred_customer_id',
        'kode_referral',
        'status_valid',
        'tanggal',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'datetime',
        ];
    }

    /**
     * Customer pemberi kode referral.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Customer baru yang terdaftar menggunakan kode ini.
     */
    public function referredCustomer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'referred_customer_id');
    }
}