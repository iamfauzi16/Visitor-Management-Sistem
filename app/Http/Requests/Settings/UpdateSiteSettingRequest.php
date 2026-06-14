<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'site_name'     => 'required|string|max:100',
            'primary_color' => 'required|string|in:blue,green,red,purple,orange,slate',
            'logo'          => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'remove_logo'   => 'boolean',
        ];
    }
}
