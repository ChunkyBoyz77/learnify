<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PaymentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only instructors can view all payments (for their courses)
        return $user->role === 'instructor';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Payment $payment): bool
    {
        // Student can view their own payments
        if ($user->id === $payment->user_id) {
            return true;
        }

        // Instructor can view payments for their courses
        if ($user->role === 'instructor' && $payment->course && $payment->course->instructor_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only instructors can manually create payments
        return $user->role === 'instructor';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Payment $payment): bool
    {
        // Only instructors can update payments for their courses
        if ($user->role === 'instructor' && $payment->course && $payment->course->instructor_id === $user->id) {
            // Only allow status changes and notes updates for data integrity
            // Critical fields (user_id, course_id, amount, transaction_id) are immutable
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Payment $payment): bool
    {
        // Only instructors can delete (void) payments for their courses
        // Cannot delete completed payments (use refund instead)
        if ($user->role === 'instructor' && $payment->course && $payment->course->instructor_id === $user->id) {
            // Only allow soft delete for pending/failed/cancelled payments
            return in_array($payment->status, ['pending', 'failed', 'cancelled']);
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Payment $payment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Payment $payment): bool
    {
        return false;
    }
}
