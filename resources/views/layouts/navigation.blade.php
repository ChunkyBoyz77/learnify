<nav x-data="{ open: false }" class="bg-gradient-to-r from-teal-50 to-cyan-50 dark:from-gray-900 dark:to-gray-800 border-b border-teal-200 dark:border-gray-700 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ Auth::check() && Auth::user()->role === 'instructor' ? route('instructor.dashboard')
                        : (Auth::check() && Auth::user()->role === 'student' ? route('student.dashboard') : route('dashboard')) }}"
                        class="flex items-center space-x-3 group">
                        <x-application-logo class="block h-12 w-auto transition-transform group-hover:scale-105" />
                        <span class="hidden md:block text-xl font-bold bg-gradient-to-r from-teal-600 to-cyan-600 dark:from-teal-400 dark:to-cyan-400 bg-clip-text text-transparent">
                            LEARNIFY
                        </span>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <div class="hidden space-x-2 sm:-my-px sm:ms-10 sm:flex items-center">

                    @php
                        $dashboardRoute = Auth::check() && Auth::user()->role === 'instructor'
                            ? route('instructor.dashboard')
                            : (Auth::check() && Auth::user()->role === 'student'
                                ? route('student.dashboard')
                                : route('dashboard'));

                        $dashboardActive = request()->routeIs('dashboard')
                            || request()->routeIs('student.dashboard')
                            || request()->routeIs('instructor.dashboard');

                        $myCourseRoute = auth()->check() && auth()->user()->role === 'instructor'
                            ? route('courses.my')
                            : route('student.mycourses');
                    @endphp

                    <!-- Dashboard -->
                    <x-nav-link :href="$dashboardRoute" :active="$dashboardActive"
                        class="px-4 py-2 rounded-lg transition-all hover:bg-teal-100 dark:hover:bg-gray-700">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 
                                001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <!-- Explore Courses -->
                    <x-nav-link :href="route('courses.index')" :active="request()->routeIs('courses.index')"
                        class="px-4 py-2 rounded-lg transition-all hover:bg-teal-100 dark:hover:bg-gray-700">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477
                                5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 
                                3.332.477 4.5 1.253v13C19.832 18.477 
                                18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        {{ __('Explore Courses') }}
                    </x-nav-link>

                    @auth
                        <!-- My Courses -->
                        <x-nav-link :href="$myCourseRoute"
                            :active="request()->routeIs('courses.my') || request()->routeIs('student.mycourses')"
                            class="px-4 py-2 rounded-lg transition-all hover:bg-teal-100 dark:hover:bg-gray-700">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 
                                    0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 
                                    01.707.293l5.414 5.414a1 1 0 
                                    01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            {{ __('My Courses') }}
                        </x-nav-link>

                        <!-- My Feedbacks -->
                        <x-nav-link :href="route('feedbacks.index')" :active="request()->routeIs('feedbacks.*')"
                            class="px-4 py-2 rounded-lg transition-all hover:bg-teal-100 dark:hover:bg-gray-700">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 
                                    3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            {{ __('My Feedbacks') }}
                        </x-nav-link>

                        <!-- Payments Dropdown (Desktop) -->
                        <x-dropdown align="left" width="56">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-4 py-2 rounded-lg transition-all hover:bg-teal-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 
                                            00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 
                                            0 002-2v-6a2 2 0 00-2-2H9a2 2 
                                            0 00-2 2v6a2 2 0 002 2zm7-5a2 2 
                                            0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span>{{ __('Payments') }}</span>
                                    <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 
                                            10.586l3.293-3.293a1 1 0 
                                            111.414 1.414l-4 4a1 1 0 
                                            01-1.414 0l-4-4a1 1 0 
                                            010-1.414z" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">

                                <!-- FIXED COLORS HERE -->
                                <x-dropdown-link :href="route('payments.history')"
                                    class="text-gray-700 dark:text-gray-300 hover:bg-teal-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white">
                                    {{ __('Payment History') }}
                                </x-dropdown-link>

                                @if(Auth::user()->role === 'instructor')
                                    <x-dropdown-link :href="route('security.metrics.index')"
                                        class="text-gray-700 dark:text-gray-300 hover:bg-teal-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white">
                                        {{ __('Security Metrics') }}
                                    </x-dropdown-link>
                                @endif

                            </x-slot>
                        </x-dropdown>

                    @endauth
                </div>
            </div>

            <!-- Profile Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 
                                        10.586l3.293-3.293a1 1 0 
                                        111.414 1.414l-4 4a1 1 0 
                                        01-1.414 0l-4-4a1 1 0 
                                        010-1.414z" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">

                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>

                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }"
                            class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }"
                            class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">

        <div class="pt-2 pb-3 space-y-1">

            <x-responsive-nav-link :href="$dashboardRoute" :active="$dashboardActive">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('courses.index')" :active="request()->routeIs('courses.index')">
                {{ __('Explore Courses') }}
            </x-responsive-nav-link>

            @auth

                <x-responsive-nav-link :href="$myCourseRoute"
                    :active="request()->routeIs('courses.my') || request()->routeIs('student.mycourses')">
                    {{ __('My Courses') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('feedbacks.index')" :active="request()->routeIs('feedbacks.*')">
                    {{ __('My Feedbacks') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('payments.history')" :active="request()->routeIs('payments.history')">
                    {{ __('Payment History') }}
                </x-responsive-nav-link>

                @if(Auth::user()->role === 'instructor')
                    <x-responsive-nav-link :href="route('security.metrics.index')" :active="request()->routeIs('security.*')">
                        {{ __('Security Metrics') }}
                    </x-responsive-nav-link>
                @endif

            @endauth

        </div>

        <!-- Responsive Settings -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
