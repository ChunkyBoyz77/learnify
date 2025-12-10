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
    ): void {
        $this->logSecurityEvent(
            'unauthorized_access_attempt',
            'critical',
            "Unauthorized access attempt to {$resource}",
            $payment,
            array_merge($metadata, [
                'resource' => $resource,
                'user_id' => auth()->id(),
            ])
        );
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
     */
    public function calculateSecurityMetrics(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? Carbon::now()->subDays(30);
        $endDate = $endDate ?? Carbon::now();

        // Total payments in period
        $totalPayments = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();

        // Payments with encryption verification
        $encryptedPayments = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get()
            ->filter(function ($payment) {
                return $this->verifyPaymentEncryption($payment);
            })
            ->count();

        // Unauthorized access attempts
        $unauthorizedAttempts = SecurityLog::where('event_type', 'unauthorized_access_attempt')
            ->whereBetween('occurred_at', [$startDate, $endDate])
            ->count();

        // Non-secure connection attempts
        $nonSecureConnections = SecurityLog::where('event_type', 'non_secure_connection')
            ->whereBetween('occurred_at', [$startDate, $endDate])
            ->count();

        // Encryption failures
        $encryptionFailures = SecurityLog::where('event_type', 'encryption_verification_failed')
            ->whereBetween('occurred_at', [$startDate, $endDate])
            ->count();

        // Suspicious activities
        $suspiciousActivities = SecurityLog::where('event_type', 'suspicious_activity')
            ->whereBetween('occurred_at', [$startDate, $endDate])
            ->count();

        // Total security events
        $totalSecurityEvents = SecurityLog::whereBetween('occurred_at', [$startDate, $endDate])
            ->count();

        // Calculate percentages
        $encryptionSuccessRate = $totalPayments > 0 
            ? ($encryptedPayments / $totalPayments) * 100 
            : 100;

        return [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
            'payments' => [
                'total' => $totalPayments,
                'encrypted' => $encryptedPayments,
                'encryption_success_rate' => round($encryptionSuccessRate, 2),
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
                'hundred_percent_encryption' => $encryptionSuccessRate === 100.0,
                'zero_non_secure_connections' => $nonSecureConnections === 0,
                'hundred_percent_negative_records' => $totalSecurityEvents === 0 || 
                    ($encryptionFailures === 0 && $unauthorizedAttempts === 0 && $nonSecureConnections === 0),
            ],
        ];
    }

    /**
     * Get security metrics summary.
     */
    public function getSecurityMetricsSummary(): array
    {
        $metrics = $this->calculateSecurityMetrics();
        
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
     */
    public function getRecentSecurityEvents(int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return SecurityLog::with(['payment', 'user'])
            ->orderBy('occurred_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Generate security report.
     */
    public function generateSecurityReport(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $metrics = $this->calculateSecurityMetrics($startDate, $endDate);
        $recentEvents = $this->getRecentSecurityEvents(100);
        
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

