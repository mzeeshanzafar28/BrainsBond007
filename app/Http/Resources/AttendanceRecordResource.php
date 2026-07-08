<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceRecordResource extends JsonResource
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
            'date' => $this->date?->toDateString(),
            'check_in' => $this->check_in,
            'check_out' => $this->check_out,
            'face_match_score' => $this->face_match_score ? (float) $this->face_match_score : null,
            'location_status' => $this->location_status,
            'total_hours' => (float) $this->total_hours,
            'status' => $this->status,
            'notes' => $this->notes,
        ];
    }
}
