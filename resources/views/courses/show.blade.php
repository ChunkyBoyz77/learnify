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

                        {{-- NEW: ASSESSMENT INFO --}}
                        @if($course->assessment_info)
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-purple-600 dark:text-purple-400 mb-2">
                                    Assessment Information
                                </h3>
                                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">
                                    {{ $course->assessment_info }}
                                </p>
                            </div>
                        @endif

                        {{-- NEW: DURATION --}}
                        @if($course->duration)
                            <div class="mb-4">
                                <h3 class="text-md font-semibold text-indigo-600 dark:text-indigo-400 mb-1">
                                    Duration
                                </h3>
                                <p class="text-gray-700 dark:text-gray-300">
                                    {{ $course->duration }}
                                </p>
                            </div>
                        @endif

                        {{-- NEW: LEVEL --}}
                        @if($course->level)
                            <div class="mb-8">
                                <h3 class="text-md font-semibold text-amber-600 dark:text-amber-400 mb-1">
                                    Difficulty Level
                                </h3>
                                <span class="px-3 py-1 bg-amber-100 dark:bg-amber-700 text-amber-700 dark:text-amber-200 rounded-full text-sm font-semibold">
                                    {{ $course->level }}
                                </span>
                            </div>
                        @endif

                        {{-- NEW: LESSON LIST --}}
                        <div class="mt-8">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-200 mb-3">
                                Course Lessons
                            </h3>

                            @php
                                $lessons = $course->lessons()->orderBy('order_number')->get();
                            @endphp

                            @if($lessons->count() > 0)
                                <ul class="space-y-2">
                                    @foreach($lessons as $lesson)
                                        <li class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg border border-gray-300 dark:border-gray-600">
                                            <span class="font-semibold text-teal-700 dark:text-teal-300">
                                                Lesson {{ $lesson->order_number }}:
                                            </span>
                                            <span class="text-gray-800 dark:text-gray-200">
                                                {{ $lesson->title }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-gray-500 dark:text-gray-400">No lessons added yet.</p>
                            @endif
                        </div>

                        {{-- ACTION BUTTONS --}}
                        <div class="mt-6 space-y-4">

                            @auth

                                {{-- INSTRUCTOR --}}
                                @if(auth()->user()->role === 'instructor' && auth()->id() === $course->instructor_id)
                                    <div class="bg-teal-50 dark:bg-teal-900/20 border border-teal-200 dark:border-teal-700 px-4 py-4 rounded-xl mb-4">
                                        <p class="font-semibold text-teal-700 dark:text-teal-300">
                                            You are the instructor of this course.
                                        </p>
                                    </div>

                                {{-- STUDENT --}}
                                @elseif(auth()->user()->role === 'student')

                                    @if($isEnrolled)
                                        <div class="bg-green-100 dark:bg-green-900 border border-green-400 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg mb-4">
                                            ✓ You are enrolled in this course.
                                        </div>

                                        <a href="{{ route('enrollments.index') }}"
                                           class="w-full block text-center bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg shadow transition">
                                            Go to My Courses
                                        </a>

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

                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
