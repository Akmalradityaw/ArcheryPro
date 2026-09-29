<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EksporPerformaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'format' => ['required', 'in:excel,pdf'],
            'sesi_latihan_id' => ['nullable', 'exists:sesi_latihan,id'],
            'event_id' => ['nullable', 'exists:event,id'],
        ];
    }
}
