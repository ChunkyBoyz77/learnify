<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
    ];

    /**
     * Get the payments for the payment method.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
