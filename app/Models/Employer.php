<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'registration_number',
        'industry',
        'email',
        'phone',
        'website',
        'address',
        'logo',
        'description',
        'status',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(EmployerJob::class);
    }

    public function activeJobs(): HasMany
    {
        return $this->hasMany(EmployerJob::class)->where('status', 'open')->where('type', 'job');
    }

    public function internships(): HasMany
    {
        return $this->hasMany(EmployerJob::class)->where('status', 'open')->where('type', 'internship');
    }

    public function employmentRecords(): HasMany
    {
        return $this->hasMany(EmploymentRecord::class);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getLogoUrlAttribute(): string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        $name = urlencode($this->company_name);
        return "https://ui-avatars.com/api/?name={$name}&background=0d9488&color=fff&size=128&bold=true";
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
