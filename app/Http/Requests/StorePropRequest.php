<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'country' => 'required|string|max:255',
            'exe_url' => 'required|string|unique:props,exe_url',
            'organization_location' => 'required|string|max:255',
        ];
    }
}
