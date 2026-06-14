<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->employee->id;

        return [
            'nik'        => "nullable|string|max:20|unique:employees,nik,{$id}",
            'name'       => 'required|string|max:255',
            'department' => 'required|string|max:100',
            'position'   => 'required|string|max:100',
            'email'      => "nullable|email|max:100|unique:employees,email,{$id}",
            'phone'      => 'nullable|string|max:20',
            'is_active'  => 'boolean',
        ];
    }
}
