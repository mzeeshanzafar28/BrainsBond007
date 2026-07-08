<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UploadScreenshotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => 'required|integer|exists:employees,id',
            'session_id' => 'required|integer|exists:work_sessions,id',
            'screenshot' => 'required|image|max:5120', // 5MB max
            'resolution' => 'nullable|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'screenshot.max' => 'Screenshot must not exceed 5MB.',
            'screenshot.image' => 'The file must be a valid image.',
        ];
    }
}
