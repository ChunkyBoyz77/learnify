/**
 * Payment Performance Monitoring
 * Automatically tracks and logs payment performance metrics
 */

class PaymentPerformanceMonitor {
    constructor() {
        this.marks = {};
        this.measures = {};
        this.apiCalls = [];
        this.init();
    }

    init() {
        // Mark page load start
        this.mark('page-load-start');
        
        // Track when page is fully loaded
        if (document.readyState === 'complete') {
            this.onPageLoad();
        } else {
            window.addEventListener('load', () => this.onPageLoad());
        }

        // Track API calls
        this.trackFetchRequests();
        
        // Track form submissions
        this.trackFormSubmissions();
    }

    mark(name) {
        performance.mark(name);
        this.marks[name] = performance.now();
        console.log(`⏱️  Mark: ${name} at ${this.marks[name].toFixed(2)}ms`);
    }

    measure(name, startMark, endMark) {
        try {
            performance.measure(name, startMark, endMark);
            const measure = performance.getEntriesByName(name)[0];
            this.measures[name] = measure.duration;
            
            // Color code based on performance
            let emoji = '✅';
            let color = 'color: green';
            if (measure.duration > 1000) {
                emoji = '⚠️';
                color = 'color: orange';
            }
            if (measure.duration > 3000) {
                emoji = '❌';
                color = 'color: red';
            }
            
            console.log(
                `%c${emoji} Measure: ${name} = ${measure.duration.toFixed(2)}ms`,
                `font-weight: bold; ${color}`
            );
            return measure.duration;
        } catch (error) {
            console.warn(`Could not measure ${name}:`, error);
            return null;
        }
    }

    onPageLoad() {
        this.mark('page-load-end');
        this.measure('page-load-time', 'page-load-start', 'page-load-end');
        
        // Log page load summary
        const loadTime = this.measures['page-load-time'];
        console.log(
            `%c📊 Page Load Performance`,
            'font-weight: bold; font-size: 14px; color: #3b82f6'
        );
        console.table({
            'Page Load Time': `${loadTime.toFixed(2)}ms`,
            'Status': loadTime < 1000 ? '✅ Good' : loadTime < 3000 ? '⚠️ Fair' : '❌ Slow'
        });
    }

    trackFetchRequests() {
        const originalFetch = window.fetch;
        const self = this;
        
        window.fetch = async function(...args) {
            const url = args[0];
            const startTime = performance.now();
            const markName = `api-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
            
            self.mark(`${markName}-start`);
            
            try {
                const response = await originalFetch.apply(this, args);
                const endTime = performance.now();
                const duration = endTime - startTime;
                
                self.mark(`${markName}-end`);
                self.measure(`${markName}-duration`, `${markName}-start`, `${markName}-end`);
                
                // Track API call
                const apiCall = {
                    url: url.toString(),
                    method: args[1]?.method || 'GET',
                    status: response.status,
                    duration: duration,
                    timestamp: new Date().toISOString()
                };
                
                self.apiCalls.push(apiCall);
                
                // Log API call
                let statusEmoji = '✅';
                if (response.status >= 400) {
                    statusEmoji = '❌';
                } else if (response.status >= 300) {
                    statusEmoji = '⚠️';
                }
                
                console.log(
                    `%c${statusEmoji} API Call: ${apiCall.method} ${url}`,
                    `font-weight: bold; color: ${response.status >= 400 ? 'red' : response.status >= 300 ? 'orange' : 'green'}`
                );
                console.log(`   Status: ${response.status} | Duration: ${duration.toFixed(2)}ms`);
                
                return response;
            } catch (error) {
                const endTime = performance.now();
                const duration = endTime - startTime;
                
                console.error(
                    `%c❌ API Error: ${url}`,
                    'font-weight: bold; color: red'
                );
                console.error(`   Error: ${error.message} | Duration: ${duration.toFixed(2)}ms`);
                
                throw error;
            }
        };
    }

    trackFormSubmissions() {
        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (form.id === 'payment-form' || form.closest('.payment-form')) {
                this.mark('form-submit-start');
                console.log(
                    `%c📝 Form Submission Started`,
                    'font-weight: bold; font-size: 14px; color: #8b5cf6'
                );
            }
        });
    }

    startPaymentFlow() {
        this.mark('payment-flow-start');
        console.log(
            `%c💳 Payment Flow Started`,
            'font-weight: bold; font-size: 14px; color: #10b981'
        );
    }

    endPaymentFlow() {
        this.mark('payment-flow-end');
        const duration = this.measure('payment-flow-duration', 'payment-flow-start', 'payment-flow-end');
        
        console.log(
            `%c💳 Payment Flow Complete`,
            'font-weight: bold; font-size: 14px; color: #10b981'
        );
        console.table({
            'Total Payment Time': `${duration.toFixed(2)}ms`,
            'API Calls': this.apiCalls.length,
            'Status': duration < 3000 ? '✅ Excellent' : duration < 5000 ? '⚠️ Good' : '❌ Slow'
        });
        
        // Log all API calls
        if (this.apiCalls.length > 0) {
            console.log('📡 API Calls Summary:');
            console.table(this.apiCalls);
        }
    }

    trackPaymentIntentCreation() {
        this.mark('payment-intent-start');
    }

    endPaymentIntentCreation() {
        this.mark('payment-intent-end');
        this.measure('payment-intent-duration', 'payment-intent-start', 'payment-intent-end');
    }

    trackStripeConfirmation() {
        this.mark('stripe-confirm-start');
    }

    endStripeConfirmation() {
        this.mark('stripe-confirm-end');
        this.measure('stripe-confirm-duration', 'stripe-confirm-start', 'stripe-confirm-end');
    }

    getSummary() {
        return {
            marks: this.marks,
            measures: this.measures,
            apiCalls: this.apiCalls,
            summary: {
                pageLoad: this.measures['page-load-time'],
                paymentFlow: this.measures['payment-flow-duration'],
                paymentIntent: this.measures['payment-intent-duration'],
                stripeConfirm: this.measures['stripe-confirm-duration'],
                totalApiCalls: this.apiCalls.length,
                averageApiTime: this.apiCalls.length > 0 
                    ? this.apiCalls.reduce((sum, call) => sum + call.duration, 0) / this.apiCalls.length 
                    : 0
            }
        };
    }

    logSummary() {
        const summary = this.getSummary();
        console.log(
            `%c📊 Payment Performance Summary`,
            'font-weight: bold; font-size: 16px; color: #3b82f6; padding: 10px;'
        );
        console.table(summary.summary);
        return summary;
    }
}

// Initialize performance monitor
window.paymentPerformance = new PaymentPerformanceMonitor();

// Make it available globally
window.performanceMonitor = window.paymentPerformance;

