<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\RefundRequest;
use App\Models\User;
use App\Services\PaymentService;
use App\Services\RefundService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;
    protected RefundService $refundService;

    public function __construct(PaymentService $paymentService, RefundService $refundService)
    {
        $this->paymentService = $paymentService;
        $this->refundService = $refundService;
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
            ->with(['course', 'paymentMethod', 'enrollment', 'refundRequests'])
            ->latest()
            ->paginate(15);

        // Check refund eligibility for each payment
        $refundEligibilities = [];
        foreach ($payments as $payment) {
            if ($payment->status === 'completed') {
                $refundEligibilities[$payment->id] = $this->refundService->checkRefundEligibility($payment);
            }
        }

        return view('payments.history', [
            'payments' => $payments,
            'refundEligibilities' => $refundEligibilities,
        ]);
    }

    /**
     * Show payment details.
     */
    public function show(Payment $payment): View
    {
        $this->authorize('view', $payment);

        $payment->load(['course', 'enrollment', 'paymentMethod', 'user', 'refundRequests']);
        
        // Check refund eligibility for students
        $refundEligibility = null;
        $existingRefundRequest = null;
        
        if (Auth::user()->role === 'student' && $payment->user_id === Auth::id()) {
            $refundEligibility = $this->refundService->checkRefundEligibility($payment);
            $existingRefundRequest = $payment->refundRequests()->latest()->first();
        }

        return view('payments.show', [
            'payment' => $payment,
            'refundEligibility' => $refundEligibility,
            'existingRefundRequest' => $existingRefundRequest,
        ]);
    }

    /**
     * Show all payments for instructor's courses.
     */
    public function instructorPayments(Request $request): View
    {
        $user = Auth::user();
        
        // Ensure user is an instructor
        if ($user->role !== 'instructor') {
            abort(403, 'Only instructors can view this page.');
        }

        // Get all course IDs owned by the instructor
        $courseIds = $user->courses()->pluck('id');

        // Start building the query for payments
        $paymentsQuery = Payment::whereIn('course_id', $courseIds)
            ->with(['course', 'user', 'paymentMethod', 'enrollment'])
            ->latest();

        // Filter by course if provided
        if ($request->has('course_id') && $request->course_id) {
            // Verify the course belongs to the instructor
            if ($courseIds->contains($request->course_id)) {
                $paymentsQuery->where('course_id', $request->course_id);
            }
        }

        // Filter by status if provided
        if ($request->has('status') && $request->status) {
            $paymentsQuery->where('status', $request->status);
        }

        $payments = $paymentsQuery->paginate(20)->withQueryString();

        // Get courses for filter dropdown
        $courses = $user->courses()->orderBy('title')->get();

        return view('payments.instructor-index', [
            'payments' => $payments,
            'courses' => $courses,
            'selectedCourse' => $request->course_id,
            'selectedStatus' => $request->status,
        ]);
    }

    /**
     * Show payments for a specific course (instructor view).
     */
    public function instructorCoursePayments(Course $course, Request $request): View
    {
        $user = Auth::user();
        
        // Ensure user is an instructor and owns this course
        if ($user->role !== 'instructor' || $course->instructor_id !== $user->id) {
            abort(403, 'You do not have permission to view payments for this course.');
        }

        // Start building the query for payments
        $paymentsQuery = Payment::where('course_id', $course->id)
            ->with(['user', 'paymentMethod', 'enrollment'])
            ->latest();

        // Filter by status if provided
        if ($request->has('status') && $request->status) {
            $paymentsQuery->where('status', $request->status);
        }

        $payments = $paymentsQuery->paginate(20)->withQueryString();

        return view('payments.instructor-course', [
            'course' => $course,
            'payments' => $payments,
            'selectedStatus' => $request->status,
        ]);
    }

    /**
     * Show the form for creating a new manual payment.
     */
    public function create(Request $request): View
    {
        $user = Auth::user();
        
        // Ensure user is an instructor
        if ($user->role !== 'instructor') {
            abort(403, 'Only instructors can create payments.');
        }

        $courses = $user->courses()->orderBy('title')->get();
        $students = User::where('role', 'student')->orderBy('name')->get();
        $paymentMethods = PaymentMethod::where('is_active', true)->orderBy('name')->get();
        
        // Pre-select course if provided
        $selectedCourse = $request->has('course_id') ? Course::find($request->course_id) : null;

        return view('payments.create', [
            'courses' => $courses,
            'students' => $students,
            'paymentMethods' => $paymentMethods,
            'selectedCourse' => $selectedCourse,
        ]);
    }

    /**
     * Store a newly created manual payment.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        // Ensure user is an instructor
        if ($user->role !== 'instructor') {
            abort(403, 'Only instructors can create payments.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0.01',
            'status' => 'required|in:pending,processing,completed,failed,cancelled,refunded',
            'payment_type' => 'required|in:enrollment,subscription,other',
            'transaction_id' => 'nullable|string|max:255|unique:payments,transaction_id',
            'notes' => 'nullable|string|max:1000',
            'paid_at' => 'nullable|date',
        ]);

        // Verify the course belongs to the instructor
        $course = Course::findOrFail($validated['course_id']);
        if ($course->instructor_id !== $user->id) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'You do not have permission to create payments for this course.');
        }

        // Verify the user is a student
        $student = User::findOrFail($validated['user_id']);
        if ($student->role !== 'student') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Payments can only be created for students.');
        }

        // Generate transaction ID if not provided
        if (empty($validated['transaction_id'])) {
            do {
                $transactionId = 'MAN-' . strtoupper(Str::random(12));
            } while (Payment::where('transaction_id', $transactionId)->exists());
            
            $validated['transaction_id'] = $transactionId;
        }

        // Set paid_at if status is completed and paid_at not provided
        if ($validated['status'] === 'completed' && empty($validated['paid_at'])) {
            $validated['paid_at'] = now();
        }

        // Create the payment
        $payment = Payment::create($validated);

        // If payment is completed and payment_type is enrollment, create/update enrollment
        if ($validated['status'] === 'completed' && $validated['payment_type'] === 'enrollment') {
            $enrollment = Enrollment::where('user_id', $validated['user_id'])
                ->where('course_id', $validated['course_id'])
                ->first();

            if (!$enrollment) {
                $enrollment = Enrollment::create([
                    'user_id' => $validated['user_id'],
                    'course_id' => $validated['course_id'],
                    'status' => 'active',
                    'enrolled_at' => now(),
                ]);
            } else {
                $enrollment->update([
                    'status' => 'active',
                    'enrolled_at' => $enrollment->enrolled_at ?? now(),
                ]);
            }

            // Link payment to enrollment
            $payment->update(['enrollment_id' => $enrollment->id]);
        }

        return redirect()->route('instructor.payments.course', $course)
            ->with('success', 'Payment created successfully.');
    }

    /**
     * Show the form for editing a payment.
     */
    public function edit(Payment $payment): View
    {
        $this->authorize('update', $payment);

        $payment->load(['course', 'user', 'paymentMethod']);
        
        return view('payments.edit', [
            'payment' => $payment,
            'paymentMethods' => PaymentMethod::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    /**
     * Update a payment.
     * 
     * IMPORTANT: For data integrity, only status and notes can be updated.
     * Critical fields (user_id, course_id, amount, transaction_id) are IMMUTABLE.
     */
    public function update(Request $request, Payment $payment): RedirectResponse
    {
        $this->authorize('update', $payment);

        // Only allow status and notes to be updated for data integrity
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,failed,cancelled,refunded',
            'notes' => 'nullable|string|max:1000',
            'paid_at' => 'nullable|date',
        ]);

        // Log the status change for audit trail
        $oldStatus = $payment->status;
        $statusChanged = $oldStatus !== $validated['status'];

        // SECURITY: Prevent changing completed payments to incomplete status (maintains data integrity)
        if ($payment->status === 'completed' && $validated['status'] !== 'completed') {
            \Log::warning('Attempted to change completed payment status', [
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'attempted_by' => Auth::id(),
                'attempted_by_name' => Auth::user()->name,
                'old_status' => $payment->status,
                'attempted_status' => $validated['status'],
                'timestamp' => now(),
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Cannot change status of completed payments. Use "refunded" status for refunds to maintain audit trail.');
        }

        // SECURITY: Prevent changing refunded payments (immutable final state)
        if ($payment->status === 'refunded' && $validated['status'] !== 'refunded') {
            \Log::warning('Attempted to change refunded payment status', [
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'attempted_by' => Auth::id(),
                'attempted_by_name' => Auth::user()->name,
                'old_status' => $payment->status,
                'attempted_status' => $validated['status'],
                'timestamp' => now(),
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Cannot change status of refunded payments. This maintains data integrity and audit trail.');
        }

        // Set paid_at if status is being changed to completed
        if ($validated['status'] === 'completed' && $payment->status !== 'completed') {
            $validated['paid_at'] = $validated['paid_at'] ?? now();
        }

        // Clear paid_at if status is changed from completed (but we prevent this above)
        if ($payment->status === 'completed' && $validated['status'] !== 'completed') {
            $validated['paid_at'] = null;
        }

        // Log status change for audit trail
        if ($statusChanged) {
            \Log::info('Payment status changed', [
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
                'changed_by' => Auth::id(),
                'changed_by_name' => Auth::user()->name,
                'timestamp' => now(),
            ]);
        }

        // Update only allowed fields
        $payment->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'],
            'paid_at' => $validated['paid_at'] ?? $payment->paid_at,
        ]);

        // If payment is now completed and payment_type is enrollment, ensure enrollment exists
        if ($validated['status'] === 'completed' && $payment->payment_type === 'enrollment' && !$payment->enrollment_id) {
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
            }

            $payment->update(['enrollment_id' => $enrollment->id]);
        }

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Payment updated successfully.');
    }

    /**
     * Delete (void) a payment.
     */
    public function destroy(Payment $payment): RedirectResponse
    {
        $this->authorize('delete', $payment);

        $course = $payment->course;

        // Soft delete the payment
        $payment->delete();

        return redirect()->route('instructor.payments.course', $course)
            ->with('success', 'Payment voided successfully.');
    }

    /**
     * Show the refund request form for a payment.
     */
    public function requestRefund(Payment $payment): View|RedirectResponse
    {
        // Only student who made the payment can request refund
        if ($payment->user_id !== Auth::id()) {
            abort(403, 'You can only request refunds for your own payments.');
        }

        // Check refund eligibility
        $eligibility = $this->refundService->checkRefundEligibility($payment);

        if (!$eligibility['eligible']) {
            return redirect()->route('payments.show', $payment)
                ->with('error', 'This payment is not eligible for refund: ' . implode(' ', $eligibility['reasons']));
        }

        return view('payments.request-refund', [
            'payment' => $payment->load(['course', 'enrollment', 'paymentMethod']),
            'eligibility' => $eligibility,
            'refundAmount' => $this->refundService->calculateRefundAmount($payment),
        ]);
    }

    /**
     * Store a refund request.
     */
    public function storeRefundRequest(Request $request, Payment $payment): RedirectResponse
    {
        // Only student who made the payment can request refund
        if ($payment->user_id !== Auth::id()) {
            abort(403, 'You can only request refunds for your own payments.');
        }

        // Check refund eligibility again
        $eligibility = $this->refundService->checkRefundEligibility($payment);

        if (!$eligibility['eligible']) {
            return redirect()->route('payments.show', $payment)
                ->with('error', 'This payment is not eligible for refund: ' . implode(' ', $eligibility['reasons']));
        }

        $validated = $request->validate([
            'reason' => 'required|string|min:10|max:1000',
        ]);

        // Create refund request
        $refundRequest = RefundRequest::create([
            'payment_id' => $payment->id,
            'user_id' => Auth::id(),
            'course_id' => $payment->course_id,
            'enrollment_id' => $payment->enrollment_id,
            'requested_amount' => $this->refundService->calculateRefundAmount($payment),
            'status' => 'pending',
            'reason' => $validated['reason'],
        ]);

        // Log the refund request
        \Log::info('Refund request created', [
            'refund_request_id' => $refundRequest->id,
            'payment_id' => $payment->id,
            'transaction_id' => $payment->transaction_id,
            'student_id' => Auth::id(),
            'amount' => $refundRequest->requested_amount,
            'reason' => $validated['reason'],
        ]);

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Refund request submitted successfully. The instructor will review your request.');
    }

    /**
     * Show refund requests for instructor's courses.
     */
    public function instructorRefundRequests(Request $request): View
    {
        $user = Auth::user();
        
        if ($user->role !== 'instructor') {
            abort(403, 'Only instructors can view refund requests.');
        }

        // Get refund requests for instructor's courses
        $courseIds = $user->courses()->pluck('id');
        
        $refundRequestsQuery = RefundRequest::whereIn('course_id', $courseIds)
            ->with(['payment', 'user', 'course', 'enrollment'])
            ->latest();

        // Filter by status
        if ($request->has('status') && $request->status) {
            $refundRequestsQuery->where('status', $request->status);
        }

        $refundRequests = $refundRequestsQuery->paginate(20)->withQueryString();

        return view('payments.instructor-refunds', [
            'refundRequests' => $refundRequests,
            'selectedStatus' => $request->status,
        ]);
    }

    /**
     * Show refund request details for instructor review.
     */
    public function showRefundRequest(RefundRequest $refundRequest): View
    {
        $user = Auth::user();
        
        // Ensure user is instructor and owns the course
        if ($user->role !== 'instructor' || $refundRequest->course->instructor_id !== $user->id) {
            abort(403, 'You do not have permission to view this refund request.');
        }

        $refundRequest->load(['payment', 'user', 'course', 'enrollment']);

        return view('payments.refund-request-detail', [
            'refundRequest' => $refundRequest,
        ]);
    }

    /**
     * Approve a refund request.
     */
    public function approveRefund(Request $request, RefundRequest $refundRequest): RedirectResponse
    {
        $user = Auth::user();
        
        // Ensure user is instructor and owns the course
        if ($user->role !== 'instructor' || $refundRequest->course->instructor_id !== $user->id) {
            abort(403, 'You do not have permission to approve this refund request.');
        }

        // Cannot approve if already processed
        if ($refundRequest->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'This refund request has already been processed.');
        }

        $validated = $request->validate([
            'instructor_response' => 'nullable|string|max:1000',
        ]);

        // Get payment and refresh to ensure we have latest status with relationships
        $payment = $refundRequest->payment()->with('paymentMethod')->first();
        $payment->refresh();
        
        // Check if payment is already refunded
        if ($payment->status === 'refunded') {
            return redirect()->back()
                ->with('error', 'This payment has already been refunded.');
        }

        // Check if payment is completed (required for refund)
        if ($payment->status !== 'completed') {
            return redirect()->back()
                ->with('error', 'Only completed payments can be refunded.');
        }

        // Use database transaction to ensure data consistency
        \DB::beginTransaction();
        
        try {
            $refundProcessed = false;
            $stripeRefundId = null;
            
            // Check if this is a Stripe payment
            // Load payment method relationship if not loaded
            if (!$payment->relationLoaded('paymentMethod')) {
                $payment->load('paymentMethod');
            }
            
            $paymentMethod = $payment->paymentMethod;
            $paymentDetails = $payment->payment_details ?? [];
            
            // Check if payment method code is 'stripe'
            $hasStripePaymentMethod = $paymentMethod && $paymentMethod->code === 'stripe';
            
            // Check if payment has Stripe identifiers in payment_details
            $hasStripeIdentifiers = !empty($paymentDetails['stripe_checkout_session_id']) 
                || !empty($paymentDetails['stripe_payment_intent_id']) 
                || !empty($paymentDetails['stripe_charge_id']);
            
            // Determine if this is a Stripe payment that can be refunded via Stripe API
            // Only treat as Stripe payment if it has Stripe identifiers (actual Stripe transaction)
            // If payment method is Stripe but no identifiers, it's a manually created payment (treat as manual)
            $isStripePayment = $hasStripeIdentifiers; // Only if it has actual Stripe transaction identifiers
            
            // If payment method is Stripe but no identifiers, it was manually created - treat as manual refund
            if ($hasStripePaymentMethod && !$hasStripeIdentifiers) {
                \Log::info('Payment has Stripe payment method but no Stripe identifiers - treating as manual refund', [
                    'payment_id' => $payment->id,
                    'payment_method_code' => $paymentMethod->code,
                ]);
            }
            
            // Log diagnostic information
            \Log::info('Refund approval - Payment diagnostics', [
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'payment_method_id' => $payment->payment_method_id,
                'payment_method_code' => $paymentMethod ? $paymentMethod->code : 'NULL',
                'has_stripe_payment_method' => $hasStripePaymentMethod,
                'has_stripe_identifiers' => $hasStripeIdentifiers,
                'is_stripe_payment' => $isStripePayment,
                'payment_details_keys' => array_keys($paymentDetails),
                'payment_status' => $payment->status,
            ]);

            // Process refund
            if ($isStripePayment) {
                // Process Stripe refund (PaymentService handles payment status update and enrollment cancellation)
                try {
                    // Log before attempting refund
                    \Log::info('Attempting Stripe refund', [
                        'payment_id' => $payment->id,
                        'transaction_id' => $payment->transaction_id,
                        'payment_method_code' => $paymentMethod ? $paymentMethod->code : 'NULL',
                        'payment_details' => $paymentDetails,
                    ]);
                    
                    $refundProcessed = $this->paymentService->refundPayment($payment, $refundRequest->requested_amount);
                    
                    if (!$refundProcessed) {
                        // Get more diagnostic information after failed attempt
                        $payment->refresh();
                        $paymentDetails = $payment->payment_details ?? [];
                        $hasSessionId = !empty($paymentDetails['stripe_checkout_session_id']);
                        $hasPaymentIntentId = !empty($paymentDetails['stripe_payment_intent_id']);
                        $hasChargeId = !empty($paymentDetails['stripe_charge_id']);
                        
                        $errorMsg = 'Stripe refund failed. ';
                        
                        // Provide specific error message based on what's missing
                        if (!$hasStripeIdentifiers && !$hasStripePaymentMethod) {
                            $errorMsg .= 'This payment was not processed through Stripe. ';
                            $errorMsg .= 'Payment method: ' . ($paymentMethod ? $paymentMethod->code : 'not set') . '. ';
                            $errorMsg .= 'Please process this refund manually.';
                        } elseif (!$hasChargeId && !$hasPaymentIntentId && !$hasSessionId) {
                            $errorMsg .= 'Payment does not have required Stripe identifiers stored. ';
                            $errorMsg .= 'This may be a manually created payment that was not processed through Stripe checkout.';
                        } elseif (!$hasChargeId) {
                            $errorMsg .= 'Unable to retrieve Stripe charge ID from payment intent. ';
                            $errorMsg .= 'The payment may have been manually created or the Stripe transaction is incomplete. ';
                            $errorMsg .= 'This refund will need to be processed manually.';
                        } else {
                            $errorMsg .= 'Unable to process refund through Stripe API. ';
                            $errorMsg .= 'Please check Stripe dashboard or contact support.';
                        }
                        
                        throw new \Exception($errorMsg);
                    }
                    
                    // Refresh payment to get updated data from PaymentService
                    $payment->refresh();
                    $stripeRefundId = $payment->payment_details['stripe_refund_id'] ?? null;
                    
                    // PaymentService already updated payment status to 'refunded' and cancelled enrollment
                    // Just verify it was done correctly
                    if ($payment->status !== 'refunded') {
                        throw new \Exception('Payment status was not updated to refunded after Stripe refund.');
                    }
                    
                } catch (\Exception $e) {
                    \DB::rollBack();
                    \Log::error('Stripe refund processing failed', [
                        'refund_request_id' => $refundRequest->id,
                        'payment_id' => $payment->id,
                        'transaction_id' => $payment->transaction_id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Failed to process Stripe refund: ' . $e->getMessage() . '. Please check the payment details and try again.');
                }
            } else {
                // For non-Stripe payments, manually update payment status and cancel enrollment
                $payment->update([
                    'status' => 'refunded',
                    'notes' => ($payment->notes ?? '') . "\n" . now()->format('Y-m-d H:i:s') . " - Refund approved (Manual): RM" . number_format($refundRequest->requested_amount, 2) . " (Refund Request #{$refundRequest->id})",
                ]);
                
                // Cancel enrollment if exists
                $enrollment = $refundRequest->enrollment;
                if ($enrollment && $enrollment->status !== 'cancelled') {
                    $enrollment->update([
                        'status' => 'cancelled',
                        'completed_at' => null, // Clear completion date if exists
                    ]);
                }
                
                $refundProcessed = true;
            }

            // Double-check: Ensure enrollment is cancelled for Stripe payments (PaymentService should have done this)
            if ($isStripePayment) {
                $enrollment = $refundRequest->enrollment;
                if ($enrollment && $enrollment->status !== 'cancelled') {
                    $enrollment->update([
                        'status' => 'cancelled',
                        'completed_at' => null,
                    ]);
                }
            }

            // Update refund request status
            // 'processed' for successful Stripe refunds, 'approved' for manual processing
            $refundRequestStatus = ($isStripePayment && $refundProcessed) ? 'processed' : 'approved';
            
            $refundRequest->update([
                'status' => $refundRequestStatus,
                'processed_by' => $user->id,
                'processed_at' => now(),
                'instructor_response' => $validated['instructor_response'] ?? null,
            ]);

            // Commit transaction
            \DB::commit();

            // Log successful refund
            \Log::info('Refund approved and processed', [
                'refund_request_id' => $refundRequest->id,
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'processed_by' => $user->id,
                'processed_by_name' => $user->name,
                'amount' => $refundRequest->requested_amount,
                'stripe_refund_id' => $stripeRefundId,
                'payment_method' => $isStripePayment ? 'stripe' : 'manual',
                'enrollment_cancelled' => $enrollment ? true : false,
                'timestamp' => now(),
            ]);

            $successMessage = $isStripePayment 
                ? 'Refund approved and processed successfully via Stripe. The payment status has been updated to "refunded" and enrollment has been cancelled.'
                : 'Refund approved. Payment status updated to "refunded" and enrollment cancelled. Manual refund processing required.';

            return redirect()->route('payments.refund-requests.show', $refundRequest)
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            // Rollback transaction on any error
            \DB::rollBack();
            
            \Log::error('Refund approval failed', [
                'refund_request_id' => $refundRequest->id,
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to process refund: ' . $e->getMessage());
        }
    }

    /**
     * Reject a refund request.
     */
    public function rejectRefund(Request $request, RefundRequest $refundRequest): RedirectResponse
    {
        $user = Auth::user();
        
        // Ensure user is instructor and owns the course
        if ($user->role !== 'instructor' || $refundRequest->course->instructor_id !== $user->id) {
            abort(403, 'You do not have permission to reject this refund request.');
        }

        // Cannot reject if already processed
        if ($refundRequest->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'This refund request has already been processed.');
        }

        $validated = $request->validate([
            'instructor_response' => 'required|string|min:10|max:1000',
        ], [
            'instructor_response.required' => 'Please provide a reason for rejecting the refund request.',
            'instructor_response.min' => 'Rejection reason must be at least 10 characters.',
        ]);

        // Update refund request
        $refundRequest->update([
            'status' => 'rejected',
            'processed_by' => $user->id,
            'processed_at' => now(),
            'instructor_response' => $validated['instructor_response'],
        ]);

        \Log::info('Refund request rejected', [
            'refund_request_id' => $refundRequest->id,
            'payment_id' => $refundRequest->payment_id,
            'rejected_by' => $user->id,
            'reason' => $validated['instructor_response'],
        ]);

        return redirect()->route('payments.refund-requests.show', $refundRequest)
            ->with('success', 'Refund request rejected. The student has been notified.');
    }
}
