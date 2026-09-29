<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['status' => ['required', 'in:draft,berlangsung,selesai']];
    }
}
