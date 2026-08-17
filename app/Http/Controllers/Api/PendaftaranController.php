<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    use ApiResponser;

    public function setujui(Request $request, Pendaftaran $pendaftaran)
    {
        try {
            // Memastikan yang menyetujui adalah penyelenggara dari turnamen tersebut
            $pengguna = $request->user();
            if ($pendaftaran->turnamen->id_penyelenggara !== $pengguna->id_pengguna && !$pengguna->isAdmin()) {
                return $this->errorResponse('Anda tidak memiliki hak akses untuk pendaftaran ini.', 403);
            }

            $pendaftaran->update(['status_pendaftaran' => 'disetujui']);

            return $this->successResponse(null, 'Pendaftaran berhasil disetujui.');
        } catch (Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function tolak(Request $request, Pendaftaran $pendaftaran)
    {
        try {
            $request->validate(['keterangan_penolakan' => 'required|string|max:255']);
            
            $pengguna = $request->user();
            if ($pendaftaran->turnamen->id_penyelenggara !== $pengguna->id_pengguna && !$pengguna->isAdmin()) {
                return $this->errorResponse('Anda tidak memiliki hak akses untuk pendaftaran ini.', 403);
            }

            $pendaftaran->update([
                'status_pendaftaran' => 'ditolak',
                'keterangan_penolakan' => $request->keterangan_penolakan
            ]);

            return $this->successResponse(null, 'Pendaftaran ditolak.');
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
}