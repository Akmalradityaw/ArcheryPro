<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SekolahRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_sekolah' => ['required', 'string', 'max:100', Rule::unique('sekolah')->ignore($this->route('sekolah')?->id)],
            'alamat' => ['nullable', 'string'],
            'kota' => ['nullable', 'string', 'max:50'],
        ];
    }
}
