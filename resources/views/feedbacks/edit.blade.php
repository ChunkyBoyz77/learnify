<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Feedback
            </h2>

            <a href="{{ route('feedbacks.index') }}"
               class="inline-flex items-center px-5 py-2 bg-gradient-to-r from-gray-500 to-gray-600 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                ← Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-2xl p-8">
                <form action="{{ route('feedbacks.update', $feedback->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Course (read-only) -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Course
                        </label>
                        <input type="text"
                               value="{{ $feedback->course->title ?? 'General Feedback' }}"
                               disabled
                               class="w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200">
                    </div>

                    <!-- Star Rating (same behavior as create) -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Rating
                        </label>

                        <div id="star-rating" class="flex items-center space-x-2" data-initial="{{ (int) old('rating', $feedback->rating ?? 0) }}">
                            @for($i = 1; $i <= 5; $i++)
                                <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}"
                                       class="hidden" {{ (int) old('rating', $feedback->rating ?? 0) === $i ? 'checked' : '' }}>
                                <label for="star{{ $i }}" class="cursor-pointer text-gray-400 transition-colors">
                                    <svg class="w-8 h-8 star-icon text-gray-400" data-value="{{ $i }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.178c.969 0 1.371 1.24.588 1.81l-3.385 2.46a1 1 0 00-.364 1.118l1.286 3.966c.3.922-.755 1.688-1.54 1.118l-3.385-2.46a1 1 0 00-1.176 0l-3.385 2.46c-.785.57-1.84-.196-1.54-1.118l1.286-3.966a1 1 0 00-.364-1.118L2.049 9.394c-.783-.57-.38-1.81.588-1.81h4.178a1 1 0 00.95-.69l1.286-3.967z"/>
                                    </svg>
                                </label>
                            @endfor
                        </div>

                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            Click a star to set your rating. Hover to preview.
                        </p>
                    </div>

                    <!-- Feedback Message -->
                    <div class="mb-6">
                        <label for="comment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Feedback Message
                        </label>
                        <textarea name="comment" id="comment" rows="4" required
                                  class="w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:text-gray-200">{{ old('comment', $feedback->comment) }}</textarea>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-between mt-6">
                        <a href="{{ route('feedbacks.index') }}"
                           class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                            Cancel
                        </a>

                        <button type="submit"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-teal-600 to-cyan-600 hover:from-teal-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Star Rating Script (same logic as create, with initial value) -->
    <script>
        (function () {
            const container = document.getElementById('star-rating');
            if (!container) return;

            const stars = container.querySelectorAll('.star-icon');
            const initial = parseInt(container.dataset.initial || '0', 10);
            let selectedValue = initial > 0 ? initial : 0;

            function applyHighlight(value, hover = false) {
                stars.forEach(s => {
                    const starValue = parseInt(s.dataset.value);
                    if (starValue <= value) {
                        s.classList.add(hover ? 'text-yellow-400' : 'text-yellow-500');
                        s.classList.remove('text-gray-400');
                    } else {
                        s.classList.add('text-gray-400');
                        s.classList.remove('text-yellow-400', 'text-yellow-500');
                    }
                });
            }

            // Initialize visual state and radio checked state
            if (selectedValue > 0) {
                applyHighlight(selectedValue);
                const checkedRadio = document.getElementById('star' + selectedValue);
                if (checkedRadio) checkedRadio.checked = true;
            } else {
                applyHighlight(0);
            }

            stars.forEach(star => {
                const value = parseInt(star.dataset.value);

                // Hover preview
                star.addEventListener('mouseover', () => applyHighlight(value, true));

                // Mouseout: revert to current selection
                star.addEventListener('mouseout', () => {
                    if (selectedValue > 0) {
                        applyHighlight(selectedValue);
                    } else {
                        applyHighlight(0);
                    }
                });

                // Click to lock selection
                star.addEventListener('click', () => {
                    selectedValue = value;
                    applyHighlight(value);
                    const radio = document.getElementById('star' + value);
                    if (radio) radio.checked = true;
                });
            });
        })();
    </script>
</x-app-layout>
