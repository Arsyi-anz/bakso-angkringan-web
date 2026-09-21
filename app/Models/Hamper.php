<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $customer_id
 * @property string|null $periode
 * @property int $jumlah_repeat_order
 * @property int $jumlah_referral
 * @property string $status_kelayakan
 */
class Hamper extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'periode',
        'jumlah_repeat_order',
        'jumlah_referral',
        'status_kelayakan',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}