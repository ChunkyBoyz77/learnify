<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                My Courses
            </h2>

            <a href="{{ route('courses.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-teal-600 to-cyan-600 
                      hover:from-teal-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
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

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

            {{-- ================= STATISTICS ================= --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border-l-8 border-teal-500">
                    <p class="text-gray-500 text-sm">Total Courses</p>
                    <p class="text-3xl font-bold">{{ $totalCourses }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border-l-8 border-green-500">
                    <p class="text-gray-500 text-sm">Active Courses</p>
                    <p class="text-3xl font-bold text-green-600">
                        {{ $activeCourses->count() }}
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border-l-8 border-red-500">
                    <p class="text-gray-500 text-sm">Archived Courses</p>
                    <p class="text-3xl font-bold text-red-600">
                        {{ $archivedCourses->count() }}
                    </p>
                </div>
            </div>

            {{-- ================= ACTIVE COURSES ================= --}}
            <div>
                <h3 class="text-2xl font-bold mb-6">Active Courses</h3>

                @if($activeCourses->count())
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($activeCourses as $course)

                            @php
                                $studentCount = $course->enrollments()
                                    ->whereIn('status', ['active','completed'])
                                    ->count();
                            @endphp

                            <a href="{{ route('courses.content', $course->id) }}"
                               class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg 
                                      hover:shadow-2xl transition-all border border-teal-100 
                                      overflow-hidden relative">

                                {{-- IMAGE --}}
                                <div class="relative">
                                    @if($course->image)
                                        <img src="{{ asset('storage/'.$course->image) }}"
                                             class="w-full h-48 object-cover group-hover:scale-105 transition">
                                    @else
                                        <div class="w-full h-48 bg-gradient-to-br from-teal-400 via-cyan-400 to-blue-500
                                                    flex items-center justify-center">
                                            <svg class="w-16 h-16 text-white opacity-80" fill="none"
                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                        </div>
                                    @endif

                                    {{-- ARCHIVE ICON --}}
                                    <form action="{{ route('courses.archive', $course->id) }}"
                                          method="POST"
                                          class="absolute top-3 right-3 z-10"
                                          onclick="event.stopPropagation();"
                                          onsubmit="return confirm('Archive this course?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-full shadow">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>

                                {{-- BODY --}}
                                <div class="p-6">
                                    <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">
                                        {{ $course->title }}
                                    </h4>

                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M17 20h5v-2a4 4 0 00-5-4M9 20H4v-2a4 4 0 015-4m8-4a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        {{ $studentCount }} students enrolled
                                    </div>
                                </div>
                            </a>

                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 italic">No active courses.</p>
                @endif
            </div>

            {{-- ================= ARCHIVED COURSES ================= --}}
            <div>
                <h3 class="text-2xl font-bold mb-6">Archived Courses</h3>

                @if($archivedCourses->count())
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($archivedCourses as $course)
                            <div class="bg-gray-100 dark:bg-gray-700 rounded-2xl shadow p-6">
                                <h4 class="text-lg font-bold text-gray-800 dark:text-gray-100">
                                    {{ $course->title }}
                                </h4>
                                <span class="inline-block mt-3 px-3 py-1 bg-red-600 text-white text-xs rounded-full">
                                    Archived
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 italic">No archived courses.</p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
