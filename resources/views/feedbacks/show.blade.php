<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <!-- Title -->
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                Feedback Details
            </h2>

            <!-- Back Button -->
            <a href="{{ route('feedbacks.index') }}"
               class="inline-flex items-center px-5 py-2 bg-gradient-to-r from-gray-500 to-gray-600 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-2xl p-8">

                <!-- Feedback Card -->
                <div class="p-6 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl shadow-sm">

                    <!-- Course Title -->
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                        {{ $feedback->course->title ?? 'General Feedback' }}
                    </h3>

                    <!-- Author + Date -->
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        By <span class="font-medium text-gray-700 dark:text-gray-300">{{ $feedback->user->name }}</span>
                        • {{ $feedback->created_at->format('M d, Y') }}
                    </p>

                    <!-- Instructor Name -->
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Instructor: <span class="font-medium text-gray-700 dark:text-gray-300">{{ $feedback->course->instructor->name ?? 'N/A' }}</span>
                    </p>

                    <!-- Feedback Comment -->
                    <p class="text-gray-700 dark:text-gray-300 mb-6">
                        {{ $feedback->comment }}
                    </p>

                    <!-- Star Rating -->
                    <div class="flex items-center gap-1 mb-4">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-6 h-6 {{ $i <= $feedback->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.966a1 1 0 00.95.69h4.174c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.966c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.176 0l-3.38 2.455c-.785.57-1.84-.197-1.54-1.118l1.287-3.966a1 1 0 00-.364-1.118L2.05 9.393c-.783-.57-.38-1.81.588-1.81h4.174a1 1 0 00.95-.69l1.287-3.966z"/>
                            </svg>
                        @endfor
                    </div>


                    <!-- Actions -->
                    <div class="flex justify-end gap-3 border-t border-gray-200 dark:border-gray-600 pt-4">
                        @if(auth()->user()->role === 'student' && auth()->id() === $feedback->user_id)
                            <a href="{{ route('feedbacks.edit', $feedback->id) }}"
                               class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md text-sm font-semibold">
                                Edit
                            </a>
                            <form action="{{ route('feedbacks.destroy', $feedback->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm font-semibold" onclick="return confirm('Are you sure you want to delete this feedback?');">
                                    Delete
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
