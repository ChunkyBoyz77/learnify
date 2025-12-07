<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl bg-gradient-to-r from-teal-600 to-cyan-600 dark:from-teal-400 dark:to-cyan-400 bg-clip-text text-transparent leading-tight">
                    {{ __('Instructor Dashboard') }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Welcome back, {{ Auth::user()->name }}! Manage your courses and students</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Card with Illustration -->
            <div class="bg-gradient-to-br from-white to-teal-50 dark:from-gray-800 dark:to-gray-800/50 overflow-hidden shadow-xl sm:rounded-2xl border border-teal-100 dark:border-gray-700 mb-8">
                <div class="p-8 md:p-12">
                    <div class="grid md:grid-cols-2 gap-8 items-center">
                        <div>
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-teal-500 to-cyan-500 mb-4">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-3">
                                {{ __("Welcome to Your Instructor Dashboard!") }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-6">
                                Create and manage your courses, track student progress, and build your teaching community.
                            </p>
                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('courses.index') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-teal-600 to-cyan-600 hover:from-teal-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Create Course
                                </a>
                                <a href="{{ route('courses.index') }}" class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-700 text-teal-600 dark:text-teal-400 font-semibold rounded-lg border-2 border-teal-200 dark:border-teal-700 hover:border-teal-300 dark:hover:border-teal-600 transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    My Courses
                                </a>
                            </div>
                        </div>
                        <div class="hidden md:block">
                            <!-- Instructor Illustration -->
                            <div class="relative">
                                <svg class="w-full h-auto max-w-md" viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <!-- Background Circle -->
                                    <circle cx="200" cy="150" r="120" fill="url(#gradient2)" opacity="0.1"/>
                                    <!-- Presentation Board -->
                                    <g transform="translate(100, 60)">
                                        <rect x="0" y="0" width="200" height="150" rx="8" fill="#2D7A7A" stroke="#1A5A5A" stroke-width="3"/>
                                        <line x1="20" y1="30" x2="180" y2="30" stroke="#D4AF37" stroke-width="2"/>
                                        <line x1="20" y1="60" x2="160" y2="60" stroke="#D4AF37" stroke-width="2"/>
                                        <line x1="20" y1="90" x2="140" y2="90" stroke="#D4AF37" stroke-width="2"/>
                                    </g>
                                    <!-- Teacher Figure -->
                                    <g transform="translate(270, 140)">
                                        <circle cx="0" cy="0" r="25" fill="#3A9A9A"/>
                                        <rect x="-15" y="25" width="30" height="50" rx="15" fill="#2D7A7A"/>
                                        <circle cx="0" cy="10" r="8" fill="#F5E6D3"/>
                                    </g>
                                    <!-- Students Icons -->
                                    <g transform="translate(50, 200)">
                                        <circle cx="0" cy="0" r="15" fill="#3A9A9A" opacity="0.7"/>
                                    </g>
                                    <g transform="translate(100, 210)">
                                        <circle cx="0" cy="0" r="15" fill="#3A9A9A" opacity="0.7"/>
                                    </g>
                                    <g transform="translate(150, 205)">
                                        <circle cx="0" cy="0" r="15" fill="#3A9A9A" opacity="0.7"/>
                                    </g>
                                    <defs>
                                        <linearGradient id="gradient2" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" style="stop-color:#2D7A7A;stop-opacity:1" />
                                            <stop offset="100%" style="stop-color:#3A9A9A;stop-opacity:1" />
                                        </linearGradient>
                                    </defs>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-teal-100 dark:border-gray-700 hover:shadow-xl transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Courses</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ Auth::user()->courses()->count() }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center">
                            <svg class="w-6 h-6 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-teal-100 dark:border-gray-700 hover:shadow-xl transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Published Courses</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ Auth::user()->courses()->where('is_published', true)->count() }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center">
                            <svg class="w-6 h-6 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-teal-100 dark:border-gray-700 hover:shadow-xl transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Students</p>
                            @php
                                $totalStudents = \App\Models\Enrollment::whereIn('course_id', Auth::user()->courses()->pluck('id'))->distinct('user_id')->count();
                            @endphp
                            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $totalStudents }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-teal-100 dark:border-gray-700 hover:shadow-xl transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Enrollments</p>
                            @php
                                $totalEnrollments = \App\Models\Enrollment::whereIn('course_id', Auth::user()->courses()->pluck('id'))->count();
                            @endphp
                            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $totalEnrollments }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Courses Section -->
            <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-teal-100 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">My Courses</h3>
                @php
                    $myCourses = Auth::user()->courses()->latest()->take(5)->get();
                @endphp
                @if($myCourses->count() > 0)
                    <div class="space-y-4">
                        @foreach($myCourses as $course)
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 rounded-lg bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ $course->title }}</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Status: 
                                            <span class="capitalize">
                                                @if($course->is_published)
                                                    <span class="text-green-600 dark:text-green-400">Published</span>
                                                @else
                                                    <span class="text-yellow-600 dark:text-yellow-400">Draft</span>
                                                @endif
                                            </span>
                                            • {{ $course->enrollment_count ?? 0 }} enrollments
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ route('courses.show', $course) }}" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors">
                                    View
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-600 dark:text-gray-400 text-center py-8">No courses yet. <a href="{{ route('courses.index') }}" class="text-teal-600 hover:text-teal-700 font-semibold">Create your first course</a> to get started!</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

