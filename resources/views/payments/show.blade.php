<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Payment Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Payment Information</h3>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Transaction ID:</span>
                                <span class="text-gray-900 dark:text-gray-100 font-mono font-semibold">{{ $payment->transaction_id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Status:</span>
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                        'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                        'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                        'failed' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                        'cancelled' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                        'refunded' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                    ];
                                    $color = $statusColors[$payment->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $color }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Amount:</span>
                                <span class="text-gray-900 dark:text-gray-100 font-semibold text-lg">${{ number_format($payment->amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Payment Method:</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ $payment->paymentMethod->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Payment Type:</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ ucfirst($payment->payment_type) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Created At:</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ $payment->created_at->format('M d, Y h:i A') }}</span>
                            </div>
                            @if($payment->paid_at)
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Paid At:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $payment->paid_at->format('M d, Y h:i A') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Course Information</h3>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                            <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">{{ $payment->course->title }}</h4>
                            <p class="text-gray-600 dark:text-gray-400">{{ Str::limit($payment->course->description, 200) }}</p>
                            <a href="{{ route('courses.show', $payment->course) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mt-4 inline-block">
                                View Course →
                            </a>
                        </div>
                    </div>

                    @if($payment->enrollment)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Enrollment Information</h3>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <div class="flex justify-between mb-2">
                                    <span class="text-gray-600 dark:text-gray-400">Enrollment Status:</span>
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        {{ ucfirst($payment->enrollment->status) }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Enrolled At:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $payment->enrollment->enrolled_at->format('M d, Y h:i A') }}</span>
                                </div>
                                <a href="{{ route('enrollments.show', $payment->enrollment) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mt-4 inline-block">
                                    View Enrollment →
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($payment->notes)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Notes</h3>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $payment->notes }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="flex gap-4">
                        <a href="{{ route('payments.history') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                            Back to History
                        </a>
                        @if($payment->status === 'completed' && $payment->enrollment)
                            <a href="{{ route('courses.show', $payment->course) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                                Access Course
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

