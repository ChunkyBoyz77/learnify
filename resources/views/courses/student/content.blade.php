<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            {{-- Back Button --}}
            <a href="{{ route('student.mycourses') }}"
               class="flex items-center text-gray-600 dark:text-gray-400
                      hover:text-teal-600 dark:hover:text-teal-400 transition-colors duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>

            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight truncate max-w-xl">
                {{ $course->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                {{-- ================= LEFT SIDEBAR: CURRICULUM ================= --}}
                <div class="lg:col-span-4 xl:col-span-3">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 sticky top-6 overflow-hidden">
                        <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
                            <h3 class="font-bold text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Course Syllabus</h3>
                        </div>

                        <div class="p-2 space-y-1 max-h-[calc(100vh-200px)] overflow-y-auto">
                            @foreach ($lessons as $lesson)
                                <a href="{{ route('student.course.content', ['course' => $course->id, 'lesson' => $lesson->id]) }}"
                                   class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-all group
                                      {{ $selectedLesson && $selectedLesson->id == $lesson->id 
                                        ? 'bg-teal-600 text-white shadow-sm' 
                                        : 'text-gray-600 dark:text-gray-400 hover:bg-teal-50 dark:hover:bg-gray-700 hover:text-teal-600 dark:hover:text-teal-300' }}">
                                    
                                    <span class="w-6 h-6 flex-shrink-0 rounded-md bg-black/5 dark:bg-white/5 flex items-center justify-center text-[10px] font-bold">
                                        {{ $lesson->order_number }}
                                    </span>
                                    
                                    <span class="truncate flex-grow leading-tight">{{ $lesson->title }}</span>

                                    @if($selectedLesson && $selectedLesson->id == $lesson->id)
                                        <div class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- ================= RIGHT CONTENT AREA ================= --}}
                <div class="lg:col-span-8 xl:col-span-9">
                    @if ($selectedLesson)
                        <div class="space-y-6">
                            
                            {{-- LESSON HEADER --}}
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 border-l-4 border-teal-500">
                                <span class="text-[10px] font-bold uppercase text-teal-600 dark:text-teal-400 tracking-wider block mb-1">Module {{ $selectedLesson->order_number }}</span>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white leading-tight">
                                    {{ $selectedLesson->title }}
                                </h3>
                            </div>

                            @php
                                $video = $selectedLesson->materials->firstWhere('file_type', 'video');
                                $videoUrlObj = $selectedLesson->materials->firstWhere('file_type', 'video_url');
                                $pdf = $selectedLesson->materials->firstWhere('file_type', 'pdf');
                                
                                // Automatic Embed Converter
                                $finalUrl = null;
                                if ($videoUrlObj) {
                                    $url = $videoUrlObj->file_path;
                                    if (str_contains($url, 'youtube.com/watch?v=')) {
                                        $url = str_replace('watch?v=', 'embed/', $url);
                                        $url = explode('&', $url)[0];
                                    } elseif (str_contains($url, 'youtu.be/')) {
                                        $videoId = last(explode('/', $url));
                                        $url = "https://www.youtube.com/embed/" . $videoId;
                                    }
                                    if (str_contains($url, 'vimeo.com/') && !str_contains($url, 'player.vimeo.com')) {
                                        $vimeoId = last(explode('/', $url));
                                        $url = "https://player.vimeo.com/video/" . $vimeoId;
                                    }
                                    $finalUrl = $url;
                                }
                            @endphp

                            {{-- 1. VIDEO SECTION BOX --}}
                            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-10 h-10 rounded-lg bg-teal-50 dark:bg-teal-900/20 flex items-center justify-center text-teal-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-bold text-gray-900 dark:text-white">Lecture Video</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Watch the session for this module.</p>
                                    </div>
                                </div>

                                <div class="max-w-2xl mx-auto w-full">
                                    <div class="bg-black rounded-xl overflow-hidden shadow-lg border border-gray-200 dark:border-gray-700 aspect-video relative">
                                        @if($video)
                                            <video controls class="w-full h-full object-contain">
                                                <source src="{{ asset('storage/' . $video->file_path) }}">
                                            </video>
                                        @elseif($finalUrl)
                                            <iframe src="{{ $finalUrl }}" class="w-full h-full" allowfullscreen frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                                        @else
                                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-500 space-y-3">
                                                <svg class="w-12 h-12 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l-4 4m0 0l-4-4m4 4V3m0 18a9 9 0 110-18 9 9 0 010 18z"/>
                                                </svg>
                                                <p class="font-bold uppercase text-[10px] tracking-widest">No Video Content Available</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- 2. NOTES SECTION --}}
                            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-10 h-10 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center justify-center text-red-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-bold text-gray-900 dark:text-white">Lesson Notes</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Supplementary study materials for this module.</p>
                                    </div>
                                </div>

                                @if($pdf)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-700">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Module Documentation (PDF)</span>
                                        <a href="{{ asset('storage/' . $pdf->file_path) }}" target="_blank"
                                           class="inline-flex items-center justify-center bg-gray-800 dark:bg-gray-700 text-white font-bold uppercase text-[10px] tracking-wider px-4 py-2 rounded-lg transition-all hover:bg-gray-700 dark:hover:bg-gray-600 active:scale-95">
                                            Download PDF
                                            <svg class="w-3.5 h-3.5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        </a>
                                    </div>
                                @else
                                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-dashed border-gray-200 dark:border-gray-700 text-center">
                                        <p class="text-xs text-gray-400 italic">No notes have been uploaded for this lesson yet.</p>
                                    </div>
                                @endif
                            </div>

                            {{-- 3. QUIZ SECTION --}}
                            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm border-b-4 border-indigo-500">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h2"/></svg>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-bold text-gray-900 dark:text-white">Module Assessment</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Verify your understanding of the material.</p>
                                    </div>
                                </div>

                                @if($selectedLesson->quiz && $selectedLesson->quiz->questions->count() > 0)
                                    <div class="p-4 bg-indigo-50/50 dark:bg-indigo-900/10 rounded-lg border border-indigo-100 dark:border-indigo-800 flex items-center justify-between">
                                        <p class="text-sm font-medium text-indigo-800 dark:text-indigo-300">The quiz is ready. Good luck!</p>
                                        <a href="{{ route('quiz.take', $selectedLesson->id) }}"
                                           class="inline-flex items-center justify-center bg-teal-600 hover:bg-teal-700 text-white font-bold uppercase text-[10px] tracking-wider px-6 py-2.5 rounded-lg transition-all shadow-sm active:scale-95">
                                            Start Quiz
                                            <svg class="w-3.5 h-3.5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                        </a>
                                    </div>
                                @else
                                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-dashed border-gray-200 dark:border-gray-700 text-center">
                                        <p class="text-xs text-gray-400 italic">No quiz is available for this lesson at this time.</p>
                                    </div>
                                @endif
                            </div>

                        </div>
                    @else
                        {{-- SELECT LESSON STATE --}}
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 py-24 text-center">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Ready to start?</h3>
                            <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto text-sm">Select a lesson from the sidebar to begin your learning journey.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>