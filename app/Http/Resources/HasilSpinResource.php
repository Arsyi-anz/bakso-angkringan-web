<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\HasilSpin */
class HasilSpinResource extends JsonResource
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
            'customer_id' => $this->customer_id,
            'transaksi_id' => $this->transaksi_id,
            'bukti_ig_story_id' => $this->bukti_ig_story_id,
            'jenis_reward' => $this->jenis_reward,
            'tanggal_spin' => $this->tanggal_spin,
            'voucher' => new VoucherResource(
                $this->whenLoaded('voucher'),
            ),
        ];
    }
}
