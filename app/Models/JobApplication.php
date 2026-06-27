<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'employer_job_id',
        'student_id',
        'cover_letter',
        'cv_file',
        'status',
        'applied_at',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
    ];

    // Status options
    public const STATUSES = [
        'submitted'            => ['label' => 'Submitted',           'color' => '#6b7280'],
        'under_review'         => ['label' => 'Under Review',        'color' => '#d97706'],
        'shortlisted'          => ['label' => 'Shortlisted',         'color' => '#2563eb'],
        'interview_scheduled'  => ['label' => 'Interview Scheduled', 'color' => '#7c3aed'],
        'hired'                => ['label' => 'Hired',               'color' => '#16a34a'],
        'rejected'             => ['label' => 'Rejected',            'color' => '#dc2626'],
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function job(): BelongsTo
    {
        return $this->belongsTo(EmployerJob::class, 'employer_job_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function interview(): HasOne
    {
        return $this->hasOne(Interview::class);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status]['label'] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUSES[$this->status]['color'] ?? '#6b7280';
    }
}
