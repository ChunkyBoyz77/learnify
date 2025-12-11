<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100 leading-tight">
            My Enrolled Courses
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if($courses->count() === 0)
                <div class="bg-white dark:bg-gray-800 p-10 rounded-xl shadow text-center">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">No Enrollments Yet</h3>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">
                        Enroll in a course to start learning!
                    </p>
                    <a href="{{ route('courses.index') }}"
                       class="mt-4 inline-block bg-teal-600 hover:bg-teal-700 text-white px-6 py-3 rounded-lg">
                        Browse Courses
                    </a>
                </div>
            @else

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                    @foreach($courses as $course)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">

                            {{-- IMAGE --}}
                            @if($course->image)
                                <img src="{{ asset('storage/' . $course->image) }}"
                                     class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gradient-to-br from-teal-400 to-cyan-500"></div>
                            @endif

                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ $course->title }}
                                </h3>

                                <p class="text-gray-600 dark:text-gray-400 mt-2 line-clamp-3">
                                    {{ $course->description }}
                                </p>

                                {{-- ENTER COURSE --}}
                                <a href="{{ route('student.course.content', $course->id) }}"
                                   class="block mt-4 text-center bg-teal-600 hover:bg-teal-700 text-white py-2 rounded-lg">
                                    Enter Course
                                </a>
                            </div>
                        </div>
                    @endforeach

                </div>
            @endif
        </div>
    </div>
</x-app-layout>
