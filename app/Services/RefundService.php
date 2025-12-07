<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\RefundRequest;
use Carbon\Carbon;

class RefundService
{
    /**
     * Maximum days allowed for refund request after payment completion.
     * Default: 30 days (standard money-back guarantee period)
     */
    protected int $refundWindowDays = 30;

    /**
     * Maximum course completion percentage allowed for refund.
     * Default: 25% (students who completed more than 25% are not eligible)
     */
    protected int $maxProgressPercentage = 25;

    /**
     * Check if a payment is eligible for refund request.
     * 
     * @return array Returns ['eligible' => bool, 'reasons' => array]
     */
    public function checkRefundEligibility(Payment $payment): array
    {
        $reasons = [];
        $eligible = true;

        // Condition 1: Payment must be completed
        if ($payment->status !== 'completed') {
            $eligible = false;
            $reasons[] = 'Payment must be completed to request a refund.';
        }

        // Condition 2: Payment must not already be refunded
        if ($payment->status === 'refunded') {
            $eligible = false;
            $reasons[] = 'This payment has already been refunded.';
        }

        // Condition 3: Payment must have been completed within refund window
        // Use application timezone for accurate date calculations
        $now = Carbon::now(config('app.timezone'));
        $daysSincePayment = 0;
        $daysRemaining = 0;
        
        if ($payment->paid_at) {
            $paidAt = Carbon::parse($payment->paid_at)->setTimezone(config('app.timezone'));
            // Calculate days since payment (absolute difference)
            $daysSincePayment = (int) $now->diffInDays($paidAt);
            
            if ($daysSincePayment > $this->refundWindowDays) {
                $eligible = false;
                $reasons[] = "Refund requests must be made within {$this->refundWindowDays} days of payment completion. (" . ($daysSincePayment - $this->refundWindowDays) . " days overdue)";
            } else {
                // Calculate days remaining
                $daysRemaining = max(0, $this->refundWindowDays - $daysSincePayment);
            }
        } else {
            // If no paid_at date, use created_at (shouldn't happen for completed payments)
            $createdAt = Carbon::parse($payment->created_at)->setTimezone(config('app.timezone'));
            $daysSincePayment = (int) $now->diffInDays($createdAt);
            
            if ($daysSincePayment > $this->refundWindowDays) {
                $eligible = false;
                $reasons[] = "Refund requests must be made within {$this->refundWindowDays} days of payment. (" . ($daysSincePayment - $this->refundWindowDays) . " days overdue)";
            } else {
                $daysRemaining = max(0, $this->refundWindowDays - $daysSincePayment);
            }
        }

        // Condition 4: Cannot have an existing pending/approved refund request
        $existingRequest = RefundRequest::where('payment_id', $payment->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingRequest) {
            $eligible = false;
            $reasons[] = 'You already have a refund request pending or approved for this payment.';
        }

        // Condition 5: Enrollment status check
        if ($payment->enrollment) {
            // Cannot refund if course is completed
            if ($payment->enrollment->status === 'completed') {
                $eligible = false;
                $reasons[] = 'Cannot request refund for a completed course.';
            }

            // Check course progress based on enrollment completion
            if ($payment->enrollment->completed_at) {
                $eligible = false;
                $reasons[] = 'Cannot request refund as you have completed this course.';
            }
        }

        // Condition 6: Only the student who made the payment can request refund
        // (This will be checked in authorization, but we can add it here for clarity)
        
        return [
            'eligible' => $eligible,
            'reasons' => $reasons,
            'days_remaining' => $daysRemaining,
        ];
    }

    /**
     * Calculate refund amount (can be used for partial refunds in future).
     */
    public function calculateRefundAmount(Payment $payment): float
    {
        // For now, return full amount
        // In future, can implement partial refund logic based on course progress
        return (float) $payment->amount;
    }

    /**
     * Get refund window in days.
     */
    public function getRefundWindowDays(): int
    {
        return $this->refundWindowDays;
    }
}
