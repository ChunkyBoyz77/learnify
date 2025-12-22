<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Search Results – Learnify</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white min-h-screen">

    {{-- HEADER --}}
    @include('layouts.auth-header')

    {{-- MAIN --}}
    <main class="max-w-7xl mx-auto px-6 py-12">

        {{-- Search heading --}}
        <div class="mb-10">
            <h1 class="text-4xl font-bold text-gray-900">
                Search results
            </h1>

            @if(request('q'))
                <p class="text-gray-600 mt-2">
                    Showing results for
                    <span class="font-semibold text-blue-600">
                        "{{ request('q') }}"
                    </span>
                </p>
            @endif
        </div>

        {{-- RESULTS --}}
        @if($courses->count())

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                @foreach($courses as $course)
                    <div
                        class="bg-white border border-gray-200 rounded-2xl shadow-sm
                               hover:shadow-xl transition transform hover:-translate-y-1">

                        {{-- Image --}}
                        <a href="{{ route('courses.show', $course) }}">
                            @if($course->image)
                                <img
                                    src="{{ asset('storage/' . $course->image) }}"
                                    class="w-full h-48 object-cover rounded-t-2xl">
                            @else
                                <div
                                    class="w-full h-48 rounded-t-2xl
                                           bg-gradient-to-br from-teal-400 via-cyan-400 to-blue-400
                                           flex items-center justify-center">
                                    <i class="fa-solid fa-book-open text-white text-4xl"></i>
                                </div>
                            @endif
                        </a>

                        {{-- Content --}}
                        <div class="p-6">

                            {{-- Title --}}
                            <a href="{{ route('courses.show', $course) }}">
                                <h3 class="text-lg font-bold text-gray-900 hover:text-blue-600 transition">
                                    {{ $course->title }}
                                </h3>
                            </a>

                            {{-- Description --}}
                            <p class="text-gray-600 text-sm mt-2 line-clamp-3">
                                {{ $course->description }}
                            </p>

                            {{-- Instructor --}}
                            <p class="text-sm text-gray-500 mt-4">
                                Instructor:
                                <span class="font-medium text-gray-700">
                                    {{ $course->instructor->name }}
                                </span>
                            </p>

                            {{-- Price --}}
                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-blue-600 font-semibold">
                                    RM {{ number_format($course->price, 2) }}
                                </span>

                                <a href="{{ route('courses.show', $course) }}"
                                   class="text-sm font-medium text-gray-700 hover:text-blue-600 transition">
                                    View course →
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>

            {{-- Pagination --}}
            <div class="mt-10">
                {{ $courses->withQueryString()->links() }}
            </div>

        @else
            {{-- EMPTY STATE --}}
            <div class="text-center py-20">
                <i class="fa-solid fa-magnifying-glass text-gray-300 text-6xl mb-6"></i>

                <h2 class="text-2xl font-bold text-gray-800">
                    No courses found
                </h2>

                <p class="text-gray-500 mt-2">
                    Try searching with different keywords.
                </p>
            </div>
        @endif

    </main>

</body>
</html>
