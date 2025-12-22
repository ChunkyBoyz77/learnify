<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Feedback;
use App\Models\Material;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizAttempt;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /* =========================
     * 1. Explore Courses
     * ========================= */
    public function index(Request $request)
    {
        $search = $request->input('search') ?? $request->input('q');;

        $courses = Course::query()
            ->where('is_archived', false)   // ✅ Only show active courses
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(12);

        if (auth()->guest()) {
        return view('courses.guestpreview', compact('courses'));
        }

        return view('courses.index', compact('courses'));
    }


    /* =========================
     * 2. Show Course Detail
     * ========================= */
    public function show(Course $course)
    {
        $isEnrolled = false;
        if (Auth::check() && Auth::user()->role === 'student') {
            $isEnrolled = Enrollment::where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->exists();
        }

        // Try removing the 'status' and 'feedback_type' filters first to see if data appears
        $feedbacks = Feedback::with('user')
            ->where('course_id', $course->id)
            ->latest()
            ->get();

        return view('courses.show', compact('course', 'isEnrolled', 'feedbacks'));
    }

    /* =========================
     * 3. Instructor My Courses
     * ========================= */
    public function myCoursesInstructor()
    {
        $courses = Course::where('instructor_id', Auth::id())->latest()->get();
        return view('courses.instructor.my-courses', compact('courses'));
    }


    /* =========================
     * 4. Student My Courses
     * ========================= */
    public function myCoursesStudent()
    {
        $courses = Course::join('enrollments', 'courses.id', '=', 'enrollments.course_id')
            ->where('enrollments.user_id', Auth::id())
            ->select('courses.*')
            ->get();

        return view('courses.student.my-courses', compact('courses'));
    }


    /* =========================
     * 5. Show Create Course Form
     * ========================= */
    public function create()
    {
        return view('courses.instructor.create');
    }


    /* =========================
     * 6. Store Course + Lessons
     * ========================= */
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required',
            'description' => 'required',
            'price'       => 'required|numeric',
            'image'       => 'nullable|image',
        ]);

        $path = $request->hasFile('image')
            ? $request->file('image')->store('course_images', 'public')
            : null;

        $course = Course::create([
            'instructor_id'      => Auth::id(),
            'title'              => $request->title,
            'description'        => $request->description,
            'what_you_will_learn'=> $request->what_you_will_learn,
            'skills_gain'        => $request->skills_gain,
            'assessment_info'    => $request->assessment_info,
            'duration'           => $request->duration,
            'price'              => $request->price,
            'level'              => $request->level,
            'image'              => $path,
        ]);

        // SAVE LESSONS
        if ($request->has('lessons')) {
            foreach (array_values($request->lessons) as $index => $lesson) {
                if (!empty($lesson['title'])) {
                    Lesson::create([
                        'course_id'    => $course->id,
                        'title'        => $lesson['title'],
                        'order_number' => $index + 1,
                    ]);
                }
            }
        }

        return redirect()->route('courses.my')->with('success', 'Course created successfully!');
    }


    /* =========================
     * 7. Course Content Page
     * ========================= */
    public function content(Request $request, Course $course)
    {
        $lessons = $course->lessons()
            ->with(['materials', 'quiz.questions'])
            ->orderBy('order_number')
            ->get();

        // selected lesson
        $selectedLesson = $request->lesson
            ? $lessons->firstWhere('id', $request->lesson)
            : $lessons->first();

        return view('courses.instructor.content', compact('course', 'lessons', 'selectedLesson'));
    }


    /* =========================
     * 8. Edit Lesson Material
     * ========================= */
    public function editMaterial(Lesson $lesson)
    {
        if (Auth::id() !== $lesson->course->instructor_id) abort(403);

        return view('courses.instructor.edit-material', [
            'lesson'    => $lesson,
            'materials' => $lesson->materials,
            'quiz'      => $lesson->quiz()->with('questions')->first(),
        ]);
    }

    public function updateMaterial(Request $request, Lesson $lesson)
    {
        if (Auth::id() !== $lesson->course->instructor_id) abort(403);

        // Video file
        if ($request->hasFile('video_file')) {
            $path = $request->file('video_file')->store('materials/videos', 'public');
            $lesson->materials()->create([
                'file_path' => $path,
                'file_type' => 'video',
            ]);
        }

        // YouTube URL
        if ($request->filled('video_url')) {
            $lesson->materials()->create([
                'file_path' => $request->video_url,
                'file_type' => 'video',
            ]);
        }

        // Notes PDF
        if ($request->hasFile('note_file')) {
            $path = $request->file('note_file')->store('materials/notes', 'public');
            $lesson->materials()->create([
                'file_path' => $path,
                'file_type' => 'pdf',
            ]);
        }

        return back()->with('success', 'Material updated successfully.');
    }


    /* =========================
     * 9. QUIZ EDITOR
     * ========================= */
    public function quizEditor(Lesson $lesson)
    {
        if (Auth::id() !== $lesson->course->instructor_id) abort(403);

        $quiz = $lesson->quiz()->firstOrCreate([
            'title' => 'Quiz for ' . $lesson->title,
        ]);

        $quiz->load('questions');

        return view('courses.instructor.quiz-create', compact('lesson', 'quiz'));
    }


    public function quizStore(Request $request, Lesson $lesson)
    {
        if (Auth::id() !== $lesson->course->instructor_id) abort(403);

        $request->validate([
            'question_text' => 'required',
            'option_a'      => 'required',
            'option_b'      => 'required',
            'option_c'      => 'required',
            'correct_option'=> 'required|in:A,B,C',
        ]);

        $quiz = $lesson->quiz()->firstOrCreate([
            'title' => 'Quiz for ' . $lesson->title,
        ]);

        $options = [
            $request->option_a,
            $request->option_b,
            $request->option_c,
        ];

        $correct = ['A'=>0,'B'=>1,'C'=>2][$request->correct_option];

        QuizQuestion::create([
            'quiz_id'              => $quiz->id,
            'question_text'        => $request->question_text,
            'options'              => $options,
            'correct_option_index' => $correct,
        ]);

        return back()->with('success', 'Question added!');
    }

    public function studentContent(Request $request, Course $course)
{
    // must be enrolled
    $isEnrolled = Enrollment::where('user_id', Auth::id())
        ->where('course_id', $course->id)
        ->exists();

    if (!$isEnrolled) {
        abort(403, 'You must be enrolled to access this course.');
    }

    $lessons = $course->lessons()
        ->with(['materials', 'quiz.questions'])
        ->orderBy('order_number')
        ->get();

    $selectedLesson = null;

    if ($request->has('lesson')) {
        $selectedLesson = $lessons->firstWhere('id', (int)$request->lesson);
    } elseif ($lessons->count() > 0) {
        $selectedLesson = $lessons->first();
    }

    return view('courses.student.content', compact('course', 'lessons', 'selectedLesson'));
}

    /* =========================
     * 10. Student Take Quiz
     * ========================= */
    public function quizTake(Lesson $lesson)
    {
        if (!Auth::check() || Auth::user()->role !== 'student') abort(403);

        $quiz = $lesson->quiz()->with('questions')->firstOrFail();

        return view('courses.student.quiz-take', compact('lesson','quiz'));
    }

    public function quizSubmit(Request $request, Lesson $lesson)
    {
        if (Auth::user()->role !== 'student') abort(403);

        $quiz = $lesson->quiz()->with('questions')->firstOrFail();
        $questions = $quiz->questions;

        $answers = $request->answers;
        $score = 0;

        foreach ($questions as $q) {
            if (isset($answers[$q->id]) &&
                (int)$answers[$q->id] === $q->correct_option_index) {
                $score++;
            }
        }

        QuizAttempt::create([
            'user_id' => Auth::id(),
            'quiz_id' => $quiz->id,
            'answers' => $answers,
            'score'   => $score,
        ]);

        return back()->with('success', "Your score: {$score} / {$questions->count()}");
    }


    public function archive(Course $course)
    {
        if (auth()->id() !== $course->instructor_id) {
            abort(403, 'Unauthorized action.');
        }

        $course->update([
            'is_archived' => 1,
        ]);

        return redirect()->route('courses.my')
            ->with('success', 'Course archived successfully.');
    }

    public function deleteMaterial(Material $material)
    {
        $lesson = $material->lesson;
        $course = $lesson->course;

        if (auth()->id() !== $course->instructor_id) {
            abort(403, 'Unauthorized action.');
        }

        // Delete file if stored locally
        if (in_array($material->file_type, ['video', 'pdf'])) {
            if (\Storage::disk('public')->exists($material->file_path)) {
                \Storage::disk('public')->delete($material->file_path);
            }
        }

        $material->delete();

        return back()->with('success', 'Material deleted successfully.');
    }

    public function deleteQuestion(QuizQuestion $question)
    {
        $quiz = $question->quiz;
        $lesson = $quiz->lesson;
        $course = $lesson->course;

        if (auth()->id() !== $course->instructor_id) {
            abort(403, 'Unauthorized action.');
        }

        $question->delete();

        return back()->with('success', 'Quiz question deleted.');
    }

    public function guestSearch(Request $request)
    {
        $q = $request->q;

        $courses = Course::with('instructor')
            ->when($q, fn ($query) =>
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
            )
            ->paginate(12);

        return view('courses.guestpreview', compact('courses'));
    }





}
