<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'org_type',
        'contact_person',
        'phone',
        'email',
        'website',
        'address',
        'county',
        'logo_path',
        'description',
        'status',
    ];

    // ── Type Labels ────────────────────────────────────────────────────────────

    public static function typeLabels(): array
    {
        return [
            'ngo'             => 'NGO / Non-Profit',
            'private_company' => 'Private Company',
            'ajira'           => 'Ajira Program',
            'trainer'         => 'Professional Trainer',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return self::typeLabels()[$this->org_type] ?? ucfirst($this->org_type);
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->org_type) {
            'ngo'             => '🤝',
            'private_company' => '🏢',
            'ajira'           => '💻',
            'trainer'         => '👨‍🏫',
            default           => '🏗️',
        };
    }

    public function getLogoUrlAttribute(): string
    {
        if ($this->logo_path) {
            return asset('storage/' . $this->logo_path);
        }
        return asset('images/default-organization.png');
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function programs(): HasMany
    {
        return $this->hasMany(SkillProgram::class);
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
