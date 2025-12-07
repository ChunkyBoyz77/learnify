<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('payments.show', $payment) }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                    ←
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Edit Payment') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Error Message -->
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Payment Info Card -->
            <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-teal-100 dark:border-gray-700">
                <div class="mb-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-yellow-800 dark:text-yellow-200">Data Integrity Notice</p>
                            <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                                For security and audit purposes, critical payment fields (Student, Course, Amount, Transaction ID) are <strong>immutable</strong> and cannot be changed. Only status and notes can be updated.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Transaction ID</p>
                        <p class="font-mono font-semibold text-gray-900 dark:text-gray-100">{{ $payment->transaction_id }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">🔒 Immutable</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Student</p>
                        <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $payment->user->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">🔒 Immutable</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Course</p>
                        <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $payment->course->title }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">🔒 Immutable</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Amount</p>
                        <p class="font-semibold text-gray-900 dark:text-gray-100">RM{{ number_format($payment->amount, 2) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">🔒 Immutable</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <form method="POST" action="{{ route('payments.update', $payment) }}">
                        @csrf
                        @method('PUT')

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
                            <!-- Payment Method (Read-only) -->
                            <div>
                                <x-input-label for="payment_method_display" :value="__('Payment Method')" />
                                <input type="text" id="payment_method_display" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm bg-gray-100" value="{{ $payment->paymentMethod->name ?? 'N/A' }}" readonly disabled>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">🔒 This field cannot be changed</p>
                            </div>

                            <!-- Status -->
                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <select id="status" name="status" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 dark:focus:border-teal-600 focus:ring-teal-500 dark:focus:ring-teal-600 rounded-md shadow-sm" required>
                                    <option value="pending" {{ old('status', $payment->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ old('status', $payment->status) == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="completed" {{ old('status', $payment->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="failed" {{ old('status', $payment->status) == 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="cancelled" {{ old('status', $payment->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="refunded" {{ old('status', $payment->status) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                @if($payment->status === 'completed')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">⚠️ <strong>Restricted:</strong> Completed payments cannot be changed to other statuses. Use 'refunded' status for refunds.</p>
                                @elseif($payment->status === 'refunded')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">⚠️ <strong>Restricted:</strong> Refunded payments cannot be changed.</p>
                                @else
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Status changes are logged for audit purposes.</p>
                                @endif
                            </div>

                            <!-- Paid At -->
                            <div>
                                <x-input-label for="paid_at" :value="__('Paid At')" />
                                <x-text-input id="paid_at" class="block mt-1 w-full" type="datetime-local" name="paid_at" :value="old('paid_at', $payment->paid_at ? $payment->paid_at->format('Y-m-d\TH:i') : '')" />
                                <x-input-error :messages="$errors->get('paid_at')" class="mt-2" />
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Leave blank if payment hasn't been completed</p>
                            </div>

                            <!-- Notes -->
                            <div>
                                <x-input-label for="notes" :value="__('Notes')" />
                                <textarea id="notes" name="notes" rows="4" placeholder="Additional notes about this payment..." class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 dark:focus:border-teal-600 focus:ring-teal-500 dark:focus:ring-teal-600 rounded-md shadow-sm">{{ old('notes', $payment->notes) }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-8 flex items-center justify-between">
                            <form method="POST" action="{{ route('payments.destroy', $payment) }}" onsubmit="return confirm('Are you sure you want to void this payment? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                @if(in_array($payment->status, ['pending', 'failed', 'cancelled']))
                                    <button type="submit" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-md transition-colors">
                                        {{ __('Void Payment') }}
                                    </button>
                                @endif
                            </form>
                            <div class="flex items-center space-x-4">
                                <a href="{{ route('payments.show', $payment) }}" class="px-6 py-2 bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-400 dark:hover:bg-gray-600 transition-colors">
                                    {{ __('Cancel') }}
                                </a>
                                <button type="submit" class="px-8 py-2 text-lg bg-gradient-to-r from-teal-600 to-cyan-600 hover:from-teal-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                                    {{ __('Update Payment') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
