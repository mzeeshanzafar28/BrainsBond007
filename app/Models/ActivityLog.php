<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    protected $fillable = [
        'employee_id',
        'session_id',
        'user_id',
        'type',
        'file_path',
        'metadata',
        'captured_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'captured_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(WorkSession::class, 'session_id');
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

    /**
     * Scope by activity type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
