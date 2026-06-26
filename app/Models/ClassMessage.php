<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassMessage extends Model
{
    protected $fillable = [
        'virtual_class_id',
        'user_id',
        'message',
    ];

    public function virtualClass(): BelongsTo
    {
        return $this->belongsTo(VirtualClass::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
