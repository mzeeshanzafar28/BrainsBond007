<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ActivityLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'session_id' => $this->session_id,
            'type' => $this->type,
            'file_url' => $this->file_path ? Storage::url($this->file_path) : null,
            'metadata' => $this->metadata,
            'captured_at' => $this->captured_at?->toIso8601String(),
        ];
    }
}
