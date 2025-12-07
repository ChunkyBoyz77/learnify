<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('payments.show', $payment) }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                    ←
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Request Refund') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <!-- Payment Info Card -->
            <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-teal-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Payment Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Transaction ID</p>
                        <p class="font-mono font-semibold text-gray-900 dark:text-gray-100">{{ $payment->transaction_id }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Course</p>
                        <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $payment->course->title }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Amount Paid</p>
                        <p class="font-semibold text-gray-900 dark:text-gray-100">RM{{ number_format($payment->amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Payment Date</p>
                        <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $payment->paid_at ? $payment->paid_at->format('M d, Y') : $payment->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
                @if($eligibility['days_remaining'] > 0)
                    <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <p class="text-sm text-blue-800 dark:text-blue-200">
                            <strong>Refund Window:</strong> You have {{ $eligibility['days_remaining'] }} day(s) remaining to request a refund.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Refund Policy Info -->
            <div class="mb-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-200 mb-3">Refund Policy</h3>
                <ul class="space-y-2 text-sm text-yellow-800 dark:text-yellow-300">
                    <li class="flex items-start">
                        <span class="mr-2">✓</span>
                        <span>Refund requests must be made within {{ $eligibility['days_remaining'] > 0 ? $eligibility['days_remaining'] : 30 }} days of payment completion.</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">✓</span>
                        <span>Course must not be completed to be eligible for refund.</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">✓</span>
                        <span>Refund requests are subject to instructor approval.</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">✓</span>
                        <span>Refunds will be processed to your original payment method within 5-10 business days after approval.</span>
                    </li>
                </ul>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <form method="POST" action="{{ route('payments.refund-request.store', $payment) }}">
                        @csrf

                        <!-- Display Validation Errors -->
                        @if ($errors->any())
                            <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                            Please correct the following errors:
                                        </h3>
                                        <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                            <ul class="list-disc list-inside space-y-1">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="space-y-6">
                            <!-- Refund Amount Display -->
                            <div>
                                <x-input-label for="refund_amount_display" :value="__('Refund Amount')" />
                                <div class="mt-1 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                    <p class="text-2xl font-bold text-green-800 dark:text-green-200">RM{{ number_format($refundAmount, 2) }}</p>
                                    <p class="text-sm text-green-700 dark:text-green-300 mt-1">Full refund amount</p>
                                </div>
                            </div>

                            <!-- Refund Reason -->
                            <div>
                                <x-input-label for="reason" :value="__('Reason for Refund')" />
                                <textarea id="reason" name="reason" rows="6" placeholder="Please explain why you are requesting a refund. This will help the instructor understand your situation..." class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 dark:focus:border-teal-600 focus:ring-teal-500 dark:focus:ring-teal-600 rounded-md shadow-sm" required>{{ old('reason') }}</textarea>
                                <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Minimum 10 characters. Please provide a detailed reason for your refund request.</p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-8 flex items-center justify-end space-x-4">
                            <a href="{{ route('payments.show', $payment) }}" class="px-6 py-2 bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-400 dark:hover:bg-gray-600 transition-colors">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="px-8 py-2 text-lg bg-gradient-to-r from-teal-600 to-cyan-600 hover:from-teal-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                                {{ __('Submit Refund Request') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
