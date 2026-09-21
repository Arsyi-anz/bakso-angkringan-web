<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HasilSpin extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'transaksi_id',
        'bukti_ig_story_id',
        'jenis_reward',
        'tanggal_spin',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_spin' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function buktiIgStory(): BelongsTo
    {
        return $this->belongsTo(BuktiIgStory::class);
    }

    public function voucher(): HasOne
    {
        return $this->hasOne(Voucher::class, 'hasil_spin_id');
    }
}
