<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Role-based dashboard routes
Route::get('/student/dashboard', function () {
    return view('student-dashboard');
})->middleware(['auth', 'verified'])->name('student.dashboard');

Route::get('/instructor/dashboard', function () {
    return view('instructor-dashboard');
})->middleware(['auth', 'verified'])->name('instructor.dashboard');

// Public course routes
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');

// Stripe webhook (must be outside auth middleware and CSRF protection)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->name('stripe.webhook')
    ->middleware('web');

// Payment routes (require authentication)
Route::middleware('auth')->group(function () {
    // Payment routes - Checkout redirects to Stripe
    Route::get('/courses/{course}/checkout', [PaymentController::class, 'checkout'])->name('payments.checkout');
    Route::post('/courses/{course}/pay', [PaymentController::class, 'process'])->name('payments.process');
    Route::post('/courses/{course}/create-payment-intent', [PaymentController::class, 'createIntent'])->name('payments.create-intent');
    Route::post('/payments/confirm', [PaymentController::class, 'confirm'])->name('payments.confirm');
    Route::get('/payments/history', [PaymentController::class, 'history'])->name('payments.history');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('/payments/{payment}/success', [PaymentController::class, 'success'])->name('payments.success');
    Route::get('/payments/{payment}/failure', [PaymentController::class, 'failure'])->name('payments.failure');
    Route::get('/payments/{payment}/pending', [PaymentController::class, 'pending'])->name('payments.pending');

    // Enrollment routes
    Route::get('/enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
    Route::get('/enrollments/{enrollment}', [EnrollmentController::class, 'show'])->name('enrollments.show');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
