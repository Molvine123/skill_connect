<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'student_id',
        'amount',
        'payment_method',
        'transaction_reference',
        'status',
        'checkout_request_id',
        'merchant_request_id',
        'mpesa_receipt_number',
        'phone_number',
    ];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'paid'    => 'Paid',
            'pending' => 'Pending',
            'failed'  => 'Failed',
            'refunded'=> 'Refunded',
            default   => ucfirst($this->status),
        };
    }
}
