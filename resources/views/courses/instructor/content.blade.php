<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            {{-- Back Button --}}
            <a href="{{ route('courses.my') }}"
               class="flex items-center text-gray-600 dark:text-gray-400
                      hover:text-teal-600 dark:hover:text-teal-400 transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>

            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                Manage Content: {{ $course->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                <!-- ================= LEFT SIDEBAR: CURRICULUM LIST ================= -->
                <div class="lg:col-span-4 xl:col-span-3">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 sticky top-6">
                        <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 rounded-t-xl">
                            <h3 class="font-bold text-base text-gray-700 dark:text-gray-300 uppercase tracking-widest">Course Syllabus</h3>
                        </div>

                        <div class="p-2 space-y-1 max-h-[calc(100vh-250px)] overflow-y-auto">
                            @foreach ($lessons as $lesson)
                                <a href="{{ route('courses.content', ['course' => $course->id, 'lesson' => $lesson->id]) }}"
                                   class="flex items-center gap-3 px-4 py-4 rounded-lg text-base font-medium transition-all group
                                      {{ $selectedLesson && $selectedLesson->id == $lesson->id 
                                        ? 'bg-teal-600 text-white shadow-md' 
                                        : 'text-gray-600 dark:text-gray-400 hover:bg-teal-50 dark:hover:bg-gray-700 hover:text-teal-600 dark:hover:text-teal-300' }}">
                                    
                                    <span class="w-7 h-7 flex-shrink-0 rounded bg-black/10 dark:bg-white/10 flex items-center justify-center text-xs font-black">
                                        {{ $lesson->order_number }}
                                    </span>
                                    
                                    <span class="truncate">{{ $lesson->title }}</span>

                                    @if($selectedLesson && $selectedLesson->id == $lesson->id)
                                        <svg class="w-5 h-5 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- ================= RIGHT SIDE: CONTENT EDITOR ================= -->
                <div class="lg:col-span-8 xl:col-span-9">
                    @if ($selectedLesson)
                        <div class="space-y-6">
                            
                            <!-- Header Card -->
                            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div>
                                    <span class="text-xs font-black uppercase text-teal-600 dark:text-teal-400 tracking-widest">Active Lesson</span>
                                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white leading-tight mt-1">
                                        {{ $selectedLesson->order_number }}. {{ $selectedLesson->title }}
                                    </h3>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('lessons.edit', $selectedLesson->id) }}"
                                       class="inline-flex items-center px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-base font-bold rounded-lg transition shadow-sm">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit Lesson
                                    </a>
                                </div>
                            </div>

                            @php
                                $videoFile = $selectedLesson->materials->firstWhere('file_type', 'video');
                                $videoUrlObj  = $selectedLesson->materials->firstWhere('file_type', 'video_url');
                                $pdf = $selectedLesson->materials->firstWhere('file_type', 'pdf');
                                $quiz = $selectedLesson->quiz;

                                // URL Converter Logic for YouTube and Vimeo
                                $finalUrl = null;
                                if ($videoUrlObj) {
                                    $url = $videoUrlObj->file_path;
                                    
                                    // YouTube conversion
                                    if (str_contains($url, 'youtube.com/watch?v=')) {
                                        $url = str_replace('watch?v=', 'embed/', $url);
                                        $url = explode('&', $url)[0];
                                    } elseif (str_contains($url, 'youtu.be/')) {
                                        $videoId = last(explode('/', $url));
                                        $url = "https://www.youtube.com/embed/" . $videoId;
                                    }
                                    
                                    // Vimeo conversion
                                    if (str_contains($url, 'vimeo.com/') && !str_contains($url, 'player.vimeo.com')) {
                                        $vimeoId = last(explode('/', $url));
                                        $url = "https://player.vimeo.com/video/" . $vimeoId;
                                    }
                                    
                                    $finalUrl = $url;
                                }
                            @endphp

                            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                                
                                <!-- Video Content Section -->
                                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                        <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l-4 4m0 0l-4-4m4 4V3m0 18a9 9 0 110-18 9 9 0 010 18z"/></svg>
                                        Video Content
                                    </h4>

                                    @if ($videoFile)
                                        <div class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600 bg-black aspect-video mb-4">
                                            <video controls class="w-full h-full">
                                                <source src="{{ asset('storage/' . $videoFile->file_path) }}">
                                            </video>
                                        </div>
                                        <form action="{{ route('materials.delete', $videoFile->id) }}" method="POST" onsubmit="return confirm('Delete this video file?')">
                                            @csrf @method('DELETE')
                                            <button class="text-sm font-bold text-red-600 hover:text-red-700 dark:text-red-400 flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Remove Video File
                                            </button>
                                        </form>
                                    @elseif ($finalUrl)
                                        <div class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-900 aspect-video mb-4">
                                            <iframe src="{{ $finalUrl }}" class="w-full h-full" allowfullscreen frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                                        </div>
                                        <form action="{{ route('materials.delete', $videoUrlObj->id) }}" method="POST" onsubmit="return confirm('Delete this video URL?')">
                                            @csrf @method('DELETE')
                                            <button class="text-sm font-bold text-red-600 hover:text-red-700 dark:text-red-400 flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Remove URL Link
                                            </button>
                                        </form>
                                    @else
                                        <div class="py-12 text-center border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg">
                                            <p class="text-gray-400 text-base font-medium">No video content assigned</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Documents & Assessment -->
                                <div class="space-y-6">
                                    <!-- PDF Section -->
                                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                                        <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            Reading Material (PDF)
                                        </h4>
                                        @if ($pdf)
                                            <div class="flex items-center justify-between p-5 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                                <div class="flex items-center gap-4">
                                                    <div class="px-2 py-1 bg-red-100 dark:bg-red-900/40 rounded text-red-600 text-xs font-black">PDF</div>
                                                    <span class="text-base font-bold text-gray-700 dark:text-gray-300 truncate max-w-[180px]">Lesson Notes</span>
                                                </div>
                                                <div class="flex items-center gap-4">
                                                    <a href="{{ asset('storage/' . $pdf->file_path) }}" target="_blank" class="text-sm font-black text-teal-600 hover:underline tracking-tight">VIEW</a>
                                                    <form action="{{ route('materials.delete', $pdf->id) }}" method="POST" onsubmit="return confirm('Delete notes?')">
                                                        @csrf @method('DELETE')
                                                        <button class="text-red-500 hover:text-red-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                                    </form>
                                                </div>
                                            </div>
                                        @else
                                            <p class="text-sm text-gray-500 italic">No notes uploaded for this module.</p>
                                        @endif
                                    </div>

                                    <!-- Quiz Section -->
                                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 border-l-4 border-indigo-500">
                                        <div class="flex items-center justify-between mb-4">
                                            <h4 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                                <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h2"/></svg>
                                                Knowledge Assessment
                                            </h4>
                                            @if ($quiz && $quiz->questions->count() > 0)
                                                <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-600 text-xs font-black uppercase rounded">Active</span>
                                            @endif
                                        </div>

                                        <p class="text-base text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
                                            @if ($quiz && $quiz->questions->count() > 0)
                                                This lesson includes a quiz with <strong>{{ $quiz->questions->count() }}</strong> questions to verify student learning.
                                            @else
                                                Enhance this lesson by adding a quiz to challenge your students.
                                            @endif
                                        </p>

                                        <a href="{{ route('lessons.quiz.editor', $selectedLesson->id) }}"
                                           class="inline-flex w-full items-center justify-center px-4 py-3.5 border-2 border-indigo-500 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 text-sm font-black uppercase tracking-widest rounded-xl transition">
                                            {{ ($quiz && $quiz->questions->count() > 0) ? 'Manage Quiz Questions' : 'Configure Quiz' }}
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @else
                        <!-- No selection state -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 py-24 text-center">
                            <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Curriculum Management</h3>
                            <p class="text-base text-gray-500 dark:text-gray-400 max-w-md mx-auto leading-relaxed">Please select a lesson from the syllabus sidebar to manage its educational materials and assessment settings.</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>