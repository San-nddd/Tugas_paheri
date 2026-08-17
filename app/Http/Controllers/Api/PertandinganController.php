<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pertandingan;
use App\Http\Requests\UpdateSkorRequest;
use App\Http\Resources\PertandinganResource;
use App\Traits\ApiResponser;

class PertandinganController extends Controller
{
    use ApiResponser;

    /**
     * Memperbarui skor menggunakan Route Model Binding (parameter {pertandingan}).
     */
    public function updateSkor(UpdateSkorRequest $request, Pertandingan $pertandingan)
    {
        try {
            // Field skor_1, skor_2, id_tim_pemenang sudah sesuai dengan DB dan Model
            $pertandingan->update([
                'skor_1'              => $request->validated('skor_1'),
                'skor_2'              => $request->validated('skor_2'),
                'id_tim_pemenang'     => $request->validated('id_tim_pemenang'),
                'bukti_hasil'         => $request->validated('bukti_hasil'),
                'status_pertandingan' => 'selesai'
            ]);

            // Melakukan Eager Loading untuk Resource (Pastikan relasi di Model sudah diubah ke tim1 & tim2)
            $pertandingan->load(['tim1', 'tim2', 'timPemenang']);

            return $this->successResponse(
                new PertandinganResource($pertandingan),
                'Skor pertandingan berhasil diperbarui.'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal memperbarui skor: ' . $e->getMessage(), 500);
        }
    }
}