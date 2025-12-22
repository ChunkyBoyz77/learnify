<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('My Courses') }}
            </h2>

            <a href="{{ route('courses.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-teal-600 to-cyan-600 
                      hover:from-teal-700 hover:to-cyan-700 text-white font-bold rounded-xl shadow-lg transition-all transform active:scale-95">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Create Course
            </a>
        </div>
    </x-slot>

    @php
        $totalCourses    = $courses->count();
        $activeCourses   = $courses->where('is_archived', false);
        $archivedCourses = $courses->where('is_archived', true);
    @endphp

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

            <!-- ================= STATISTICS ================= -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Total Courses -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 border-l-8 border-teal-500 transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm font-bold uppercase tracking-wider">Total Courses</p>
                            <p class="text-4xl font-black text-gray-800 dark:text-gray-100">{{ $totalCourses }}</p>
                        </div>
                        <div class="p-3 bg-teal-50 dark:bg-teal-900/30 rounded-xl">
                            <svg class="w-8 h-8 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Active -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 border-l-8 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm font-bold uppercase tracking-wider">Active</p>
                            <p class="text-4xl font-black text-green-600 dark:text-green-400">{{ $activeCourses->count() }}</p>
                        </div>
                        <div class="p-3 bg-green-50 dark:bg-green-900/30 rounded-xl">
                            <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Archived -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 border-l-8 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm font-bold uppercase tracking-wider">Archived</p>
                            <p class="text-4xl font-black text-red-600 dark:text-red-400">{{ $archivedCourses->count() }}</p>
                        </div>
                        <div class="p-3 bg-red-50 dark:bg-red-900/30 rounded-xl">
                            <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= ACTIVE COURSES ================= -->
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <h3 class="text-2xl font-black text-gray-800 dark:text-white">Active Courses</h3>
                    <span class="px-3 py-1 bg-teal-100 dark:bg-teal-900 text-teal-700 dark:text-teal-300 text-xs font-bold rounded-full uppercase">Current</span>
                </div>

                @if($activeCourses->count())
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($activeCourses as $course)
                            @php
                                $studentCount = $course->enrollments()
                                    ->whereIn('status', ['active','completed'])
                                    ->count();
                            @endphp

                            <div class="group bg-white dark:bg-gray-800 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-teal-100 dark:border-gray-700 overflow-hidden relative flex flex-col">
                                
                                <!-- IMAGE SECTION -->
                                <div class="relative overflow-hidden h-48">
                                    @if($course->image)
                                        <img src="{{ asset('storage/'.$course->image) }}"
                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-teal-400 via-cyan-400 to-blue-500 flex items-center justify-center">
                                            <svg class="w-16 h-16 text-white opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                        </div>
                                    @endif

                                    <!-- ARCHIVE ACTION OVERLAY -->
                                    <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <form action="{{ route('courses.archive', $course->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Are you sure you want to archive this course?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    title="Archive Course"
                                                    class="bg-red-500 hover:bg-red-600 text-white p-2.5 rounded-xl shadow-xl transition-transform hover:scale-110">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- BODY SECTION -->
                                <div class="p-6 flex-grow">
                                    <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-3 line-clamp-1">
                                        {{ $course->title }}
                                    </h4>

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center text-sm font-medium text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-lg">
                                            <svg class="w-4 h-4 mr-2 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                            </svg>
                                            {{ $studentCount }} Students
                                        </div>
                                        
                                        <a href="{{ route('courses.content', $course->id) }}" 
                                           class="text-teal-600 dark:text-teal-400 font-bold hover:underline flex items-center gap-1 text-sm">
                                            Manage Content
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-800 rounded-3xl p-12 text-center border-2 border-dashed border-gray-200 dark:border-gray-700">
                        <p class="text-gray-500 dark:text-gray-400 font-medium">You haven't created any active courses yet.</p>
                        <a href="{{ route('courses.create') }}" class="inline-block mt-4 text-teal-600 dark:text-teal-400 font-bold hover:underline">Launch your first course &rarr;</a>
                    </div>
                @endif
            </div>

            <!-- ================= ARCHIVED COURSES ================= -->
            <div class="pt-10 border-t border-gray-200 dark:border-gray-800">
                <div class="flex items-center gap-3 mb-6">
                    <h3 class="text-2xl font-black text-gray-500 dark:text-gray-400">Archived Library</h3>
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                </div>

                @if($archivedCourses->count())
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($archivedCourses as $course)
                            <div class="bg-gray-100 dark:bg-gray-800/50 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 flex items-center justify-between">
                                <div>
                                    <h4 class="text-lg font-bold text-gray-600 dark:text-gray-300">
                                        {{ $course->title }}
                                    </h4>
                                    <span class="inline-block mt-1 text-xs font-black uppercase text-red-500 dark:text-red-400 tracking-tighter">
                                        Off-Market
                                    </span>
                                </div>
                                <div class="text-gray-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400 dark:text-gray-600 italic text-sm">Your archived library is currently empty.</p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>