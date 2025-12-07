<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('instructor.payments.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                    ←
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Create Manual Payment') }}
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

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <form method="POST" action="{{ route('payments.store') }}">
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
                            <!-- Student Selection -->
                            <div>
                                <x-input-label for="user_id" :value="__('Student')" />
                                <select id="user_id" name="user_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 dark:focus:border-teal-600 focus:ring-teal-500 dark:focus:ring-teal-600 rounded-md shadow-sm" required>
                                    <option value="">Select a student</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ old('user_id') == $student->id ? 'selected' : '' }}>
                                            {{ $student->name }} ({{ $student->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                            </div>

                            <!-- Course Selection -->
                            <div>
                                <x-input-label for="course_id" :value="__('Course')" />
                                <select id="course_id" name="course_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 dark:focus:border-teal-600 focus:ring-teal-500 dark:focus:ring-teal-600 rounded-md shadow-sm" required>
                                    <option value="">Select a course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ (old('course_id') == $course->id || ($selectedCourse && $selectedCourse->id == $course->id)) ? 'selected' : '' }}>
                                            {{ $course->title }} - RM{{ number_format($course->price, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('course_id')" class="mt-2" />
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" id="course-price"></p>
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <x-input-label for="payment_method_id" :value="__('Payment Method')" />
                                <select id="payment_method_id" name="payment_method_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 dark:focus:border-teal-600 focus:ring-teal-500 dark:focus:ring-teal-600 rounded-md shadow-sm" required>
                                    <option value="">Select payment method</option>
                                    @foreach($paymentMethods as $method)
                                        <option value="{{ $method->id }}" {{ old('payment_method_id') == $method->id ? 'selected' : '' }}>
                                            {{ $method->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('payment_method_id')" class="mt-2" />
                            </div>

                            <!-- Amount -->
                            <div>
                                <x-input-label for="amount" :value="__('Amount (RM)')" />
                                <x-text-input id="amount" class="block mt-1 w-full" type="number" name="amount" step="0.01" min="0.01" placeholder="0.00" :value="old('amount')" required />
                                <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter the payment amount</p>
                            </div>

                            <!-- Status -->
                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <select id="status" name="status" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 dark:focus:border-teal-600 focus:ring-teal-500 dark:focus:ring-teal-600 rounded-md shadow-sm" required>
                                    <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ old('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="failed" {{ old('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="refunded" {{ old('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <!-- Payment Type -->
                            <div>
                                <x-input-label for="payment_type" :value="__('Payment Type')" />
                                <select id="payment_type" name="payment_type" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 dark:focus:border-teal-600 focus:ring-teal-500 dark:focus:ring-teal-600 rounded-md shadow-sm" required>
                                    <option value="enrollment" {{ old('payment_type', 'enrollment') == 'enrollment' ? 'selected' : '' }}>Enrollment</option>
                                    <option value="subscription" {{ old('payment_type') == 'subscription' ? 'selected' : '' }}>Subscription</option>
                                    <option value="other" {{ old('payment_type') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                <x-input-error :messages="$errors->get('payment_type')" class="mt-2" />
                            </div>

                            <!-- Transaction ID (Optional) -->
                            <div>
                                <x-input-label for="transaction_id" :value="__('Transaction ID (Optional)')" />
                                <x-text-input id="transaction_id" class="block mt-1 w-full" type="text" name="transaction_id" placeholder="Leave blank to auto-generate" :value="old('transaction_id')" />
                                <x-input-error :messages="$errors->get('transaction_id')" class="mt-2" />
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Leave blank to auto-generate a transaction ID</p>
                            </div>

                            <!-- Paid At -->
                            <div>
                                <x-input-label for="paid_at" :value="__('Paid At (Optional)')" />
                                <x-text-input id="paid_at" class="block mt-1 w-full" type="datetime-local" name="paid_at" :value="old('paid_at')" />
                                <x-input-error :messages="$errors->get('paid_at')" class="mt-2" />
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Leave blank to use current time if status is completed</p>
                            </div>

                            <!-- Notes -->
                            <div>
                                <x-input-label for="notes" :value="__('Notes (Optional)')" />
                                <textarea id="notes" name="notes" rows="4" placeholder="Additional notes about this payment..." class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 dark:focus:border-teal-600 focus:ring-teal-500 dark:focus:ring-teal-600 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-8 flex items-center justify-end space-x-4">
                            <a href="{{ route('instructor.payments.index') }}" class="px-6 py-2 bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-400 dark:hover:bg-gray-600 transition-colors">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="px-8 py-2 text-lg bg-gradient-to-r from-teal-600 to-cyan-600 hover:from-teal-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                                {{ __('Create Payment') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const courseSelect = document.getElementById('course_id');
            const amountInput = document.getElementById('amount');
            const coursePriceDisplay = document.getElementById('course-price');
            
            // Store course prices
            const coursePrices = {
                @foreach($courses as $course)
                    {{ $course->id }}: {{ $course->price }},
                @endforeach
            };

            // Update amount when course is selected
            courseSelect.addEventListener('change', function() {
                const courseId = this.value;
                if (courseId && coursePrices[courseId]) {
                    const price = coursePrices[courseId];
                    amountInput.value = price.toFixed(2);
                    coursePriceDisplay.textContent = `Course price: RM${price.toFixed(2)}`;
                } else {
                    amountInput.value = '';
                    coursePriceDisplay.textContent = '';
                }
            });

            // Set initial value if course is pre-selected
            if (courseSelect.value && coursePrices[courseSelect.value]) {
                const price = coursePrices[courseSelect.value];
                if (!amountInput.value) {
                    amountInput.value = price.toFixed(2);
                }
                coursePriceDisplay.textContent = `Course price: RM${price.toFixed(2)}`;
            }
        });
    </script>
</x-app-layout>
