<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('My Courses') }}
            </h2>

            {{-- Create Course Button --}}
            <a href="{{ route('courses.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-teal-600 to-cyan-600 
                      hover:from-teal-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg 
                      hover:shadow-xl transition-all">

                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Create Course
            </a>
        </div>
    </x-slot>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if($courses->count() > 0)

                {{-- Grid of Courses --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                    @foreach($courses as $course)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden rounded-2xl shadow-lg 
                                    border border-teal-100 dark:border-gray-700 
                                    hover:shadow-2xl transition-all duration-300 group">

                            {{-- IMAGE --}}
                            <div class="relative">
                                @if($course->image)
                                    <img src="{{ asset('storage/' . $course->image) }}"
                                         class="w-full h-48 object-cover rounded-t-2xl group-hover:scale-105 transition duration-300">
                                @else
                                    <div class="w-full h-48 bg-gradient-to-br from-teal-400 via-cyan-400 to-blue-500 
                                                flex items-center justify-center rounded-t-2xl">
                                        <svg class="w-20 h-20 text-white opacity-80" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                    </div>
                                @endif

                                {{-- Price Badge --}}
                                <span class="absolute top-4 right-4 bg-white/80 dark:bg-gray-900/80 
                                             text-teal-600 dark:text-teal-400 font-bold py-1 px-3 rounded-full 
                                             shadow backdrop-blur">
                                    ${{ number_format($course->price, 2) }}
                                </span>

                                @if($course->is_archived)
                                    <span class="absolute top-4 left-4 bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-full">
                                        Archived
                                    </span>
                                @endif
                            </div>


                            {{-- BODY --}}
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2 
                                           group-hover:text-teal-600 dark:group-hover:text-teal-400 transition">
                                    {{ $course->title }}
                                </h3>

                                <p class="text-gray-600 dark:text-gray-400 mb-4 text-sm line-clamp-3">
                                    {{ Str::limit($course->description, 120) }}
                                </p>

                                {{-- VIEW CONTENT BUTTON --}}
                                <a href="{{ route('courses.content', $course->id) }}"
                                   class="block w-full text-center bg-gradient-to-r from-teal-600 to-cyan-600 
                                          hover:from-teal-700 hover:to-cyan-700 text-white font-semibold py-2 
                                          rounded-lg shadow-md hover:shadow-lg transition">
                                    View Course Content
                                </a>

                                {{-- ARCHIVE BUTTON --}}
                                @if(!$course->is_archived)
                                <form action="{{ route('courses.archive', $course->id) }}" method="POST"
                                      class="mt-3"
                                      onsubmit="return confirm('Archive this course? Students will still have access, but you can no longer edit it.')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                                        Archive
                                    </button>
                                </form>
                                @endif

                            </div>
                        </div>

                    @endforeach

                </div>

            @else
                {{-- EMPTY STATE --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border 
                            border-teal-100 dark:border-gray-700">
                    <div class="p-12 text-center">
                        <div class="w-20 h-20 mx-auto bg-teal-100 dark:bg-teal-900/30 rounded-full 
                                    flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                            You haven't created any courses yet
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Start by creating your first course.
                        </p>
                        <a href="{{ route('courses.create') }}"
                           class="inline-block bg-gradient-to-r from-teal-600 to-cyan-600 
                                  hover:from-teal-700 hover:to-cyan-700 text-white font-semibold 
                                  px-6 py-3 rounded-lg shadow transition">
                            Create Course
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>

</x-app-layout>
