<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'order_number',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function quiz()
    {
        return $this->hasOne(Quiz::class);
    }

    // New helper relationship to directly access quiz questions
    public function quizQuestions()
    {
        return $this->hasManyThrough(
            QuizQuestion::class,
            Quiz::class,
            'lesson_id',  // FK on quizzes table
            'quiz_id',    // FK on quiz_questions table
            'id',         // lessons.id
            'id'          // quizzes.id
        );
    }
}
