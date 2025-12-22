<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            {{-- Back Button --}}
            <a href="{{ route('courses.content', $lesson->course_id) }}"
               class="flex items-center text-gray-600 dark:text-gray-400
                      hover:text-teal-600 dark:hover:text-teal-400 transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>

            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Quiz Management: {{ $lesson->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- SUCCESS MESSAGE --}}
            @if(session('success'))
                <div class="mb-6 bg-teal-50 dark:bg-teal-900/20 border border-teal-200 dark:border-teal-800 text-teal-700 dark:text-teal-400 px-5 py-3 rounded-xl shadow-sm flex items-center gap-3">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                {{-- ================= LEFT: EXISTING QUESTIONS ================= --}}
                <div class="lg:col-span-7 xl:col-span-8 space-y-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-base font-black text-gray-700 dark:text-gray-300 uppercase tracking-widest">
                            Existing Assessment ({{ $quiz->questions->count() }})
                        </h3>
                    </div>

                    @if ($quiz->questions->count() > 0)
                        <div class="space-y-4">
                            @foreach ($quiz->questions as $q)
                                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden border-l-4 border-teal-500">
                                    <div class="p-5">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="space-y-3 flex-grow">
                                                <p class="font-bold text-gray-900 dark:text-gray-100 text-base leading-tight">
                                                    <span class="text-teal-600 dark:text-teal-400 mr-1">Q{{ $loop->iteration }}.</span> 
                                                    {{ $q->question_text }}
                                                </p>

                                                <div class="grid grid-cols-1 gap-2 ml-6">
                                                    @foreach (['A' => $q->option_a, 'B' => $q->option_b, 'C' => $q->option_c] as $key => $option)
                                                        @php $isCorrect = ($loop->index == $q->correct_option_index); @endphp
                                                        <div class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ $isCorrect ? 'bg-teal-50 dark:bg-teal-900/20 border border-teal-100 dark:border-teal-800' : 'text-gray-500 dark:text-gray-400' }}">
                                                            <span class="font-black text-[10px] w-5 h-5 flex items-center justify-center rounded {{ $isCorrect ? 'bg-teal-500 text-white' : 'bg-gray-100 dark:bg-gray-700' }}">
                                                                {{ $key }}
                                                            </span>
                                                            <span class="{{ $isCorrect ? 'font-bold text-teal-700 dark:text-teal-300' : '' }}">
                                                                {{ $option }}
                                                            </span>
                                                            @if($isCorrect)
                                                                <svg class="w-4 h-4 ml-auto text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                            {{-- Delete Button --}}
                                            <form action="{{ route('quiz.question.delete', $q->id) }}" method="POST"
                                                  onsubmit="return confirm('Remove this question permanently?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-12 text-center bg-white dark:bg-gray-800 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium italic">
                                No questions have been defined for this quiz.
                            </p>
                        </div>
                    @endif
                </div>

                {{-- ================= RIGHT: ADD NEW QUESTION ================= --}}
                <div class="lg:col-span-5 xl:col-span-4">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 sticky top-6 border-l-8 border-indigo-500">
                        <h3 class="text-lg font-black text-gray-800 dark:text-white mb-6 uppercase tracking-tighter">
                            Add New Question
                        </h3>

                        <form action="{{ route('lessons.quiz.store', $lesson->id) }}" method="POST" class="space-y-5">
                            @csrf

                            <div>
                                <label class="block text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2">Question Text</label>
                                <textarea name="question_text" required rows="3"
                                          class="w-full px-4 py-3 rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-sm outline-none transition-all"
                                          placeholder="e.g., What is the primary purpose of SQA?"></textarea>
                            </div>

                            <div class="space-y-4">
                                @foreach(['a' => 'Option A', 'b' => 'Option B', 'c' => 'Option C'] as $key => $label)
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1.5 ml-1">{{ $label }}</label>
                                        <input type="text" name="option_{{ $key }}" required
                                               class="w-full px-4 py-2.5 rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-sm outline-none transition-all"
                                               placeholder="Enter choice content...">
                                    </div>
                                @endforeach
                            </div>

                            <div class="pt-2">
                                <label class="block text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2">Correct Answer</label>
                                <div class="relative">
                                    <select name="correct_option"
                                            class="w-full px-4 py-3 rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-sm font-bold appearance-none outline-none transition-all">
                                        <option value="A">Option A</option>
                                        <option value="B">Option B</option>
                                        <option value="C">Option C</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </div>
                            </div>

                            <button type="submit"
                                    class="w-full py-4 mt-4 bg-teal-600 hover:bg-teal-700 text-white font-black uppercase text-xs tracking-[0.2em] rounded-2xl shadow-xl shadow-teal-500/20 transition-all transform active:scale-95">
                                Add to Quiz
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>