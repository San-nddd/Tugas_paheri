<?php

namespace App\Http\Requests;

use App\Models\Pertandingan;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSkorRequest extends FormRequest
{
    /**
     * Hanya penyelenggara pemilik turnamen (atau admin) yang boleh
     * meng-input skor pertandingan.
     */
    public function authorize(): bool
    {
        /** @var Pertandingan|null $pertandingan */
        $pertandingan = $this->route('pertandingan');

        if (! $pertandingan) {
            return false;
        }

        $pengguna = $this->user();

        if ($pengguna->isAdmin()) {
            return true;
        }

        return $pengguna->isPenyelenggara()
            && $pertandingan->turnamen->id_penyelenggara === $pengguna->id_pengguna;
    }

    public function rules(): array
    {
        return [
            'skor_1' => ['required', 'integer', 'min:0'],
            'skor_2' => ['required', 'integer', 'min:0'],
            'id_tim_pemenang' => ['required', 'integer', 'exists:tim,id_tim'],
            'bukti_hasil' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_tim_pemenang.required' => 'Tim pemenang wajib diisi.',
            'id_tim_pemenang.exists' => 'Tim pemenang yang dipilih tidak ditemukan.',
        ];
    }

    /**
     * Custom validation: id_tim_pemenang WAJIB sama dengan id_tim_1
     * atau id_tim_2 pada pertandingan yang sedang di-update. Ini
     * mencegah penyelenggara salah input tim yang tidak pernah
     * bertanding di match tersebut.
     */
    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (ValidatorContract $validator) {
            /** @var Pertandingan|null $pertandingan */
            $pertandingan = $this->route('pertandingan');

            if (! $pertandingan) {
                return;
            }

            $idTimPemenang = (int) $this->input('id_tim_pemenang');

            $timValid = array_filter([
                $pertandingan->id_tim_1,
                $pertandingan->id_tim_2,
            ]);

            if (! in_array($idTimPemenang, $timValid, true)) {
                $validator->errors()->add(
                    'id_tim_pemenang',
                    'Tim pemenang harus salah satu dari tim yang bertanding pada match ini.'
                );
            }

            // Validasi tambahan: skor pemenang tidak boleh lebih rendah dari skor lawannya
            if ($idTimPemenang === $pertandingan->id_tim_1
                && (int) $this->input('skor_1') < (int) $this->input('skor_2')) {
                $validator->errors()->add(
                    'skor_1',
                    'Skor tim pemenang tidak boleh lebih rendah dari skor lawan.'
                );
            }

            if ($idTimPemenang === $pertandingan->id_tim_2
                && (int) $this->input('skor_2') < (int) $this->input('skor_1')) {
                $validator->errors()->add(
                    'skor_2',
                    'Skor tim pemenang tidak boleh lebih rendah dari skor lawan.'
                );
            }
        });
    }

    protected function failedValidation(ValidatorContract $validator): void
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Validasi gagal.',
            'errors' => $validator->errors(),
        ], 422));
    }

    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Anda tidak memiliki hak akses untuk meng-input skor pertandingan ini.',
            'data' => null,
        ], 403));
    }
}
