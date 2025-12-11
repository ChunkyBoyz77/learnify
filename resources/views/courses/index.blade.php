<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Explore Courses') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Search Box --}}
            <div class="mb-6">
                <form method="GET" action="{{ route('courses.index') }}">
                    <input type="text" name="search"
                        placeholder="Search courses..."
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                </form>
            </div>

            @if($courses->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                    @foreach($courses as $course)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 rounded-2xl border border-teal-100 dark:border-gray-700 transform hover:-translate-y-2 group">

                            <div class="relative overflow-hidden">
                                @if($course->image)
                                    <img src="{{ asset('storage/' . $course->image) }}"
                                        class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-300">
                                @else
                                    <div class="w-full h-48 bg-gradient-to-br from-teal-400 via-cyan-400 to-blue-400 flex items-center justify-center">
                                        <svg class="w-20 h-20 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                @endif

                                <span class="absolute top-4 right-4 bg-white/90 dark:bg-gray-900/70 px-3 py-1 rounded-full text-teal-600 dark:text-teal-400 font-semibold shadow-md">
                                    RM {{ number_format($course->price, 2) }}
                                </span>
                            </div>

                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 group-hover:text-teal-600 dark:group-hover:text-teal-400 transition">
                                    {{ $course->title }}
                                </h3>

                                <p class="text-gray-600 dark:text-gray-400 mt-2 text-sm line-clamp-3">
                                    {{ $course->description }}
                                </p>
                                <div class="flex items-center justify-between gap-3">
                                    @auth
                                        @php
                                            // Only show as enrolled if enrollment status is 'active' or 'completed'
                                            // Cancelled enrollments (after refund) should not show as enrolled
                                            $isEnrolled = auth()->user()->enrollments()
                                                ->where('course_id', $course->id)
                                                ->whereIn('status', ['active', 'completed'])
                                                ->exists();
                                        @endphp
                                        @if($isEnrolled)
                                            <span class="flex-1 inline-flex items-center justify-center bg-gradient-to-r from-green-500 to-emerald-500 text-white font-semibold py-3 px-4 rounded-lg text-sm shadow-md">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Enrolled
                                            </span>
                                        @else
                                            <a href="{{ route('payments.checkout', $course) }}" class="flex-1 inline-flex items-center justify-center bg-gradient-to-r from-teal-600 to-cyan-600 hover:from-teal-700 hover:to-cyan-700 text-white font-semibold py-3 px-4 rounded-lg text-sm shadow-md hover:shadow-lg transition-all transform hover:scale-105">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                                Enroll Now 
                                            </a>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" class="flex-1 inline-flex items-center justify-center bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-4 rounded-lg text-sm shadow-md hover:shadow-lg transition-all">
                                            Login to Enroll
                                        </a>
                                    @endauth
                                    <a href="{{ route('courses.show', $course) }}" class="inline-flex items-center justify-center bg-white dark:bg-gray-700 text-teal-600 dark:text-teal-400 font-semibold py-3 px-4 rounded-lg border-2 border-teal-200 dark:border-teal-700 hover:border-teal-300 dark:hover:border-teal-600 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>

                                <div class="mt-4 flex justify-between items-center">
                                    {{-- View Details --}}
                                    <a href="{{ route('courses.show', $course) }}"
                                        class="px-4 py-2 rounded-lg border border-teal-300 dark:border-teal-700 text-teal-600 dark:text-teal-400 font-semibold hover:bg-teal-100/50">
                                        View Detail
                                    </a>

                                    {{-- Enroll Button (role independent) --}}
                                    <a href="{{ route('payments.checkout', $course) }}"
                                        class="px-4 py-2 rounded-lg bg-gradient-to-r from-teal-600 to-cyan-600 text-white font-semibold hover:from-teal-700 hover:to-cyan-700">
                                        Enroll
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>

                <div class="mt-6">
                    {{ $courses->links() }}
                </div>

            @else
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl p-10 text-center">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">No Courses Found</h3>
                    <p class="text-gray-500 dark:text-gray-400">Try searching again later.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
