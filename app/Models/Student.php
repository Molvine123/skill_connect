<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'institution_id',
        'registration_number',
        'phone',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function getTotalHoursTrained(): float
    {
        $hours = 0;
        foreach ($this->attendances()->where('status', 'present')->with('session')->get() as $attendance) {
            if ($attendance->session) {
                $start = $attendance->session->start_date;
                $end = $attendance->session->end_date;
                if ($start && $end) {
                    $hours += $end->diffInMinutes($start) / 60;
                }
            }
        }
        return round($hours, 1);
    }
}
