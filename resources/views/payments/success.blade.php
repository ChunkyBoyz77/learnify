<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Payment Successful') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <div class="mb-6">
                        <svg class="mx-auto h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                        Payment Successful!
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Thank you for your payment. You have been successfully enrolled in the course.
                    </p>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6 text-left">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Payment Details</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Transaction ID:</span>
                                <span class="text-gray-900 dark:text-gray-100 font-mono">{{ $payment->transaction_id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Course:</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ $payment->course->title }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Amount:</span>
                                <span class="text-gray-900 dark:text-gray-100 font-semibold">${{ number_format($payment->amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Payment Method:</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ $payment->paymentMethod->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Date:</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ $payment->paid_at->format('M d, Y h:i A') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 justify-center">
                        <a href="{{ route('courses.show', $payment->course) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                            View Course
                        </a>
                        <a href="{{ route('enrollments.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                            My Enrollments
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

