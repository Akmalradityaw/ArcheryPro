<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PengaturanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token_pelatih' => ['required', 'string', 'max:100'],
            'token_scoring' => ['required', 'string', 'max:100'],
            'jumlah_panah_per_end' => ['required', 'integer', 'min:1', 'max:12'],
            'backup_otomatis' => ['required', 'in:harian,mingguan,bulanan,off'],
            'leaderboard_publik' => ['required', 'in:0,1'],
        ];
    }
}
