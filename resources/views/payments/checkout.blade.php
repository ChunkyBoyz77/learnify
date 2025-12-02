<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Checkout') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Course Summary -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Course Details</h3>
                            <div class="flex gap-4">
                                @if($course->image)
                                    <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" class="w-32 h-32 object-cover rounded">
                                @else
                                    <div class="w-32 h-32 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">
                                        <span class="text-gray-400 text-sm">No Image</span>
                                    </div>
                                @endif
                                <div>
                                    <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $course->title }}</h4>
                                    <p class="text-gray-600 dark:text-gray-400 mt-2">{{ Str::limit($course->description, 150) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Payment Method</h3>
                            <form id="payment-form">
                                @csrf
                                
                                <div class="space-y-4 mb-6">
                                    @foreach($paymentMethods as $method)
                                        <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 payment-method-option {{ old('payment_method') == $method->code || $method->code == 'stripe' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900' : 'border-gray-300 dark:border-gray-600' }}">
                                            <input type="radio" name="payment_method" value="{{ $method->code }}" class="mr-3" {{ old('payment_method') == $method->code || $method->code == 'stripe' ? 'checked' : '' }} required>
                                            <div>
                                                <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $method->name }}</div>
                                                @if($method->description)
                                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $method->description }}</div>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                @error('payment_method')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror

                                <!-- Stripe Payment Element -->
                                <div id="stripe-payment-element" class="mb-6">
                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Card Details</h4>
                                    <div id="card-element" class="p-4 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700">
                                        <!-- Stripe Elements will create form elements here -->
                                    </div>
                                    <div id="card-errors" class="text-red-500 text-sm mt-2" role="alert"></div>
                                </div>

                                <!-- Other payment methods (for non-Stripe) -->
                                <div id="other-payment-details" class="hidden mb-6">
                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Payment Details</h4>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm">Please follow the instructions for your selected payment method.</p>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes (Optional)</label>
                                    <textarea name="notes" id="payment-notes" rows="3" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300" placeholder="Any additional notes..."></textarea>
                                </div>

                                <button type="submit" id="submit-button" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span id="button-text">Complete Payment</span>
                                    <span id="spinner" class="hidden">Processing...</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg sticky top-4">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Order Summary</h3>
                            <div class="space-y-3 mb-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Course Price:</span>
                                    <span class="text-gray-900 dark:text-gray-100">${{ number_format($course->price, 2) }}</span>
                                </div>
                                <div class="border-t border-gray-300 dark:border-gray-600 pt-3">
                                    <div class="flex justify-between text-lg font-bold">
                                        <span class="text-gray-900 dark:text-gray-100">Total:</span>
                                        <span class="text-blue-600 dark:text-blue-400">${{ number_format($course->price, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stripe = Stripe('{{ config("services.stripe.key") }}');
            let elements;
            let paymentElement;
            let clientSecret = null;
            let paymentId = null;

            const paymentForm = document.getElementById('payment-form');
            const submitButton = document.getElementById('submit-button');
            const buttonText = document.getElementById('button-text');
            const spinner = document.getElementById('spinner');
            const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
            const stripePaymentElement = document.getElementById('stripe-payment-element');
            const otherPaymentDetails = document.getElementById('other-payment-details');

            // Initialize Stripe Elements
            function initializeStripe() {
                if (!clientSecret) {
                    createPaymentIntent();
                    return;
                }

                elements = stripe.elements({ clientSecret });
                paymentElement = elements.create('payment');
                paymentElement.mount('#card-element');

                paymentElement.on('change', function(event) {
                    const displayError = document.getElementById('card-errors');
                    if (event.error) {
                        displayError.textContent = event.error.message;
                    } else {
                        displayError.textContent = '';
                    }
                });
            }

            // Create payment intent
            async function createPaymentIntent() {
                try {
                    const response = await fetch('{{ route("payments.create-intent", $course) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            payment_method: document.querySelector('input[name="payment_method"]:checked').value
                        })
                    });

                    const data = await response.json();
                    if (data.client_secret) {
                        clientSecret = data.client_secret;
                        paymentId = data.payment_id;
                        initializeStripe();
                    } else {
                        alert('Failed to initialize payment. Please try again.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Failed to initialize payment. Please try again.');
                }
            }

            // Handle payment method change
            paymentMethods.forEach(method => {
                method.addEventListener('change', function() {
                    if (this.value === 'stripe' || this.value === 'credit_card') {
                        stripePaymentElement.classList.remove('hidden');
                        otherPaymentDetails.classList.add('hidden');
                        if (!clientSecret) {
                            createPaymentIntent();
                        }
                    } else {
                        stripePaymentElement.classList.add('hidden');
                        otherPaymentDetails.classList.remove('hidden');
                    }
                });
            });

            // Handle form submission
            paymentForm.addEventListener('submit', async function(event) {
                event.preventDefault();

                const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;

                if (selectedMethod === 'stripe' || selectedMethod === 'credit_card') {
                    submitButton.disabled = true;
                    buttonText.classList.add('hidden');
                    spinner.classList.remove('hidden');

                    try {
                        const { error, paymentIntent } = await stripe.confirmPayment({
                            elements,
                            confirmParams: {
                                return_url: '{{ route("payments.success", ":payment_id") }}'.replace(':payment_id', paymentId),
                            },
                            redirect: 'if_required'
                        });

                        if (error) {
                            buttonText.classList.remove('hidden');
                            spinner.classList.add('hidden');
                            submitButton.disabled = false;
                            document.getElementById('card-errors').textContent = error.message;
                        } else if (paymentIntent && paymentIntent.status === 'succeeded') {
                            // Payment succeeded, redirect to success page
                            window.location.href = '{{ route("payments.success", ":payment_id") }}'.replace(':payment_id', paymentId);
                        } else {
                            // Check payment status
                            const confirmResponse = await fetch('{{ route("payments.confirm", $course) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    payment_id: paymentId
                                })
                            });

                            const confirmData = await confirmResponse.json();
                            if (confirmData.success) {
                                window.location.href = confirmData.redirect;
                            } else {
                                buttonText.classList.remove('hidden');
                                spinner.classList.add('hidden');
                                submitButton.disabled = false;
                                alert(confirmData.message || 'Payment is processing. Please wait...');
                            }
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        buttonText.classList.remove('hidden');
                        spinner.classList.add('hidden');
                        submitButton.disabled = false;
                        alert('An error occurred. Please try again.');
                    }
                } else {
                    // Handle other payment methods (legacy)
                    paymentForm.action = '{{ route("payments.process", $course) }}';
                    paymentForm.method = 'POST';
                    paymentForm.submit();
                }
            });

            // Initialize on page load if Stripe is selected
            const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
            if (selectedMethod && (selectedMethod.value === 'stripe' || selectedMethod.value === 'credit_card')) {
                createPaymentIntent();
            } else {
                stripePaymentElement.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>

