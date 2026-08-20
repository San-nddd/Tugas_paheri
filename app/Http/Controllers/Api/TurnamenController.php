<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTurnamenRequest;
use App\Http\Resources\TurnamenResource;
use App\Models\Turnamen;
use App\Traits\ApiResponser;

class TurnamenController extends Controller
{
    use ApiResponser;

    // Endpoint Publik: Lihat daftar turnamen
    public function index()
    {
        try {
            $turnamen = Turnamen::with('penyelenggara')->where('status_turnamen', 'buka')->get();

            return $this->successResponse(
                TurnamenResource::collection($turnamen),
                'Berhasil mengambil daftar turnamen.'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengambil data: '.$e->getMessage(), 500);
        }
    }

    public function store(StoreTurnamenRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['id_penyelenggara'] = $request->user()->id_pengguna;
            $validatedData['status_turnamen'] = 'draf';

            $turnamen = Turnamen::create($validatedData);

            return $this->successResponse(
                new TurnamenResource($turnamen),
                'Turnamen berhasil dibuat sebagai draf.',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal membuat turnamen: '.$e->getMessage(), 500);
        }
    }
}
