<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePropRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $propId = $this->input('prop_id');

        return [
            'prop_id' => 'required|integer|exists:props,id',
            'country' => 'sometimes|required|string|max:255',
            'exe_url' => 'sometimes|required|string|unique:props,exe_url,' . $propId,
            'is_premium' => 'boolean',
            'organization_location' => 'sometimes|required|string|max:255',
            'port' => 'sometimes|required|integer',
            'connection_url' => 'sometimes|required|string|max:255',
        ];
    }
}
