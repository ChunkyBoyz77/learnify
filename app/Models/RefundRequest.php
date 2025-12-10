<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefundRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'user_id',
        'course_id',
        'enrollment_id',
        'requested_amount',
        'status',
        'reason',
        'instructor_response',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'requested_amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the payment that this refund request belongs to.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Get the user (student) who requested the refund.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course related to this refund request.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the enrollment related to this refund request.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Get the user (instructor) who processed the refund.
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}