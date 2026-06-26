<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'registration_number',
        'location',
        'county',
        'phone',
        'email',
        'website',
        'logo_path',
        'description',
        'status',
    ];

    // ── Type Labels ────────────────────────────────────────────────────────────

    public static function typeLabels(): array
    {
        return [
            'university' => 'University',
            'college'    => 'College',
            'tvet'       => 'TVET Institute',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return self::typeLabels()[$this->type] ?? ucfirst($this->type);
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'university' => '🎓',
            'college'    => '🏫',
            'tvet'       => '🔧',
            default      => '🏛️',
        };
    }

    public function getLogoUrlAttribute(): string
    {
        if ($this->logo_path) {
            return asset('storage/' . $this->logo_path);
        }
        return asset('images/default-institution.png');
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
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
