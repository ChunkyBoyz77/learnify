<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure an instructor exists
        $instructor = User::firstOrCreate(
            ['email' => 'instructor@learnify.com'],
            [
                'name' => 'Demo Instructor',
                'password' => bcrypt('password'),
                'role' => 'instructor',
            ]
        );

        echo "📌 Instructor: {$instructor->email} (password: password)\n";

        // Sample course list
        $courses = [
            [
                'title' => 'Software Quality Assurance Basics',
                'description' => 'Learn the fundamentals of SQA, testing strategies, and quality standards.',
                'what_you_will_learn' => 'Testing principles, QA processes, verification vs validation',
                'skills_gain' => 'Critical thinking, test case writing, defect analysis',
                'assessment_info' => 'Quiz after each module',
                'duration' => '4 weeks',
                'price' => 49.99,
                'level' => 'Beginner',
            ],
            [
                'title' => 'Web Development for Beginners',
                'description' => 'A beginner-friendly introduction to HTML, CSS, and JavaScript.',
                'what_you_will_learn' => 'Build static websites, learn responsive design',
                'skills_gain' => 'HTML, CSS, JS fundamentals',
                'assessment_info' => 'Project submission required',
                'duration' => '6 weeks',
                'price' => 79.99,
                'level' => 'Beginner',
            ],
            [
                'title' => 'Laravel Fullstack Development',
                'description' => 'Build dynamic web apps using Laravel and MySQL.',
                'what_you_will_learn' => 'MVC, routing, migrations, blade templates',
                'skills_gain' => 'PHP, Laravel, MySQL',
                'assessment_info' => 'Final exam & practical tasks',
                'duration' => '8 weeks',
                'price' => 129.99,
                'level' => 'Intermediate',
            ],
        ];

        foreach ($courses as $courseData) {
            $course = Course::firstOrCreate(
                ['title' => $courseData['title']], // Avoid duplicates
                array_merge($courseData, [
                    'instructor_id' => $instructor->id,
                ])
            );

            echo "✅ Course added: {$course->title}\n";
        }

        echo "\n🎉 Course seeding completed!\n";

    }
}
