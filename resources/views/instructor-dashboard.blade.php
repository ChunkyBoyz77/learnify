<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Instructor Console') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Manage your courses and monitor student success</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- 1. Instructor Hero Section --}}
            <div class="bg-gradient-to-br from-teal-600 to-cyan-700 rounded-[2.5rem] p-8 md:p-12 text-white shadow-xl relative overflow-hidden">
                <div class="relative z-10 grid md:grid-cols-2 gap-8 items-center">
                    <div>
                        <h3 class="text-2xl md:text-4xl font-black mb-4 leading-tight">
                            Inspire your students,<br>{{ Auth::user()->name }} 🚀
                        </h3>
                        <p class="text-teal-50 opacity-90 text-sm md:text-base max-w-md leading-relaxed">
                            Monitor your reach, manage your revenue, and continue building world-class learning experiences for your community.
                        </p>
                        <div class="mt-8 flex flex-wrap gap-4">
                            <a href="{{ route('instructor.payments.index') }}" class="bg-white text-teal-700 px-6 py-3 rounded-2xl font-bold text-sm shadow-lg hover:bg-teal-50 transition transform hover:scale-105 active:scale-95">
                                Revenue Dashboard
                            </a>
                            <a href="{{ route('courses.my') }}" class="bg-teal-800/30 backdrop-blur-sm text-white px-6 py-3 rounded-2xl font-bold text-sm border border-white/20 hover:bg-teal-800/50 transition">
                                My Courses
                            </a>
                        </div>
                    </div>
                    
                    <div class="hidden md:grid grid-cols-2 gap-4">
                        <div class="bg-white/10 backdrop-blur-md rounded-[2rem] p-6 border border-white/20 shadow-2xl">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 rounded-full bg-teal-400/20 flex items-center justify-center text-xl">👥</div>
                                <p class="text-[10px] font-black uppercase tracking-widest opacity-70">Active Students</p>
                            </div>
                            @php
                                $totalStudents = \App\Models\Enrollment::whereIn('course_id', Auth::user()->courses()->pluck('id'))->distinct('user_id')->count();
                            @endphp
                            <p class="text-3xl font-black">{{ number_format($totalStudents) }}</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-md rounded-[2rem] p-6 border border-white/20 shadow-2xl">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 rounded-full bg-cyan-400/20 flex items-center justify-center text-xl">📚</div>
                                <p class="text-[10px] font-black uppercase tracking-widest opacity-70">Total Courses</p>
                            </div>
                            <p class="text-3xl font-black">{{ Auth::user()->courses()->count() }}</p>
                        </div>
                    </div>
                </div>
                {{-- Decorative elements --}}
                <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-cyan-400/20 rounded-full blur-3xl"></div>
            </div>

            {{-- 2. Financial & Growth Metrics --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                @php
                    $totalRevenue = \App\Models\Payment::whereIn('course_id', Auth::user()->courses()->pluck('id'))->where('status', 'completed')->sum('amount');
                    $totalEnrollments = \App\Models\Enrollment::whereIn('course_id', Auth::user()->courses()->pluck('id'))->count();
                @endphp

                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm p-6 border border-gray-100 dark:border-gray-700 hover:shadow-md transition">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-900/20 flex items-center justify-center text-teal-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Revenue</p>
                            <p class="text-2xl font-black text-gray-900 dark:text-white">RM {{ number_format($totalRevenue, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm p-6 border border-gray-100 dark:border-gray-700 hover:shadow-md transition">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Enrollments</p>
                            <p class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format($totalEnrollments) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center text-amber-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Avg. Rating</p>
                            <p class="text-2xl font-black text-gray-900 dark:text-white">4.9<span class="text-sm font-normal text-gray-400">/5</span></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center text-purple-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Last Sale</p>
                            @php
                                $lastSale = \App\Models\Payment::whereIn('course_id', Auth::user()->courses()->pluck('id'))->latest()->first();
                            @endphp
                            <p class="text-lg font-black text-gray-900 dark:text-white">{{ $lastSale ? $lastSale->created_at->format('d M') : 'No sales' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Detailed Course Management --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- LEFT: My Published Courses --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-black text-gray-800 dark:text-gray-200">Active Courses</h3>
                    </div>

                    @php
                        $myCourses = Auth::user()->courses()->withCount('enrollments')->latest()->take(5)->get();
                    @endphp

                    @if($myCourses->count() > 0)
                        <div class="space-y-4">
                            @foreach($myCourses as $course)
                                <div class="bg-white dark:bg-gray-800 p-5 rounded-[2rem] border border-gray-100 dark:border-gray-700 flex items-center gap-5 hover:shadow-xl transition group">
                                    <div class="w-20 h-20 rounded-2xl overflow-hidden flex-shrink-0 bg-gray-50">
                                        @if($course->image)
                                            <img src="{{ asset('storage/' . $course->image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-teal-500 to-indigo-600 flex items-center justify-center text-white text-xl font-black">
                                                {{ substr($course->title, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            @if($course->is_archived)
                                                <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-500 text-[8px] font-black uppercase tracking-widest rounded-md">Archived</span>
                                            @else
                                                <span class="px-2 py-0.5 bg-green-50 dark:bg-green-900/20 text-green-600 text-[8px] font-black uppercase tracking-widest rounded-md">Published</span>
                                            @endif
                                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{ $course->enrollments_count }} Enrolled</span>
                                        </div>
                                        <h4 class="font-black text-gray-900 dark:text-white truncate text-lg leading-tight">{{ $course->title }}</h4>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        {{-- Earnings Button beside the View Button --}}
                                        <a href="{{ route('instructor.payments.course', $course) }}" class="px-4 py-2 bg-teal-50 dark:bg-teal-900/20 text-teal-600 dark:text-teal-400 rounded-xl text-[10px] font-black uppercase tracking-tighter hover:bg-teal-600 hover:text-white transition-all shadow-sm">
                                            Earnings
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-white dark:bg-gray-800 p-12 rounded-[2.5rem] text-center border-2 border-dashed border-gray-100 dark:border-gray-700">
                             <p class="text-gray-500 text-sm">Launch your first course to begin teaching.</p>
                             <a href="{{ route('courses.create') }}" class="mt-4 inline-block text-teal-600 font-bold hover:underline">Create a Course</a>
                        </div>
                    @endif
                </div>

                {{-- RIGHT: Student Activity & Quick Links --}}
                <div class="space-y-8">
                    <div>
                        <h3 class="text-xl font-black text-gray-800 dark:text-gray-200 mb-6">Student Recent Activity</h3>
                        @php
                            $recentEnrollments = \App\Models\Enrollment::whereIn('course_id', Auth::user()->courses()->pluck('id'))
                                ->with(['student', 'course'])
                                ->latest()
                                ->take(4)
                                ->get();
                        @endphp

                        <div class="space-y-4">
                            @forelse($recentEnrollments as $enrollment)
                                <div class="bg-white dark:bg-gray-800 rounded-3xl p-4 border border-gray-100 dark:border-gray-700 shadow-sm flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-teal-50 dark:bg-teal-900/20 flex items-center justify-center font-black text-teal-600 text-[10px] uppercase">
                                        {{ substr($enrollment->student->name, 0, 2) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $enrollment->student->name }}</p>
                                        <p class="text-[10px] text-gray-400 truncate">{{ $enrollment->course->title }}</p>
                                    </div>
                                    <div class="text-[8px] font-black text-teal-600 uppercase tracking-widest opacity-60">
                                        {{ $enrollment->created_at->diffForHumans(null, true) }}
                                    </div>
                                </div>
                            @empty
                                <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl text-center border border-dashed border-gray-100">
                                    <p class="text-gray-400 text-xs italic">Waiting for new enrollments.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>