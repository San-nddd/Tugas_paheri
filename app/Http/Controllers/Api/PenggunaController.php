<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Traits\ApiResponser;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PenggunaController extends Controller
{
    use ApiResponser;

    public function index()
    {
        try {
            $pengguna = Pengguna::latest()->get();

            return $this->successResponse($pengguna, 'Daftar pengguna berhasil diambil.');
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengambil data pengguna: '.$e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'email' => 'required|email|unique:pengguna,email',
                'kata_sandi' => 'required|string|min:4',
                'no_telepon' => 'nullable|string|max:20',
                'peran' => 'nullable|string|in:admin,penyelenggara,kapten_tim',
            ]);

            $validated['kata_sandi'] = Hash::make($validated['kata_sandi']);

            $pengguna = Pengguna::create($validated);

            return $this->successResponse($pengguna, 'Pengguna berhasil dibuat.', 201);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validasi gagal.', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal membuat pengguna: '.$e->getMessage(), 500);
        }
    }

    public function show(string $id)
    {
        try {
            $pengguna = Pengguna::findOrFail($id);

            return $this->successResponse($pengguna, 'Detail pengguna berhasil dimuat.');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Data pengguna tidak ditemukan', 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: '.$e->getMessage(), 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $pengguna = Pengguna::findOrFail($id);

            $validated = $request->validate([
                'nama' => ['sometimes', 'required', 'string', 'max:255'],
                'email' => ['sometimes', 'required', 'email', 'unique:pengguna,email,'.$id.',id_pengguna'],
                'kata_sandi' => ['nullable', 'string', 'min:4'],
                'no_telepon' => ['nullable', 'string', 'max:20'],
                'peran' => ['nullable', 'string', 'in:admin,penyelenggara,kapten_tim'],
            ]);

            if (! empty($validated['kata_sandi'])) {
                $validated['kata_sandi'] = Hash::make($validated['kata_sandi']);
            } else {
                unset($validated['kata_sandi']);
            }

            $pengguna->update($validated);

            return $this->successResponse($pengguna, 'Data pengguna berhasil diperbarui.');
        } catch (ValidationException $e) {
            return $this->errorResponse('Validasi gagal.', 422, $e->errors());
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Data pengguna tidak ditemukan', 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal memperbarui pengguna: '.$e->getMessage(), 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $pengguna = Pengguna::findOrFail($id);
            $pengguna->delete();

            return $this->successResponse(null, 'Pengguna berhasil dihapus.');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Pengguna tidak ditemukan', 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal menghapus pengguna: '.$e->getMessage(), 500);
        }
    }
}
