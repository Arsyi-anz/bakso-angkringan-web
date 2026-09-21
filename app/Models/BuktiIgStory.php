<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $customer_id
 * @property int|null $admin_id
 * @property string $url_bukti
 * @property string $status_verifikasi
 * @property \Illuminate\Support\Carbon $tanggal_kirim
 */
class BuktiIgStory extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'admin_id',
        'url_bukti',
        'status_verifikasi',
        'tanggal_kirim',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kirim' => 'datetime',
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

    public function hasilSpins(): HasMany
    {
        return $this->hasMany(HasilSpin::class);
    }
}
