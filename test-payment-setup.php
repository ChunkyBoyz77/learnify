<?php

/**
 * Quick Test Setup Script for Stripe Payments
 * 
 * Run this script to quickly set up test data:
 * php test-payment-setup.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Course;
use App\Models\PaymentMethod;

echo "🚀 Setting up test data for Stripe payments...\n\n";

// Check if Stripe is configured
$stripeKey = config('services.stripe.key');
$stripeSecret = config('services.stripe.secret');

if (!$stripeKey || !$stripeSecret) {
    echo "❌ ERROR: Stripe keys not configured!\n";
    echo "Please add to your .env file:\n";
    echo "STRIPE_KEY=pk_test_...\n";
    echo "STRIPE_SECRET=sk_test_...\n\n";
    exit(1);
}

echo "✅ Stripe keys found\n";

// Create or get test user
$user = User::firstOrCreate(
    ['email' => 'test@learnify.com'],
    [
        'name' => 'Test User',
        'password' => bcrypt('password'),
        'role' => 'learner',
    ]
);
echo "✅ Test user: {$user->email} (password: password)\n";

// Create test course
$course = Course::firstOrCreate(
    ['slug' => 'test-course'],
    [
        'title' => 'Test Course for Payment',
        'description' => 'This is a test course to verify Stripe payment integration.',
        'price' => 99.99,
        'is_active' => true,
        'is_published' => true,
    ]
);
echo "✅ Test course created: {$course->title} (\${$course->price})\n";

// Ensure Stripe payment method exists
$stripeMethod = PaymentMethod::firstOrCreate(
    ['code' => 'stripe'],
    [
        'name' => 'Credit Card (Stripe)',
        'description' => 'Pay securely with your credit or debit card via Stripe',
        'is_active' => true,
    ]
);
echo "✅ Stripe payment method configured\n\n";

echo "📋 Test Information:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Login URL: http://localhost:8000/login\n";
echo "Email: {$user->email}\n";
echo "Password: password\n\n";
echo "Course URL: http://localhost:8000/courses/{$course->slug}\n";
echo "Checkout URL: http://localhost:8000/courses/{$course->id}/checkout\n\n";
echo "Test Card: 4242 4242 4242 4242\n";
echo "Expiry: Any future date (e.g., 12/25)\n";
echo "CVC: Any 3 digits (e.g., 123)\n";
echo "ZIP: Any 5 digits (e.g., 12345)\n\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
echo "✅ Setup complete! You can now test the payment flow.\n";


