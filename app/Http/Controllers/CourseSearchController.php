<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseSearchController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::query();

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                ->orWhere('description', 'like', '%' . $request->q . '%');
            });
        }

        $courses = $query->latest()
                        ->paginate(12)
                        ->withQueryString();

        return view('courses.index', compact('courses'));
    }

}

