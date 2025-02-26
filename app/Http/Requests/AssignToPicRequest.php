<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignToPicRequest extends FormRequest
{
    public function authorize()
    {
        return in_array('ehs', $this->user()->roleLct->pluck('name')->toArray());
    }
    public function rules()
    {
        return [
            'departemen_id' => 'bail|required|exists:lct_departement,id',
            'pic_id' => 'bail|required|exists:lct_pic,id',
            'tingkat_bahaya' => [
                'bail',
                'required',
                function ($attribute, $value, $fail) {
                    $allowed = ['low', 'medium', 'high'];
                    if (!in_array(strtolower($value), $allowed)) {
                        $fail("The $attribute must be one of: Low, Medium, High.");
                    }
                }
            ],
            'rekomendasi' => 'bail|required|string|max:500',
            'due_date' => 'bail|required|date|date_format:Y-m-d|after_or_equal:today',
        ];
    }


    public function messages()
    {
        return [
            'departemen.required' => 'Departemen harus diisi.',
            'departemen.exists' => 'Departemen tidak valid.',
            'pic_id.required' => 'PIC harus dipilih.',
            'pic_id.exists' => 'PIC tidak valid.',
            'tingkat_bahaya.required' => 'Tingkat Bahaya harus dipilih.',
            'tingkat_bahaya.in' => 'Tingkat Bahaya tidak valid.',
            'rekomendasi.required' => 'Rekomendasi harus diisi.',
            'due_date.required' => 'Due Date harus diisi.',
            'due_date.date' => 'Due Date harus berupa tanggal.',
        ];
    }
}
