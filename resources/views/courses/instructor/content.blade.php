<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto grid grid-cols-12 gap-6 px-4">

            <!-- ================= LEFT SIDEBAR (LIST OF LESSONS) ================= -->
            <div class="col-span-3">
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-md border border-gray-200">

                    <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-4">Lessons</h3>

                    @foreach ($lessons as $lesson)
                        <a href="{{ route('courses.content', ['course' => $course->id, 'lesson' => $lesson->id]) }}"
                           class="block mb-3 px-4 py-2 rounded-lg border
                                  {{ $selectedLesson && $selectedLesson->id == $lesson->id 
                                     ? 'bg-teal-600 text-white border-teal-700' 
                                     : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 border-gray-300 hover:bg-gray-200' }}">
                            {{ $lesson->order_number + 1 }}. {{ $lesson->title }}
                        </a>
                    @endforeach

                </div>
            </div>

            <!-- ================= RIGHT SIDE CONTENT AREA ================= -->
            <div class="col-span-9">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-200">

                    @if ($selectedLesson)
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-200">
                                {{ $selectedLesson->order_number + 1 }}. {{ $selectedLesson->title }}
                            </h3>

                            <a href="{{ route('lessons.edit', $selectedLesson->id) }}"
                               class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">
                                Edit
                            </a>
                        </div>

                        <!-- ================= VIDEO SECTION ================= -->
                        <div class="mb-6 p-6 rounded-xl bg-gray-100 dark:bg-gray-700 border border-gray-300">
                            <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">Video</h4>

                            @if ($selectedLesson->video_file)
                                <video controls class="w-full rounded-lg">
                                    <source src="{{ asset('storage/' . $selectedLesson->video_file) }}">
                                </video>

                            @elseif ($selectedLesson->video_url)
                                <iframe src="{{ $selectedLesson->video_url }}" 
                                        class="w-full h-64 rounded-lg border"
                                        allowfullscreen></iframe>

                            @else
                                <p class="text-gray-600 dark:text-gray-300 italic">No video uploaded yet.</p>
                            @endif
                        </div>

                        <!-- ================= NOTES SECTION ================= -->
                        <div class="mb-6 p-4 rounded-xl bg-gray-100 dark:bg-gray-700 border border-gray-300">
                            <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">Notes File</h4>

                            @if ($selectedLesson->note_file)
                                <a href="{{ asset('storage/' . $selectedLesson->note_file) }}" 
                                   class="text-blue-600 dark:text-blue-400 underline" 
                                   target="_blank">
                                    Download Notes
                                </a>
                            @else
                                <p class="text-gray-600 dark:text-gray-300 italic">No notes uploaded yet.</p>
                            @endif
                        </div>

                        <!-- ================= QUIZ SECTION ================= -->
                        <div class="mb-6 p-4 rounded-xl bg-gray-100 dark:bg-gray-700 border border-gray-300">
                            <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">Quiz</h4>

                            @php
                                $hasQuiz = $selectedLesson->quizQuestions()->exists();
                            @endphp

                            @if ($hasQuiz)
                                <p class="text-green-700 dark:text-green-400">Quiz available</p>
                                <a href="{{ route('lessons.quiz.editor', $selectedLesson->id) }}"
                                   class="underline text-blue-600 dark:text-blue-400">
                                    Manage Quiz
                                </a>
                            @else
                                <p class="text-gray-600 dark:text-gray-300 italic">No quiz added yet.</p>
                                <a href="{{ route('lessons.quiz.editor', $selectedLesson->id) }}"
                                   class="underline text-blue-600 dark:text-blue-400">
                                    Add Quiz
                                </a>
                            @endif
                        </div>

                    @else
                        <p class="text-gray-600 dark:text-gray-300">
                            Select a lesson from the left to view its content.
                        </p>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
