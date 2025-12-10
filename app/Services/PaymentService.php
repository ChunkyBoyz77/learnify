<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Services\PaymentSecurityService;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;

class PaymentService
{
    protected PaymentSecurityService $securityService;

    public function __construct(PaymentSecurityService $securityService)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $this->securityService = $securityService;
    }

    /**
     * Create a Stripe Checkout Session for course enrollment.
     * This redirects to Stripe's hosted checkout page with FPX and card options.
     */
    public function createCheckoutSession(User $user, Course $course, array $paymentData = []): array
    {
        // Generate unique transaction ID
        $transactionId = $this->generateTransactionId();

        // Get or create payment method
        $paymentMethod = PaymentMethod::where('code', 'stripe')->first();
        
        if (!$paymentMethod) {
            $paymentMethod = PaymentMethod::create([
                'name' => 'Stripe',
                'code' => 'stripe',
                'description' => 'Pay securely with credit/debit card or FPX via Stripe',
                'is_active' => true,
            ]);
        }

        // Create payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'payment_method_id' => $paymentMethod->id,
            'transaction_id' => $transactionId,
            'amount' => $course->price,
            'status' => 'pending',
            'payment_type' => 'enrollment',
            'payment_details' => $paymentData['payment_details'] ?? null,
            'notes' => $paymentData['notes'] ?? null,
        ]);

        try {
            // Get currency from config (default to MYR for FPX support)
            $currency = config('services.stripe.currency', 'myr');
            
            // FPX only works with MYR currency
            $paymentMethodTypes = $currency === 'myr' ? ['card', 'fpx'] : ['card'];
            
            // Create Stripe Checkout Session
            $checkoutSession = Session::create([
                'payment_method_types' => $paymentMethodTypes,
                'line_items' => [[
                    'price_data' => [
                        'currency' => $currency,
                        'product_data' => [
                            'name' => $course->title,
                            'description' => Str::limit($course->description ?? 'Course enrollment', 500),
                        ],
                        'unit_amount' => (int)($course->price * 100), // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('payments.success', ['payment' => $payment->id]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payments.failure', ['payment' => $payment->id]),
                'customer_email' => $user->email,
                'metadata' => [
                    'payment_id' => $payment->id,
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'transaction_id' => $transactionId,
                ],
            ]);

            // Update payment with Stripe checkout session ID
            $payment->update([
                'payment_details' => [
                    'stripe_checkout_session_id' => $checkoutSession->id,
                    'stripe_payment_status' => $checkoutSession->payment_status,
                ],
                'status' => 'processing',
            ]);

            return [
                'payment' => $payment,
                'checkout_url' => $checkoutSession->url,
                'session_id' => $checkoutSession->id,
            ];
        } catch (ApiErrorException $e) {
            $payment->update([
                'status' => 'failed',
                'notes' => 'Stripe error: ' . $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Create a Stripe Payment Intent for course enrollment (legacy method).
     */
    public function createPaymentIntent(User $user, Course $course, array $paymentData = []): array
    {
        // Generate unique transaction ID
        $transactionId = $this->generateTransactionId();

        // Get or create payment method
        $paymentMethod = PaymentMethod::where('code', 'stripe')->first();
        
        if (!$paymentMethod) {
            $paymentMethod = PaymentMethod::create([
                'name' => 'Stripe',
                'code' => 'stripe',
                'description' => 'Pay securely with credit or debit card via Stripe',
                'is_active' => true,
            ]);
        }

        // Create payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'payment_method_id' => $paymentMethod->id,
            'transaction_id' => $transactionId,
            'amount' => $course->price,
            'status' => 'pending',
            'payment_type' => 'enrollment',
            'payment_details' => $paymentData['payment_details'] ?? null,
            'notes' => $paymentData['notes'] ?? null,
        ]);

        try {
            // Create Stripe Payment Intent
            $paymentIntent = PaymentIntent::create([
                'amount' => (int)($course->price * 100), // Convert to cents
                'currency' => 'usd',
                'metadata' => [
                    'payment_id' => $payment->id,
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'transaction_id' => $transactionId,
                ],
                'description' => "Payment for course: {$course->title}",
            ]);

            // Update payment with Stripe payment intent ID
            $payment->update([
                'payment_details' => [
                    'stripe_payment_intent_id' => $paymentIntent->id,
                    'stripe_client_secret' => $paymentIntent->client_secret,
                    'status' => $paymentIntent->status,
                ],
                'status' => 'processing',
            ]);

            return [
                'payment' => $payment,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
            ];
        } catch (ApiErrorException $e) {
            $payment->update([
                'status' => 'failed',
                'notes' => 'Stripe error: ' . $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Process a payment for course enrollment (legacy method for non-Stripe payments).
     */
    public function processPayment(User $user, Course $course, array $paymentData): Payment
    {
        // Generate unique transaction ID
        $transactionId = $this->generateTransactionId();

        // Get or create payment method
        $paymentMethod = PaymentMethod::where('code', $paymentData['payment_method'] ?? 'credit_card')->first();
        
        if (!$paymentMethod) {
            $paymentMethod = PaymentMethod::create([
                'name' => ucfirst(str_replace('_', ' ', $paymentData['payment_method'] ?? 'credit_card')),
                'code' => $paymentData['payment_method'] ?? 'credit_card',
                'is_active' => true,
            ]);
        }

        // Create payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'payment_method_id' => $paymentMethod->id,
            'transaction_id' => $transactionId,
            'amount' => $course->price,
            'status' => 'pending',
            'payment_type' => 'enrollment',
            'payment_details' => $paymentData['payment_details'] ?? null,
            'notes' => $paymentData['notes'] ?? null,
        ]);

        // Process payment based on method
        $this->processPaymentByMethod($payment, $paymentData);

        return $payment->fresh();
    }

    /**
     * Process payment based on payment method.
     */
    protected function processPaymentByMethod(Payment $payment, array $paymentData): void
    {
        $method = $payment->paymentMethod->code;

        switch ($method) {
            case 'credit_card':
                $this->processCreditCardPayment($payment, $paymentData);
                break;
            case 'paypal':
                $this->processPayPalPayment($payment, $paymentData);
                break;
            case 'bank_transfer':
                $this->processBankTransferPayment($payment, $paymentData);
                break;
            default:
                $this->processGenericPayment($payment, $paymentData);
        }
    }

    /**
     * Handle Stripe webhook events.
     */
    public function handleStripeWebhook(array $eventData): void
    {
        $eventType = $eventData['type'];
        $eventObject = $eventData['data']['object'];

        // Handle Checkout Session events
        if ($eventType === 'checkout.session.completed') {
            $sessionId = $eventObject['id'];
            $payment = Payment::whereJsonContains('payment_details->stripe_checkout_session_id', $sessionId)->first();

            if ($payment) {
                if ($eventObject['payment_status'] === 'paid') {
                    $this->handleCheckoutSuccess($payment, $eventObject);
                } else {
                    $this->handleCheckoutFailure($payment, $eventObject);
                }
                return;
            }
        }

        // Handle Payment Intent events (legacy)
        if (isset($eventObject['id']) && strpos($eventObject['id'], 'pi_') === 0) {
            $paymentIntent = $eventObject;
            $payment = Payment::whereJsonContains('payment_details->stripe_payment_intent_id', $paymentIntent['id'])->first();

            if (!$payment) {
                \Log::warning('Stripe webhook: Payment not found', ['payment_intent_id' => $paymentIntent['id']]);
                return;
            }

            switch ($eventType) {
                case 'payment_intent.succeeded':
                    $this->handlePaymentSuccess($payment, $paymentIntent);
                    break;
                case 'payment_intent.payment_failed':
                    $this->handlePaymentFailure($payment, $paymentIntent);
                    break;
                case 'payment_intent.canceled':
                    $this->handlePaymentCancellation($payment, $paymentIntent);
                    break;
            }
        }
    }

    /**
     * Handle successful checkout session.
     */
    protected function handleCheckoutSuccess(Payment $payment, array $session): void
    {
        $paymentDetails = array_merge($payment->payment_details ?? [], [
            'stripe_checkout_session_id' => $session['id'],
            'stripe_payment_intent_id' => $session['payment_intent'] ?? null,
            'stripe_customer_id' => $session['customer'] ?? null,
            'payment_status' => $session['payment_status'],
        ]);

        // Try to get charge ID from payment intent for easier refunds later
        $paymentIntentId = $session['payment_intent'] ?? null;
        if ($paymentIntentId) {
            try {
                $paymentIntent = PaymentIntent::retrieve($paymentIntentId, [
                    'expand' => ['charges.data.balance_transaction'],
                ]);
                
                if ($paymentIntent->charges && count($paymentIntent->charges->data) > 0) {
                    $paymentDetails['stripe_charge_id'] = $paymentIntent->charges->data[0]->id;
                }
            } catch (ApiErrorException $e) {
                // Log but don't fail - we can retrieve it later during refund
                \Log::warning('Could not retrieve charge ID during checkout success', [
                    'payment_id' => $payment->id,
                    'payment_intent_id' => $paymentIntentId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $payment->update([
            'status' => 'completed',
            'paid_at' => now(),
            'payment_details' => $paymentDetails,
        ]);

        // Monitor payment security
        $this->securityService->monitorPaymentCompletion($payment);

        // Create enrollment after successful payment
        $this->createEnrollment($payment);
    }

    /**
     * Handle failed checkout session.
     */
    protected function handleCheckoutFailure(Payment $payment, array $session): void
    {
        $payment->update([
            'status' => 'failed',
            'payment_details' => array_merge($payment->payment_details ?? [], [
                'stripe_checkout_session_id' => $session['id'],
                'payment_status' => $session['payment_status'],
            ]),
        ]);
    }

    /**
     * Handle successful payment.
     */
    protected function handlePaymentSuccess(Payment $payment, array $paymentIntent): void
    {
        $payment->update([
            'status' => 'completed',
            'paid_at' => now(),
            'payment_details' => array_merge($payment->payment_details ?? [], [
                'stripe_payment_intent_id' => $paymentIntent['id'],
                'stripe_charge_id' => $paymentIntent['charges']['data'][0]['id'] ?? null,
                'status' => $paymentIntent['status'],
            ]),
        ]);

        // Monitor payment security
        $this->securityService->monitorPaymentCompletion($payment);

        // Create enrollment after successful payment
        $this->createEnrollment($payment);
    }

    /**
     * Handle failed payment.
     */
    protected function handlePaymentFailure(Payment $payment, array $paymentIntent): void
    {
        $payment->update([
            'status' => 'failed',
            'payment_details' => array_merge($payment->payment_details ?? [], [
                'stripe_payment_intent_id' => $paymentIntent['id'],
                'status' => $paymentIntent['status'],
                'error' => $paymentIntent['last_payment_error']['message'] ?? 'Payment failed',
            ]),
        ]);
    }

    /**
     * Handle cancelled payment.
     */
    protected function handlePaymentCancellation(Payment $payment, array $paymentIntent): void
    {
        $payment->update([
            'status' => 'cancelled',
            'payment_details' => array_merge($payment->payment_details ?? [], [
                'stripe_payment_intent_id' => $paymentIntent['id'],
                'status' => $paymentIntent['status'],
            ]),
        ]);
    }

    /**
     * Verify payment status with Stripe.
     */
    public function verifyPaymentStatus(Payment $payment): bool
    {
        // Check for checkout session first
        $sessionId = $payment->payment_details['stripe_checkout_session_id'] ?? null;
        if ($sessionId) {
            try {
                $session = Session::retrieve($sessionId);
                if ($session->payment_status === 'paid' && $payment->status !== 'completed') {
                    $this->handleCheckoutSuccess($payment, $session->toArray());
                }
                return $session->payment_status === 'paid';
            } catch (ApiErrorException $e) {
                \Log::error('Stripe checkout session verification error', ['error' => $e->getMessage()]);
                return false;
            }
        }

        // Fallback to payment intent (legacy)
        $paymentIntentId = $payment->payment_details['stripe_payment_intent_id'] ?? null;
        if (!$paymentIntentId) {
            return false;
        }

        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            if ($paymentIntent->status === 'succeeded' && $payment->status !== 'completed') {
                $this->handlePaymentSuccess($payment, $paymentIntent->toArray());
            }

            return $paymentIntent->status === 'succeeded';
        } catch (ApiErrorException $e) {
            \Log::error('Stripe verification error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Process credit card payment (now uses Stripe).
     */
    protected function processCreditCardPayment(Payment $payment, array $paymentData): void
    {
        // This method is kept for backward compatibility
        // For Stripe, use createPaymentIntent instead
        $payment->update([
            'status' => 'processing',
        ]);
    }

    /**
     * Process PayPal payment (simulated).
     */
    protected function processPayPalPayment(Payment $payment, array $paymentData): void
    {
        // Similar to credit card, but for PayPal integration
        $payment->update([
            'status' => 'processing',
        ]);

        if (($paymentData['simulate_success'] ?? true)) {
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
            ]);

            $this->createEnrollment($payment);
        } else {
            $payment->update([
                'status' => 'failed',
            ]);
        }
    }

    /**
     * Process bank transfer payment.
     */
    protected function processBankTransferPayment(Payment $payment, array $paymentData): void
    {
        // Bank transfers are typically pending until manually verified
        $payment->update([
            'status' => 'pending',
            'notes' => 'Payment pending bank transfer verification.',
        ]);
    }

    /**
     * Process generic payment method.
     */
    protected function processGenericPayment(Payment $payment, array $paymentData): void
    {
        $payment->update([
            'status' => 'processing',
        ]);
    }

    /**
     * Create enrollment after successful payment.
     */
    protected function createEnrollment(Payment $payment): void
    {
        // Check if enrollment already exists
        $enrollment = Enrollment::where('user_id', $payment->user_id)
            ->where('course_id', $payment->course_id)
            ->first();

        if (!$enrollment) {
            $enrollment = Enrollment::create([
                'user_id' => $payment->user_id,
                'course_id' => $payment->course_id,
                'status' => 'active',
                'enrolled_at' => now(),
            ]);

            // Update payment with enrollment ID
            $payment->update([
                'enrollment_id' => $enrollment->id,
            ]);
        } else {
            // Update existing enrollment to active
            $enrollment->update([
                'status' => 'active',
                'enrolled_at' => $enrollment->enrolled_at ?? now(),
            ]);

            $payment->update([
                'enrollment_id' => $enrollment->id,
            ]);
        }
    }

    /**
     * Generate unique transaction ID.
     */
    protected function generateTransactionId(): string
    {
        do {
            $transactionId = 'TXN-' . strtoupper(Str::random(12));
        } while (Payment::where('transaction_id', $transactionId)->exists());

        return $transactionId;
    }

    /**
     * Verify payment status.
     */
    public function verifyPayment(Payment $payment): bool
    {
        // In a real application, this would check with the payment gateway
        return $payment->status === 'completed';
    }

    /**
     * Refund a payment via Stripe.
     */
    public function refundPayment(Payment $payment, ?float $amount = null): bool
    {
        if ($payment->status !== 'completed') {
            \Log::warning('Cannot refund payment: Payment status is not completed', [
                'payment_id' => $payment->id,
                'status' => $payment->status,
            ]);
            return false;
        }

        $paymentDetails = $payment->payment_details ?? [];
        $chargeId = $paymentDetails['stripe_charge_id'] ?? null;
        $paymentIntentId = $paymentDetails['stripe_payment_intent_id'] ?? null;
        $sessionId = $paymentDetails['stripe_checkout_session_id'] ?? null;

        // Check if payment has any Stripe identifiers at all
        // If no Stripe identifiers, this payment cannot be refunded via Stripe
        if (!$chargeId && !$paymentIntentId && !$sessionId) {
            \Log::warning('Cannot refund via Stripe: Payment has no Stripe identifiers', [
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'payment_details' => $paymentDetails,
            ]);
            return false;
        }

        // If we don't have charge ID, try to get it from PaymentIntent or Checkout Session
        if (!$chargeId) {
            try {
                // First, try to get PaymentIntent ID from Checkout Session if available
                if ($sessionId && !$paymentIntentId) {
                    try {
                        $session = Session::retrieve($sessionId);
                        $paymentIntentId = $session->payment_intent ?? null;
                        
                        // Update payment details with payment intent ID if we got it
                        if ($paymentIntentId) {
                            $payment->update([
                                'payment_details' => array_merge($paymentDetails, [
                                    'stripe_payment_intent_id' => $paymentIntentId,
                                ]),
                            ]);
                            $paymentDetails = $payment->refresh()->payment_details ?? [];
                        }
                    } catch (ApiErrorException $e) {
                        \Log::error('Stripe refund error: Could not retrieve checkout session', [
                            'payment_id' => $payment->id,
                            'session_id' => $sessionId,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                // Now try to get charge ID from PaymentIntent
                if ($paymentIntentId) {
                    try {
                        // Retrieve payment intent with expanded charges
                        $paymentIntent = PaymentIntent::retrieve($paymentIntentId, [
                            'expand' => ['charges.data.balance_transaction', 'latest_charge'],
                        ]);
                        
                        // Check payment intent status
                        $paymentIntentStatus = $paymentIntent->status ?? 'unknown';
                        
                        // If payment intent is not succeeded, we can't refund via Stripe
                        if ($paymentIntentStatus !== 'succeeded') {
                            \Log::error('Stripe refund: Payment intent status is not succeeded', [
                                'payment_id' => $payment->id,
                                'payment_intent_id' => $paymentIntentId,
                                'payment_intent_status' => $paymentIntentStatus,
                                'payment_status' => $payment->status,
                            ]);
                            return false;
                        }
                        
                        \Log::info('Retrieved payment intent for refund', [
                            'payment_id' => $payment->id,
                            'payment_intent_id' => $paymentIntentId,
                            'payment_intent_status' => $paymentIntentStatus,
                            'has_charges' => isset($paymentIntent->charges),
                            'charges_count' => isset($paymentIntent->charges) ? count($paymentIntent->charges->data ?? []) : 0,
                            'has_latest_charge' => isset($paymentIntent->latest_charge),
                        ]);
                        
                        // Method 1: Try to get charge ID from latest_charge (most reliable for newer Stripe API)
                        if (!empty($paymentIntent->latest_charge)) {
                            if (is_string($paymentIntent->latest_charge)) {
                                $chargeId = $paymentIntent->latest_charge;
                            } elseif (is_object($paymentIntent->latest_charge)) {
                                $chargeId = $paymentIntent->latest_charge->id ?? null;
                            }
                            
                            if ($chargeId) {
                                \Log::info('Found charge ID from latest_charge', [
                                    'payment_id' => $payment->id,
                                    'charge_id' => $chargeId,
                                ]);
                            }
                        }
                        
                        // Method 2: Try to get charge ID from charges list
                        if (!$chargeId && isset($paymentIntent->charges) && count($paymentIntent->charges->data ?? []) > 0) {
                            $chargeId = $paymentIntent->charges->data[0]->id;
                            \Log::info('Found charge ID from charges list', [
                                'payment_id' => $payment->id,
                                'charge_id' => $chargeId,
                            ]);
                        }
                        
                        // Method 3: Try to list charges separately if we still don't have charge ID
                        if (!$chargeId) {
                            try {
                                $charges = \Stripe\Charge::all([
                                    'payment_intent' => $paymentIntentId,
                                    'limit' => 1,
                                ]);
                                
                                if (count($charges->data) > 0) {
                                    $chargeId = $charges->data[0]->id;
                                    \Log::info('Found charge ID by listing charges', [
                                        'payment_id' => $payment->id,
                                        'charge_id' => $chargeId,
                                    ]);
                                }
                            } catch (\Exception $e) {
                                \Log::warning('Could not list charges separately', [
                                    'payment_id' => $payment->id,
                                    'error' => $e->getMessage(),
                                ]);
                            }
                        }
                        
                        // Update payment details with charge ID if we found it
                        if ($chargeId) {
                            $payment->update([
                                'payment_details' => array_merge($paymentDetails, [
                                    'stripe_charge_id' => $chargeId,
                                ]),
                            ]);
                        } else {
                            // Log detailed information about why we couldn't get charge ID
                            \Log::error('Stripe refund: Could not retrieve charge ID from payment intent', [
                                'payment_id' => $payment->id,
                                'payment_intent_id' => $paymentIntentId,
                                'payment_intent_status' => $paymentIntentStatus,
                                'has_latest_charge' => isset($paymentIntent->latest_charge),
                                'latest_charge_value' => is_string($paymentIntent->latest_charge ?? null) 
                                    ? $paymentIntent->latest_charge 
                                    : (isset($paymentIntent->latest_charge) ? 'object' : 'null'),
                                'has_charges' => isset($paymentIntent->charges),
                                'charges_count' => isset($paymentIntent->charges) ? count($paymentIntent->charges->data ?? []) : 0,
                            ]);
                            return false;
                        }
                    } catch (ApiErrorException $e) {
                        \Log::error('Stripe refund error: Could not retrieve payment intent', [
                            'payment_id' => $payment->id,
                            'payment_intent_id' => $paymentIntentId,
                            'error' => $e->getMessage(),
                            'error_code' => $e->getStripeCode() ?? 'unknown',
                            'error_type' => get_class($e),
                        ]);
                        return false;
                    }
                }
                
                // If we still don't have payment intent ID, try to get it from session
                if (!$paymentIntentId && $sessionId) {
                    try {
                        $session = Session::retrieve($sessionId);
                        $paymentIntentId = is_string($session->payment_intent) 
                            ? $session->payment_intent 
                            : ($session->payment_intent->id ?? null);
                        
                        if ($paymentIntentId) {
                            // Retry getting charge ID with the payment intent from session
                            $paymentIntent = PaymentIntent::retrieve($paymentIntentId, [
                                'expand' => ['charges.data.balance_transaction'],
                            ]);
                            
                            if ($paymentIntent->charges && count($paymentIntent->charges->data) > 0) {
                                $chargeId = $paymentIntent->charges->data[0]->id;
                                
                                // Update payment details
                                $payment->update([
                                    'payment_details' => array_merge($paymentDetails, [
                                        'stripe_payment_intent_id' => $paymentIntentId,
                                        'stripe_charge_id' => $chargeId,
                                    ]),
                                ]);
                            }
                        }
                    } catch (ApiErrorException $e) {
                        \Log::error('Stripe refund error: Could not retrieve checkout session', [
                            'payment_id' => $payment->id,
                            'session_id' => $sessionId,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Stripe refund error: Unexpected error retrieving charge ID', [
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                return false;
            }
        }

        if (!$chargeId) {
            \Log::error('Stripe refund error: Could not find charge ID', [
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'payment_status' => $payment->status,
                'has_session_id' => !empty($sessionId),
                'has_payment_intent_id' => !empty($paymentIntentId),
                'has_charge_id' => !empty($paymentDetails['stripe_charge_id']),
                'payment_details_keys' => array_keys($paymentDetails),
            ]);
            return false;
        }

        try {
            $refundAmount = $amount ? (int)($amount * 100) : null; // Convert to cents

            $refund = \Stripe\Refund::create([
                'charge' => $chargeId,
                'amount' => $refundAmount,
            ]);

            $refundAmount = $refund->amount / 100;
            $currency = config('services.stripe.currency', 'myr');
            $currencySymbol = strtoupper($currency) === 'MYR' ? 'RM' : '$';
            
            $payment->update([
                'status' => 'refunded',
                'notes' => ($payment->notes ?? '') . "\n" . now()->format('Y-m-d H:i:s') . " - Refunded via Stripe: {$currencySymbol}" . number_format($refundAmount, 2) . " (Stripe Refund ID: {$refund->id})",
                'payment_details' => array_merge($payment->payment_details ?? [], [
                    'stripe_refund_id' => $refund->id,
                    'refund_amount' => $refundAmount,
                    'refunded_at' => now()->toIso8601String(),
                    'refund_status' => 'succeeded',
                ]),
            ]);

            // Cancel enrollment when payment is refunded
            if ($payment->enrollment) {
                $payment->enrollment->update([
                    'status' => 'cancelled',
                    'completed_at' => null, // Clear completion date if exists
                ]);
            }

            return true;
        } catch (ApiErrorException $e) {
            \Log::error('Stripe refund error', ['error' => $e->getMessage()]);
            return false;
        }
    }
}

