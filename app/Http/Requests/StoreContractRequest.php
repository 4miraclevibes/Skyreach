<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContractRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() && $this->user()->isStaff();
    }

    public function rules()
    {
        return [
            'subject' => ['required', 'string', 'max:180'],
            'activation_date' => ['required', 'date'],
            'documents' => ['nullable', 'array'],
            'documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ];
    }

    public function messages()
    {
        return [
            'subject.required' => 'Perihal wajib diisi.',
            'subject.max' => 'Perihal maksimal 180 karakter.',
            'activation_date.required' => 'Tanggal aktivasi wajib diisi.',
            'activation_date.date' => 'Tanggal aktivasi tidak valid.',
            'documents.*.file' => 'Setiap lampiran harus berupa berkas.',
            'documents.*.mimes' => 'Lampiran harus PDF, JPG, atau PNG.',
            'documents.*.max' => 'Ukuran tiap lampiran maksimal 10 MB.',
        ];
    }
}
