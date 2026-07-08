<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class AgentLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'admin_id' => 'required|integer|exists:users,id',
            'employee_email' => 'required|email',
        ];
    }

    public function messages(): array
    {
        return [
            'admin_id.exists' => 'The specified organization does not exist.',
            'employee_email.required' => 'Employee email is required for authentication.',
        ];
    }
}
