<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'age' => 'nullable|integer|min:18|max:100',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'face_images' => 'required|json',
            'cnic' => 'required|string|max:15',
            'start_working_hour' => 'required|date_format:H:i',
            'end_working_hour' => 'required|date_format:H:i|after:start_working_hour',
            'allow_remote' => 'boolean',
            'remote_locations' => 'nullable|json',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'An employee with this email already exists.',
            'end_working_hour.after' => 'End time must be after start time.',
        ];
    }
}
