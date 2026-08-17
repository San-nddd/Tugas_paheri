<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tim;
use App\Traits\ApiResponser; // Menggunakan Trait ApiResponser
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TimController extends Controller
{
    use ApiResponser;

    /**
     * Menampilkan semua daftar tim beserta kapten dan daftar pemainnya.
     */
    public function index()
    {
        try {
            $tim = Tim::with(['kapten:id_pengguna,nama,email', 'pemain'])->latest()->get();

            return response()->json([
                'status'  => 'success',
                'message' => 'Daftar tim berhasil diambil',
                'data'    => $tim,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengambil data tim: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Membuat tim baru. ID kapten otomatis diambil dari user yang sedang login.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_tim'  => 'required|string|max:100',
                'foto_logo' => 'nullable|string',
            ]);

            $validated['id_kapten'] = auth()->id();

            $tim = Tim::create($validated);

            return response()->json([
                'status'  => 'success',
                'message' => 'Tim berhasil dibuat',
                'data'    => $tim->load('kapten:id_pengguna,nama,email'),
            ], 201);

        } catch (ValidationException $e) {
            // ✅ PERBAIKAN: Gunakan response()->json langsung untuk penanganan error validasi
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return $this->errorResponse('Gagal membuat tim: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Menampilkan detail 1 tim beserta daftar pemainnya.
     */
    public function show($id)
    {
        try {
            $tim = Tim::with(['kapten:id_pengguna,nama,email', 'pemain'])->find($id);

            if (!$tim) {
                return $this->errorResponse('Data tim tidak ditemukan', 404);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Detail tim berhasil diambil',
                'data'    => $tim,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengambil detail tim: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Memperbarui data tim.
     */
    public function update(Request $request, $id)
    {
        try {
            $tim = Tim::find($id);

            if (!$tim) {
                return $this->errorResponse('Data tim tidak ditemukan', 404);
            }

            if ($tim->id_kapten !== auth()->id() && !auth()->user()->isAdmin()) {
                return $this->errorResponse('Anda tidak memiliki hak akses untuk mengubah data tim ini.', 403);
            }

            $validated = $request->validate([
                'nama_tim'  => 'sometimes|required|string|max:100',
                'foto_logo' => 'nullable|string',
            ]);

            $tim->update($validated);

            return response()->json([
                'status'  => 'success',
                'message' => 'Data tim berhasil diperbarui',
                'data'    => $tim,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal memperbarui data tim: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Menghapus tim.
     */
    public function destroy($id)
    {
        try {
            $tim = Tim::find($id);

            if (!$tim) {
                return $this->errorResponse('Data tim tidak ditemukan', 404);
            }

            if ($tim->id_kapten !== auth()->id() && !auth()->user()->isAdmin()) {
                return $this->errorResponse('Anda tidak memiliki hak akses untuk menghapus tim ini.', 403);
            }

            $tim->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Tim berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal menghapus tim: ' . $e->getMessage(), 500);
        }
    }
}