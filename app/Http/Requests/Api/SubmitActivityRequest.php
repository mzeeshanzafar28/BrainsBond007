<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SubmitActivityRequest extends FormRequest
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
            'type' => 'required|in:keystroke_summary,app_usage,idle_alert',
            'data' => 'required|array',
        ];
    }
}
