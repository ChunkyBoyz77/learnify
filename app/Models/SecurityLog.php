<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityLog extends Model
{
    protected $fillable = [
        'event_type',
        'severity',
        'description',
        'payment_id',
        'user_id',
        'ip_address',
        'user_agent',
        'metadata',
        'occurred_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'occurred_at' => 'datetime',
    ];

    /**
     * Get the payment associated with this security log.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Get the user associated with this security log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
