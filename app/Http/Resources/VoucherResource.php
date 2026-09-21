<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Voucher */
class VoucherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_voucher' => $this->kode_voucher,
            'status' => $this->status,
            'tanggal_kadaluarsa' => $this->tanggal_kadaluarsa,
            'reward' => $this->whenLoaded('hasilSpin', fn () => [
                'jenis_reward' => $this->hasilSpin->jenis_reward,
            ]),
        ];
    }
}
