<?php

namespace App\Http\Controllers\Api; 

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use App\Traits\ApiResponseTrait;

class JadwalController extends Controller
{
    use ApiResponseTrait; 
    /**
     * Menampilkan daftar jadwal pertandingan publik.
     */
    public function index(Request $request)
    {
        try {
            // Mengambil query dasar dari view database
            $query = DB::table('v_jadwal_publik');

            // Opsional: Filter berdasarkan turnamen jika ada parameter ?turnamen_id=
            if ($request->has('turnamen_id')) {
                $query->where('turnamen_id', $request->turnamen_id);
            }

            // Opsional: Filter berdasarkan tanggal jika ada parameter ?tanggal=YYYY-MM-DD
            if ($request->has('tanggal')) {
                $query->whereDate('tanggal_pertandingan', $request->tanggal);
            }

            $jadwal = $query->get();

            return response()->json([
                'status'  => 'success',
                'message' => 'Daftar jadwal pertandingan publik',
                'data'    => $jadwal
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengambil data jadwal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan detail 1 jadwal berdasarkan ID.
     */
    public function show(string $id)
    {
        try {
            $jadwal = DB::table('v_jadwal_publik')->where('id', $id)->first();

            if (! $jadwal) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Jadwal pertandingan tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Detail jadwal pertandingan',
                'data'    => $jadwal
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengambil detail jadwal: ' . $e->getMessage()
            ], 500);
        }
    }
}