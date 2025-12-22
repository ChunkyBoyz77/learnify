<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                Feedback
            </h2>

            @auth
                @if(auth()->user()->role === 'student')
                    <a href="{{ route('feedbacks.create') }}"
                       class="inline-flex items-center px-5 py-2 bg-gradient-to-r from-teal-500 to-cyan-500 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                        + Add Feedback
                    </a>
                @endif
            @endauth
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-2xl p-8">

                <!-- Search & Filter (only for instructors) -->
                @if(auth()->user()->role === 'instructor')
                    <form method="GET" action="{{ route('feedbacks.index') }}" class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Keyword -->
                            <input type="text" name="keyword" value="{{ request('keyword') }}"
                                   placeholder="Search comments..."
                                   class="w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:text-gray-200">

                            <!-- Course -->
                            <select name="course_id"
                                    class="w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:text-gray-200">
                                <option value="">All Courses</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Student -->
                            <select name="student_id"
                                    class="w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:text-gray-200">
                                <option value="">All Students</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                        [ID: {{ $student->id }}] {{ $student->name }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Rating -->
                            <select name="rating"
                                    class="w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:text-gray-200">
                                <option value="">All Ratings</option>
                                @for($i=1; $i<=5; $i++)
                                    <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                        {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="mt-4 flex justify-end gap-3">
                            <button type="submit"
                                    class="px-5 py-2 bg-gradient-to-r from-teal-500 to-cyan-500 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                                Filter
                            </button>
                            <a href="{{ route('feedbacks.index') }}"
                               class="px-5 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                                Reset
                            </a>
                        </div>
                    </form>
                @endif

                <!-- Feedback List -->
                <div class="space-y-6">
                    @forelse($feedback as $item)
                        <div class="p-6 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl shadow-sm hover:shadow-md transition">
                            <!-- Sequence + Course -->
                            <div class="flex items-start gap-4 mb-3">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-teal-600 text-white text-sm font-semibold">
                                    {{ $loop->iteration + (($feedback->currentPage() - 1) * $feedback->perPage()) }}
                                </span>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $item->course->title ?? 'General Feedback' }}
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        By <span class="font-medium text-gray-700 dark:text-gray-300">
                                            {{ $item->user->name }} [ID: {{ $item->user->id }}]
                                        </span>
                                        • {{ $item->created_at->format('M d, Y') }} • {{ $item->created_at->format('h:i A') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Comment -->
                            <p class="text-gray-700 dark:text-gray-300 mb-4">
                                {{ Str::limit($item->comment, 180) }}
                            </p>

                            <!-- Star Rating -->
                            <div class="flex items-center gap-1 mb-4">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-6 h-6 {{ $i <= $item->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286
                                                 3.967a1 1 0 00.95.69h4.178c.969 0
                                                 1.371 1.24.588 1.81l-3.385 2.46a1 1
                                                 0 00-.364 1.118l1.286 3.966c.3.922-.755
                                                 1.688-1.54 1.118l-3.385-2.46a1 1
                                                 0 00-1.176 0l-3.385 2.46c-.785.57-1.84-.196-1.54-1.118l1.286-3.966a1 1
                                                 0 00-.364-1.118L2.049 9.394c-.783-.57-.38-1.81.588-1.81h4.178a1 1
                                                 0 00.95-.69l1.286-3.967z"/>
                                    </svg>
                                @endfor
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-600">
                                <a href="{{ route('feedbacks.show', $item->id) }}"
                                   class="text-teal-600 hover:text-teal-700 font-semibold text-sm">
                                    View Details →
                                </a>

                                @if(auth()->user()->role === 'student' && auth()->id() === $item->user_id)
                                    <div class="flex gap-3">
                                        <a href="{{ route('feedbacks.edit', $item->id) }}"
                                           class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md text-sm font-semibold">
                                            Edit
                                        </a>
                                        <form action="{{ route('feedbacks.destroy', $item->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm font-semibold" onclick="return confirm('Are you sure you want to delete this feedback?');">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <p class="text-gray-700 dark:text-gray-300 text-lg">No feedback available yet.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($feedback->hasPages())
                    <div class="mt-8 flex justify-center">
                        {{ $feedback->onEachSide(1)->links('vendor.pagination.tailwind') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
