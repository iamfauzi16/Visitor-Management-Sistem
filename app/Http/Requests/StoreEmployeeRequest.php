<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nik'        => 'nullable|string|max:20|unique:employees,nik',
            'name'       => 'required|string|max:255',
            'department' => 'required|string|max:100',
            'position'   => 'required|string|max:100',
            'email'      => 'nullable|email|max:100|unique:employees,email',
            'phone'      => 'nullable|string|max:20',
            'is_active'  => 'boolean',
        ];
    }
}
