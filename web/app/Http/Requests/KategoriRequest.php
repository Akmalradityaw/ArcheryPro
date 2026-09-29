<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KategoriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_kategori' => ['required', 'string', 'max:50'],
            'jarak_tempuh' => ['required', 'integer', 'min:1'],
            'jumlah_panah_per_end' => ['required', 'integer', 'min:1', 'max:12'],
        ];
    }
}
