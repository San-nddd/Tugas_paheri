<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PertandinganResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_pertandingan' => $this->id_pertandingan,
            'id_turnamen' => $this->id_turnamen,
            'babak' => $this->babak,
            'tim_1' => [
                'id_tim' => $this->id_tim_1,
                'nama_tim' => $this->whenLoaded('timSatu', fn () => $this->timSatu?->nama_tim),
            ],
            'tim_2' => [
                'id_tim' => $this->id_tim_2,
                'nama_tim' => $this->whenLoaded('timDua', fn () => $this->timDua?->nama_tim),
            ],
            'skor_1' => $this->skor_1,
            'skor_2' => $this->skor_2,
            'tim_pemenang' => [
                'id_tim' => $this->id_tim_pemenang,
                'nama_tim' => $this->whenLoaded('timPemenang', fn () => $this->timPemenang?->nama_tim),
            ],
            'bukti_hasil' => $this->bukti_hasil,
            'status_pertandingan' => $this->status_pertandingan,
            'next_match_id' => $this->next_match_id,
            'dibuat_pada' => $this->created_at?->toIso8601String(),
            'diperbarui_pada' => $this->updated_at?->toIso8601String(),
        ];
    }
}
