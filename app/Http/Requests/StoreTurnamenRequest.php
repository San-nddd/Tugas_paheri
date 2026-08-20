<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTurnamenRequest extends FormRequest
{
    public function authorize(): bool
    {
        $pengguna = $this->user();

        return $pengguna && ($pengguna->isPenyelenggara() || $pengguna->isAdmin());
    }

    public function rules(): array
    {
        return [
            'nama_turnamen' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'kuota_maksimal' => ['required', 'integer', 'min:2'],
            'biaya' => ['nullable', 'numeric', 'min:0'],
            'kode_akses' => ['nullable', 'string', 'max:50', 'unique:turnamen,kode_akses'],
            'tanggal' => ['required', 'date', 'after:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_turnamen.required' => 'Nama turnamen wajib diisi.',
            'kuota_maksimal.required' => 'Kuota maksimal wajib diisi.',
            'kuota_maksimal.min' => 'Kuota maksimal minimal 2 tim.',
            'tanggal.required' => 'Tanggal turnamen wajib diisi.',
            'tanggal.after' => 'Tanggal turnamen harus di masa depan.',
            'kode_akses.unique' => 'Kode akses sudah digunakan.',
        ];
    }
}
