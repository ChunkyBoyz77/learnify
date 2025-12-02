<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Payment Pending') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <div class="mb-6">
                        <svg class="mx-auto h-16 w-16 text-yellow-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                        Payment Pending
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Your payment is being processed. You will be notified once the payment is completed.
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
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Status:</span>
                                <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 rounded text-xs font-semibold uppercase">
                                    {{ $payment->status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 justify-center">
                        <a href="{{ route('payments.show', $payment) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                            View Payment Details
                        </a>
                        <a href="{{ route('payments.history') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                            Payment History
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

