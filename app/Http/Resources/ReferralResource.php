<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Referral */
class ReferralResource extends JsonResource
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
            'referred_customer_id' => $this->referred_customer_id,
            'referred_customer' => $this->whenLoaded('referredCustomer', fn () => [
                'id' => $this->referredCustomer->id,
                'nama' => $this->referredCustomer->nama,
                'no_hp' => $this->referredCustomer->no_hp,
            ]),
            'kode_referral' => $this->kode_referral,
            'status_valid' => $this->status_valid,
            'tanggal' => $this->tanggal,
        ];
    }
}
