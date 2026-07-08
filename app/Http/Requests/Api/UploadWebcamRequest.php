<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UploadWebcamRequest extends FormRequest
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
            'image' => 'required|image|max:3072', // 3MB max
        ];
    }
}
