<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employeeId = $this->input('employee_id');

        return [
            'employee_id' => 'required|integer|exists:employees,id',
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:employees,email,' . $employeeId,
            'age' => 'nullable|integer|min:18|max:100',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'face_images' => 'sometimes|required|json',
            'cnic' => 'sometimes|required|string|max:15',
            'start_working_hour' => 'sometimes|required|date_format:H:i',
            'end_working_hour' => 'sometimes|required|date_format:H:i',
            'allow_remote' => 'boolean',
            'remote_locations' => 'nullable|json',
            'status' => 'sometimes|in:active,inactive,suspended',
        ];
    }
}
