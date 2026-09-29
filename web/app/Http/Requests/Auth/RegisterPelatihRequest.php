<?php

namespace App\Http\Requests\Auth;

use App\Models\PengaturanSistem;
use Illuminate\Foundation\Http\FormRequest;

class RegisterPelatihRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'min:3', 'max:50', 'unique:users,username'],
            'email' => ['required', 'email', 'max:100'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'token' => ['required', 'string', function ($attr, $value, $fail) {
                $valid = PengaturanSistem::where('key_setting', 'token_pelatih')->value('value_setting');
                if ($value !== $valid) {
                    $fail('Token pelatih tidak valid.');
                }
            }],
        ];
    }
}
