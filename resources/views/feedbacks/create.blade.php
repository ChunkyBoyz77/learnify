<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Feedback') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-2xl p-8">
                <form action="{{ route('feedbacks.store') }}" method="POST">
                    @csrf

                    <!-- Select Course -->
                    <div class="mb-6">
                        <label for="course_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Course
                        </label>
                        <select name="course_id" id="course_id" required
                                class="w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:text-gray-200">
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Star Rating -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Rating
                        </label>
                        <div id="star-rating" class="flex items-center space-x-2">
                            @for($i = 1; $i <= 5; $i++)
                                <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}" class="hidden">
                                <label for="star{{ $i }}" class="cursor-pointer text-gray-400 transition-colors">
                                    <svg class="w-8 h-8 star-icon" data-value="{{ $i }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.178c.969 0 1.371 1.24.588 1.81l-3.385 2.46a1 1 0 00-.364 1.118l1.286 3.966c.3.922-.755 1.688-1.54 1.118l-3.385-2.46a1 1 0 00-1.176 0l-3.385 2.46c-.785.57-1.84-.196-1.54-1.118l1.286-3.966a1 1 0 00-.364-1.118L2.049 9.394c-.783-.57-.38-1.81.588-1.81h4.178a1 1 0 00.95-.69l1.286-3.967z"/>
                                    </svg>
                                </label>
                            @endfor
                        </div>
                    </div>

                    <!-- Feedback Message -->
                    <div class="mb-6">
                        <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Feedback Message
                        </label>
                        <textarea name="message" id="message" rows="4" required
                                  class="w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:text-gray-200"></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-teal-600 to-cyan-600 hover:from-teal-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 4v16m8-8H4"/>
                            </svg>
                            Submit Feedback
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Star Rating Script -->
    <script>
        const stars = document.querySelectorAll('#star-rating .star-icon');
        let selectedValue = 0;

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

        stars.forEach(star => {
            const value = parseInt(star.dataset.value);

            // Hover preview
            star.addEventListener('mouseover', () => {
                applyHighlight(value, true);
            });

            // Mouseout: revert to previous selection
            star.addEventListener('mouseout', () => {
                if (selectedValue > 0) {
                    applyHighlight(selectedValue);
                } else {
                    applyHighlight(0);
                }
            });

            // Click: lock selection
            star.addEventListener('click', () => {
                selectedValue = value;
                applyHighlight(value);
                document.getElementById('star' + value).checked = true;
            });
        });

        // Initialize with no selection
        applyHighlight(0);
    </script>
</x-app-layout>
