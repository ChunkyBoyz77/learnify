<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Payment Successful') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <div class="mb-6">
                        <svg class="mx-auto h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                        Payment Successful!
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Thank you for your payment. You have been successfully enrolled in the course.
                    </p>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6 text-left">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Payment Details</h3>
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
                                <span class="text-gray-600 dark:text-gray-400">Payment Method:</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ $payment->paymentMethod->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Date:</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ $payment->paid_at->format('M d, Y h:i A') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 justify-center">
                        <a href="{{ route('courses.show', $payment->course) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                            View Course
                        </a>
                        <a href="{{ route('enrollments.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                            My Enrollments
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Performance monitoring for success page
        (function() {
            const pageLoadStart = performance.now();
            const paymentStartTime = sessionStorage.getItem('payment-start-time');
            const checkoutRedirectTime = sessionStorage.getItem('checkout-redirect-time');
            
            // Track API calls
            const originalFetch = window.fetch;
            const apiCalls = [];
            
            window.fetch = async function(...args) {
                const url = args[0];
                const startTime = performance.now();
                const method = args[1]?.method || 'GET';
                
                try {
                    const response = await originalFetch.apply(this, args);
                    const duration = performance.now() - startTime;
                    apiCalls.push({ 
                        URL: url.toString().substring(0, 50) + (url.toString().length > 50 ? '...' : ''), 
                        Method: method, 
                        Status: response.status, 
                        'Time (ms)': duration.toFixed(2) 
                    });
                    return response;
                } catch (error) {
                    const duration = performance.now() - startTime;
                    apiCalls.push({ 
                        URL: url.toString().substring(0, 50) + (url.toString().length > 50 ? '...' : ''), 
                        Method: method, 
                        Status: 'ERROR', 
                        'Time (ms)': duration.toFixed(2),
                        Error: error.message 
                    });
                    throw error;
                }
            };
            
            window.addEventListener('load', () => {
                const pageLoadTime = performance.now() - pageLoadStart;
                
                // Build summary object
                const summary = {};
                
                // Page Load Time
                summary['Success Page Load'] = `${pageLoadTime.toFixed(2)}ms`;
                const pageLoadStatus = pageLoadTime < 500 ? '✅' : pageLoadTime < 1000 ? '⚠️' : '❌';
                
                // Checkout Session Creation Time (from server)
                let checkoutCreationTime = null;
                @if(session('checkout-creation-time'))
                    checkoutCreationTime = {{ number_format(session('checkout-creation-time'), 2, '.', '') }};
                    summary['Checkout Session Creation'] = `${checkoutCreationTime}ms`;
                @endif
                const checkoutStatus = checkoutCreationTime !== null ? (checkoutCreationTime < 300 ? '✅' : checkoutCreationTime < 500 ? '⚠️' : '❌') : '';
                
                // Total Payment Time
                // Try to get from sessionStorage first (if available), otherwise use server session
                let totalPaymentTime = null;
                let paymentStartTimeValue = paymentStartTime;
                
                // If sessionStorage has it (didn't get cleared), use it
                if (paymentStartTimeValue) {
                    // paymentStartTimeValue is an absolute timestamp (Date.now())
                    const currentTime = Date.now();
                    totalPaymentTime = currentTime - parseFloat(paymentStartTimeValue);
                    summary['Total Payment Time'] = `${totalPaymentTime.toFixed(2)}ms`;
                } else {
                    // sessionStorage was cleared (happens when redirecting to Stripe)
                    // Use server-side timing instead
                    @if(session('payment-start-time'))
                        // Server has the payment start time (absolute timestamp from client)
                        const serverPaymentStart = {{ number_format(session('payment-start-time'), 2, '.', '') }};
                        const currentTime = Date.now();
                        totalPaymentTime = currentTime - serverPaymentStart;
                        summary['Total Payment Time'] = `${totalPaymentTime.toFixed(2)}ms`;
                    @endif
                }
                
                // Display formatted report
                console.log('%c═══════════════════════════════════════════════════════════', 'color: #3b82f6; font-weight: bold;');
                console.log('%c📊 PAYMENT PERFORMANCE REPORT', 'font-weight: bold; font-size: 16px; color: #3b82f6; background: #f0f9ff; padding: 8px;');
                console.log('%c═══════════════════════════════════════════════════════════', 'color: #3b82f6; font-weight: bold;');
                console.log('');
                
                // Display metrics with status
                console.log(`${pageLoadStatus} Success Page Load: ${pageLoadTime.toFixed(2)}ms`);
                
                if (checkoutCreationTime !== null) {
                    console.log(`${checkoutStatus} Checkout Session Creation: ${checkoutCreationTime}ms`);
                }
                
                if (totalPaymentTime !== null) {
                    const status = totalPaymentTime < 3000 ? '✅' : totalPaymentTime < 5000 ? '⚠️' : '❌';
                    const statusText = totalPaymentTime < 3000 ? 'Excellent' : totalPaymentTime < 5000 ? 'Good' : 'Slow';
                    console.log(`${status} Total Payment Time: ${totalPaymentTime.toFixed(2)}ms (${statusText})`);
                    
                    // Breakdown
                    console.log('');
                    console.log('📋 Time Breakdown:');
                    console.log(`   • From "Enroll Now" click to success page`);
                    if (checkoutCreationTime !== null) {
                        console.log(`   • Checkout session creation: ${checkoutCreationTime}ms`);
                    }
                    if (checkoutRedirectTime && paymentStartTime) {
                        const serverTime = parseFloat(paymentStartTime) - parseFloat(checkoutRedirectTime);
                        if (serverTime > 0) {
                            console.log(`   • Server processing: ~${serverTime.toFixed(2)}ms`);
                        }
                    }
                    if (totalPaymentTime !== null) {
                        let stripeTime = totalPaymentTime - pageLoadTime;
                        if (checkoutCreationTime !== null) {
                            stripeTime = stripeTime - checkoutCreationTime;
                        }
                        if (stripeTime > 0) {
                            console.log(`   • Stripe checkout processing: ~${stripeTime.toFixed(2)}ms`);
                        }
                    }
                } else {
                    console.log('⚠️ Total payment time not available (payment started before tracking)');
                }
                
                // API Calls Summary
                if (apiCalls.length > 0) {
                    console.log('');
                    console.log('📡 API Calls Made:');
                    console.table(apiCalls);
                }
                
                // Summary Table
                console.log('');
                console.log('📋 Performance Summary:');
                console.table(summary);
                
                // Performance tips
                console.log('');
                if (pageLoadTime > 1000) {
                    console.log('%c💡 Tip: Success page load is slow. Consider optimizing images or reducing JavaScript.', 'color: #f59e0b; font-style: italic;');
                }
                if (totalPaymentTime && totalPaymentTime > 5000) {
                    console.log('%c💡 Tip: Total payment time is high. Consider optimizing checkout flow or server response times.', 'color: #f59e0b; font-style: italic;');
                }
                if (pageLoadTime < 500 && (!totalPaymentTime || totalPaymentTime < 3000)) {
                    console.log('%c✅ Excellent performance! All metrics are within optimal ranges.', 'color: #10b981; font-style: italic;');
                }
                
                console.log('%c═══════════════════════════════════════════════════════════', 'color: #3b82f6; font-weight: bold;');
                
                // Clear stored times
                if (paymentStartTime) {
                    sessionStorage.removeItem('payment-start-time');
                    sessionStorage.removeItem('checkout-redirect-time');
                }
            });
        })();
    </script>
</x-app-layout>

