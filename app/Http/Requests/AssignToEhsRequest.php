<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignToEhsRequest extends FormRequest
{
    /**
     * Tentukan apakah user berhak melakukan request ini.
     */
    public function authorize(): bool
    {
        return true; // Pastikan ini `true` agar bisa digunakan
    }

    /**
     * Aturan validasi untuk request ini.
     */
    public function rules(): array
    {
        return [
            'date_completion' => ['required', 'date', 'after_or_equal:today'],
            'bukti_perbaikan' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'], 
        ];
    }

    /**
     * Pesan error kustom jika validasi gagal.
     */
    public function messages(): array
    {
        return [
            'date_completion.required' => 'Tanggal penyelesaian harus diisi.',
            'date_completion.date' => 'Tanggal penyelesaian harus berupa tanggal yang valid.',
            'date_completion.after_or_equal' => 'Tanggal penyelesaian tidak boleh sebelum hari ini.',
            'bukti_perbaikan.file' => 'Bukti perbaikan harus berupa file.',
            'bukti_perbaikan.mimes' => 'Bukti perbaikan harus berupa file JPG, JPEG, PNG, atau PDF.',
            'bukti_perbaikan.max' => 'Ukuran bukti perbaikan maksimal 2MB.',
        ];
    }
}
