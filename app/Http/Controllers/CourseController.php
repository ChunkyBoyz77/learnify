<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
     * Show the form for creating a new course.
     */
    public function create(): View
    {
        return view('courses.create');
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'what_you_will_learn' => ['nullable', 'string'],
            'skills_gain' => ['nullable', 'string'],
            'assessment_info' => ['nullable', 'string'],
            'duration' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'level' => ['nullable', 'string', 'max:255'],
            'modules' => ['nullable', 'string'], // JSON string of modules
        ]);

        DB::beginTransaction();
        try {
            $course = Course::create([
                'instructor_id' => Auth::id(),
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'what_you_will_learn' => $validated['what_you_will_learn'] ?? null,
                'skills_gain' => $validated['skills_gain'] ?? null,
                'assessment_info' => $validated['assessment_info'] ?? null,
                'duration' => $validated['duration'] ?? null,
                'price' => $validated['price'],
                'level' => $validated['level'] ?? null,
            ]);

            // Create lessons/modules if provided
            if (!empty($validated['modules'])) {
                $modules = json_decode($validated['modules'], true);
                if (is_array($modules)) {
                    foreach ($modules as $index => $module) {
                        Lesson::create([
                            'course_id' => $course->id,
                            'title' => $module['title'] ?? '',
                            'description' => $module['description'] ?? null,
                            'order_number' => $index + 1,
                        ]);
                    }
                }
            }

            DB::commit();
            
            return redirect()->route('courses.show', $course)
                ->with('success', 'Course created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            throw $e; // Re-throw validation exceptions to show validation errors
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the error for debugging
            \Log::error('Course creation failed: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return back()
                ->with('error', 'Failed to create course. Please check your input and try again.')
                ->withInput();
        }
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

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $course): View
    {
        // Ensure the authenticated user is the instructor of this course
        if (Auth::id() !== $course->instructor_id) {
            abort(403, 'Unauthorized. You can only edit your own courses.');
        }

        return view('courses.edit', [
            'course' => $course,
        ]);
    }

    /**
     * Update the specified course in storage.
     */
    public function update(Request $request, Course $course): RedirectResponse
    {
        // Ensure the authenticated user is the instructor of this course
        if (Auth::id() !== $course->instructor_id) {
            abort(403, 'Unauthorized. You can only update your own courses.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'what_you_will_learn' => ['nullable', 'string'],
            'skills_gain' => ['nullable', 'string'],
            'assessment_info' => ['nullable', 'string'],
            'duration' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'level' => ['nullable', 'string', 'max:255'],
        ]);

        $course->update($validated);

        return redirect()->route('courses.show', $course)
            ->with('success', 'Course updated successfully!');
    }

    /**
     * Remove the specified course from storage.
     */
    public function destroy(Course $course): RedirectResponse
    {
        // Ensure the authenticated user is the instructor of this course
        if (Auth::id() !== $course->instructor_id) {
            abort(403, 'Unauthorized. You can only delete your own courses.');
        }

        $course->delete();

        return redirect()->route('courses.index')
            ->with('success', 'Course deleted successfully!');
    }
}
