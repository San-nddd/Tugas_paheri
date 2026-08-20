<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TurnamenResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_turnamen' => $this->id_turnamen,
            'nama_turnamen' => $this->nama_turnamen,
            'deskripsi' => $this->deskripsi,
            'kuota_maksimal' => $this->kuota_maksimal,
            'biaya' => $this->biaya,
            'kode_akses' => $this->kode_akses,
            'status_turnamen' => $this->status_turnamen,
            'tanggal' => $this->tanggal?->toDateString(),
            'penyelenggara' => [
                'id_pengguna' => $this->penyelenggara?->id_pengguna,
                'nama' => $this->penyelenggara?->nama,
            ],
            'dibuat_pada' => $this->created_at?->toIso8601String(),
            'diperbarui_pada' => $this->updated_at?->toIso8601String(),
        ];
    }
}
