<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\SecurityLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentSecurityService
{
    /**
     * Log a security event related to payments.
     */
    public function logSecurityEvent(
        string $eventType,
        string $severity,
        string $description,
        ?Payment $payment = null,
        array $metadata = []
    ): SecurityLog {
        return SecurityLog::create([
            'event_type' => $eventType,
            'severity' => $severity,
            'description' => $description,
            'payment_id' => $payment?->id,
            'user_id' => $payment?->user_id ?? auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => $metadata,
            'occurred_at' => now(),
        ]);
    }

    /**
     * Verify that payment data is encrypted.
     * Returns true if payment details are properly encrypted/tokenized.
     */
    public function verifyPaymentEncryption(Payment $payment): bool
    {
        // Check if payment uses Stripe (which tokenizes card data)
        $paymentDetails = $payment->payment_details ?? [];
        
        // Stripe payments should have tokenized identifiers, not raw card data
        $hasStripeToken = isset($paymentDetails['stripe_payment_intent_id']) 
            || isset($paymentDetails['stripe_checkout_session_id'])
            || isset($paymentDetails['stripe_charge_id']);
        
        // Check if payment method is secure (Stripe tokenizes all card data)
        $isSecureMethod = in_array($payment->paymentMethod->code ?? '', ['stripe', 'credit_card']);
        
        // Log encryption verification
        $isEncrypted = $hasStripeToken || $isSecureMethod;
        
        if (!$isEncrypted) {
            $this->logSecurityEvent(
                'encryption_verification_failed',
                'high',
                "Payment {$payment->transaction_id} does not have encrypted/tokenized payment data",
                $payment,
                ['payment_method' => $payment->paymentMethod->code ?? 'unknown']
            );
        }
        
        return $isEncrypted;
    }

    /**
     * Verify that payment was transmitted over HTTPS.
     */
    public function verifyHttpsConnection(Payment $payment): bool
    {
        $isHttps = request()->secure() || request()->header('X-Forwarded-Proto') === 'https';
        
        if (!$isHttps) {
            $this->logSecurityEvent(
                'non_secure_connection',
                'critical',
                "Payment {$payment->transaction_id} was processed over non-HTTPS connection",
                $payment,
                ['protocol' => request()->getScheme()]
            );
        }
        
        return $isHttps;
    }

    /**
     * Log unauthorized access attempt.
     */
    public function logUnauthorizedAccess(
        string $resource,
        ?Payment $payment = null,
        array $metadata = []
    ): SecurityLog {
        // For unauthorized access, we want to log the user who attempted access, not the payment owner
        return SecurityLog::create([
            'event_type' => 'unauthorized_access_attempt',
            'severity' => 'critical',
            'description' => "Unauthorized access attempt to {$resource}",
            'payment_id' => $payment?->id,
            'user_id' => auth()->id(), // User who attempted unauthorized access
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => array_merge($metadata, [
                'resource' => $resource,
                'payment_owner_id' => $payment?->user_id, // Store payment owner separately
            ]),
            'occurred_at' => now(),
        ]);
    }

    /**
     * Log suspicious activity.
     */
    public function logSuspiciousActivity(
        string $description,
        ?Payment $payment = null,
        array $metadata = []
    ): void {
        $this->logSecurityEvent(
            'suspicious_activity',
            'high',
            $description,
            $payment,
            $metadata
        );
    }

    /**
     * Calculate security metrics for a given date range.
     * For instructors, only calculates metrics for their courses.
     */
    public function calculateSecurityMetrics(?Carbon $startDate = null, ?Carbon $endDate = null, ?int $instructorId = null): array
    {
        $startDate = $startDate ?? Carbon::now()->subDays(30);
        $endDate = $endDate ?? Carbon::now();

        // Build base query for payments - filter by instructor if provided
        $paymentQuery = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed');
        
        if ($instructorId !== null) {
            $paymentQuery->whereHas('course', function ($q) use ($instructorId) {
                $q->where('instructor_id', $instructorId);
            });
        }

        // Total payments in period
        $totalPayments = $paymentQuery->count();

        // Payments with encryption verification
        $encryptedPayments = (clone $paymentQuery)->get()
            ->filter(function ($payment) {
                return $this->verifyPaymentEncryption($payment);
            })
            ->count();

        // Build base query for security logs - filter by instructor's courses if provided
        $securityLogQuery = SecurityLog::whereBetween('occurred_at', [$startDate, $endDate]);
        
        if ($instructorId !== null) {
            $securityLogQuery->where(function ($q) use ($instructorId) {
                // Events with payments that belong to instructor's courses
                $q->whereHas('payment.course', function ($courseQuery) use ($instructorId) {
                    $courseQuery->where('instructor_id', $instructorId);
                })
                // OR general security events (non-secure connection, suspicious activity)
                ->orWhere(function ($subQ) {
                    $subQ->whereNull('payment_id')
                         ->whereIn('event_type', ['non_secure_connection', 'suspicious_activity']);
                });
            });
        }

        // Unauthorized access attempts
        $unauthorizedAttempts = (clone $securityLogQuery)
            ->where('event_type', 'unauthorized_access_attempt')
            ->count();

        // Non-secure connection attempts
        $nonSecureConnections = (clone $securityLogQuery)
            ->where('event_type', 'non_secure_connection')
            ->count();

        // Encryption failures
        $encryptionFailures = (clone $securityLogQuery)
            ->where('event_type', 'encryption_verification_failed')
            ->count();

        // Suspicious activities
        $suspiciousActivities = (clone $securityLogQuery)
            ->where('event_type', 'suspicious_activity')
            ->count();

        // Total security events
        $totalSecurityEvents = $securityLogQuery->count();

        // Calculate percentages
        $encryptionSuccessRate = $totalPayments > 0 
            ? ($encryptedPayments / $totalPayments) * 100 
            : 100;

        // Round the encryption success rate for display and comparison
        $roundedEncryptionRate = round($encryptionSuccessRate, 2);

        return [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
            'payments' => [
                'total' => $totalPayments,
                'encrypted' => $encryptedPayments,
                'encryption_success_rate' => $roundedEncryptionRate,
            ],
            'security_events' => [
                'unauthorized_access_attempts' => $unauthorizedAttempts,
                'non_secure_connections' => $nonSecureConnections,
                'encryption_failures' => $encryptionFailures,
                'suspicious_activities' => $suspiciousActivities,
                'total' => $totalSecurityEvents,
            ],
            'compliance' => [
                'zero_unauthorized_access' => $unauthorizedAttempts === 0,
                'hundred_percent_encryption' => $roundedEncryptionRate >= 100.0,
                'zero_non_secure_connections' => $nonSecureConnections === 0,
                'hundred_percent_negative_records' => $totalSecurityEvents === 0 || 
                    ($encryptionFailures === 0 && $unauthorizedAttempts === 0 && $nonSecureConnections === 0),
            ],
        ];
    }

    /**
     * Get security metrics summary.
     * For instructors, only shows summary for their courses.
     */
    public function getSecurityMetricsSummary(?int $instructorId = null): array
    {
        $metrics = $this->calculateSecurityMetrics(null, null, $instructorId);
        
        return [
            'overall_status' => $this->getOverallSecurityStatus($metrics),
            'key_metrics' => [
                'Unauthorized Access Attempts' => [
                    'value' => $metrics['security_events']['unauthorized_access_attempts'],
                    'target' => 0,
                    'status' => $metrics['compliance']['zero_unauthorized_access'] ? 'pass' : 'fail',
                ],
                'Encryption Success Rate' => [
                    'value' => $metrics['payments']['encryption_success_rate'] . '%',
                    'target' => '100%',
                    'status' => $metrics['compliance']['hundred_percent_encryption'] ? 'pass' : 'fail',
                ],
                'Non-Secure Connections' => [
                    'value' => $metrics['security_events']['non_secure_connections'],
                    'target' => 0,
                    'status' => $metrics['compliance']['zero_non_secure_connections'] ? 'pass' : 'fail',
                ],
                'Security Failures' => [
                    'value' => $metrics['security_events']['encryption_failures'] + 
                              $metrics['security_events']['unauthorized_access_attempts'] +
                              $metrics['security_events']['non_secure_connections'],
                    'target' => 0,
                    'status' => $metrics['compliance']['hundred_percent_negative_records'] ? 'pass' : 'fail',
                ],
            ],
            'detailed_metrics' => $metrics,
        ];
    }

    /**
     * Get overall security status.
     */
    protected function getOverallSecurityStatus(array $metrics): string
    {
        $allCompliant = 
            $metrics['compliance']['zero_unauthorized_access'] &&
            $metrics['compliance']['hundred_percent_encryption'] &&
            $metrics['compliance']['zero_non_secure_connections'] &&
            $metrics['compliance']['hundred_percent_negative_records'];

        return $allCompliant ? 'compliant' : 'non_compliant';
    }

    /**
     * Monitor payment completion and verify security.
     */
    public function monitorPaymentCompletion(Payment $payment): void
    {
        // Verify encryption
        $isEncrypted = $this->verifyPaymentEncryption($payment);
        
        // Verify HTTPS (if payment is being created now)
        if ($payment->wasRecentlyCreated) {
            $isHttps = $this->verifyHttpsConnection($payment);
        }
        
        // Log successful secure payment
        if ($isEncrypted) {
            $this->logSecurityEvent(
                'payment_encryption_verified',
                'info',
                "Payment {$payment->transaction_id} successfully encrypted/tokenized",
                $payment,
                ['payment_method' => $payment->paymentMethod->code ?? 'unknown']
            );
        }
    }

    /**
     * Get recent security events.
     * For instructors, only shows events related to their courses.
     */
    public function getRecentSecurityEvents(int $limit = 50, ?int $instructorId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = SecurityLog::with(['payment.course', 'user'])
            ->orderBy('occurred_at', 'desc');

        // If instructor ID is provided, filter to only show events for their courses
        if ($instructorId !== null) {
            $query->where(function ($q) use ($instructorId) {
                // Events with payments that belong to instructor's courses
                $q->whereHas('payment.course', function ($courseQuery) use ($instructorId) {
                    $courseQuery->where('instructor_id', $instructorId);
                })
                // OR events without payments but related to instructor's courses (if any metadata indicates course)
                ->orWhere(function ($subQ) {
                    // General security events that don't have payment_id
                    // These are shown to all instructors as they're system-wide
                    $subQ->whereNull('payment_id')
                         ->whereIn('event_type', ['non_secure_connection', 'suspicious_activity']);
                });
            });
        }

        return $query->limit($limit)->get();
    }

    /**
     * Generate security report.
     * For instructors, only includes data for their courses.
     */
    public function generateSecurityReport(?Carbon $startDate = null, ?Carbon $endDate = null, ?int $instructorId = null): array
    {
        $metrics = $this->calculateSecurityMetrics($startDate, $endDate, $instructorId);
        $recentEvents = $this->getRecentSecurityEvents(100, $instructorId);
        
        return [
            'report_generated_at' => now()->toIso8601String(),
            'period' => $metrics['period'],
            'metrics' => $metrics,
            'recent_events' => $recentEvents->map(function ($event) {
                return [
                    'id' => $event->id,
                    'event_type' => $event->event_type,
                    'severity' => $event->severity,
                    'description' => $event->description,
                    'occurred_at' => $event->occurred_at->toIso8601String(),
                    'payment_id' => $event->payment_id,
                    'user_id' => $event->user_id,
                    'ip_address' => $event->ip_address,
                ];
            }),
            'summary' => $this->getSecurityMetricsSummary(),
        ];
    }
}

