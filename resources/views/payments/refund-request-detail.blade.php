<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('payments.refund-requests.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                    ←
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Refund Request Details') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Request Info Card -->
            <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-teal-100 dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Request ID</p>
                        <p class="font-mono font-semibold text-gray-900 dark:text-gray-100">#{{ $refundRequest->id }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Requested Amount</p>
                        <p class="text-2xl font-bold text-teal-600 dark:text-teal-400">RM{{ number_format($refundRequest->requested_amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                'approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                'processed' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                            ];
                            $color = $statusColors[$refundRequest->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="inline-block mt-1 px-3 py-1 text-sm font-semibold rounded-full {{ $color }}">
                            {{ ucfirst($refundRequest->status) }}
                        </span>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Requested At</p>
                        <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $refundRequest->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    @if($refundRequest->processed_at)
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Processed At</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $refundRequest->processed_at->format('M d, Y h:i A') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Student & Payment Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-teal-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Student Information</h3>
                    <div class="space-y-2">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Name</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $refundRequest->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Email</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $refundRequest->user->email }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-teal-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Payment Information</h3>
                    <div class="space-y-2">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Transaction ID</p>
                            <p class="font-mono font-semibold text-gray-900 dark:text-gray-100">{{ $refundRequest->payment->transaction_id }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Course</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $refundRequest->course->title }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Amount Paid</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">RM{{ number_format($refundRequest->payment->amount, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Payment Date</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $refundRequest->payment->paid_at ? $refundRequest->payment->paid_at->format('M d, Y') : $refundRequest->payment->created_at->format('M d, Y') }}</p>
                        </div>
                        @if($refundRequest->payment->paid_at)
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Days Since Payment</p>
                                <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $refundRequest->payment->paid_at->diffInDays(now()) }} days</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Student's Refund Reason -->
            <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-teal-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Student's Refund Reason</h3>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $refundRequest->reason }}</p>
                </div>
            </div>

            <!-- Instructor Response (if exists) -->
            @if($refundRequest->instructor_response)
                <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-teal-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Your Response</h3>
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $refundRequest->instructor_response }}</p>
                    </div>
                </div>
            @endif

            <!-- Action Buttons (only if pending) -->
            @if($refundRequest->status === 'pending')
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-teal-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Take Action</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Approve Form -->
                        <div class="border border-green-200 dark:border-green-800 rounded-lg p-4">
                            <h4 class="font-semibold text-green-800 dark:text-green-200 mb-3">Approve Refund</h4>
                            <form method="POST" action="{{ route('payments.refund-requests.approve', $refundRequest) }}" onsubmit="return confirm('Are you sure you want to approve this refund request? The payment will be refunded and enrollment will be cancelled.');">
                                @csrf
                                <div class="mb-3">
                                    <label for="approve_response" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Optional Notes
                                    </label>
                                    <textarea id="approve_response" name="instructor_response" rows="3" placeholder="Optional notes about the approval..." class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-green-500 dark:focus:border-green-600 focus:ring-green-500 dark:focus:ring-green-600 rounded-md shadow-sm"></textarea>
                                </div>
                                <button type="submit" 
                                        style="background-color: #16a34a !important; color: #ffffff !important;" 
                                        class="w-full px-4 py-2 font-semibold rounded-lg transition-all hover:bg-green-700 hover:shadow-lg"
                                        onmouseover="this.style.backgroundColor='#15803d'" 
                                        onmouseout="this.style.backgroundColor='#16a34a'">
                                    <span style="color: #ffffff !important;">Approve Refund</span>
                                </button>
                            </form>
                        </div>

                        <!-- Reject Form -->
                        <div class="border border-red-200 dark:border-red-800 rounded-lg p-4">
                            <h4 class="font-semibold text-red-800 dark:text-red-200 mb-3">Reject Refund</h4>
                            <form method="POST" action="{{ route('payments.refund-requests.reject', $refundRequest) }}" onsubmit="return confirm('Are you sure you want to reject this refund request? Please make sure you provide a valid reason.');">
                                @csrf
                                <div class="mb-3">
                                    <label for="reject_response" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Rejection Reason <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="reject_response" name="instructor_response" rows="3" placeholder="Please explain why this refund request is being rejected..." class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 dark:focus:border-red-600 focus:ring-red-500 dark:focus:ring-red-600 rounded-md shadow-sm" required></textarea>
                                    @error('instructor_response')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors">
                                    Reject Refund
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Back Button -->
            <div class="mt-6">
                <a href="{{ route('payments.refund-requests.index') }}" class="inline-flex items-center px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors">
                    ← Back to Refund Requests
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
