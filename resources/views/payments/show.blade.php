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
                                <span class="text-gray-900 dark:text-gray-100 font-semibold text-lg">RM{{ number_format($payment->amount, 2) }}</span>
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

                    @if(Auth::user()->role === 'instructor' && $payment->user)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Student Information</h3>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <div class="flex justify-between mb-2">
                                    <span class="text-gray-600 dark:text-gray-400">Student Name:</span>
                                    <span class="text-gray-900 dark:text-gray-100 font-semibold">{{ $payment->user->name }}</span>
                                </div>
                                <div class="flex justify-between mb-2">
                                    <span class="text-gray-600 dark:text-gray-400">Student Email:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $payment->user->email }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($payment->enrollment)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Enrollment Information</h3>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <div class="flex justify-between mb-2">
                                    <span class="text-gray-600 dark:text-gray-400">Enrollment Status:</span>
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full 
                                        {{ $payment->enrollment->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                           ($payment->enrollment->status === 'completed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 
                                           ($payment->enrollment->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                           'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200')) }}">
                                        {{ ucfirst($payment->enrollment->status) }}
                                        @if($payment->enrollment->status === 'cancelled')
                                            (Refunded)
                                        @endif
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Enrolled At:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $payment->enrollment->enrolled_at ? $payment->enrollment->enrolled_at->format('M d, Y h:i A') : 'N/A' }}</span>
                                </div>
                                @if($payment->enrollment->status === 'cancelled')
                                    <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded text-sm text-red-700 dark:text-red-300">
                                        ⚠️ This enrollment has been cancelled due to a refund. Course access is no longer available.
                                    </div>
                                @elseif(Auth::user()->role !== 'instructor')
                                    <a href="{{ route('enrollments.show', $payment->enrollment) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mt-4 inline-block">
                                        View Enrollment →
                                    </a>
                                @endif
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

                    <!-- Refund Request Status (for students) -->
                    @if(Auth::user()->role === 'student' && $payment->user_id === Auth::id() && $existingRefundRequest)
                        <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-teal-100 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Refund Request Status</h3>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                @php
                                    $refundStatusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                        'approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                        'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                        'processed' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                    ];
                                    $refundColor = $refundStatusColors[$existingRefundRequest->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-gray-600 dark:text-gray-400">Status:</span>
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $refundColor }}">
                                        {{ ucfirst($existingRefundRequest->status) }}
                                    </span>
                                </div>
                                <div class="mb-2">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Your Reason:</p>
                                    <p class="text-gray-700 dark:text-gray-300">{{ $existingRefundRequest->reason }}</p>
                                </div>
                                @if($existingRefundRequest->instructor_response)
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Instructor Response:</p>
                                        <p class="text-gray-700 dark:text-gray-300">{{ $existingRefundRequest->instructor_response }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="flex gap-4 justify-between">
                        <div class="flex gap-4">
                            @if(Auth::user()->role === 'instructor')
                                @if($payment->course->instructor_id === Auth::id())
                                    <a href="{{ route('instructor.payments.course', $payment->course) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                                        Back to Course Payments
                                    </a>
                                    <a href="{{ route('instructor.payments.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                                        All Payments
                                    </a>
                                @else
                                    <a href="{{ route('instructor.payments.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                                        Back to Payments
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('payments.history') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                                    Back to History
                                </a>
                                @if($payment->status === 'completed' && $payment->enrollment)
                                    <a href="{{ route('courses.show', $payment->course) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                                        Access Course
                                    </a>
                                @endif
                            @endif
                        </div>
                        @if(Auth::user()->role === 'student' && $payment->user_id === Auth::id() && $payment->status === 'completed')
                            <div class="flex gap-4">
                                @if($existingRefundRequest)
                                    @if($existingRefundRequest->status === 'pending')
                                        <span class="bg-yellow-500 text-white font-bold py-2 px-6 rounded cursor-not-allowed opacity-75">
                                            Refund Request Pending
                                        </span>
                                    @elseif($existingRefundRequest->status === 'approved')
                                        <span class="bg-green-500 text-white font-bold py-2 px-6 rounded">
                                            Refund Approved
                                        </span>
                                    @elseif($existingRefundRequest->status === 'rejected')
                                        <span class="bg-red-500 text-white font-bold py-2 px-6 rounded cursor-not-allowed opacity-75">
                                            Refund Rejected
                                        </span>
                                    @endif
                                @else
                                    @if($refundEligibility && $refundEligibility['eligible'])
                                        <a href="{{ route('payments.refund-request.create', $payment) }}" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-6 rounded">
                                            Request Refund
                                        </a>
                                    @else
                                        <span class="bg-gray-400 text-white font-bold py-2 px-6 rounded cursor-not-allowed opacity-75" title="{{ $refundEligibility && !empty($refundEligibility['reasons']) ? implode(' ', $refundEligibility['reasons']) : 'Refund not available' }}">
                                            Refund Not Available
                                        </span>
                                    @endif
                                @endif
                            </div>
                        @endif
                    </div>
                    @if(Auth::user()->role === 'instructor' && $payment->course->instructor_id === Auth::id())
                        <div class="mt-4 flex gap-4 justify-end">
                            @can('update', $payment)
                                <a href="{{ route('payments.edit', $payment) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                                    Edit Payment
                                </a>
                            @endcan
                            @can('delete', $payment)
                                <form method="POST" action="{{ route('payments.destroy', $payment) }}" onsubmit="return confirm('Are you sure you want to void this payment? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-6 rounded">
                                        Void Payment
                                    </button>
                                </form>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

