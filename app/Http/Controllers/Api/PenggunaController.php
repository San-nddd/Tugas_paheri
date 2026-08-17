<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PenggunaController extends Controller
{
    use ApiResponser;

    /**
     * Menampilkan semua daftar pengguna.
     */
    public function index()
    {
        try {
            $pengguna = Pengguna::latest()->get();

            return response()->json([
                'status'  => 'success',
                'message' => 'Daftar pengguna berhasil diambil',
                'data'    => $pengguna,
            ], 200);

        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengambil data pengguna: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Menambahkan pengguna baru.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama'       => 'required|string|max:255',
                'email'      => 'required|email|unique:pengguna,email',
                'kata_sandi' => 'required|string|min:4',
                'no_telepon' => 'nullable|string|max:20',
                'peran'      => 'nullable|string|in:admin,user,penyelenggara,kapten_tim',
            ]);

            $validated['kata_sandi'] = Hash::make($validated['kata_sandi']);

            $pengguna = Pengguna::create($validated);

            return response()->json([
                'status'  => 'success',
                'message' => 'Pengguna berhasil dibuat',
                'data'    => $pengguna,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal membuat pengguna: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Menampilkan detail 1 pengguna.
     */
    public function show(string $id)
    {
        try {
            $pengguna = Pengguna::findOrFail($id);

            return response()->json([
                'status'  => 'success',
                'message' => 'Detail pengguna berhasil dimuat',
                'data'    => $pengguna,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Data pengguna tidak ditemukan', 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Memperbarui data pengguna.
     */
    public function update(Request $request, string $id)
    {
        try {
            $pengguna = Pengguna::findOrFail($id);

            $validated = $request->validate([
                'nama'       => ['sometimes', 'required', 'string', 'max:255'],
                'email'      => ['sometimes', 'required', 'email', 'unique:pengguna,email,' . $id . ',id_pengguna'],
                'kata_sandi' => ['nullable', 'string', 'min:4'],
                'no_telepon' => ['nullable', 'string', 'max:20'],
                'peran'      => ['nullable', 'string', 'in:admin,user,penyelenggara,kapten_tim'],
            ]);

            // Jika kata_sandi diisi, lakukan Hashing. Jika kosong, hapus dari array update.
            if (!empty($validated['kata_sandi'])) {
                $validated['kata_sandi'] = Hash::make($validated['kata_sandi']);
            } else {
                unset($validated['kata_sandi']);
            }

            $pengguna->update($validated);

            return response()->json([
                'status'  => 'success',
                'message' => 'Data pengguna berhasil diperbarui',
                'data'    => $pengguna,
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Data pengguna tidak ditemukan', 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal memperbarui pengguna: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Menghapus pengguna.
     */
    public function destroy(string $id)
    {
        try {
            $pengguna = Pengguna::findOrFail($id);
            $pengguna->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Pengguna berhasil dihapus',
            ], 200);

        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Pengguna tidak ditemukan', 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal menghapus pengguna: ' . $e->getMessage(), 500);
        }
    }
}