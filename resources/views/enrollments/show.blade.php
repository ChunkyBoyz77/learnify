<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Enrollment Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Course Information</h3>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                            @if($enrollment->course->image)
                                <img src="{{ asset('storage/' . $enrollment->course->image) }}" alt="{{ $enrollment->course->title }}" class="w-full h-64 object-cover rounded-lg mb-4">
                            @endif
                            <h4 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">{{ $enrollment->course->title }}</h4>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $enrollment->course->description }}</p>
                            <a href="{{ route('courses.show', $enrollment->course) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                View Course →
                            </a>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Enrollment Information</h3>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Status:</span>
                                <span class="px-3 py-1 text-sm font-semibold rounded-full 
                                    {{ $enrollment->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                       ($enrollment->status === 'completed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 
                                       ($enrollment->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                       'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200')) }}">
                                    {{ ucfirst($enrollment->status) }}
                                    @if($enrollment->status === 'cancelled')
                                        (Refunded)
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Enrolled At:</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ $enrollment->enrolled_at->format('M d, Y h:i A') }}</span>
                            </div>
                            @if($enrollment->completed_at)
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Completed At:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $enrollment->completed_at->format('M d, Y h:i A') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($enrollment->payments->count() > 0)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Payment History</h3>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <div class="space-y-4">
                                    @foreach($enrollment->payments as $payment)
                                        <div class="border-b border-gray-300 dark:border-gray-600 pb-4 last:border-0 last:pb-0">
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <p class="text-sm font-mono text-gray-900 dark:text-gray-100">{{ $payment->transaction_id }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $payment->created_at->format('M d, Y') }}</p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($payment->amount, 2) }}</p>
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                        {{ $payment->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                                           ($payment->status === 'failed' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                                           'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200') }}">
                                                        {{ ucfirst($payment->status) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <a href="{{ route('payments.show', $payment) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 text-sm mt-2 inline-block">
                                                View Payment Details →
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($enrollment->status === 'cancelled')
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-red-600 dark:text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <div>
                                    <h4 class="font-semibold text-red-800 dark:text-red-300">Enrollment Cancelled</h4>
                                    <p class="text-sm text-red-600 dark:text-red-400 mt-1">
                                        This enrollment has been cancelled due to a refund. You no longer have access to this course content.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex gap-4">
                        <a href="{{ route('enrollments.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                            Back to Enrollments
                        </a>
                        @if($enrollment->status !== 'cancelled')
                            <a href="{{ route('courses.show', $enrollment->course) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                                Access Course
                            </a>
                        @else
                            <a href="{{ route('courses.show', $enrollment->course) }}" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-6 rounded cursor-not-allowed opacity-50" onclick="return false;" title="Access denied - Enrollment cancelled due to refund">
                                Access Course (Unavailable)
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

