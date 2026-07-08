<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StartSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => 'required|integer|exists:employees,id',
            'face_verified' => 'boolean',
            'face_match_score' => 'nullable|numeric|min:0|max:100',
            'location_verified' => 'boolean',
        ];
    }
}
