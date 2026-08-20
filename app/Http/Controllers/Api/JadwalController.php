<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalController extends Controller
{
    use ApiResponser;

    public function index(Request $request)
    {
        try {
            $query = DB::table('v_jadwal_publik');

            if ($request->has('turnamen_id')) {
                $query->where('turnamen_id', $request->turnamen_id);
            }

            if ($request->has('tanggal')) {
                $query->whereDate('tanggal_pertandingan', $request->tanggal);
            }

            $jadwal = $query->get();

            return $this->successResponse($jadwal, 'Daftar jadwal pertandingan publik.');
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengambil data jadwal: '.$e->getMessage(), 500);
        }
    }

    public function show(string $id)
    {
        try {
            $jadwal = DB::table('v_jadwal_publik')->where('id', $id)->first();

            if (! $jadwal) {
                return $this->errorResponse('Jadwal pertandingan tidak ditemukan', 404);
            }

            return $this->successResponse($jadwal, 'Detail jadwal pertandingan.');
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengambil detail jadwal: '.$e->getMessage(), 500);
        }
    }
}
