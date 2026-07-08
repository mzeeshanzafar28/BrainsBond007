<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'age' => $this->age,
            'phone' => $this->phone,
            'department' => $this->department,
            'designation' => $this->designation,
            'face_images' => is_string($this->face_images) ? json_decode($this->face_images, true) : $this->face_images,
            'cnic' => $this->cnic,
            'start_working_hour' => $this->start_working_hour,
            'end_working_hour' => $this->end_working_hour,
            'allow_remote' => (bool) $this->allow_remote,
            'remote_locations' => is_string($this->remote_locations) ? json_decode($this->remote_locations, true) : $this->remote_locations,
            'status' => $this->status,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
