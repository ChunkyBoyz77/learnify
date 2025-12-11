<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Material;
use App\Models\Quiz;
use App\Models\QuizQuestion;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        echo "===== LMS FULL SEEDER STARTED =====\n\n";

        // ---------------------------------------------------------
        // 1. Create demo instructor
        // ---------------------------------------------------------
        $instructor = User::firstOrCreate(
            ['email' => 'instructor@learnify.com'],
            [
                'name' => 'Demo Instructor',
                'password' => bcrypt('password'),
                'role' => 'instructor',
            ]
        );

        echo "👨‍🏫 Instructor: {$instructor->email} (password: password)\n\n";

        // ---------------------------------------------------------
        // 2. Define demo courses and lessons
        // ---------------------------------------------------------
        $courses = [

            [
                'title' => 'Web Development Bootcamp (HTML, CSS & JavaScript)',
                'description' => 'Beginner-friendly course to learn how to build websites.',
                'what_you_will_learn' => 'HTML, CSS, JS, DOM',
                'skills_gain' => 'Front-end basics',
                'assessment_info' => 'Quizzes + Projects',
                'duration' => '8 weeks',
                'price' => 79.90,
                'level' => 'Beginner',
                'lessons' => [
                    'Introduction to Web Development',
                    'HTML Fundamentals',
                    'CSS Layout & Styling',
                    'JavaScript Basics',
                    'DOM Manipulation',
                ],
            ],

            [
                'title' => 'Java Programming Essentials',
                'description' => 'Learn core Java & OOP concepts.',
                'what_you_will_learn' => 'OOP, Classes, File I/O',
                'skills_gain' => 'Debugging, Logic building',
                'assessment_info' => 'Quizzes + Final Project',
                'duration' => '6 weeks',
                'price' => 89.99,
                'level' => 'Intermediate',
                'lessons' => [
                    'Getting Started With Java',
                    'Variables & Operators',
                    'OOP Concepts',
                    'Inheritance & Polymorphism',
                    'Java File Handling',
                ],
            ],

            [
                'title' => 'Cybersecurity Fundamentals',
                'description' => 'Learn security principles and modern threats.',
                'what_you_will_learn' => 'Security, Malware, Networks',
                'skills_gain' => 'Risk analysis, threat detection',
                'assessment_info' => 'Labs + Hands-on Test',
                'duration' => '5 weeks',
                'price' => 120.00,
                'level' => 'Beginner',
                'lessons' => [
                    'Introduction to Cybersecurity',
                    'Understanding Malware',
                    'Network Security',
                    'Web Security Basics',
                    'Pen Testing Basics',
                ],
            ],

            [
                'title' => 'Artificial Intelligence & Machine Learning Basics',
                'description' => 'Learn AI/ML workflow & algorithms.',
                'what_you_will_learn' => 'Regression, Classification',
                'skills_gain' => 'Data analysis & model evaluation',
                'assessment_info' => 'Mini ML projects',
                'duration' => '7 weeks',
                'price' => 149.00,
                'level' => 'Beginner',
                'lessons' => [
                    'Intro to AI & ML',
                    'Data Preparation',
                    'Linear Regression',
                    'Classification Models',
                    'Model Evaluation',
                ],
            ],

            [
                'title' => 'Mobile App Development with Flutter',
                'description' => 'Build beautiful cross-platform apps.',
                'what_you_will_learn' => 'Widgets, State, APIs',
                'skills_gain' => 'UI development, mobile app design',
                'assessment_info' => '3 Mini Projects',
                'duration' => '8 weeks',
                'price' => 99.90,
                'level' => 'Intermediate',
                'lessons' => [
                    'Intro to Flutter',
                    'Widgets & Layouts',
                    'State Management',
                    'Navigation & Routing',
                    'API Integration',
                ],
            ],

        ];

        // ---------------------------------------------------------
        // 3. Create courses → lessons → materials → quizzes
        // ---------------------------------------------------------
        foreach ($courses as $data) {

            $course = Course::create([
                'instructor_id' => $instructor->id,
                'title' => $data['title'],
                'description' => $data['description'],
                'what_you_will_learn' => $data['what_you_will_learn'],
                'skills_gain' => $data['skills_gain'],
                'assessment_info' => $data['assessment_info'],
                'duration' => $data['duration'],
                'price' => $data['price'],
                'level' => $data['level'],
            ]);

            echo "📘 Course created: {$course->title}\n";

            foreach ($data['lessons'] as $index => $lessonTitle) {

                $lesson = Lesson::create([
                    'course_id' => $course->id,
                    'title' => $lessonTitle,
                    'order_number' => $index + 1,
                ]);

                echo "   ➤ Lesson: {$lesson->title}\n";

                // ---------------------------------------------------------
                // Insert demo material
                // ---------------------------------------------------------
                Material::create([
                    'lesson_id' => $lesson->id,
                    'file_path' => "https://example.com/video{$lesson->id}",
                    'file_type' => 'video_url',
                ]);

                echo "       📎 Material added\n";

                // ---------------------------------------------------------
                // Create quiz
                // ---------------------------------------------------------
                $quiz = Quiz::create([
                    'lesson_id' => $lesson->id,
                    'title' => "Quiz for {$lesson->title}",
                ]);

                echo "       📝 Quiz created\n";

                // ---------------------------------------------------------
                // Add 3 questions
                // ---------------------------------------------------------
                QuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => "What is the main concept of {$lesson->title}?",
                    'options' => ['Concept A', 'Concept B', 'Concept C'],
                    'correct_option_index' => 0,
                ]);

                QuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => "Why is {$lesson->title} important?",
                    'options' => ['Reason 1', 'Reason 2', 'Reason 3'],
                    'correct_option_index' => 1,
                ]);

                QuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => "Which statement is true about {$lesson->title}?",
                    'options' => ['Statement 1', 'Statement 2', 'Statement 3'],
                    'correct_option_index' => 2,
                ]);

                echo "       ❓ 3 Questions added\n";
            }

            echo "\n";
        }

        echo "🎉 LMS FULL SEEDING COMPLETED!\n\n";
    }
}
