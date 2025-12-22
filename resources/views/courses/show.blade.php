<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            {{-- Back Button --}}
            <a href="{{ route('courses.index') }}"
               class="flex items-center gap-2 text-gray-600 dark:text-gray-400
                      hover:text-teal-600 dark:hover:text-teal-400 transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 19l-7-7 7-7"/>
                </svg>
            </a>

            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $course->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- SUCCESS MESSAGE --}}
            @if (session('success'))
                <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-xl shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ================= HERO SECTION ================= --}}
            <div class="relative rounded-3xl overflow-hidden shadow-2xl mb-10 bg-gray-200 dark:bg-gray-800 h-[360px]">
                @if($course->image)
                    <img src="{{ asset('storage/' . $course->image) }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-teal-500 via-cyan-500 to-blue-500
                                flex items-center justify-center">
                        <svg class="w-32 h-32 text-white opacity-20" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                @endif
                
                {{-- Gradient Overlay --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                
                {{-- Floating Badges --}}
                <div class="absolute top-6 left-6 flex gap-3">
                    <span class="px-4 py-1.5 bg-teal-500 text-white text-xs font-black uppercase tracking-widest rounded-full shadow-lg">
                        {{ $course->level ?? 'General' }}
                    </span>
                    <span class="px-4 py-1.5 bg-white/20 backdrop-blur-md text-white text-xs font-bold rounded-full border border-white/30">
                        {{ $course->duration ?? 'Self-Paced' }}
                    </span>
                </div>

                {{-- Hero Content --}}
                <div class="absolute bottom-10 left-10 right-10">
                    <h1 class="text-3xl md:text-5xl font-black text-white mb-4 drop-shadow-lg">
                        {{ $course->title }}
                    </h1>
                    <div class="flex items-center gap-4 text-teal-300">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.394 2.813a1 1 0 00-.788 0l-7 3a1 1 0 000 1.837l7 3a1 1 0 00.788 0l7-3a1 1 0 000-1.837l-7-3z"/>
                                <path d="M14.61 8.905L10.394 10.713a1 1 0 01-.788 0L5.39 8.905 2.14 10.293a1 1 0 000 1.837l7 3a1 1 0 00.788 0l7-3a1 1 0 000-1.837l-3.25-1.388z"/>
                                <path d="M14.61 12.905L10.394 14.713a1 1 0 01-.788 0L5.39 12.905 2.14 14.293a1 1 0 000 1.837l7 3a1 1 0 00.788 0l7-3a1 1 0 000-1.837l-3.25-1.388z"/>
                            </svg>
                            <span class="font-bold">By {{ $course->instructor->name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= MAIN LAYOUT ================= --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-start">

                {{-- ================= LEFT CONTENT ================= --}}
                <div class="lg:col-span-2">

                    {{-- Modern Tabs --}}
                    <div class="bg-white dark:bg-gray-800 p-1.5 rounded-2xl shadow-sm mb-8 flex gap-1.5 border border-gray-100 dark:border-gray-700">
                        <button class="tab-btn flex-1 py-2.5 px-4 rounded-xl font-bold text-sm transition-all duration-200 text-teal-600 bg-teal-50 dark:bg-teal-900/30 shadow-sm"
                                onclick="openTab(event, 'about')">
                            Overview
                        </button>
                        <button class="tab-btn flex-1 py-2.5 px-4 rounded-xl font-bold text-sm transition-all duration-200 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700"
                                onclick="openTab(event, 'module')">
                            Module
                        </button>
                        <button class="tab-btn flex-1 py-2.5 px-4 rounded-xl font-bold text-sm transition-all duration-200 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700"
                                onclick="openTab(event, 'review')">
                            Reviews
                        </button>
                    </div>

                    {{-- ================= ABOUT TAB ================= --}}
                    <div id="about" class="tab-content space-y-6">
                        {{-- About This Course --}}
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 border-l-4 border-teal-500">
                            <h3 class="text-xl font-black text-gray-800 dark:text-white mb-3">About This Course</h3>
                            <p class="text-gray-600 dark:text-gray-400 leading-relaxed text-base">
                                {{ $course->description }}
                            </p>
                        </div>

                        {{-- What You Will Learn --}}
                        @if($course->what_you_will_learn)
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 border-l-4 border-cyan-500">
                                <h3 class="text-xl font-black text-gray-800 dark:text-white mb-3">What You Will Learn</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach(explode("\n", $course->what_you_will_learn) as $benefit)
                                        @if(trim($benefit))
                                            <div class="flex items-start gap-2.5">
                                                <svg class="w-5 h-5 text-teal-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span class="text-gray-600 dark:text-gray-400 text-sm font-medium">{{ trim($benefit) }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Skills Gained --}}
                        @if($course->skills_gain)
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 border-l-4 border-violet-500">
                                <h3 class="text-xl font-black text-gray-800 dark:text-white mb-3">Skills You Will Gain</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(explode(",", $course->skills_gain) as $skill)
                                        <span class="px-3 py-1.5 bg-violet-50 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 rounded-lg text-xs font-bold border border-violet-100 dark:border-violet-800">
                                            {{ trim($skill) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Assessment Info --}}
                        @if($course->assessment_info)
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 border-l-4 border-indigo-500">
                                <h3 class="text-xl font-black text-gray-800 dark:text-white mb-3">Assessment Information</h3>
                                <div class="flex items-start gap-3">
                                    <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl text-indigo-600 flex-shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed font-medium">
                                        {{ $course->assessment_info }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- ================= MODULE TAB ================= --}}
                    <div id="module" class="tab-content hidden">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 border-l-4 border-indigo-500">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-black text-gray-800 dark:text-white">Course Lesson</h3>
                                <span class="px-2.5 py-1 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 text-[10px] font-bold rounded-lg uppercase">
                                    {{ $course->lessons()->count() }} Lessons
                                </span>
                            </div>

                            @php
                                $lessons = $course->lessons()->orderBy('order_number')->get();
                            @endphp

                            @if($lessons->count())
                                <div class="space-y-3">
                                    @foreach($lessons as $lesson)
                                        <div class="flex items-center gap-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 hover:border-teal-500 dark:hover:border-teal-500 transition-colors group">
                                            <div class="w-8 h-8 rounded-lg bg-white dark:bg-gray-800 flex items-center justify-center font-black text-teal-600 shadow-sm border border-gray-100 dark:border-gray-700 text-sm">
                                                {{ $lesson->order_number }}
                                            </div>
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300 group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors">
                                                {{ $lesson->title }}
                                            </span>
                                            <svg class="w-4 h-4 ml-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                    <p class="text-gray-500 text-sm italic">Curriculum is being finalized.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- ================= REVIEW TAB ================= --}}
                    <div id="review" class="tab-content hidden">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 border-l-4 border-amber-500">
                            <h3 class="text-xl font-black text-gray-800 dark:text-white mb-6">Student Success Stories</h3>

                            @if(isset($feedbacks) && $feedbacks->count())
                                <div class="space-y-4">
                                    @foreach($feedbacks as $feedback)
                                        <div class="p-5 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-700">
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-full bg-teal-500 flex items-center justify-center text-white font-black text-sm">
                                                        {{ substr($feedback->user->name, 0, 1) }}
                                                    </div>
                                                    <span class="font-black text-gray-800 dark:text-gray-100 uppercase text-[10px] tracking-tighter">
                                                        {{ $feedback->user->name }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center bg-amber-100 dark:bg-amber-900/50 px-2.5 py-0.5 rounded-lg">
                                                    <span class="text-amber-600 dark:text-amber-400 font-black text-[10px]">⭐ {{ $feedback->rating }}/5</span>
                                                </div>
                                            </div>
                                            <p class="text-gray-600 dark:text-gray-400 italic text-sm">
                                                "{{ $feedback->comment }}"
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-10 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                                    <p class="text-gray-500 text-sm font-medium italic">
                                        Be the first student to review this course!
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ================= RIGHT COURSE CARD ================= --}}
                <div class="sticky top-24">
                    <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl p-6 border border-teal-100 dark:border-gray-700 space-y-5 relative overflow-hidden">
                        {{-- Background Accent --}}
                        <div class="absolute -top-10 -right-10 w-24 h-24 bg-teal-500/10 rounded-full blur-2xl"></div>

                        <div class="text-center">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Enrollment Fee</p>
                            <p class="text-4xl font-black text-gray-900 dark:text-white flex items-center justify-center gap-1">
                                <span class="text-lg text-teal-600">RM</span>{{ number_format($course->price, 2) }}
                            </p>
                        </div>

                        <div class="space-y-4 pt-5 border-t border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400 font-bold uppercase tracking-tighter">Content Duration</span>
                                <span class="text-gray-800 dark:text-gray-200 font-black">{{ $course->duration ?? 'Not Specified' }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400 font-bold uppercase tracking-tighter">Difficulty Level</span>
                                <span class="px-3 py-1 bg-teal-100 dark:bg-teal-900 text-teal-600 dark:text-teal-400 rounded-lg font-black text-xs">
                                    {{ $course->level ?? 'ALL LEVELS' }}
                                </span>
                            </div>
                        </div>

                        {{-- ENROLL BUTTON AREA --}}
                        <div class="pt-4">
                            @auth
                                {{-- Check if user is an Instructor --}}
                                @if(auth()->user()->role === 'instructor')
                                    <div class="w-full text-center p-3 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-xl font-black uppercase text-xs border border-gray-200 dark:border-gray-600">
                                        {{ auth()->id() === $course->instructor_id ? 'You are the Instructor' : 'Instructors cannot enroll' }}
                                    </div>
                                @else
                                    {{-- User is a Student --}}
                                    @if($isEnrolled)
                                        <div class="w-full text-center p-3 bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400 rounded-xl font-black uppercase text-xs border border-green-200 dark:border-green-800 flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            Course Enrolled
                                        </div>
                                    @else
                                        <a href="{{ route('payments.checkout', $course) }}"
                                           class="group relative block w-full text-center py-3.5 bg-gradient-to-br from-teal-600 to-cyan-600 text-white font-black rounded-xl shadow-xl hover:shadow-teal-500/20 transition-all transform active:scale-95 uppercase tracking-widest text-xs overflow-hidden">
                                            <span class="relative z-10">Secure Enrollment</span>
                                            <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                                        </a>
                                    @endif
                                @endif
                            @else
                                {{-- Guest User --}}
                                <a href="{{ route('login') }}"
                                   class="block w-full text-center py-3.5 bg-gray-800 dark:bg-white text-white dark:text-gray-900 font-black rounded-xl shadow-lg hover:opacity-90 transition-all uppercase tracking-widest text-xs">
                                    Login to Enroll
                                </a>
                            @endauth
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- ================= TAB SCRIPT ================= --}}
    <script>
        function openTab(event, tabName) {
            // Hide all contents
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            
            // Reset all buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('text-teal-600', 'bg-teal-50', 'dark:bg-teal-900/30', 'shadow-sm');
                btn.classList.add('text-gray-500', 'dark:text-gray-400');
            });

            // Show current tab
            document.getElementById(tabName).classList.remove('hidden');
            
            // Highlight current button
            event.currentTarget.classList.add('text-teal-600', 'bg-teal-50', 'dark:bg-teal-900/30', 'shadow-sm');
            event.currentTarget.classList.remove('text-gray-500', 'dark:text-gray-400');
        }
    </script>
</x-app-layout>