<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyFaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'admin_id' => 'required|integer|exists:users,id',
            'image' => 'required|string', // Base64 encoded image
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'A face image is required for verification.',
        ];
    }
}
