<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl bg-gradient-to-r from-teal-600 to-cyan-600 dark:from-teal-400 dark:to-cyan-400 bg-clip-text text-transparent leading-tight">
                    {{ __('Course Payments') }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">View all payments made by students for your courses</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('payments.refund-requests.index') }}" 
                   style="background: linear-gradient(to right, #ea580c, #dc2626);" 
                   class="inline-flex items-center px-4 py-2 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all hover:opacity-90">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="text-white">Refund Requests</span>
                    @php
                        $pendingRefunds = \App\Models\RefundRequest::whereIn('course_id', Auth::user()->courses()->pluck('id'))
                            ->where('status', 'pending')
                            ->count();
                    @endphp
                    @if($pendingRefunds > 0)
                        <span class="ml-2 px-2 py-0.5 text-xs font-bold bg-white text-orange-600 rounded-full">{{ $pendingRefunds }}</span>
                    @endif
                </a>
                <a href="{{ route('instructor.dashboard') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-teal-100 dark:border-gray-700 mb-6">
                <form method="GET" action="{{ route('instructor.payments.index') }}" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label for="course_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Filter by Course
                        </label>
                        <select name="course_id" id="course_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-teal-500 focus:ring-teal-500">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ $selectedCourse == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-[200px]">
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Filter by Status
                        </label>
                        <select name="status" id="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-teal-500 focus:ring-teal-500">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ $selectedStatus == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $selectedStatus == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="completed" {{ $selectedStatus == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="failed" {{ $selectedStatus == 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="cancelled" {{ $selectedStatus == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="refunded" {{ $selectedStatus == 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                            Filter
                        </button>
                    </div>
                    @if($selectedCourse || $selectedStatus)
                        <div>
                            <a href="{{ route('instructor.payments.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                                Clear
                            </a>
                        </div>
                    @endif
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                @php
                    $totalPayments = $payments->total();
                    $completedPayments = \App\Models\Payment::whereIn('course_id', Auth::user()->courses()->pluck('id'))
                        ->where('status', 'completed')->count();
                    $totalRevenue = \App\Models\Payment::whereIn('course_id', Auth::user()->courses()->pluck('id'))
                        ->where('status', 'completed')->sum('amount');
                    $pendingPayments = \App\Models\Payment::whereIn('course_id', Auth::user()->courses()->pluck('id'))
                        ->where('status', 'pending')->count();
                @endphp
                
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-teal-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Payments</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $totalPayments }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center">
                            <svg class="w-6 h-6 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-teal-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Completed</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $completedPayments }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-teal-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Revenue</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">RM{{ number_format($totalRevenue, 2) }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-teal-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Pending</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $pendingPayments }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payments Table -->
            @if($payments->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-teal-100 dark:border-gray-700">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Transaction ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Student</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Course</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Payment Method</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($payments as $payment)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-gray-100">
                                                {{ $payment->transaction_id }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm">
                                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $payment->user->name }}</div>
                                                    <div class="text-gray-500 dark:text-gray-400">{{ $payment->user->email }}</div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm">
                                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $payment->course->title }}</div>
                                                    <a href="{{ route('instructor.payments.course', $payment->course) }}" class="text-teal-600 hover:text-teal-800 dark:text-teal-400 dark:hover:text-teal-300 text-xs">
                                                        View all payments →
                                                    </a>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                RM{{ number_format($payment->amount, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
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
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $color }}">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $payment->paymentMethod->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                <div>{{ $payment->created_at->format('M d, Y') }}</div>
                                                <div class="text-xs text-gray-400">{{ $payment->created_at->format('h:i A') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex items-center space-x-3">
                                                    <a href="{{ route('payments.show', $payment) }}" class="text-teal-600 hover:text-teal-900 dark:text-teal-400 dark:hover:text-teal-300">
                                                        View
                                                    </a>
                                                    @can('update', $payment)
                                                        <a href="{{ route('payments.edit', $payment) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                            Edit
                                                        </a>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            {{ $payments->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-12 text-center border border-teal-100 dark:border-gray-700">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No payments found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        @if($selectedCourse || $selectedStatus)
                            No payments match your current filters. Try adjusting your filters.
                        @else
                            No payments have been made for your courses yet.
                        @endif
                    </p>
                    @if($selectedCourse || $selectedStatus)
                        <div class="mt-6">
                            <a href="{{ route('instructor.payments.index') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg transition-colors">
                                Clear Filters
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
