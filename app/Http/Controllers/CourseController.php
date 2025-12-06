<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Display a listing of courses.
     */
    public function index(): View
    {
        $courses = Course::latest()->paginate(12);

    return view('courses.index', [
        'courses' => $courses,
    ]);
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course): View
    {
        $isEnrolled = false;
        
        if (auth()->check()) {
            $isEnrolled = auth()->user()->enrollments()
                ->where('course_id', $course->id)
                ->where('status', 'active')
                ->exists();
        }

        return view('courses.show', [
            'course' => $course,
            'isEnrolled' => $isEnrolled,
        ]);
    }
}
