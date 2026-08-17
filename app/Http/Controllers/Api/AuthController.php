<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'nama'     => 'required|string|max:100',
                'email'    => 'required|email|unique:pengguna,email',
                'password' => 'required|string|min:6',
                'peran'    => 'nullable|in:admin,penyelenggara,kapten_tim',
            ]);

            // Kunci array HARUS 'kata_sandi' agar cocok dengan $fillable di Model Pengguna
            $pengguna = Pengguna::create([
                'nama'       => $request->nama,
                'email'      => $request->email,
                'kata_sandi' => Hash::make($request->password), // <-- Wajib 'kata_sandi'
                'peran'      => $request->peran ?? 'kapten_tim',
            ]);

            $token = $pengguna->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status'  => 'success',
                'message' => 'Registrasi berhasil',
                'data'    => $pengguna,
                'token'   => $token,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat registrasi: ' . $e->getMessage(),
                'errors'  => null
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $pengguna = Pengguna::where('email', $request->email)->first();

        // Pengecekan Hash menggunakan properti $pengguna->kata_sandi
        if (! $pengguna || ! Hash::check($request->password, $pengguna->kata_sandi)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Email atau password salah.',
            ], 401);
        }

        $token = $pengguna->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Login berhasil',
            'data'    => $pengguna,
            'token'   => $token,
        ]);
    }

    /**
     * Mengambil data profil pengguna yang sedang terautentikasi.
     */
    public function me(Request $request)
    {
        return response()->json([
            'status'  => 'success',
            'message' => 'Data profil berhasil diambil',
            'data'    => $request->user(),
        ]);
    }

    /**
     * Logout pengguna dan menghapus token Sanctum yang sedang digunakan.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logout berhasil',
        ]);
    }
}