<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <!-- Title -->
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Feedback List
            </h2>

            <!-- Add Feedback Button (students only) -->
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
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-2xl p-8">

                <!-- Feedback Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($feedback as $item)
                        <div class="p-6 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm hover:shadow-md transition">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                {{ $item->course->title ?? 'General Feedback' }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                                By <strong>{{ $item->user->name }}</strong>
                                on {{ $item->created_at->format('M d, Y') }}
                            </p>
                            <p class="text-gray-700 dark:text-gray-300 mb-4 line-clamp-3">
                                {{ Str::limit($item->comment, 120) }}
                            </p>

                            <!-- Star Rating -->
                            <div class="flex items-center gap-1 mb-3">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= $item->rating ? 'text-yellow-500' : 'text-gray-300 dark:text-gray-600' }}"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l..."/>
                                    </svg>
                                @endfor
                            </div>

                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                                Status: {{ ucfirst($item->status) }}
                            </p>

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
                        <p class="text-gray-700 dark:text-gray-300">No feedback available.</p>
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
