<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\WorkSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SessionService
{
    /**
     * Start a new work session and update or create attendance log.
     */
    public function startSession(int $employeeId, bool $faceVerified, ?float $faceMatchScore, bool $locationVerified, string $ipAddress): WorkSession
    {
        $employee = Employee::findOrFail($employeeId);

        $session = WorkSession::create([
            'employee_id' => $employee->id,
            'user_id' => $employee->user_id,
            'started_at' => now(),
            'status' => 'active',
            'face_verified' => $faceVerified,
            'face_match_score' => $faceMatchScore,
            'location_verified' => $locationVerified,
            'ip_address' => $ipAddress,
            'last_heartbeat_at' => now(),
        ]);

        // Daily Attendance check
        $today = now()->toDateString();
        AttendanceRecord::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'date' => $today,
            ],
            [
                'user_id' => $employee->user_id,
                'check_in' => now()->toTimeString(),
                'face_match_score' => $faceMatchScore,
                'location_status' => $locationVerified ? 'verified' : 'unverified',
                'status' => $this->determineAttendanceStatus($employee),
            ]
        );

        return $session;
    }

    /**
     * End an active session.
     */
    public function endSession(WorkSession $session, bool $faceVerified): WorkSession
    {
        $now = now();
        $totalMinutes = $session->started_at->diffInMinutes($now);

        $session->update([
            'ended_at' => $now,
            'status' => 'completed',
            'face_verified' => $faceVerified,
            'total_active_minutes' => max(0, $totalMinutes - $session->total_idle_minutes),
        ]);

        // Update attendance check out
        $attendance = AttendanceRecord::where('employee_id', $session->employee_id)
            ->where('date', now()->toDateString())
            ->first();

        if ($attendance) {
            $attendance->update([
                'check_out' => $now->toTimeString(),
                'total_hours' => round($totalMinutes / 60, 2),
            ]);
        }

        return $session;
    }

    /**
     * Track active/idle time based on client heartbeat pings.
     */
    public function recordHeartbeat(WorkSession $session, bool $isActive): void
    {
        $minutesSinceLastBeat = $session->last_heartbeat_at
            ? $session->last_heartbeat_at->diffInMinutes(now())
            : 0;

        $updateData = ['last_heartbeat_at' => now()];

        if ($isActive) {
            $updateData['total_active_minutes'] = $session->total_active_minutes + $minutesSinceLastBeat;
        } else {
            $updateData['total_idle_minutes'] = $session->total_idle_minutes + $minutesSinceLastBeat;
        }

        $session->update($updateData);
    }

    /**
     * Determine attendance status (present/late/remote).
     */
    private function determineAttendanceStatus(Employee $employee): string
    {
        $now = now()->toTimeString();
        $start = $employee->start_working_hour;

        $startTime = Carbon::createFromTimeString($start);
        $currentTime = Carbon::createFromTimeString($now);

        if ($currentTime->diffInMinutes($startTime, false) < -15) {
            return 'late';
        }

        return $employee->allow_remote ? 'remote' : 'present';
    }
}
