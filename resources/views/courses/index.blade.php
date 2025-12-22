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
                           value="{{ request('search') }}"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                </form>
            </div>

            @if($courses->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                    @foreach($courses as $course)
                        <div
                            class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg hover:shadow-2xl
                                   transition-all duration-300 rounded-2xl border border-teal-100
                                   dark:border-gray-700 transform hover:-translate-y-2 group">

                            {{-- Course Image --}}
                            <div class="relative overflow-hidden">
                                <a href="{{ route('courses.show', $course) }}">
                                    @if($course->image)
                                        <img src="{{ asset('storage/' . $course->image) }}"
                                             class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-300">
                                    @else
                                        <div
                                            class="w-full h-48 bg-gradient-to-br from-teal-400 via-cyan-400 to-blue-400
                                                   flex items-center justify-center">
                                            <svg class="w-20 h-20 text-white opacity-80" fill="none"
                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5
                                                         S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18
                                                         7.5 18s3.332.477 4.5 1.253m0-13
                                                         C13.168 5.477 14.754 5 16.5 5
                                                         c1.747 0 3.332.477 4.5 1.253v13
                                                         C19.832 18.477 18.247 18 16.5 18
                                                         c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                        </div>
                                    @endif
                                </a>

                                {{-- Price --}}
                                <span
                                    class="absolute top-4 right-4 bg-white/90 dark:bg-gray-900/70
                                           px-3 py-1 rounded-full text-teal-600 dark:text-teal-400
                                           font-semibold shadow-md">
                                    RM {{ number_format($course->price, 2) }}
                                </span>
                            </div>

                            {{-- Course Content --}}
                            <div class="p-6">

                                {{-- Title --}}
                                <a href="{{ route('courses.show', $course) }}">
                                    <h3
                                        class="text-lg font-bold text-gray-900 dark:text-gray-100
                                               group-hover:text-teal-600 dark:group-hover:text-teal-400 transition">
                                        {{ $course->title }}
                                    </h3>
                                </a>

                                {{-- Conditional Rating Section (Calculated from Feedback) --}}
                                <div class="mt-2 min-h-[1.5rem] flex items-center">
                                    @php
                                        // Calculate average from feedback relationship
                                        $avgRating = $course->feedbacks->avg('rating');
                                    @endphp

                                    @if($avgRating)
                                        <div class="flex items-center text-yellow-400">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 fill-current {{ $i <= round($avgRating) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            @endfor
                                            <span class="ml-2 text-xs font-bold text-gray-600 dark:text-gray-400">
                                                {{ number_format($avgRating, 1) }}/5.0
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-xs font-medium text-gray-400 dark:text-gray-500 italic">
                                            {{ __('No rating yet') }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Description --}}
                                <p class="text-gray-600 dark:text-gray-400 mt-2 text-sm line-clamp-2">
                                    {{ $course->description }}
                                </p>

                                {{-- Instructor + Level --}}
                                <div class="mt-4 text-sm text-gray-700 dark:text-gray-300 space-y-1">
                                    <p>
                                        <span class="font-semibold text-teal-600 dark:text-teal-400">Instructor:</span>
                                        {{ $course->instructor->name }}
                                    </p>

                                    <p>
                                        <span class="font-semibold text-teal-600 dark:text-teal-400">Level:</span>
                                        {{ $course->level ?? 'N/A' }}
                                    </p>
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
                    <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                        No Courses Found
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400">
                        Try searching again later.
                    </p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>