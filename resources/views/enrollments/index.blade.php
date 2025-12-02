<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Enrollments') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($enrollments->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($enrollments as $enrollment)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            @if($enrollment->course->image)
                                <img src="{{ asset('storage/' . $enrollment->course->image) }}" alt="{{ $enrollment->course->title }}" class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                    <span class="text-gray-400">No Image</span>
                                </div>
                            @endif
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                    {{ $enrollment->course->title }}
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-2">
                                    {{ Str::limit($enrollment->course->description, 100) }}
                                </p>
                                <div class="flex items-center justify-between mb-4">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                        {{ $enrollment->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                           ($enrollment->status === 'completed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 
                                           'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200') }}">
                                        {{ ucfirst($enrollment->status) }}
                                    </span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $enrollment->enrolled_at->format('M d, Y') }}
                                    </span>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('courses.show', $enrollment->course) }}" class="flex-1 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center text-sm">
                                        View Course
                                    </a>
                                    <a href="{{ route('enrollments.show', $enrollment) }}" class="flex-1 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-center text-sm">
                                        Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $enrollments->links() }}
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-900 dark:text-gray-100">
                        <p class="mb-4">You haven't enrolled in any courses yet.</p>
                        <a href="{{ route('courses.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded inline-block">
                            Browse Courses
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

