<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassMaterial extends Model
{
    protected $fillable = [
        'virtual_class_id',
        'title',
        'file_path',
        'file_type',
    ];

    public function virtualClass(): BelongsTo
    {
        return $this->belongsTo(VirtualClass::class);
    }

    public function getIconAttribute(): string
    {
        return match($this->file_type) {
            'pdf'  => '📄',
            'ppt', 'pptx' => '📊',
            'doc', 'docx' => '📝',
            'xls', 'xlsx' => '📈',
            'mp4', 'avi', 'mov' => '🎬',
            default => '📎',
        };
    }
}
