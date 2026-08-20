<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSkorRequest;
use App\Http\Resources\PertandinganResource;
use App\Models\Pertandingan;
use App\Traits\ApiResponser;

class PertandinganController extends Controller
{
    use ApiResponser;

    public function updateSkor(UpdateSkorRequest $request, Pertandingan $pertandingan)
    {
        try {
            $pertandingan->update([
                'skor_1' => $request->validated('skor_1'),
                'skor_2' => $request->validated('skor_2'),
                'id_tim_pemenang' => $request->validated('id_tim_pemenang'),
                'bukti_hasil' => $request->validated('bukti_hasil'),
                'status_pertandingan' => 'selesai',
            ]);

            $pertandingan->load(['timSatu', 'timDua', 'timPemenang']);

            return $this->successResponse(
                new PertandinganResource($pertandingan),
                'Skor pertandingan berhasil diperbarui.'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal memperbarui skor: '.$e->getMessage(), 500);
        }
    }
}
