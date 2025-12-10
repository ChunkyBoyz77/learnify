<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of the user's enrollments.
     * Only shows active and completed enrollments (excludes cancelled/refunded).
     */
    public function index(): View
    {
        $enrollments = Auth::user()->enrollments()
            ->whereIn('status', ['active', 'completed']) // Exclude cancelled enrollments (refunded)
            ->with(['course'])
            ->latest()
            ->paginate(15);

        return view('enrollments.index', [
            'enrollments' => $enrollments,
        ]);
    }

    /**
     * Show the enrollment details.
     */
    public function show(Enrollment $enrollment): View
    {
        $this->authorize('view', $enrollment);

        return view('enrollments.show', [
            'enrollment' => $enrollment->load(['course', 'payments']),
        ]);
    }
}
