<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl bg-gradient-to-r from-teal-600 to-cyan-600 dark:from-teal-400 dark:to-cyan-400 bg-clip-text text-transparent leading-tight">
                    {{ __('Welcome to Your Dashboard') }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Start your learning journey today</p>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-3">
                                {{ __("You're logged in!") }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-6">
                                Explore our wide range of courses and start learning today. Your journey to knowledge begins here.
                            </p>
                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('courses.index') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-teal-600 to-cyan-600 hover:from-teal-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    Browse Courses
                                </a>
                                @auth
                                    <a href="{{ route('enrollments.index') }}" class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-700 text-teal-600 dark:text-teal-400 font-semibold rounded-lg border-2 border-teal-200 dark:border-teal-700 hover:border-teal-300 dark:hover:border-teal-600 transition-all duration-200">
                                        My Enrollments
                                    </a>
                                @endauth
                            </div>
                        </div>
                        <div class="hidden md:block">
                            <!-- Learning Illustration -->
                            <div class="relative">
                                <svg class="w-full h-auto max-w-md" viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <!-- Background Circle -->
                                    <circle cx="200" cy="150" r="120" fill="url(#gradient1)" opacity="0.1"/>
                                    <!-- Books Stack -->
                                    <g transform="translate(150, 80)">
                                        <!-- Book 1 -->
                                        <rect x="0" y="0" width="60" height="80" rx="4" fill="#2D7A7A" stroke="#1A5A5A" stroke-width="2"/>
                                        <rect x="5" y="5" width="50" height="70" fill="#3A9A9A"/>
                                        <!-- Book 2 -->
                                        <rect x="10" y="-10" width="60" height="80" rx="4" fill="#2D7A7A" stroke="#1A5A5A" stroke-width="2"/>
                                        <rect x="15" y="-5" width="50" height="70" fill="#3A9A9A"/>
                                        <!-- Book 3 -->
                                        <rect x="20" y="-20" width="60" height="80" rx="4" fill="#2D7A7A" stroke="#1A5A5A" stroke-width="2"/>
                                        <rect x="25" y="-15" width="50" height="70" fill="#3A9A9A"/>
                                    </g>
                                    <!-- Light Bulb -->
                                    <g transform="translate(280, 100)">
                                        <circle cx="0" cy="0" r="25" fill="#D4AF37" opacity="0.3"/>
                                        <path d="M-15,-20 L15,-20 L10,-35 L-10,-35 Z" fill="#D4AF37"/>
                                        <circle cx="0" cy="-5" r="8" fill="#F5E6D3"/>
                                    </g>
                                    <!-- Graduation Cap -->
                                    <g transform="translate(120, 200)">
                                        <path d="M0,0 L40,0 L35,15 L5,15 Z" fill="#2D7A7A"/>
                                        <circle cx="20" cy="0" r="15" fill="#D4AF37" opacity="0.5"/>
                                    </g>
                                    <defs>
                                        <linearGradient id="gradient1" x1="0%" y1="0%" x2="100%" y2="100%">
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-teal-100 dark:border-gray-700 hover:shadow-xl transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Courses</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">0</p>
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
                            <p class="text-sm text-gray-600 dark:text-gray-400">My Enrollments</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">0</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center">
                            <svg class="w-6 h-6 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-teal-100 dark:border-gray-700 hover:shadow-xl transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Progress</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">0%</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
