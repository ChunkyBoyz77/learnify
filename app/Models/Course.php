<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id',
        'title',
        'description',
        'what_you_will_learn',
        'skills_gain',
        'assessment_info',
        'duration',
        'price',
        'level',
        'is_archived',
    ];

    // Instructor relationship
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    // Course has many lessons
    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    // Course has many enrollments
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }


    /**
     * Get the payments for the course.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
