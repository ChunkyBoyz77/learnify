<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SecurityMetricsController;
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

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');



/*
|--------------------------------------------------------------------------
| Instructor Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'instructor'])->group(function () {

// CREATE / STORE
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');

    // EDIT / UPDATE
    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');

    // DELETE
    Route::patch('/courses/{course}/archive', [CourseController::class, 'archive'])
        ->name('courses.archive');

    Route::delete('/materials/{material}/delete', [CourseController::class, 'deleteMaterial'])
        ->name('materials.delete');

    Route::delete('/quiz-question/{question}/delete', [CourseController::class, 'deleteQuestion'])
        ->name('quiz.question.delete');

    // COURSE CONTENT
    Route::get('/courses/{course}/content', [CourseController::class, 'content'])->name('courses.content');

    // MATERIALS
    Route::get('/lessons/{lesson}/edit-material', [CourseController::class, 'editMaterial'])->name('lessons.edit');
    Route::post('/lessons/{lesson}/update-material', [CourseController::class, 'updateMaterial'])->name('lessons.update');

    // QUIZ EDITOR
    Route::get('/lessons/{lesson}/quiz', [CourseController::class, 'quizEditor'])->name('lessons.quiz.editor');
    Route::post('/lessons/{lesson}/quiz/store', [CourseController::class, 'quizStore'])->name('lessons.quiz.store');

    // Instructor Course List
    Route::get('/instructor/my-courses', [CourseController::class, 'myCoursesInstructor'])
        ->name('courses.my');

    // Instructor payment routes
    Route::get('/instructor/payments', [PaymentController::class, 'instructorPayments'])->name('instructor.payments.index');
    Route::get('/instructor/courses/{course}/payments', [PaymentController::class, 'instructorCoursePayments'])->name('instructor.payments.course');
    
    // Payment CRUD routes (instructor only)
    Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
    Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    Route::patch('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

    // Refund request management routes (instructor only)
    Route::get('/instructor/refund-requests', [PaymentController::class, 'instructorRefundRequests'])->name('payments.refund-requests.index');
    Route::get('/instructor/refund-requests/{refundRequest}', [PaymentController::class, 'showRefundRequest'])->name('payments.refund-requests.show');
    Route::post('/instructor/refund-requests/{refundRequest}/approve', [PaymentController::class, 'approveRefund'])->name('payments.refund-requests.approve');
    Route::post('/instructor/refund-requests/{refundRequest}/reject', [PaymentController::class, 'rejectRefund'])->name('payments.refund-requests.reject');
});


/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','student'])->group(function () {

    Route::get('/student/my-courses', [CourseController::class, 'myCoursesStudent'])
        ->name('student.mycourses');

    Route::get('/student/course/{course}/content', [CourseController::class, 'studentContent'])
        ->name('student.course.content');
    
    Route::get('/quiz/{lesson}/take', [CourseController::class, 'quizTake'])
        ->name('quiz.take');

    Route::post('/quiz/{lesson}/submit', [CourseController::class, 'quizSubmit'])
        ->name('quiz.submit');

    

});

Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');

// Stripe webhook (must be outside auth middleware and CSRF protection)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->name('stripe.webhook')
    ->middleware('web');

// Security metrics routes (instructor only - auth middleware applied, role check in controller)
Route::middleware('auth')->group(function () {
    Route::get('/security/metrics', [SecurityMetricsController::class, 'index'])->name('security.metrics.index');
    Route::get('/security/report', [SecurityMetricsController::class, 'report'])->name('security.metrics.report');
});

// Payment routes (require authentication and HTTPS enforcement)
Route::middleware(['auth', 'https.payments'])->group(function () {
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

    // Refund request routes (students)
    Route::get('/payments/{payment}/request-refund', [PaymentController::class, 'requestRefund'])->name('payments.refund-request.create');
    Route::post('/payments/{payment}/refund-request', [PaymentController::class, 'storeRefundRequest'])->name('payments.refund-request.store');

    // Enrollment routes
    Route::get('/enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
    Route::get('/enrollments/{enrollment}', [EnrollmentController::class, 'show'])->name('enrollments.show');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Feedback routes
    Route::resource('feedbacks', FeedbackController::class);
});

require __DIR__.'/auth.php';
