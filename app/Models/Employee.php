<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Employee extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'age',
        'phone',
        'department',
        'designation',
        'face_images',
        'cnic',
        'start_working_hour',
        'end_working_hour',
        'allow_remote',
        'remote_locations',
        'status',
    ];

    protected $casts = [
        'face_images' => 'array',
        'remote_locations' => 'array',
        'allow_remote' => 'boolean',
    ];

    /**
     * The admin (user) who owns this employee.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Work sessions belonging to this employee.
     */
    public function workSessions(): HasMany
    {
        return $this->hasMany(WorkSession::class);
    }

    /**
     * Activity logs for this employee.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Attendance records for this employee.
     */
    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    /**
     * Scope to isolate data per admin (tenant).
     */
    public function scopeForAdmin($query, $userId = null)
    {
        return $query->where('user_id', $userId ?? Auth::id());
    }
}
