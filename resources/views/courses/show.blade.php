<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- SUCCESS MESSAGE --}}
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            {{-- MAIN CARD --}}
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl p-8 border border-teal-100 dark:border-gray-700">

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                    {{-- LEFT SIDE: IMAGE --}}
                    <div>
                        @if($course->image)
                            <img src="{{ asset('storage/' . $course->image) }}"
                                 alt="{{ $course->title }}"
                                 class="w-full h-72 object-cover rounded-xl shadow-md">
                        @else
                            <div class="w-full h-72 rounded-xl bg-gradient-to-br from-teal-400 via-cyan-400 to-blue-400 flex items-center justify-center shadow-md">
                                <svg class="w-24 h-24 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- RIGHT SIDE: INFO --}}
                    <div>
                        <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                            {{ $course->title }}
                        </h1>

                        {{-- PRICE --}}
                        <div class="mb-4">
                            <span class="text-4xl font-bold bg-gradient-to-r from-teal-600 to-cyan-600 text-transparent bg-clip-text">
                                ${{ number_format($course->price, 2) }}
                            </span>
                        </div>

                        {{-- DESCRIPTION --}}
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed mb-6">
                            {{ $course->description }}
                        </p>

                        {{-- WHAT YOU WILL LEARN --}}
                        @if($course->what_you_will_learn)
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-teal-600 dark:text-teal-400 mb-2">
                                    What You Will Learn
                                </h3>
                                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">
                                    {{ $course->what_you_will_learn }}
                                </p>
                            </div>
                            
                            <!-- Enrollment/Instructor Actions Section -->
                            <div class="mt-6 space-y-4">
                                @auth
                                    @if(auth()->user()->role === 'instructor' && auth()->id() === $course->instructor_id)
                                        <!-- Instructor Actions -->
                                        <div class="bg-teal-50 dark:bg-teal-900/20 border border-teal-200 dark:border-teal-800 px-4 py-3 rounded mb-4">
                                            <p class="font-semibold text-teal-800 dark:text-teal-300 mb-3">This is your course</p>
                                            <div class="flex gap-3">
                                                <a href="{{ route('courses.edit', $course) }}" class="bg-teal-600 hover:bg-teal-700 text-white font-semibold py-2 px-4 rounded transition-colors">
                                                    ✏️ Edit Course
                                                </a>
                                                <form method="POST" action="{{ route('courses.destroy', $course) }}" onsubmit="return confirm('Are you sure you want to delete this course? This action cannot be undone.');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded transition-colors">
                                                        🗑️ Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @elseif(auth()->user()->role === 'student')
                                        <!-- Student Enrollment -->
                                        @if($isEnrolled)
                                            <div class="bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-4">
                                                <p class="font-semibold">✓ You are enrolled in this course!</p>
                                            </div>
                                            <a href="{{ route('enrollments.index') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-6 rounded inline-block">
                                                View My Enrollments
                                            </a>
                                        @else
                                            <form action="{{ route('payments.checkout', $course) }}" method="GET" id="enroll-form-{{ $course->id }}">
                                                <input type="hidden" name="payment_start_time" id="payment_start_time_{{ $course->id }}" value="">
                                                <button type="submit"
                                                   onclick="
                                                       const startTime = Date.now(); // Use absolute timestamp
                                                       document.getElementById('payment_start_time_{{ $course->id }}').value = startTime;
                                                       sessionStorage.setItem('payment-start-time', startTime);
                                                       sessionStorage.setItem('checkout-redirect-time', startTime);
                                                       console.log('%c💳 Payment Process Started', 'font-weight: bold; font-size: 14px; color: #10b981');
                                                       console.log('   Timestamp:', new Date().toISOString());
                                                   "
                                                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded inline-block text-lg w-full text-center">
                                                    🎓 Enroll Now - ${{ number_format($course->price, 2) }}
                                                </button>
                                            </form>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Secure payment via Stripe</p>
                                        @endif
                                    @endif
                                @else
                                    <!-- Guest Enrollment -->
                                    <form action="{{ route('payments.checkout', $course) }}" method="GET" id="enroll-form-guest-{{ $course->id }}">
                                        <input type="hidden" name="payment_start_time" id="payment_start_time_guest_{{ $course->id }}" value="">
                                        <button type="submit"
                                           onclick="
                                               const startTime = Date.now(); // Use absolute timestamp
                                               document.getElementById('payment_start_time_guest_{{ $course->id }}').value = startTime;
                                               sessionStorage.setItem('payment-start-time', startTime);
                                               sessionStorage.setItem('checkout-redirect-time', startTime);
                                               console.log('%c💳 Payment Process Started', 'font-weight: bold; font-size: 14px; color: #10b981');
                                               console.log('   Timestamp:', new Date().toISOString());
                                           "
                                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded inline-block text-lg w-full text-center">
                                            🎓 Enroll Now - ${{ number_format($course->price, 2) }}
                                        </button>
                                    </form>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                        <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Login</a> or 
                                        <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Register</a> to enroll
                                    </p>
                                @endauth
                        @endif

                        {{-- SKILLS GAINED --}}
                        @if($course->skills_gain)
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-cyan-600 dark:text-cyan-400 mb-2">
                                    Skills You Will Gain
                                </h3>
                                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">
                                    {{ $course->skills_gain }}
                                </p>
                            </div>
                        @endif

                        {{-- ACTION BUTTONS --}}
                        <div class="mt-6 space-y-4">

                            @auth
                                {{-- IF INSTRUCTOR OWNS THIS COURSE --}}
                                @if(auth()->user()->role === 'instructor' && auth()->id() === $course->instructor_id)

                                    <div class="bg-teal-50 dark:bg-teal-900/20 border border-teal-200 dark:border-teal-700 px-4 py-4 rounded-xl mb-4">
                                        <p class="font-semibold text-teal-700 dark:text-teal-300 mb-3">
                                            You are the instructor of this course.
                                        </p>

                                        
                                    </div>

                                {{-- IF STUDENT --}}
                                @elseif(auth()->user()->role === 'student')

                                    {{-- ALREADY ENROLLED --}}
                                    @if($isEnrolled)
                                        <div class="bg-green-100 dark:bg-green-900 border border-green-400 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg mb-4">
                                            ✓ You are enrolled in this course.
                                        </div>

                                        <a href="{{ route('enrollments.index') }}"
                                           class="w-full block text-center bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg shadow transition">
                                            Go to My Courses
                                        </a>

                                    {{-- NOT ENROLLED YET --}}
                                    @else
                                        <a href="{{ route('payments.checkout', $course) }}"
                                           class="w-full block text-center bg-gradient-to-r from-teal-600 to-cyan-600 hover:from-teal-700 hover:to-cyan-700 text-white font-bold py-3 rounded-lg shadow-lg transition">
                                            🎓 Enroll Now — ${{ number_format($course->price, 2) }}
                                        </a>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Secure Stripe Payment</p>
                                    @endif

                                @endif

                            @else
                                {{-- GUEST --}}
                                <a href="{{ route('login') }}"
                                   class="w-full block text-center bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 rounded-lg shadow transition">
                                    Login to Enroll
                                </a>
                            @endauth
                        </div>

                    </div> {{-- END RIGHT SIDE --}}
                </div> {{-- END GRID --}}

            </div> {{-- END CARD --}}
        </div>
    </div>
</x-app-layout>
