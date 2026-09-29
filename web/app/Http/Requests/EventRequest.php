<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_event' => ['required', 'string', 'max:100'],
            'tanggal' => ['required', 'date'],
            'lokasi' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:draft,berlangsung,selesai'],
            'is_publik' => ['nullable', 'boolean'],
        ];
    }
}
