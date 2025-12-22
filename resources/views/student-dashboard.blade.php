<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Student Dashboard') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Track your progress and celebrate your wins</p>
            </div>
            {{-- Status Badge --}}
            <div class="hidden sm:flex items-center gap-2 text-xs font-bold text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 px-3 py-1 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                <svg class="w-3.5 h-3.5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span class="uppercase tracking-wider">{{ Auth::user()->enrollments->count() }} Enrolled</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- 1. Welcome Hero Section --}}
            <div class="bg-gradient-to-br from-teal-600 to-cyan-700 rounded-[2.5rem] p-8 md:p-12 text-white shadow-xl relative overflow-hidden">
                <div class="relative z-10 grid md:grid-cols-2 gap-8 items-center">
                    <div>
                        <h3 class="text-2xl md:text-4xl font-black mb-4">
                            Ready to learn,<br>{{ Auth::user()->name }}? 🎓
                        </h3>
                        <p class="text-teal-50 opacity-90 text-sm md:text-base max-w-md leading-relaxed">
                            "The beautiful thing about learning is that no one can take it away from you." Start where you left off or find a new passion.
                        </p>
                        <div class="mt-8 flex flex-wrap gap-4">
                            <a href="{{ route('courses.index') }}" class="bg-white text-teal-700 px-6 py-3 rounded-xl font-bold text-sm shadow-lg hover:bg-teal-50 transition transform hover:scale-105 active:scale-95">
                                Browse Catalog
                            </a>
                        </div>
                    </div>
                    <div class="hidden md:flex justify-end">
                        <div class="bg-white/10 backdrop-blur-md rounded-3xl p-6 border border-white/20 shadow-2xl">
                             <div class="flex items-center gap-4">
                                 <div class="w-12 h-12 bg-teal-400 rounded-full flex items-center justify-center text-2xl">🏆</div>
                                 <div>
                                     <p class="text-xs uppercase font-black tracking-widest opacity-70">Courses Completed</p>
                                     <p class="text-2xl font-bold">
                                         {{ Auth::user()->enrollments()->whereNotNull('completed_at')->count() }} 
                                     </p>
                                 </div>
                             </div>
                        </div>
                    </div>
                </div>
                {{-- Decorative circles --}}
                <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-cyan-400/20 rounded-full blur-3xl"></div>
            </div>

            <!-- 2. Summary Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Total Enrolled --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-900/30 flex items-center justify-center text-teal-600 dark:text-teal-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Enrolled</p>
                            <p class="text-2xl font-black text-gray-900 dark:text-white">{{ Auth::user()->enrollments->count() }}</p>
                        </div>
                    </div>
                </div>

                {{-- Completed Stats --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-green-50 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Completed</p>
                            <p class="text-2xl font-black text-gray-900 dark:text-white">{{ Auth::user()->enrollments()->whereNotNull('completed_at')->count() }}</p>
                        </div>
                    </div>
                </div>

                {{-- Member Since --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-cyan-50 dark:bg-cyan-900/30 flex items-center justify-center text-cyan-600 dark:text-cyan-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Learning Since</p>
                            <p class="text-lg font-black text-gray-900 dark:text-white">{{ Auth::user()->created_at->format('M Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- LEFT: Ongoing Courses --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-black text-gray-800 dark:text-gray-200">Continue Learning</h3>
                    </div>

                    @php
                        // Filter for only active (non-completed) enrollments
                        $ongoingEnrollments = Auth::user()->enrollments()
                            ->with('course')
                            ->whereNull('completed_at')
                            ->latest()
                            ->take(4)
                            ->get();
                    @endphp

                    @if($ongoingEnrollments->count() > 0)
                        <div class="grid gap-4">
                            @foreach($ongoingEnrollments as $enrollment)
                                <div class="bg-white dark:bg-gray-800 p-4 rounded-3xl border border-gray-100 dark:border-gray-700 flex items-center gap-4 hover:shadow-md transition">
                                    <div class="w-16 h-16 rounded-2xl overflow-hidden flex-shrink-0">
                                        @if($enrollment->course->image)
                                            <img src="{{ asset('storage/' . $enrollment->course->image) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-teal-500 flex items-center justify-center text-white font-bold">
                                                {{ substr($enrollment->course->title, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold text-gray-900 dark:text-white truncate">{{ $enrollment->course->title }}</h4>
                                        <p class="text-xs text-gray-500 mt-1 italic">Started on {{ $enrollment->created_at->format('d M') }}</p>
                                    </div>
                                    <a href="{{ route('courses.student.content', $enrollment->course->id) }}" class="px-4 py-2 bg-gray-50 dark:bg-gray-700 rounded-xl text-xs font-black text-teal-600 uppercase tracking-tighter">
                                        Resume
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-white dark:bg-gray-800 p-12 rounded-[2.5rem] text-center border-2 border-dashed border-gray-200 dark:border-gray-700">
                             <p class="text-gray-500 text-sm">No ongoing courses at the moment.</p>
                             <a href="{{ route('courses.index') }}" class="mt-4 inline-block text-teal-600 font-bold hover:underline">Start a new course</a>
                        </div>
                    @endif
                </div>

                {{-- RIGHT: Completed Achievements --}}
                <div class="space-y-6">
                    <h3 class="text-xl font-black text-gray-800 dark:text-gray-200">Finished Courses</h3>
                    
                    @php
                        // Filter for only completed enrollments
                        $completedEnrollments = Auth::user()->enrollments()
                            ->with('course')
                            ->whereNotNull('completed_at')
                            ->latest('completed_at')
                            ->take(3)
                            ->get();
                    @endphp

                    <div class="space-y-4">
                        @forelse($completedEnrollments as $enrollment)
                            <div class="bg-white dark:bg-gray-800 rounded-3xl p-5 border border-green-50 dark:border-green-900/30 shadow-sm relative overflow-hidden group">
                                <div class="absolute -top-2 -right-2 w-10 h-10 bg-green-500/10 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                </div>
                                
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm leading-tight mb-2 pr-6">{{ $enrollment->course->title }}</h4>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Completed: {{ $enrollment->completed_at->format('d M, Y') }}</p>
                            </div>
                        @empty
                            <div class="bg-gray-50 dark:bg-gray-700/50 p-6 rounded-3xl text-center border border-dashed border-gray-200 dark:border-gray-600">
                                <p class="text-gray-400 text-xs italic">Complete all available quizzes to finish a course!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>