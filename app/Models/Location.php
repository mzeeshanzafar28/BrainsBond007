<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Location extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'address',
        'latitude',
        'longitude',
        'radius_meters',
        'type',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'radius_meters' => 'integer',
        'is_active' => 'boolean',
    ];

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
     * Scope to only active locations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
