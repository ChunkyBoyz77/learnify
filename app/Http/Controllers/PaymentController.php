<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }


    /**
     * Create Stripe Checkout Session and redirect to Stripe.
     */
    public function checkout(Course $course): View|RedirectResponse
    {
        // Redirect to login if not authenticated
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('info', 'Please login to enroll in this course.');
        }

        $user = Auth::user();

        // Check if user is already enrolled
        $enrollment = $user->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->first();

        if ($enrollment) {
            return redirect()->route('courses.show', $course)
                ->with('info', 'You are already enrolled in this course.');
        }

        try {
            // Create Stripe Checkout Session
            $result = $this->paymentService->createCheckoutSession($user, $course, [
                'notes' => request()->notes,
            ]);

            // Redirect to Stripe Checkout
            return redirect($result['checkout_url']);
        } catch (\Exception $e) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'Failed to initialize payment: ' . $e->getMessage());
        }
    }

    /**
     * Create Stripe Payment Intent (legacy method for embedded elements).
     */
    public function createIntent(Request $request, Course $course)
    {
        $request->validate([
            'payment_method' => 'required|string|in:stripe,credit_card',
        ]);

        $user = Auth::user();

        try {
            $result = $this->paymentService->createPaymentIntent($user, $course, [
                'notes' => $request->notes,
            ]);

            return response()->json([
                'client_secret' => $result['client_secret'],
                'payment_id' => $result['payment']->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create payment intent: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process the payment (for non-Stripe methods).
     */
    public function process(Request $request, Course $course): RedirectResponse
    {
        $request->validate([
            'payment_method' => 'required|string',
            'payment_details' => 'nullable|array',
        ]);

        $user = Auth::user();

        // For Stripe, redirect to checkout with payment intent
        if ($request->payment_method === 'stripe' || $request->payment_method === 'credit_card') {
            try {
                $result = $this->paymentService->createPaymentIntent($user, $course, [
                    'notes' => $request->notes,
                ]);

                return redirect()->route('payments.checkout', $course)
                    ->with('payment_intent', $result['client_secret'])
                    ->with('payment_id', $result['payment']->id);
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'Failed to initialize payment: ' . $e->getMessage());
            }
        }

        // Process other payment methods (legacy)
        $payment = $this->paymentService->processPayment($user, $course, [
            'payment_method' => $request->payment_method,
            'payment_details' => $request->payment_details,
            'notes' => $request->notes,
        ]);

        if ($payment->status === 'completed') {
            return redirect()->route('payments.success', $payment)
                ->with('success', 'Payment successful! You have been enrolled in the course.');
        } elseif ($payment->status === 'failed') {
            return redirect()->route('payments.failure', $payment)
                ->with('error', 'Payment failed. Please try again.');
        } else {
            return redirect()->route('payments.pending', $payment)
                ->with('info', 'Payment is being processed. You will be notified once it is completed.');
        }
    }

    /**
     * Confirm payment after Stripe payment intent succeeds.
     */
    public function confirm(Request $request, Course $course)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
        ]);

        $payment = Payment::findOrFail($request->payment_id);
        $this->authorize('view', $payment);

        // Verify payment status with Stripe
        $isCompleted = $this->paymentService->verifyPaymentStatus($payment);
        $payment->refresh();

        if ($payment->status === 'completed') {
            return response()->json([
                'success' => true,
                'redirect' => route('payments.success', $payment),
            ]);
        }

        return response()->json([
            'success' => false,
            'status' => $payment->status,
            'message' => 'Payment is still processing. Please wait...',
        ]);
    }

    /**
     * Show payment success page.
     */
    public function success(Payment $payment, Request $request): View
    {
        $this->authorize('view', $payment);

        // Verify payment status if coming from Stripe Checkout
        if ($request->has('session_id')) {
            $this->paymentService->verifyPaymentStatus($payment);
            $payment->refresh();
        }

        return view('payments.success', [
            'payment' => $payment->load(['course', 'enrollment']),
        ]);
    }

    /**
     * Show payment failure page.
     */
    public function failure(Payment $payment): View
    {
        $this->authorize('view', $payment);

        return view('payments.failure', [
            'payment' => $payment->load('course'),
        ]);
    }

    /**
     * Show pending payment page.
     */
    public function pending(Payment $payment): View
    {
        $this->authorize('view', $payment);

        return view('payments.pending', [
            'payment' => $payment->load('course'),
        ]);
    }

    /**
     * Show payment history for the authenticated user.
     */
    public function history(): View
    {
        $payments = Auth::user()->payments()
            ->with(['course', 'paymentMethod', 'enrollment'])
            ->latest()
            ->paginate(15);

        return view('payments.history', [
            'payments' => $payments,
        ]);
    }

    /**
     * Show payment details.
     */
    public function show(Payment $payment): View
    {
        $this->authorize('view', $payment);

        return view('payments.show', [
            'payment' => $payment->load(['course', 'enrollment', 'paymentMethod']),
        ]);
    }
}
