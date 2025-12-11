<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Feedback') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-2xl p-8">
                <form action="{{ route('feedbacks.update', $feedback->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Star Rating -->
                    @php
                        $currentRating = old('rating', $feedback->rating ?? 0);
                        $containerId = 'star-rating-edit-' . $feedback->id;
                    @endphp

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Rating
                        </label>
                        <div id="{{ $containerId }}" class="flex items-center space-x-2">
                            @for($i = 1; $i <= 5; $i++)
                                <input type="radio" name="rating" id="star{{ $i }}-edit-{{ $feedback->id }}" value="{{ $i }}"
                                       class="hidden" {{ (int)$currentRating === $i ? 'checked' : '' }}>
                                <label for="star{{ $i }}-edit-{{ $feedback->id }}" class="cursor-pointer">
                                    <svg class="w-8 h-8 star-icon text-gray-400 transition-colors"
                                         data-value="{{ $i }}" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.178c.969 0 1.371 1.24.588 1.81l-3.385 2.46a1 1 0 00-.364 1.118l1.286 3.966c.3.922-.755 1.688-1.54 1.118l-3.385-2.46a1 1 0 00-1.176 0l-3.385 2.46c-.785.57-1.84-.196-1.54-1.118l1.286-3.966a1 1 0 00-.364-1.118L2.049 9.394c-.783-.57-.38-1.81.588-1.81h4.178a1 1 0 00.95-.69l1.286-3.967z"/>
                                    </svg>
                                </label>
                            @endfor
                        </div>
                    </div>

                    <!-- Feedback Message (comments) -->
                    <div class="mb-6">
                        <label for="comments" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Feedback Message
                        </label>
                        <textarea name="comments" id="comments" rows="4" required
                                  class="w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:text-gray-200">{{ old('comments', $feedback->comments) }}</textarea>
                    </div>

                    <div class="flex justify-end">
                        <!-- Cancel Button -->
                        <a href="{{ route('feedbacks.show', $feedback->id ?? 0) ?? route('feedbacks.index') }}"
                           class="inline-flex items-center px-6 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all mr-4">
                            Cancel
                        </a>

                        <!-- Submit Button -->
                        <button type="submit"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-yellow-500 to-amber-500 hover:from-yellow-600 hover:to-amber-600 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Star Rating Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const containerId = "{{ $containerId }}";
        const container = document.getElementById(containerId);
        if (!container) return;

        const stars = Array.from(container.querySelectorAll('.star-icon'));
        const radios = Array.from(container.querySelectorAll('input[type="radio"][name="rating"]'));

        let selectedValue = (function getInitialSelectedValue() {
            const checked = radios.find(r => r.checked);
            return checked ? parseInt(checked.value, 10) : 0;
        })();

        applyHighlight(selectedValue);

        // Hover preview (left to right)
        container.addEventListener('mouseover', function (e) {
            const star = e.target.closest('.star-icon');
            if (!star) return;
            const value = parseInt(star.dataset.value, 10);
            applyHighlight(value, true);
        });

        // Mouseout: revert to previous selection
        container.addEventListener('mouseout', function (e) {
            const related = e.relatedTarget;
            if (!container.contains(related)) {
                applyHighlight(selectedValue);
            }
        });

        // Click: lock selection
        container.addEventListener('click', function (e) {
            const star = e.target.closest('.star-icon');
            if (!star) return;
            const value = parseInt(star.dataset.value, 10);
            selectedValue = value;

            const radio = document.getElementById('star' + value + '-edit-{{ $feedback->id }}');
            if (radio) radio.checked = true;

            applyHighlight(selectedValue);
        });

        function applyHighlight(value, isHover = false) {
            stars.forEach(s => {
                const v = parseInt(s.dataset.value, 10);
                if (v <= value) {
                    s.classList.remove('text-gray-400', 'text-yellow-500', 'text-yellow-400');
                    s.classList.add(isHover ? 'text-yellow-400' : 'text-yellow-500');
                } else {
                    s.classList.remove('text-yellow-500', 'text-yellow-400');
                    s.classList.add('text-gray-400');
                }
            });
        }
    });
    </script>
</x-app-layout>
