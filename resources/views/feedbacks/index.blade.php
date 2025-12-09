<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Feedback List') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-2xl p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($feedback as $item)
                        <div class="p-6 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm hover:shadow-md transition">
                            <!-- Course Title -->
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                {{ $item->course->title ?? 'General Feedback' }}
                            </h3>

                            <!-- User + Date -->
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                                By <strong>{{ $item->user->name }}</strong>
                                on {{ $item->created_at->format('M d, Y') }}
                            </p>

                            <!-- Feedback Message (short preview) -->
                            <p class="text-gray-700 dark:text-gray-300 mb-4 line-clamp-3">
                                {{ Str::limit($item->comments, 120) }}
                            </p>

                            <!-- Star Rating -->
                            <div class="flex items-center gap-1 mb-3">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= $item->rating ? 'text-yellow-500' : 'text-gray-300 dark:text-gray-600' }}"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.178c.969 0
                                                 1.371 1.24.588 1.81l-3.385 2.46a1 1 0 00-.364 1.118l1.286
                                                 3.966c.3.922-.755 1.688-1.54 1.118l-3.385-2.46a1 1 0
                                                 00-1.176 0l-3.385 2.46c-.785.57-1.84-.196-1.54-1.118l1.286-3.966a1
                                                 1 0 00-.364-1.118L2.049 9.394c-.783-.57-.38-1.81.588-1.81h4.178a1
                                                 1 0 00.95-.69l1.286-3.967z"/>
                                    </svg>
                                @endfor
                            </div>

                            <!-- Status -->
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                                Status: {{ ucfirst($item->status) }}
                            </p>

                            <!-- Actions -->
                            <div class="flex justify-between items-center">
                                <a href="{{ route('feedbacks.show', $item->id) }}"
                                   class="text-teal-600 hover:text-teal-700 font-semibold text-sm">
                                    View Details →
                                </a>

                                @if(auth()->user()->role === 'student' && auth()->id() === $item->user_id)
                                    <div class="flex gap-2">
                                        <a href="{{ route('feedbacks.edit', $item->id) }}"
                                           class="text-yellow-600 hover:text-yellow-700 font-semibold text-sm">
                                            Edit
                                        </a>
                                        <form action="{{ route('feedbacks.destroy', $item->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 font-semibold text-sm">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-600 dark:text-gray-400">No feedback available.</p>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $feedback->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
