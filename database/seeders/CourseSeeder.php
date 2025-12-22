<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Material;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "===== LMS FULL SEEDER STARTED =====\n\n";

        // 1. Create or Update two demo instructors
        $instructor1 = User::updateOrCreate(
            ['email' => 'sarah.miller@learnify.com'],
            [
                'name' => 'Dr. Sarah Miller',
                'password' => bcrypt('password'),
                'role' => 'instructor',
            ]
        );

        $instructor2 = User::updateOrCreate(
            ['email' => 'alan.turing@learnify.com'],
            [
                'name' => 'Prof. Alan Turing',
                'password' => bcrypt('password'),
                'role' => 'instructor',
            ]
        );

        echo "👨‍🏫 Instructor 1: {$instructor1->email}\n";
        echo "👨‍🏫 Instructor 2: {$instructor2->email}\n\n";

        // 2. Define new demo courses with high-quality real images
        $courses = [
            [
                'instructor_id' => $instructor1->id,
                'title' => 'Cybersecurity Fundamentals',
                'description' => 'Learn security principles and modern threats in the digital age.',
                'image' => 'course_images/cybersecurity_NicoElNino-AlamyStockPhoto.jpg',
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
                'instructor_id' => $instructor2->id,
                'title' => 'Mobile App Development with Flutter',
                'description' => 'Build beautiful cross-platform apps using the Flutter framework.',
                'image' => 'course_images/642e5f92f6147ed845692f97_How-Mobile-App-Testing-Makes-or-Breaks-Mobile-User-Experience.webp',
                'what_you_will_learn' => 'Widgets, State Management, API Integration',
                'skills_gain' => 'Cross-platform development, UI design',
                'assessment_info' => '3 Mini Projects',
                'duration' => '8 weeks',
                'price' => 99.90,
                'level' => 'Intermediate',
                'lessons' => [
                    'Getting Started with Flutter',
                    'Building Beautiful UIs',
                    'Handling User Input',
                    'State Management Patterns',
                    'Publishing to App Stores',
                ],
            ],
            [
                'instructor_id' => $instructor1->id,
                'title' => 'AI & Machine Learning Essentials',
                'description' => 'Deep dive into the world of neural networks and data-driven models.',
                'image' => 'course_images/How-Machine-Learning-and-Artificial-Intelligence-Will-Impact-Global-Industries-in-2020.png',
                'what_you_will_learn' => 'Python, Regression, Neural Networks',
                'skills_gain' => 'Model building, Data cleaning',
                'assessment_info' => 'Final Capstone Project',
                'duration' => '10 weeks',
                'price' => 150.00,
                'level' => 'Advanced',
                'lessons' => [
                    'Introduction to AI',
                    'Linear & Logistic Regression',
                    'Decision Trees & Forests',
                    'Neural Network Basics',
                    'Deploying AI Models',
                ],
            ],
            [
                'instructor_id' => $instructor2->id,
                'title' => 'UI/UX Design for Modern Apps',
                'description' => 'Master the art of user-centric design and high-fidelity prototyping.',
                'image' => 'course_images/images.png',
                'what_you_will_learn' => 'Figma, Wireframing, User Research',
                'skills_gain' => 'Visual design, Prototyping',
                'assessment_info' => 'Design Portfolio Review',
                'duration' => '6 weeks',
                'price' => 85.00,
                'level' => 'Beginner',
                'lessons' => [
                    'Design Thinking Principles',
                    'User Research & Personas',
                    'Wireframing and Sketching',
                    'Prototyping in Figma',
                    'Usability Testing',
                ],
            ],
        ];

        // 3. Process each course and its children
        foreach ($courses as $data) {
            $course = Course::updateOrCreate(
                ['title' => $data['title']],
                [
                    'instructor_id' => $data['instructor_id'],
                    'description' => $data['description'],
                    'image' => $data['image'],
                    'what_you_will_learn' => $data['what_you_will_learn'],
                    'skills_gain' => $data['skills_gain'],
                    'assessment_info' => $data['assessment_info'],
                    'duration' => $data['duration'],
                    'price' => $data['price'],
                    'level' => $data['level'],
                ]
            );

            echo "📘 Course Updated/Created: {$course->title}\n";

            foreach ($data['lessons'] as $index => $lessonTitle) {
                $lesson = Lesson::updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'title' => $lessonTitle
                    ],
                    [
                        'order_number' => $index + 1,
                    ]
                );

                echo "   ➤ Lesson: {$lesson->title}\n";

                Material::updateOrCreate(
                    ['lesson_id' => $lesson->id],
                    [
                        'file_path' => "https://example.com/video-lesson-{$lesson->id}",
                        'file_type' => 'video_url',
                    ]
                );

                $quiz = Quiz::updateOrCreate(
                    ['lesson_id' => $lesson->id],
                    [
                        'title' => "Quiz: {$lesson->title}",
                    ]
                );

                $questions = [
                    [
                        'text' => "What is the primary objective of {$lesson->title}?",
                        'options' => ['Option A', 'Option B', 'Option C'],
                        'correct' => 0
                    ],
                    [
                        'text' => "Which tool is commonly used in {$lesson->title}?",
                        'options' => ['Tool 1', 'Tool 2', 'Tool 3'],
                        'correct' => 1
                    ],
                ];

                foreach ($questions as $q) {
                    QuizQuestion::updateOrCreate(
                        [
                            'quiz_id' => $quiz->id,
                            'question_text' => $q['text']
                        ],
                        [
                            'options' => $q['options'],
                            'correct_option_index' => $q['correct'],
                        ]
                    );
                }
            }
            echo "\n";
        }

        echo "🎉 LMS FULL SEEDING COMPLETED!\n\n";
    }
}