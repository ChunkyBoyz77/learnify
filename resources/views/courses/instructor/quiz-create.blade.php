<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                Quiz Editor – {{ $lesson->title }}
            </h2>

            <a href="{{ route('courses.content', $lesson->course_id) }}"
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
               ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-10 max-w-5xl mx-auto px-4">

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-teal-200">

            {{-- SUCCESS MESSAGE --}}
            @if(session('success'))
                <div class="mb-4 bg-teal-100 text-teal-800 px-4 py-2 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ===================== EXISTING QUESTIONS ===================== --}}
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">
                Existing Quiz Questions
            </h3>

            @if ($quiz->questions->count() > 0)
                <div class="space-y-4 mb-8">
                    @foreach ($quiz->questions as $q)
                        <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg border border-gray-300">
                            <p class="font-semibold text-gray-900 dark:text-gray-100">
                                Q{{ $loop->iteration }}: {{ $q->question_text }}
                            </p>

                            <ul class="ml-4 mt-2 text-gray-700 dark:text-gray-300">
                                @foreach ($q->options as $index => $option)
                                    <li>
                                        @if($index == $q->correct_option_index)
                                            <strong class="text-teal-600">✓ {{ $option }}</strong>
                                        @else
                                            {{ $option }}
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-600 dark:text-gray-300 italic mb-8">
                    No quiz questions yet. Add one below.
                </p>
            @endif

            {{-- ===================== ADD NEW QUESTION ===================== --}}
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Add New Question</h3>

            <form action="{{ route('lessons.quiz.store', $lesson->id) }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="font-semibold text-gray-700 dark:text-gray-300">Question Text</label>
                    <textarea name="question_text" required
                              class="w-full mt-1 px-4 py-2 rounded-lg border-gray-300 mb-3"></textarea>
                </div>

                <div class="mb-4">
                    <label class="font-semibold text-gray-700 dark:text-gray-300">Option A</label>
                    <input type="text" name="option_a" required
                           class="w-full px-4 py-2 rounded-lg border-gray-300 mb-3">
                </div>

                <div class="mb-4">
                    <label class="font-semibold text-gray-700 dark:text-gray-300">Option B</label>
                    <input type="text" name="option_b" required
                           class="w-full px-4 py-2 rounded-lg border-gray-300 mb-3">
                </div>

                <div class="mb-4">
                    <label class="font-semibold text-gray-700 dark:text-gray-300">Option C</label>
                    <input type="text" name="option_c" required
                           class="w-full px-4 py-2 rounded-lg border-gray-300 mb-3">
                </div>

                <div class="mb-6">
                    <label class="font-semibold text-gray-700 dark:text-gray-300 mb-1">Correct Answer</label>
                    <select name="correct_option"
                            class="w-full px-4 py-2 rounded-lg border-gray-300">
                        <option value="A">Option A</option>
                        <option value="B">Option B</option>
                        <option value="C">Option C</option>
                    </select>
                </div>

                <div class="flex justify-end">
                    <button class="px-6 py-3 bg-teal-600 text-white rounded-lg hover:bg-teal-700">
                        Add Question
                    </button>
                </div>

            </form>

        </div>

    </div>
</x-app-layout>
