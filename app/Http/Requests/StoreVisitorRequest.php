<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVisitorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => 'required|string|max:255',
            'id_type'        => 'nullable|in:ktp,sim,passport',
            'id_number'      => 'nullable|string|max:50',
            'company'        => 'nullable|string|max:150',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:100',
            'employee_id'    => 'required|exists:employees,id',
            'purpose'        => 'nullable|string',
            'vehicle_number' => 'nullable|string|max:20',
            'badge_number'   => 'nullable|string|max:20',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'        => 'nama tamu',
            'employee_id' => 'karyawan yang dikunjungi',
        ];
    }
}
