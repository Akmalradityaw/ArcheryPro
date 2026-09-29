<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SesiLatihanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_sesi' => ['required', 'string', 'max:100'],
            'tanggal' => ['required', 'date'],
            'jam_mulai' => ['nullable', 'date_format:H:i'],
            'jam_selesai' => ['nullable', 'date_format:H:i', 'after:jam_mulai'],
            'lokasi' => ['nullable', 'string', 'max:100'],
            'jenis_latihan' => ['required', 'in:rutin_mingguan,mandiri,evaluasi_skor,simulasi'],
            'status' => ['required', 'in:terjadwal,berlangsung,selesai,batal'],
            'fokus_latihan' => ['nullable', 'string'],
            'is_publik' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'jam_selesai.after' => 'Jam selesai latihan harus lebih akhir daripada jam mulai.',
        ];
    }
}
