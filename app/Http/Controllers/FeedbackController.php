<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\User;

class FeedbackController extends Controller
{
    // List feedback
    public function index(Request $request)
    {
        $query = Feedback::with(['user','course'])->latest();

        // If the logged-in user is a student, restrict to their own feedback
        if (auth()->user()->role === 'student') {
            $query->where('user_id', auth()->id());
        } else {
            // Instructors can filter/search
            if ($request->filled('keyword')) {
                $query->where('comment', 'like', '%' . $request->keyword . '%');
            }
            if ($request->filled('course_id')) {
                $query->where('course_id', $request->course_id);
            }
            if ($request->filled('student_id')) {
                $query->where('user_id', $request->student_id);
            }
            if ($request->filled('rating')) {
                $query->where('rating', $request->rating);
            }
        }

        $feedback = $query->paginate(10)->appends($request->query());

        // Only load courses/students for instructors
        $courses = [];
        $students = [];
        if (auth()->user()->role === 'instructor') {
            $courses = Course::all();
            $students = User::where('role', 'student')->get();
        }

        return view('feedbacks.index', compact('feedback','courses','students'));
    }


    // Show form to create feedback (students only)
    public function create()
    {
        $user = auth()->user();

        // Only fetch courses the student is enrolled in
        $courses = $user->enrolledCourses()->get();

        return view('feedbacks.create', compact('courses'));
    }


    // Store feedback (students only)
    public function store(Request $request)
    {
        $user = auth()->user();

        // Validate that the course_id belongs to this student
        if (!$user->enrolledCourses()->where('courses.id', $request->course_id)->exists()) {
            return redirect()->route('feedbacks.index')
                             ->with('error', 'You can only give feedback for courses you are enrolled in.');
        }

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'rating'    => 'required|integer|min:1|max:5',
            'message'   => 'required|string|max:1000',
        ]);

        Feedback::create([
            'user_id'  => $user->id,
            'course_id'=> $request->course_id,
            'rating'   => $request->rating,
            'comment'  => $request->message,
            'status'   => 'pending',
        ]);

        return redirect()->route('feedbacks.index')->with('success', 'Feedback submitted successfully.');
    }


    // Show single feedback
    public function show(Feedback $feedback)
    {
        return view('feedbacks.show', compact('feedback'));
    }

    // Edit feedback (students only, own feedback)
    public function edit(Feedback $feedback)
    {
        $this->authorize('update', $feedback); // policy ensures only owner can edit
        return view('feedbacks.edit', compact('feedback'));
    }

    // Update feedback (students only, own feedback)
    public function update(Request $request, Feedback $feedback)
    {
        $this->authorize('update', $feedback);

        $request->validate([
            'comment' => 'required|string|max:1000',
            'rating'   => 'nullable|integer|min:1|max:5',
        ]);

        $feedback->update($request->only('comment','rating'));

        return redirect()->route('feedbacks.index')->with('success','Feedback updated!');
    }

    // Delete feedback (students only, own feedback)
    public function destroy(Feedback $feedback)
    {
        $this->authorize('delete', $feedback);
        $feedback->delete();

        return redirect()->route('feedbacks.index')->with('success','Feedback deleted!');
    }
}
