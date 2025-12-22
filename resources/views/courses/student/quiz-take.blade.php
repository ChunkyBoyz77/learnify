<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            {{-- Back Button --}}
            <a href="{{ route('student.course.content', ['course' => $lesson->course_id, 'lesson' => $lesson->id]) }}"
               class="flex items-center text-gray-600 dark:text-gray-400
                      hover:text-teal-600 dark:hover:text-teal-400 transition-colors duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>

            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Quiz Assessment: {{ $lesson->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Success Message --}}
            @if(session('success'))
                <div class="mb-8 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-5 py-3 rounded-xl shadow-sm flex items-center gap-3">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                            {{ $quiz->title ?? 'Module Knowledge Check' }}
                        </h3>
                        <span class="text-[10px] font-black uppercase bg-teal-100 dark:bg-teal-900/50 text-teal-700 dark:text-teal-300 px-3 py-1 rounded-lg">
                            {{ $quiz->questions->count() }} Questions
                        </span>
                    </div>
                </div>

                <form action="{{ route('quiz.submit', $lesson->id) }}" method="POST" class="p-6 md:p-10">
                    @csrf

                    <div class="space-y-12">
                        @foreach($quiz->questions as $index => $question)
                            <div class="relative">
                                {{-- Question Header --}}
                                <div class="flex items-start gap-4 mb-6">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-teal-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="flex-grow pt-1">
                                        <p class="text-lg font-bold text-gray-800 dark:text-gray-100 leading-snug">
                                            {{ $question->question_text }}
                                        </p>
                                    </div>
                                </div>

                                @php
                                    $options = is_array($question->options)
                                        ? $question->options
                                        : json_decode($question->options, true);
                                @endphp

                                {{-- Options Grid --}}
                                <div class="grid grid-cols-1 gap-3 ml-12">
                                    @foreach($options as $optIndex => $option)
                                        <label class="group relative flex items-center p-4 rounded-xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:border-teal-500 dark:hover:border-teal-400 hover:bg-teal-50/50 dark:hover:bg-teal-900/10 transition-all">
                                            <input type="radio" 
                                                   name="answers[{{ $question->id }}]" 
                                                   value="{{ $optIndex }}"
                                                   class="w-4 h-4 text-teal-600 border-gray-300 focus:ring-teal-500 dark:bg-gray-900 dark:border-gray-600"
                                                   required>
                                            <span class="ml-4 text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-teal-700 dark:group-hover:text-teal-300 transition-colors">
                                                {{ $option }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Form Footer --}}
                    <div class="mt-12 pt-8 border-t border-gray-100 dark:border-gray-700 flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="flex items-center text-gray-500 dark:text-gray-400 gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-xs font-medium italic">Please review your answers before submitting.</span>
                        </div>
                        
                        <button type="submit" 
                                class="w-full md:w-auto px-10 py-4 bg-teal-600 hover:bg-teal-700 text-white font-black uppercase text-xs tracking-[0.2em] rounded-2xl shadow-xl shadow-teal-500/20 transition-all transform hover:-translate-y-1 active:scale-95">
                            Submit Assessment
                        </button>
                    </div>
                </form>
            </div>
            
            {{-- Satisfaction/Trust note matching content pages --}}
            <p class="mt-8 text-center text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-[0.3em]">
                Verified SQA Assessment Module
            </p>
        </div>
    </div>
</x-app-layout>