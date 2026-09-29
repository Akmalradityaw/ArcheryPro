<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AtletRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $atlet = $this->route('atlet');

        return [
            // ponytail: required hanya saat create — form edit pelatih tak punya field username
            // (controller update memang mengabaikannya), kalau required update selalu gagal validasi
            'username' => [$this->isMethod('post') ? 'required' : 'nullable', 'string', 'max:50', Rule::unique('users')->ignore($atlet?->user_id)],
            'password' => [$this->isMethod('post') ? 'required' : 'nullable', 'string', 'min:8'],
            'nia' => ['required', 'string', 'max:20', Rule::unique('atlet')->ignore($atlet?->id)],
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'tanggal_lahir' => ['nullable', 'date'],
            'sekolah_id' => ['nullable', 'exists:sekolah,id'],
            'kategori_id' => ['nullable', 'exists:kategori,id'],
            'status' => ['required', 'in:aktif,tidak_aktif'],
        ];
    }
}
