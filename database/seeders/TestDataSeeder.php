<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test user
        $user = User::firstOrCreate(
            ['email' => 'test@learnify.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'role' => 'learner',
            ]
        );

        echo "✅ Test user created: {$user->email} (password: password)\n";

        // Create test course
        $course = Course::firstOrCreate(
            ['slug' => 'test-course'],
            [
                'title' => 'Test Course for Payment',
                'description' => 'This is a test course to verify Stripe payment integration. You can use this course to test the payment flow.',
                'price' => 99.99,
                'is_active' => true,
                'is_published' => true,
            ]
        );

        echo "✅ Test course created: {$course->title} (\${$course->price})\n";
        echo "\n📋 Test Information:\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "Login URL: http://localhost:8000/login\n";
        echo "Email: {$user->email}\n";
        echo "Password: password\n\n";
        echo "Course URL: http://localhost:8000/courses/{$course->slug}\n";
        echo "Checkout URL: http://localhost:8000/courses/{$course->id}/checkout\n\n";
        echo "Test Card: 4242 4242 4242 4242\n";
        echo "Expiry: Any future date (e.g., 12/25)\n";
        echo "CVC: Any 3 digits (e.g., 123)\n";
        echo "ZIP: Any 5 digits (e.g., 12345)\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    }
}
