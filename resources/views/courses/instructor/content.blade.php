<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ $course->title }}
        </h2>

        
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto grid grid-cols-12 gap-6 px-4">

            <!-- LEFT SIDEBAR – LESSON LIST -->
            <div class="col-span-3">
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-md border border-gray-200">

                    <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-4">Lessons</h3>

                    @foreach ($lessons as $lesson)
                        <a href="{{ route('courses.content', ['course' => $course->id, 'lesson' => $lesson->id]) }}"
                           class="block mb-3 px-4 py-2 rounded-lg border
                                  {{ $selectedLesson && $selectedLesson->id == $lesson->id 
                                    ? 'bg-teal-600 text-white border-teal-700' 
                                    : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 border-gray-300 hover:bg-gray-200' }}">
                            {{ $lesson->order_number }}. {{ $lesson->title }}
                        </a>
                    @endforeach

                </div>
            </div>

            <!-- RIGHT SIDE CONTENT -->
            <div class="col-span-9">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-200">

                    @if ($selectedLesson)

                        <!-- TITLE + EDIT BUTTON -->
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-200">
                                {{ $selectedLesson->order_number }}. {{ $selectedLesson->title }}
                            </h3>

                            <a href="{{ route('lessons.edit', $selectedLesson->id) }}"
                               class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">
                                Edit
                            </a>
                        </div>

                        @php
                            $videoFile = $selectedLesson->materials->firstWhere('file_type', 'video');
                            $videoUrl  = $selectedLesson->materials->firstWhere('file_type', 'video_url');
                        @endphp

                        <!-- ================= VIDEO (FILE) ================= -->
                        @if ($videoFile)
                            <div class="mb-2">
                                <video controls class="w-full rounded-lg">
                                    <source src="{{ asset('storage/' . $videoFile->file_path) }}">
                                </video>

                                <!-- DELETE BUTTON -->
                                <form action="{{ route('materials.delete', $videoFile->id) }}" 
                                      method="POST" class="mt-2">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                        Delete Video File
                                    </button>
                                </form>
                            </div>

                        <!-- ================= VIDEO (URL) ================= -->
                        @elseif ($videoUrl)
                            <div class="mb-2">
                                <iframe src="{{ $videoUrl->file_path }}"
                                        class="w-full h-64 rounded-lg border"
                                        allowfullscreen></iframe>

                                <!-- DELETE BUTTON -->
                                <form action="{{ route('materials.delete', $videoUrl->id) }}" 
                                      method="POST" class="mt-2">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                        Delete Video URL
                                    </button>
                                </form>
                            </div>

                        @else
                            <p class="text-gray-600 italic">No video uploaded yet.</p>
                        @endif


                        <!-- ================= NOTES (PDF) ================= -->
                        @php
                            $pdf = $selectedLesson->materials->firstWhere('file_type', 'pdf');
                        @endphp

                        @if ($pdf)
                            <div class="mt-4">
                                <a href="{{ asset('storage/' . $pdf->file_path) }}" 
                                   target="_blank" class="text-blue-600 underline">
                                    Download Notes
                                </a>

                                <!-- DELETE BUTTON -->
                                <form action="{{ route('materials.delete', $pdf->id) }}" 
                                      method="POST" class="mt-2">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                        Delete Notes
                                    </button>
                                </form>
                            </div>
                        @else
                            <p class="text-gray-600 italic">No notes uploaded yet.</p>
                        @endif


                        <!-- QUIZ SECTION -->
                        @php
                            $quiz = $selectedLesson->quiz;
                        @endphp

                        <div class="mt-6 p-4 rounded-xl bg-gray-100 dark:bg-gray-700 border border-gray-300">
                            <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">Quiz</h4>

                            @if ($quiz && $quiz->questions->count() > 0)
                                <p class="text-green-700 dark:text-green-400">Quiz available</p>
                                <a href="{{ route('lessons.quiz.editor', $selectedLesson->id) }}"
                                   class="underline text-blue-600 dark:text-blue-400">
                                    Manage Quiz
                                </a>
                            @else
                                <p class="text-gray-600 italic">No quiz added yet.</p>
                                <a href="{{ route('lessons.quiz.editor', $selectedLesson->id) }}"
                                   class="underline text-blue-600 dark:text-blue-400">
                                    Add Quiz
                                </a>
                            @endif
                        </div>

                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
