<?php

namespace App\Http\Requests;

use App\Models\Event;
use App\Models\SesiLatihan;
use Illuminate\Foundation\Http\FormRequest;

class StoreSkorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('atlet_ids') && $this->has('skor') && is_array($this->input('skor'))) {
            $allowed = array_map('strval', (array) $this->input('atlet_ids'));
            $filteredSkor = array_filter(
                (array) $this->input('skor'),
                fn ($key) => in_array((string) $key, $allowed, true),
                ARRAY_FILTER_USE_KEY
            );
            $this->merge(['skor' => $filteredSkor]);
        }
    }

    public function rules(): array
    {
        return [
            'sesi_latihan_id' => [
                'nullable',
                'exists:sesi_latihan,id',
                function ($attr, $value, $fail) {
                    if ($value && SesiLatihan::where('id', $value)->where('status', 'batal')->exists()) {
                        $fail('Jadwal latihan telah dibatalkan.');
                    }
                },
            ],
            'event_id' => [
                'nullable',
                'exists:event,id',
                function ($attr, $value, $fail) {
                    if ($value && ! Event::where('id', $value)->where('status', 'berlangsung')->exists()) {
                        $fail('Event harus berstatus berlangsung.');
                    }
                },
            ],
            'jarak_meter' => ['nullable', 'integer', 'min:5', 'max:90'],
            'catatan_pelatih' => ['nullable', 'string', 'max:1000'],
            'tanggal_sesi' => ['required', 'date'],
            'atlet_ids' => ['required', 'array', 'min:1'],
            'atlet_ids.*' => ['exists:atlet,id'],
            'skor' => ['required', 'array'],
            'skor.*' => ['required', 'array', 'min:1'],
            'skor.*.*' => ['required', 'array', 'size:6'],
            'skor.*.*.*' => ['required', 'integer', 'min:0', 'max:10'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->filled('sesi_latihan_id') && ! $this->filled('event_id')) {
                $validator->errors()->add('sesi_latihan_id', 'Pilih salah satu jadwal latihan mingguan.');
            }

            $dipilih = (array) $this->input('atlet_ids', []);
            foreach (array_keys((array) $this->input('skor', [])) as $id) {
                if (! in_array($id, $dipilih)) {
                    $validator->errors()->add('skor', 'Skor atlet di luar pilihan checkbox.');
                    break;
                }
            }
        });
    }
}
