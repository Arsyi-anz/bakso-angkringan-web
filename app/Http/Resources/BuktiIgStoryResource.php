<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\BuktiIgStory */
class BuktiIgStoryResource extends JsonResource
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
            'url_bukti' => $this->url_bukti,
            'status_verifikasi' => $this->status_verifikasi,
            'tanggal_kirim' => $this->tanggal_kirim,
            'masih_valid' => $this->tanggal_kirim->addHours(24)->isFuture(),
        ];
    }
}