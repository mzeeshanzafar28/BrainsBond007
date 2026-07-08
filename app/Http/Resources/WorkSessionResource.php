<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkSessionResource extends JsonResource
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
            'started_at' => $this->started_at?->toIso8601String(),
            'ended_at' => $this->ended_at?->toIso8601String(),
            'status' => $this->status,
            'face_verified' => (bool) $this->face_verified,
            'face_match_score' => $this->face_match_score ? (float) $this->face_match_score : null,
            'location_verified' => (bool) $this->location_verified,
            'ip_address' => $this->ip_address,
            'total_active_minutes' => (int) $this->total_active_minutes,
            'total_idle_minutes' => (int) $this->total_idle_minutes,
            'last_heartbeat_at' => $this->last_heartbeat_at?->toIso8601String(),
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
        ];
    }
}
