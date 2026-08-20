<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponser;

    public function register(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required|string|max:100',
                'email' => 'required|email|unique:pengguna,email',
                'password' => 'required|string|min:6',
            ]);

            $pengguna = Pengguna::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'kata_sandi' => Hash::make($request->password),
                'peran' => 'kapten_tim',
            ]);

            $token = $pengguna->createToken('auth_token')->plainTextToken;

            return $this->successResponse([
                'pengguna' => $pengguna,
                'token' => $token,
            ], 'Registrasi berhasil.', 201);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validasi gagal.', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan saat registrasi: '.$e->getMessage(), 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $pengguna = Pengguna::where('email', $request->email)->first();

            if (! $pengguna || ! Hash::check($request->password, $pengguna->kata_sandi)) {
                return $this->errorResponse('Email atau password salah.', 401);
            }

            $token = $pengguna->createToken('auth_token')->plainTextToken;

            return $this->successResponse([
                'pengguna' => $pengguna,
                'token' => $token,
            ], 'Login berhasil.');
        } catch (ValidationException $e) {
            return $this->errorResponse('Validasi gagal.', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan saat login: '.$e->getMessage(), 500);
        }
    }

    public function me(Request $request)
    {
        return $this->successResponse($request->user(), 'Data profil berhasil diambil.');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logout berhasil.');
    }
}
