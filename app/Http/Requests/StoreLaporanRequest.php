<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLaporanRequest extends FormRequest
{
    /**
     * Menentukan apakah user diizinkan melakukan request ini.
     */
    public function authorize(): bool
    {
        return true; // Set ke true agar semua user bisa mengakses
    }

    /**
     * Aturan validasi untuk menyimpan laporan.
     */
    public function rules(): array
    {
        return [
            'tanggal_temuan' => 'required|date',
            'area' => 'required|string|max:255',
            'detail_area' => 'required|string|max:255',
            'kategori_temuan' => 'required|string|max:255',
            'temuan_ketidaksesuaian' => 'required|string',
            'rekomendasi_safety' => 'required|string',
            'bukti_temuan' => 'required|array|max:5', // Bisa mengunggah lebih dari satu gambar
            'bukti_temuan.*' => 'image|mimes:webp,jpeg,png,jpg|max:2048', // Menerima WebP
        ];
    }

    /**
     * Custom message error jika terjadi kesalahan validasi.
     */
    public function messages(): array
    {
        return [
            'tanggal_temuan.required' => 'Tanggal temuan wajib diisi.',
            'area.required' => 'Area wajib diisi.',
            'detail_area.required' => 'Detail area wajib diisi.',
            'kategori_temuan.required' => 'Kategori temuan wajib diisi.',
            'temuan_ketidaksesuaian.required' => 'Deskripsi ketidaksesuaian wajib diisi.',
            'rekomendasi_safety.required' => 'Rekomendasi safety wajib diisi.',
            'bukti_temuan.*.mimes' => 'Format gambar harus WebP, JPEG, PNG, atau JPG.',
            'bukti_temuan.*.max' => 'Ukuran gambar maksimal 2MB.',
            'bukti_temuan.max' => 'Maksimal 5 gambar yang diizinkan.',
        ];
    }
}
