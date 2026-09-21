<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/** @mixin \App\Models\Transaksi */
class TransaksiResource extends JsonResource
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
            'admin_id' => $this->admin_id,
            'total_transaksi' => $this->total_transaksi,
            'tanggal_transaksi' => $this->tanggal_transaksi,
            'detail_transaksis' => DetailTransaksiResource::collection(
                $this->whenLoaded('detailTransaksis'),
            ),
        ];
    }
}
