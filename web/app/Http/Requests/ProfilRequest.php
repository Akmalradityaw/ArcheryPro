<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $atletId = auth()->user()->atlet?->id;

        return [
            'nia' => ['required', 'string', 'max:20', Rule::unique('atlet')->ignore($atletId)],
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'tempat_lahir' => ['nullable', 'string', 'max:50'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'sekolah_id' => ['nullable', 'exists:sekolah,id'],
            'kelas' => ['nullable', 'string', 'max:20'],
            'no_telepon' => ['nullable', 'string', 'max:15'],
            'nama_orangtua' => ['nullable', 'string', 'max:100'],
            'no_telepon_orangtua' => ['nullable', 'string', 'max:15'],
            'alamat' => ['nullable', 'string'],
            'kategori_id' => ['nullable', 'exists:kategori,id'],
            'golongan_darah' => ['nullable', 'string', 'max:5'],
            'riwayat_cedera' => ['nullable', 'string'],
            'alergi' => ['nullable', 'string'],
            'izinkan_tampil_publik' => ['nullable', 'boolean'],
        ];
    }
}
