<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

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

    public const SYARAT_TRANSAKSI = 8;

    public const SYARAT_REFERRAL = 3;

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

    /**
     * Jumlah customer yang memenuhi syarat hampers dalam rentang tanggal,
     * dihitung langsung dari data riil (transaksi & referral valid).
     */
    public static function countLayakPeriode(Carbon $awal, Carbon $akhir): int
    {
        return Customer::query()
            ->withCount([
                'transaksis' => fn ($q) => $q->whereBetween('tanggal_transaksi', [$awal, $akhir]),
                'referralGiven' => fn ($q) => $q->where('status_valid', 'valid')
                    ->whereBetween('tanggal', [$awal, $akhir]),
            ])
            ->get()
            ->filter(fn (Customer $customer) => $customer->transaksis_count >= self::SYARAT_TRANSAKSI
                && $customer->referral_given_count >= self::SYARAT_REFERRAL)
            ->count();
    }

    /**
     * Hitung ulang kelayakan hampers untuk satu periode dari data riil
     * (transaksi & referral valid). Hanya customer yang beraktivitas di
     * periode tersebut yang ditulis.
     */
    public static function sinkronkanPeriode(string $periode, ?int $customerId = null): void
    {
        $awal = Carbon::parse($periode.'-01')->startOfDay();
        $akhir = $awal->copy()->endOfMonth();

        Customer::query()
            ->where(function ($query) use ($awal, $akhir) {
                $query->whereHas('transaksis', fn ($q) => $q->whereBetween('tanggal_transaksi', [$awal, $akhir]))
                    ->orWhereHas('referralGiven', fn ($q) => $q->where('status_valid', 'valid')
                        ->whereBetween('tanggal', [$awal, $akhir]));
            })
            ->when($customerId, fn ($query) => $query->whereKey($customerId))
            ->get(['id'])
            ->each(function (Customer $customer) use ($periode, $awal, $akhir) {
                $jumlahTransaksi = $customer->transaksis()
                    ->whereBetween('tanggal_transaksi', [$awal, $akhir])
                    ->count();

                $jumlahReferral = $customer->referralGiven()
                    ->where('status_valid', 'valid')
                    ->whereBetween('tanggal', [$awal, $akhir])
                    ->count();

                $layak = $jumlahTransaksi >= self::SYARAT_TRANSAKSI
                    && $jumlahReferral >= self::SYARAT_REFERRAL;

                self::updateOrCreate(
                    [
                        'customer_id' => $customer->id,
                        'periode' => $periode,
                    ],
                    [
                        'jumlah_repeat_order' => $jumlahTransaksi,
                        'jumlah_referral' => $jumlahReferral,
                        'status_kelayakan' => $layak ? 'memenuhi' : 'belum memenuhi',
                    ],
                );
            });
    }
}