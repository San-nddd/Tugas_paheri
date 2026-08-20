<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pemain;
use App\Models\Tim;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PemainController extends Controller
{
    use ApiResponser;

    public function index(Request $request)
    {
        try {
            $query = Pemain::query();

            if ($request->has('id_tim')) {
                $query->where('id_tim', $request->id_tim);
            }

            $pemain = $query->get();

            return $this->successResponse($pemain, 'Berhasil mengambil daftar pemain.');
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengambil data pemain: '.$e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_tim' => ['required', 'exists:tim,id_tim'],
                'nama_game' => ['required', 'string', 'max:50'],
                'id_mlbb' => ['required', 'string', 'max:30'],
                'id_server' => ['required', 'string', 'max:20'],
            ]);

            $pengguna = $request->user();
            $tim = Tim::findOrFail($validated['id_tim']);

            if ($tim->id_kapten !== $pengguna->id_pengguna && ! $pengguna->isAdmin()) {
                return $this->errorResponse('Anda tidak memiliki hak akses untuk menambahkan pemain ke tim ini.', 403);
            }

            $pemain = Pemain::create($validated);

            return $this->successResponse($pemain, 'Pemain berhasil ditambahkan ke dalam tim.', 201);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validasi gagal.', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal menambahkan pemain: '.$e->getMessage(), 500);
        }
    }

    public function show(Pemain $pemain)
    {
        try {
            return $this->successResponse($pemain, 'Detail pemain berhasil dimuat.');
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: '.$e->getMessage(), 500);
        }
    }

    public function update(Request $request, Pemain $pemain)
    {
        try {
            $pengguna = $request->user();
            $tim = $pemain->tim;

            if ($tim->id_kapten !== $pengguna->id_pengguna && ! $pengguna->isAdmin()) {
                return $this->errorResponse('Anda tidak memiliki hak akses untuk mengubah pemain ini.', 403);
            }

            $validated = $request->validate([
                'nama_game' => ['sometimes', 'required', 'string', 'max:50'],
                'id_mlbb' => ['sometimes', 'required', 'string', 'max:30'],
                'id_server' => ['sometimes', 'required', 'string', 'max:20'],
            ]);

            $pemain->update($validated);

            return $this->successResponse($pemain, 'Data pemain berhasil diperbarui.');
        } catch (ValidationException $e) {
            return $this->errorResponse('Validasi gagal.', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal memperbarui pemain: '.$e->getMessage(), 500);
        }
    }

    public function destroy(Request $request, Pemain $pemain)
    {
        try {
            $pengguna = $request->user();
            $tim = $pemain->tim;

            if ($tim->id_kapten !== $pengguna->id_pengguna && ! $pengguna->isAdmin()) {
                return $this->errorResponse('Anda tidak memiliki hak akses untuk menghapus pemain ini.', 403);
            }

            $pemain->delete();

            return $this->successResponse(null, 'Pemain berhasil dihapus dari tim.');
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal menghapus pemain: '.$e->getMessage(), 500);
        }
    }
}
