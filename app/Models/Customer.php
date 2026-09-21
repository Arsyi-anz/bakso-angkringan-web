<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $nama
 * @property string $no_hp
 * @property string $password
 * @property string $kode_referral
 * @property \Illuminate\Support\Carbon $tanggal_daftar
 */
class Customer extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nama',
        'no_hp',
        'password',
        'kode_referral',
        'tanggal_daftar',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'tanggal_daftar' => 'datetime',
        ];
    }

    /**
     * Referral yang DIBERIKAN customer ini (customer_id = pemberi).
     */
    public function referralGiven(): HasMany
    {
        return $this->hasMany(Referral::class, 'customer_id');
    }

    /**
     * Referral yang DITERIMA customer ini (referred_customer_id).
     */
    public function referralReceived(): HasMany
    {
        return $this->hasMany(Referral::class, 'referred_customer_id');
    }

    public function transaksis(): HasMany
    {
        return $this->hasMany(Transaksi::class);
    }
}
