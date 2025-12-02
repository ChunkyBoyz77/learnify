<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            [
                'name' => 'Credit Card (Stripe)',
                'code' => 'stripe',
                'description' => 'Pay securely with your credit or debit card via Stripe',
                'is_active' => true,
            ],
            [
                'name' => 'Credit Card',
                'code' => 'credit_card',
                'description' => 'Pay securely with your credit or debit card (legacy)',
                'is_active' => false, // Disabled in favor of Stripe
            ],
            [
                'name' => 'PayPal',
                'code' => 'paypal',
                'description' => 'Pay with your PayPal account',
                'is_active' => false, // Can be enabled when PayPal integration is added
            ],
            [
                'name' => 'Bank Transfer',
                'code' => 'bank_transfer',
                'description' => 'Direct bank transfer (manual verification required)',
                'is_active' => true,
            ],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::updateOrCreate(
                ['code' => $method['code']],
                $method
            );
        }
    }
}
