<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassAttendance extends Model
{
    protected $fillable = [
        'virtual_class_id',
        'student_id',
        'join_time',
        'leave_time',
        'duration',
        'status',
    ];

    protected $casts = [
        'join_time'  => 'datetime',
        'leave_time' => 'datetime',
    ];

    public function virtualClass(): BelongsTo
    {
        return $this->belongsTo(VirtualClass::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
