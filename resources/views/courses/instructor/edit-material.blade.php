<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                Edit Lesson Material – {{ $lesson->title }}
            </h2>

            <a href="{{ route('courses.content', ['course' => $lesson->course_id, 'lesson' => $lesson->id]) }}"
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
               ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-10 max-w-5xl mx-auto px-4">

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-teal-200">

            @if(session('success'))
                <div class="mb-4 bg-teal-100 text-teal-800 px-4 py-2 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @php
                $video = $materials->firstWhere('file_type', 'video');
                $videoUrl = $materials->firstWhere('file_type', 'video_url');
                $pdf = $materials->firstWhere('file_type', 'pdf');
            @endphp

            <form action="{{ route('lessons.update', $lesson->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- VIDEO -->
                <h3 class="text-lg font-bold mb-2">Video</h3>

                @if($video)
                    <p class="mb-3">Current Video:
                        <a class="text-teal-600 underline" href="{{ asset('storage/'.$video->file_path) }}">View</a>
                    </p>
                @elseif($videoUrl)
                    <p class="mb-3">Current Video URL:
                        <a class="text-teal-600 underline" href="{{ $videoUrl->file_path }}" target="_blank">Open</a>
                    </p>
                @else
                    <p class="mb-3 text-gray-600">No video uploaded yet.</p>
                @endif

                <label class="font-semibold">Upload New Video File</label>
                <input type="file" name="video_file" class="block w-full mb-4 border rounded-lg">

                <label class="font-semibold">Or paste YouTube URL</label>
                <input type="text" name="video_url" placeholder="https://youtube.com/..."
                       class="w-full mb-6 px-4 py-2 rounded-lg border">

                <!-- NOTES PDF -->
                <h3 class="text-lg font-bold mb-2">Notes File (PDF)</h3>

                @if($pdf)
                    <p class="mb-3">Current Notes:
                        <a class="text-teal-600 underline" href="{{ asset('storage/'.$pdf->file_path) }}" target="_blank">
                            Download
                        </a>
                    </p>
                @else
                    <p class="mb-3 text-gray-600">No notes uploaded yet.</p>
                @endif

                <input type="file" name="note_file" accept="application/pdf"
                       class="block w-full mb-8 border rounded-lg">

                <!-- QUIZ -->
                <div class="mt-6">
                    <h3 class="text-lg font-bold mb-3">Quiz</h3>

                    @if($quiz)
                        <a href="{{ route('lessons.quiz.editor', $lesson->id) }}"
                           class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">
                           Edit Quiz
                        </a>
                    @else
                        <a href="{{ route('lessons.quiz.editor', $lesson->id) }}"
                           class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">
                           Create Quiz
                        </a>
                    @endif
                </div>

                <!-- SAVE BUTTON -->
                <div class="mt-10 flex justify-end">
                    <button class="px-6 py-3 bg-teal-600 text-white rounded-lg hover:bg-teal-700">
                        Save Changes
                    </button>
                </div>

            </form>

        </div>
    </div>
</x-app-layout>
