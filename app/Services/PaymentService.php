<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;

class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
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
        $payment->update([
            'status' => 'completed',
            'paid_at' => now(),
            'payment_details' => array_merge($payment->payment_details ?? [], [
                'stripe_checkout_session_id' => $session['id'],
                'stripe_payment_intent_id' => $session['payment_intent'] ?? null,
                'stripe_customer_id' => $session['customer'] ?? null,
                'payment_status' => $session['payment_status'],
            ]),
        ]);

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
            return false;
        }

        $paymentIntentId = $payment->payment_details['stripe_payment_intent_id'] ?? null;
        $chargeId = $payment->payment_details['stripe_charge_id'] ?? null;

        if (!$chargeId && $paymentIntentId) {
            try {
                $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
                $chargeId = $paymentIntent->charges->data[0]->id ?? null;
            } catch (ApiErrorException $e) {
                \Log::error('Stripe refund error: Could not retrieve charge', ['error' => $e->getMessage()]);
                return false;
            }
        }

        if (!$chargeId) {
            return false;
        }

        try {
            $refundAmount = $amount ? (int)($amount * 100) : null; // Convert to cents

            $refund = \Stripe\Refund::create([
                'charge' => $chargeId,
                'amount' => $refundAmount,
            ]);

            $payment->update([
                'status' => 'refunded',
                'notes' => ($payment->notes ?? '') . "\nRefunded: $" . number_format($refund->amount / 100, 2) . " (Stripe Refund ID: {$refund->id})",
                'payment_details' => array_merge($payment->payment_details ?? [], [
                    'stripe_refund_id' => $refund->id,
                    'refund_amount' => $refund->amount / 100,
                ]),
            ]);

            // Optionally cancel enrollment
            if ($payment->enrollment) {
                $payment->enrollment->update([
                    'status' => 'cancelled',
                ]);
            }

            return true;
        } catch (ApiErrorException $e) {
            \Log::error('Stripe refund error', ['error' => $e->getMessage()]);
            return false;
        }
    }
}

