<?php

namespace App\Http\Requests;

use App\Models\Registration;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Keep phone numbers as strings so their leading zero is preserved.
        if (is_string($this->input('no_hp'))) {
            $this->merge(['no_hp' => preg_replace('/[\s().-]+/', '', $this->input('no_hp'))]);
        }
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:150'],
            'jenis_kelamin' => ['nullable', Rule::in(['Pria', 'Wanita'])],
            'tanggal_lahir' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:today', 'after_or_equal:1900-01-01'],
            'no_hp' => ['required', 'string', 'regex:/^(?:08|\+?628)[0-9]{7,11}$/'],
            'jenis_usaha' => ['nullable', Rule::in(Registration::BUSINESS_TYPES)],
            'lama_usaha_thn' => ['nullable', 'integer', 'between:0,100'],
            'omset_bulanan' => ['nullable', 'numeric', 'min:0', 'max:999999999999.99', 'decimal:0,2'],
            'pinjaman_eksisting' => ['nullable', 'numeric', 'min:0', 'max:999999999999.99', 'decimal:0,2'],
            'fasilitas_diajukan' => ['nullable', Rule::in(Registration::FACILITIES)],
            'pinjaman_diajukan' => ['nullable', 'numeric', 'min:0', 'max:999999999999.99', 'decimal:0,2'],
            'alamat' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'max' => ':attribute melebihi batas yang diperbolehkan (:max).',
            'min' => ':attribute tidak boleh kurang dari :min.',
            'in' => 'Pilih :attribute yang tersedia.',
            'numeric' => ':attribute harus berupa angka.',
            'integer' => ':attribute harus berupa bilangan bulat.',
            'between' => ':attribute harus antara :min dan :max.',
            'decimal' => ':attribute maksimal memiliki 2 angka desimal.',
            'date_format' => ':attribute harus berupa tanggal yang valid.',
            'before_or_equal' => ':attribute tidak boleh melewati hari ini.',
            'after_or_equal' => ':attribute tidak boleh sebelum 1 Januari 1900.',
            'no_hp.regex' => 'Masukkan nomor HP Indonesia yang valid, diawali 08 atau +628.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nama' => 'Nama lengkap', 'jenis_kelamin' => 'jenis kelamin',
            'tanggal_lahir' => 'Tanggal lahir', 'no_hp' => 'Nomor HP',
            'jenis_usaha' => 'jenis usaha', 'lama_usaha_thn' => 'Lama usaha',
            'omset_bulanan' => 'Omset bulanan', 'pinjaman_eksisting' => 'Pinjaman Berjalan',
            'fasilitas_diajukan' => 'fasilitas yang diajukan',
            'pinjaman_diajukan' => 'Jumlah pinjaman', 'alamat' => 'Alamat usaha',
        ];
    }
}
