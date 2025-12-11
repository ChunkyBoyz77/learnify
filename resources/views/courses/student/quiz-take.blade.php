<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100 leading-tight">
            Quiz: {{ $lesson->title }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto px-4">
            
            {{-- SUCCESS MESSAGE (after submission) --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg shadow">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-200">

                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                    {{ $quiz->title }}
                </h3>

                {{-- QUIZ FORM --}}
                <form action="{{ route('quiz.submit', $lesson->id) }}" method="POST">
                    @csrf

                    @foreach($quiz->questions as $index => $question)
                        <div class="mb-8 p-5 bg-gray-100 dark:bg-gray-700 rounded-xl border border-gray-300">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                Question {{ $index + 1 }}:
                            </h4>

                            <p class="text-gray-800 dark:text-gray-200 mb-4">
                                {{ $question->question_text }}
                            </p>

                            @php
                                $options = is_array($question->options)
                                    ? $question->options
                                    : json_decode($question->options, true);
                            @endphp

                            {{-- OPTIONS --}}
                            @foreach($options as $optIndex => $option)
                                <label class="flex items-center mb-2 text-gray-800 dark:text-gray-300">
                                    <input type="radio" 
                                           name="answers[{{ $question->id }}]" 
                                           value="{{ $optIndex }}"
                                           class="mr-3 h-4 w-4 text-teal-600 focus:ring-teal-500"
                                           required>
                                    {{ $option }}
                                </label>
                            @endforeach
                        </div>
                    @endforeach

                    {{-- SUBMIT BUTTON --}}
                    <div class="flex justify-end">
                        <button class="px-6 py-3 bg-teal-600 text-white rounded-lg 
                                       hover:bg-teal-700 shadow-lg transition">
                            Submit Quiz
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
