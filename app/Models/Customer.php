<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $nama
 * @property string $no_hp
 * @property string $password
 * @property string|null $kode_referral
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

    public function referralGiven()
    {
        return $this->hasMany(Referral::class);
    }

    public function referrals()
    {
        return $this->hasMany(Referral::class);
    }
}
