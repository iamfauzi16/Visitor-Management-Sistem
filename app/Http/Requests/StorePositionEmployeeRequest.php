<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePositionEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_position' => 'required|string|max:255|unique:position_employees,name_position',
            'status'        => 'required|in:active,inactive',
        ];
    }

    public function attributes(): array
    {
        return [
            'name_position' => 'nama jabatan',
            'status'        => 'status',
        ];
    }
}
