<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SkillProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'category_id',
        'name',
        'description',
        'duration',
        'cost',
        'mode',
        'venue',
        'capacity',
        'requirements',
        'learning_outcomes',
        'status',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SkillCategory::class, 'category_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(TrainingSession::class, 'program_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'program_id');
    }
}
