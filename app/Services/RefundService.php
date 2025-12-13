<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\RefundRequest;

class RefundService
{
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

        // Condition 3: Cannot have an existing pending/approved refund request
        $existingRequest = RefundRequest::where('payment_id', $payment->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingRequest) {
            $eligible = false;
            $reasons[] = 'You already have a refund request pending or approved for this payment.';
        }

        // Condition 4: Enrollment status check
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

        // Condition 5: Cannot request refund if student has started any quiz for the course
        $course = $payment->course;
        if ($course) {
            // Get all quiz IDs for this course (through lessons)
            // Use a subquery to get quiz IDs from lessons that belong to this course
            $quizIds = Quiz::whereHas('lesson', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })->pluck('id')->toArray();

            // Check if student has started any quiz (has any quiz attempts)
            if (!empty($quizIds)) {
                $hasQuizAttempt = QuizAttempt::where('user_id', $payment->user_id)
                    ->whereIn('quiz_id', $quizIds)
                    ->exists();

                if ($hasQuizAttempt) {
                    $eligible = false;
                    $reasons[] = 'Cannot request refund as you have started taking quizzes for this course.';
                }
            }
        }

        // Condition 6: Only the student who made the payment can request refund
        // (This will be checked in authorization, but we can add it here for clarity)
        
        return [
            'eligible' => $eligible,
            'reasons' => $reasons,
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

}
