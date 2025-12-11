<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto grid grid-cols-12 gap-6 px-4">

            <!-- LESSON LIST -->
            <div class="col-span-3">
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow border border-gray-200">
                    <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-4">Lessons</h3>

                    @foreach ($lessons as $lesson)
                        <a href="{{ route('student.course.content', ['course' => $course->id, 'lesson' => $lesson->id]) }}"
                           class="block mb-3 px-4 py-2 rounded-lg border
                           {{ $selectedLesson && $selectedLesson->id == $lesson->id 
                                ? 'bg-teal-600 text-white border-teal-700' 
                                : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-200 border-gray-300 hover:bg-gray-200' }}">

                            {{ $lesson->order_number + 1 }}. {{ $lesson->title }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- LESSON CONTENT -->
            <div class="col-span-9">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow border border-gray-200">

                    @if ($selectedLesson)

                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                            Lesson {{ $selectedLesson->order_number + 1 }}: {{ $selectedLesson->title }}
                        </h3>

                        {{-- VIDEO --}}
                        <div class="mb-8">
                            <h4 class="font-semibold mb-2 text-gray-900 dark:text-gray-200">Video</h4>

                            @php
                                $video = $selectedLesson->materials->firstWhere('file_type', 'video');
                                $videoUrl = $selectedLesson->materials->firstWhere('file_type', 'video_url');
                            @endphp

                            @if($video)
                                <video controls class="w-full rounded-lg">
                                    <source src="{{ asset('storage/' . $video->file_path) }}">
                                </video>
                            @elseif($videoUrl)
                                <iframe src="{{ $videoUrl->file_path }}" 
                                        class="w-full h-64 rounded-lg border" allowfullscreen></iframe>
                            @else
                                <p class="text-gray-600 dark:text-gray-300">No video available.</p>
                            @endif
                        </div>

                        {{-- NOTES --}}
                        <div class="mb-8">
                            <h4 class="font-semibold mb-2 text-gray-900 dark:text-gray-200">Notes</h4>

                            @php
                                $pdf = $selectedLesson->materials->firstWhere('file_type', 'pdf');
                            @endphp

                            @if($pdf)
                                <a href="{{ asset('storage/' . $pdf->file_path) }}"
                                   target="_blank"
                                   class="text-teal-600 underline">
                                   Download Notes
                                </a>
                            @else
                                <p class="text-gray-600 dark:text-gray-300">No notes available.</p>
                            @endif
                        </div>

                        {{-- QUIZ --}}
                        <div class="mb-8">
                            <h4 class="font-semibold mb-2 text-gray-900 dark:text-gray-200">Quiz</h4>

                            @if($selectedLesson->quiz && $selectedLesson->quiz->questions->count() > 0)
                                <a href="{{ route('quiz.take', $selectedLesson->id) }}"
                                   class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">
                                    Take Quiz
                                </a>
                            @else
                                <p class="text-gray-600 dark:text-gray-300">No quiz available.</p>
                            @endif
                        </div>

                    @else
                        <p class="text-gray-600 dark:text-gray-300">Select a lesson to begin.</p>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
