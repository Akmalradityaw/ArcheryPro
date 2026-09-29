<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetStatusSesiLatihanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:terjadwal,berlangsung,selesai,batal'],
        ];
    }
}
