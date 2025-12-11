<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    /* ============================================================
     * 1. EXPLORE COURSES (ALL USERS)
     * ============================================================ */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $courses = Course::query()
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', "%$search%");
            })
            ->latest()
            ->paginate(12);

        return view('courses.index', compact('courses'));
    }


    /* ============================================================
     * 2. SHOW COURSE DETAIL (Before Enrollment)
     * ============================================================ */
    public function show(Course $course)
    {
        $isEnrolled = false;

        if (Auth::check() && Auth::user()->role === 'student') {
            $isEnrolled = Enrollment::where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->exists();
        }

        return view('courses.show', compact('course', 'isEnrolled'));
    }


    /* ============================================================
     * 3. INSTRUCTOR – MY COURSES
     * ============================================================ */
    public function myCoursesInstructor()
    {
        $courses = Course::where('instructor_id', Auth::id())->latest()->get();

        return view('courses.instructor.my-courses', compact('courses'));
    }


    /* ============================================================
     * 4. STUDENT – MY COURSES
     * ============================================================ */
    public function myCoursesStudent()
    {
        $courses = Course::join('enrollments', 'courses.id', '=', 'enrollments.course_id')
            ->where('enrollments.user_id', Auth::id())
            ->select('courses.*')
            ->get();

        return view('courses.student.my-courses', compact('courses'));
    }


    /* ============================================================
     * 5. INSTRUCTOR – CREATE NEW COURSE
     * ============================================================ */
    public function create()
    {
        return view('courses.instructor.create');
    }

  public function store(Request $request)
{
    $request->validate([
        'title'   => 'required|string|max:255',
        'description' => 'required',
        'price' => 'required|numeric',
        'image'  => 'nullable|image'
    ]);

    $path = null;
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('course_images', 'public');
    }

    // CREATE COURSE
    $course = Course::create([
        'title'             => $request->title,
        'description'       => $request->description,
        'what_you_will_learn' => $request->what_you_will_learn,
        'skills_gain'       => $request->skills_gain,
        'assessment_info'   => $request->assessment_info,
        'duration'          => $request->duration,
        'price'             => $request->price,
        'level'             => $request->level,
        'image'             => $path,
        'instructor_id'     => Auth::id(),
    ]);

    // SAVE LESSONS
    if ($request->has('lessons')) {
        foreach ($request->lessons as $index => $lessonData) {
            Lesson::create([
                'course_id'    => $course->id,
                'title'        => $lessonData['title'],
                'order_number' => $index, // 1,2,3,4
            ]);
        }
    }

    return redirect()->route('courses.my')
                     ->with('success', 'Course created successfully!');
}


    /* ============================================================
     * 6. COURSE CONTENT PAGE (Instructor + Student)
     * ============================================================ */
  public function content(Request $request, Course $course)
{
    $lessons = Lesson::where('course_id', $course->id)
                     ->orderBy('order_number')
                     ->get();

    // Auto-select first lesson if none selected
    $selectedLesson = null;

    if ($request->has('lesson')) {
        $selectedLesson = Lesson::find($request->lesson);
    } elseif ($lessons->count() > 0) {
        $selectedLesson = $lessons->first();
    }

    return view('courses.instructor.content', compact('course', 'lessons', 'selectedLesson'));
}


    /* ============================================================
     * 7. EDIT MODULE (Upload Video, Notes)
     * ============================================================ */
    public function editMaterial(Lesson $lesson)
{
    // Load related materials (video + notes)
    $materials = $lesson->materials;

    // Load quiz (if exists)
    $quiz = $lesson->quiz;

    return view('courses.instructor.edit-material', compact('lesson', 'materials', 'quiz'));
}

    public function updateMaterial(Request $request, Lesson $lesson)
{
    $request->validate([
        'video_file' => 'nullable|mimetypes:video/mp4,video/avi,video/mov|max:20000',
        'video_url'  => 'nullable|string',
        'note_file'  => 'nullable|mimes:pdf|max:10000',
    ]);

    /** -------------------------------
     * 1. Save VIDEO (File or URL)
     * ------------------------------- */
    if ($request->hasFile('video_file')) {
        $path = $request->file('video_file')->store('materials/videos', 'public');

        $lesson->materials()->create([
            'file_path' => $path,
            'file_type' => 'video',
        ]);
    }

    if ($request->video_url) {
        $lesson->materials()->create([
            'file_path' => $request->video_url,
            'file_type' => 'video_url',
        ]);
    }

    /** -------------------------------
     * 2. Save NOTES (PDF)
     * ------------------------------- */
    if ($request->hasFile('note_file')) {
        $path = $request->file('note_file')->store('materials/notes', 'public');

        $lesson->materials()->create([
            'file_path' => $path,
            'file_type' => 'pdf',
        ]);
    }

    return redirect()->route('courses.content', ['course' => $lesson->course_id])
            ->with('success', 'Lesson material updated successfully.');
}



    /* ============================================================
     * 8. QUIZ EDITOR (Instructor)
     * ============================================================ */
    public function quizEditor(Module $module)
    {
        $questions = QuizQuestion::where('module_id', $module->id)->get();

        return view('courses.instructor.quiz-create', compact('module', 'questions'));
    }

    public function quizStore(Request $request, Module $module)
    {
        $request->validate([
            'question' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'correct_answer' => 'required|in:A,B,C',
        ]);

        QuizQuestion::create([
            'module_id' => $module->id,
            'question' => $request->question,
            'option_a' => $request->option_a,
            'option_b' => $request->option_b,
            'option_c' => $request->option_c,
            'correct_answer' => $request->correct_answer,
        ]);

        return back()->with('success', 'Quiz question added successfully!');
    }


    /* ============================================================
     * 9. STUDENT – TAKE QUIZ
     * ============================================================ */
    public function quizTake(Module $module)
    {
        $questions = QuizQuestion::where('module_id', $module->id)->get();

        return view('courses.student.quiz-take', compact('module', 'questions'));
    }

    public function quizSubmit(Request $request, Module $module)
    {
        $questions = QuizQuestion::where('module_id', $module->id)->get();

        $score = 0;
        foreach ($questions as $question) {
            if ($request->input('answer_'.$question->id) == $question->correct_answer) {
                $score++;
            }
        }

        return back()->with('success', "Your score: $score / " . count($questions));
    }
}
