<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            {{-- Back Button --}}
            <a href="{{ route('courses.index') }}"
            class="flex items-center gap-2 text-gray-600 dark:text-gray-300
                    hover:text-teal-600 dark:hover:text-teal-400 transition">

                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 19l-7-7 7-7"/>
                </svg>
            </a>

            {{-- Page Title --}}
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $course->title }}
            </h2>
        </div>
    </x-slot>


    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4">

            {{-- SUCCESS MESSAGE --}}
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ================= HERO IMAGE SECTION ================= --}}
            <div class="relative rounded-3xl overflow-hidden shadow-2xl mb-12">

                @if($course->image)
                    <img src="{{ asset('storage/' . $course->image) }}"
                         class="w-full h-[420px] object-cover">
                @else
                    <div class="w-full h-[420px] bg-gradient-to-br from-teal-500 via-cyan-500 to-blue-500
                                flex items-center justify-center">
                        <svg class="w-32 h-32 text-white opacity-80" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                @endif

                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>

                <div class="absolute bottom-0 left-0 right-0 p-8 text-white">
                    <h1 class="text-4xl font-extrabold mb-2 drop-shadow">
                        {{ $course->title }}
                    </h1>

                    <p class="text-gray-200 max-w-3xl mb-4">
                        {{ Str::limit($course->description, 180) }}
                    </p>

                    {{-- PRICE + ENROLL --}}
                    <div class="flex flex-wrap items-center gap-4 mt-4">

                        <span class="text-3xl font-bold text-teal-300">
                            RM {{ number_format($course->price, 2) }}
                        </span>

                        @auth
                            {{-- OWNER (INSTRUCTOR OF THIS COURSE) --}}
                            @if(auth()->id() === $course->instructor_id)
                                <span class="px-6 py-3 bg-gray-500/80 rounded-lg font-semibold cursor-not-allowed">
                                    You are the instructor
                                </span>

                            {{-- ANY OTHER USER (STUDENT OR INSTRUCTOR) --}}
                            @else
                                @if($isEnrolled)
                                    <span class="px-6 py-3 bg-green-600 rounded-lg font-semibold shadow">
                                        ✓ Enrolled
                                    </span>
                                @else
                                    <a href="{{ route('payments.checkout', $course) }}"
                                       class="px-8 py-3 bg-gradient-to-r from-teal-500 to-cyan-500
                                              hover:from-teal-600 hover:to-cyan-600
                                              rounded-lg font-bold shadow-lg transition transform hover:scale-105">
                                        Enroll Now
                                    </a>
                                @endif
                            @endif
                        @else
                            <a href="{{ route('login') }}"
                               class="px-8 py-3 bg-gray-600 hover:bg-gray-700 rounded-lg font-bold shadow">
                                Login to Enroll
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            {{-- ================= MAIN CONTENT ================= --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

                {{-- LEFT CONTENT --}}
                <div class="lg:col-span-2 space-y-8">

                    {{-- ABOUT --}}
                    <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-700
                                rounded-2xl shadow-xl p-6 border-l-8 border-teal-500">
                        <h3 class="text-xl font-extrabold text-teal-700 dark:text-teal-300 mb-3">
                            📘 About This Course
                        </h3>
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                            {{ $course->description }}
                        </p>
                    </div>

                    {{-- WHAT YOU WILL LEARN --}}
                    @if($course->what_you_will_learn)
                        <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-700
                                    rounded-2xl shadow-xl p-6 border-l-8 border-cyan-500">
                            <h3 class="text-xl font-extrabold text-cyan-700 dark:text-cyan-300 mb-3">
                                🎓 What You Will Learn
                            </h3>
                            <p class="text-gray-700 dark:text-gray-300">
                                {{ $course->what_you_will_learn }}
                            </p>
                        </div>
                    @endif

                    {{-- LESSONS --}}
                    <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-700
                                rounded-2xl shadow-xl p-6 border-l-8 border-indigo-500">
                        <h3 class="text-xl font-extrabold text-indigo-700 dark:text-indigo-300 mb-4">
                            📚 Course Lessons
                        </h3>

                        @php
                            $lessons = $course->lessons()->orderBy('order_number')->get();
                        @endphp

                        @if($lessons->count())
                            <ul class="space-y-3">
                                @foreach($lessons as $lesson)
                                    <li class="p-4 rounded-xl bg-gray-100 dark:bg-gray-700">
                                        <span class="font-semibold text-indigo-600">
                                            Lesson {{ $lesson->order_number }}:
                                        </span>
                                        {{ $lesson->title }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-500 italic">No lessons added yet.</p>
                        @endif
                    </div>

                    {{-- FEEDBACK (DUMMY) --}}
                    <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-700
                                rounded-2xl shadow-xl p-6 border-l-8 border-amber-500">
                        <h3 class="text-xl font-extrabold text-amber-700 dark:text-amber-300 mb-4">
                            ⭐ Student Feedback
                        </h3>

                        <div class="space-y-4">
                            <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-xl">
                                ⭐⭐⭐⭐⭐  
                                <p>Great course! Very easy to understand.</p>
                            </div>
                            <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-xl">
                                ⭐⭐⭐⭐  
                                <p>Helpful content and clear explanation.</p>
                            </div>
                        </div>

                        <p class="mt-4 text-sm italic text-gray-500">
                            *Feedback module will be integrated later.*
                        </p>
                    </div>
                </div>

                {{-- RIGHT SIDEBAR --}}
                <div class="space-y-8">

                    {{-- INSTRUCTOR --}}
                    <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-700
                                rounded-2xl shadow-xl p-6 border-l-8 border-purple-500">
                        <h4 class="text-lg font-extrabold text-purple-700 dark:text-purple-300 mb-2">
                            👨‍🏫 Instructor
                        </h4>
                        <p class="font-semibold">{{ $course->instructor->name }}</p>
                    </div>

                    {{-- COURSE INFO --}}
                    <div class="bg-gradient-to-br from-teal-50 to-cyan-50 dark:from-gray-800 dark:to-gray-700
                                rounded-2xl shadow-xl p-6 border-l-8 border-teal-500">
                        <h4 class="text-lg font-extrabold text-teal-700 dark:text-teal-300 mb-4">
                            📄 Course Information
                        </h4>

                        <p><strong>Level:</strong> {{ $course->level ?? 'N/A' }}</p>
                        <p><strong>Duration:</strong> {{ $course->duration ?? 'N/A' }}</p>
                        <p><strong>Assessment:</strong> {{ $course->assessment_info ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
