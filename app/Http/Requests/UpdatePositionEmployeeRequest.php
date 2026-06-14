<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePositionEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('position')?->id;

        return [
            'name_position' => "required|string|max:255|unique:position_employees,name_position,{$id}",
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
