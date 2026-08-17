<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contoh pemakaian di routes:
 *   Route::middleware(['auth:sanctum', 'role:admin'])->group(...)
 *   Route::middleware(['auth:sanctum', 'role:admin,penyelenggara'])->group(...)
 */
class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$peranDiizinkan): Response
    {
        $pengguna = $request->user();

        if (! $pengguna || ! in_array($pengguna->peran, $peranDiizinkan, true)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki hak akses untuk resource ini.',
                'data' => null,
            ], 403);
        }

        return $next($request);
    }
}
