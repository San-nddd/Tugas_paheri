<?php

namespace App\Http\Controllers\Api; // Menggunakan huruf A kapital

use App\Http\Controllers\Controller;
use App\Models\Pemain;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PemainController extends Controller
{
    use ApiResponser;

    /**
     * Menampilkan daftar pemain (Bisa difilter berdasarkan ID Tim).
     */
    public function index(Request $request)
    {
        try {
            $query = Pemain::query();

            // Opsional: Jika ingin menampilkan pemain dari tim tertentu saja (?id_tim=1)
            if ($request->has('id_tim')) {
                $query->where('id_tim', $request->id_tim);
            }

            $pemain = $query->get();

            return $this->successResponse($pemain, 'Berhasil mengambil daftar pemain.');
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengambil data pemain: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Menambahkan pemain baru ke dalam tim.
     */
    public function store(Request $request)
    {
        try {
            // Sesuaikan nama field ('nama_pemain', 'nickname', dll) dengan kolom di tabel databasemu
            $validated = $request->validate([
                'id_tim'      => ['required', 'exists:tim,id'], 
                'nama_pemain' => ['required', 'string', 'max:255'],
                'nickname'    => ['nullable', 'string', 'max:255'],
                'posisi'      => ['nullable', 'string', 'max:100'],
            ]);

            $pemain = Pemain::create($validated);

            return $this->successResponse($pemain, 'Pemain berhasil ditambahkan ke dalam tim.', 201);
            
        } catch (ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal menambahkan pemain: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Menampilkan detail satu pemain.
     */
    public function show(Pemain $pemain)
    {
        try {
            return $this->successResponse($pemain, 'Detail pemain berhasil dimuat.');
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Memperbarui data pemain.
     */
    public function update(Request $request, Pemain $pemain)
    {
        try {
            $validated = $request->validate([
                'nama_pemain' => ['sometimes', 'required', 'string', 'max:255'],
                'nickname'    => ['nullable', 'string', 'max:255'],
                'posisi'      => ['nullable', 'string', 'max:100'],
            ]);

            $pemain->update($validated);

            return $this->successResponse($pemain, 'Data pemain berhasil diperbarui.');
            
        } catch (ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal memperbarui pemain: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Menghapus pemain dari tim (Kick).
     */
    public function destroy(Pemain $pemain)
    {
        try {
            $pemain->delete();
            return $this->successResponse(null, 'Pemain berhasil dihapus dari tim.');
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal menghapus pemain: ' . $e->getMessage(), 500);
        }
    }
}