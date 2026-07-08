<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class WorkSession extends Model
{
    protected $fillable = [
        'employee_id',
        'user_id',
        'started_at',
        'ended_at',
        'status',
        'face_verified',
        'face_match_score',
        'location_verified',
        'ip_address',
        'total_active_minutes',
        'total_idle_minutes',
        'last_heartbeat_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'last_heartbeat_at' => 'datetime',
        'face_verified' => 'boolean',
        'location_verified' => 'boolean',
        'face_match_score' => 'float',
        'total_active_minutes' => 'integer',
        'total_idle_minutes' => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'session_id');
    }

    /**
     * Scope to only active sessions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to isolate data per admin (tenant).
     */
    public function scopeForAdmin($query, $userId = null)
    {
        return $query->where('user_id', $userId ?? Auth::id());
    }

    /**
     * Calculate session duration in minutes.
     */
    public function getDurationMinutesAttribute(): int
    {
        if (!$this->ended_at) {
            return $this->started_at->diffInMinutes(now());
        }
        return $this->started_at->diffInMinutes($this->ended_at);
    }
}
