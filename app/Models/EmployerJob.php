<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployerJob extends Model
{
    use HasFactory;

    protected $table = 'employer_jobs';

    protected $fillable = [
        'employer_id',
        'title',
        'description',
        'type',
        'location',
        'employment_type',
        'salary',
        'duration',
        'requirements',
        'required_skills',
        'required_qualifications',
        'experience_level',
        'deadline',
        'status',
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    // ── Accessors & Helpers ───────────────────────────────────────────────────

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'internship' ? 'Internship' : 'Job';
    }

    public function getTypeBadgeColorAttribute(): string
    {
        return $this->type === 'internship' ? '#7c3aed' : '#0d9488';
    }

    public function isExpired(): bool
    {
        return $this->deadline && $this->deadline->isPast();
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeJobs($query)
    {
        return $query->where('type', 'job');
    }

    public function scopeInternships($query)
    {
        return $query->where('type', 'internship');
    }
}
