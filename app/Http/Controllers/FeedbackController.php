<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Course;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    // List feedback
    public function index()
    {
        if (auth()->user()->role === 'student') {
            // Student: only their own feedback
            $feedback = Feedback::where('user_id', auth()->id())
                                ->with('course')
                                ->latest()
                                ->paginate(10);
        } else {
            // Instructor: feedback for their courses
            $feedback = Feedback::whereHas('course', function($q) {
                $q->where('instructor_id', auth()->id());
            })->with('user','course')
              ->latest()
              ->paginate(10);
        }

        return view('feedbacks.index', compact('feedback'));
    }

    // Show form to create feedback (students only)
    public function create()
    {
        $courses = Course::all(); // or filter to enrolled courses
        return view('feedbacks.create', compact('courses'));
    }

    // Store feedback (students only)
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'rating'    => 'required|integer|min:1|max:5',
            'comments'  => 'nullable|string',
        ]);

        Feedback::create([
            'course_id' => $request->course_id,
            'user_id'   => auth()->id(), // ✅ correct usage
            'rating'    => $request->rating,
            'comments'  => $request->comments,
        ]);

        return redirect()->route('courses.show', $request->course_id)
                         ->with('success','Feedback submitted!');
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
            'comments' => 'required|string|max:1000',
            'rating'   => 'nullable|integer|min:1|max:5',
        ]);

        $feedback->update($request->only('comments','rating'));

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
