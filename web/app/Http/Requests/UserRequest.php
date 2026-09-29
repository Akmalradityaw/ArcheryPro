<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user?->id)],
            'email' => ['nullable', 'email', 'max:100'],
            'password' => [$this->isMethod('post') ? 'required' : 'nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin,pelatih,scoring,atlet'],
        ];
    }
}
