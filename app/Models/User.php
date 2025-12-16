<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

/**
     * Courses created by instructor.
     */
    public function courses()
    {
        return $this->hasMany(\App\Models\Course::class, 'instructor_id');
    }

    /**
     * Get the enrollments for the user.
     */
    public function enrollments()
    {
        return $this->hasMany(\App\Models\Enrollment::class);
    }

    /**
     * The courses that the user is enrolled in.
     */
    public function enrolledCourses()
{
    return $this->belongsToMany(\App\Models\Course::class, 'enrollments')
                ->withPivot('status')
                ->withTimestamps();
}

    /**
     * Get the payments for the user.
     */
    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class);
    }

    /**
     * Check if user has active enrollment in a course.
     * Returns false for cancelled enrollments (refunded courses).
     */
    public function hasActiveEnrollment(int $courseId): bool
    {
        return $this->enrollments()
            ->where('course_id', $courseId)
            ->whereIn('status', ['active', 'completed'])
            ->exists();
    }

    /**
     * Get active enrollment for a course.
     * Returns null if enrollment is cancelled or doesn't exist.
     */
    public function getActiveEnrollment(int $courseId): ?\App\Models\Enrollment
    {
        return $this->enrollments()
            ->where('course_id', $courseId)
            ->whereIn('status', ['active', 'completed'])
            ->first();
    }
}
