<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|max:255|unique:users,email',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
            'role'                  => 'required|in:admin,receptionist,host',
            'employee_id'           => 'nullable|exists:employees,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'                  => 'Nama',
            'email'                 => 'Email',
            'password'              => 'Password',
            'password_confirmation' => 'Konfirmasi Password',
            'role'                  => 'Role',
        ];
    }
}
