<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'venue',
        'meeting_link',
        'max_participants',
        'trainer_information',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(SkillProgram::class, 'program_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'session_id');
    }

    public function virtualClass(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(VirtualClass::class, 'training_session_id');
    }

    public function isOnline(): bool
    {
        return in_array($this->program?->mode, ['online', 'hybrid']);
    }
}
