<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Payment Failed') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <div class="mb-6">
                        <svg class="mx-auto h-16 w-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                        Payment Failed
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Unfortunately, your payment could not be processed. Please try again or contact support if the problem persists.
                    </p>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6 text-left">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Transaction Details</h3>
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
                        </div>
                    </div>

                    <div class="flex gap-4 justify-center">
                        <a href="{{ route('payments.checkout', $payment->course) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                            Try Again
                        </a>
                        <a href="{{ route('courses.show', $payment->course) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                            Back to Course
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

