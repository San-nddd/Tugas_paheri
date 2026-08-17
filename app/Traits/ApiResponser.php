<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Dipakai di semua Controller agar setiap endpoint mengembalikan
 * struktur JSON yang seragam: { status, message, data }.
 */
trait ApiResponser
{
    protected function successResponse(
        JsonResource|ResourceCollection|array|null $data = null,
        string $message = 'Berhasil.',
        int $code = 200
    ): JsonResponse {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function errorResponse(
        string $message = 'Terjadi kesalahan.',
        int $code = 400,
        mixed $errors = null
    ): JsonResponse {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }
}
