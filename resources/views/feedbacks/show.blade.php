<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Feedback Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-2xl p-8">
                <!-- Course and User -->
                <div class="mb-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                        {{ $feedback->course->title ?? 'General Feedback' }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Submitted by <strong>{{ $feedback->user->name }}</strong>
                        on {{ $feedback->created_at->format('M d, Y') }}
                    </p>
                </div>

                <!-- Feedback Message -->
                <div class="mb-6">
                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                        {{ $feedback->comments }}
                    </p>
                </div>

                <!-- Star Rating Display -->
                <div class="flex items-center gap-2 mb-6">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-6 h-6 {{ $i <= $feedback->rating ? 'text-yellow-500' : 'text-gray-300 dark:text-gray-600' }}"
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
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    <strong>Status:</strong> {{ ucfirst($feedback->status) }}
                </p>

                <!-- Student Actions -->
                @auth
                    @if(auth()->user()->role === 'student' && auth()->id() === $feedback->user_id)
                        <div class="flex items-center gap-4">
                            <a href="{{ route('feedbacks.edit', $feedback->id) }}"
                               class="inline-flex items-center px-5 py-2 bg-gradient-to-r from-yellow-500 to-amber-500 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                                Edit
                            </a>
                            <form action="{{ route('feedbacks.destroy', $feedback->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="inline-flex items-center px-5 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                                    Delete
                                </button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</x-app-layout>
