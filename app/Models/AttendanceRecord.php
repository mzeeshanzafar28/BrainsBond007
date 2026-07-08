<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class AttendanceRecord extends Model
{
    protected $fillable = [
        'employee_id',
        'user_id',
        'date',
        'check_in',
        'check_out',
        'face_match_score',
        'location_status',
        'total_hours',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'face_match_score' => 'float',
        'total_hours' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to isolate data per admin (tenant).
     */
    public function scopeForAdmin($query, $userId = null)
    {
        return $query->where('user_id', $userId ?? Auth::id());
    }
}
