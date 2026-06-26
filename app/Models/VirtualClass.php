<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VirtualClass extends Model
{
    protected $fillable = [
        'training_session_id',
        'room_name',
        'start_time',
        'end_time',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class, 'training_session_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(ClassAttendance::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ClassMessage::class)->orderBy('created_at', 'asc');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(ClassMaterial::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
