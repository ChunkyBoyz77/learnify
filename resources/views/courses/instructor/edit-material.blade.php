<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            {{-- Back Button --}}
            <a href="{{ route('courses.content', ['course' => $lesson->course_id, 'lesson' => $lesson->id]) }}"
               class="flex items-center text-gray-600 dark:text-gray-400
                      hover:text-teal-600 dark:hover:text-teal-400 transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>

            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Material: {{ $lesson->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 bg-teal-50 dark:bg-teal-900/20 border border-teal-200 dark:border-teal-800 text-teal-700 dark:text-teal-400 px-6 py-4 rounded-2xl shadow-sm flex items-center gap-3">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('lessons.update', $lesson->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                @php
                    $video = $materials->firstWhere('file_type', 'video');
                    $videoUrl = $materials->firstWhere('file_type', 'video_url');
                    $pdf = $materials->firstWhere('file_type', 'pdf');
                @endphp

                <!-- ================= VIDEO MATERIAL SECTION ================= -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 flex items-center gap-3">
                        <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l-4 4m0 0l-4-4m4 4V3m0 18a9 9 0 110-18 9 9 0 010 18z"/></svg>
                        <h3 class="font-bold text-lg text-gray-800 dark:text-gray-200">Video Content</h3>
                    </div>

                    <div class="p-8 space-y-8">
                        <!-- Current Status -->
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-indigo-50/50 dark:bg-indigo-900/10 border border-indigo-100 dark:border-indigo-900/30">
                            <div class="mt-1">
                                @if($video || $videoUrl)
                                    <span class="flex h-3 w-3 relative">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full h-3 w-3 bg-gray-400"></span>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Current Source</p>
                                @if($video)
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-600 dark:text-gray-400 text-base">File: <span class="font-mono text-xs">storage/{{ basename($video->file_path) }}</span></span>
                                        <a class="text-teal-600 dark:text-teal-400 font-bold hover:underline" href="{{ asset('storage/'.$video->file_path) }}" target="_blank">Preview</a>
                                    </div>
                                @elseif($videoUrl)
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-600 dark:text-gray-400 text-base">YouTube URL: <span class="truncate max-w-xs inline-block align-bottom font-mono text-xs">{{ $videoUrl->file_path }}</span></span>
                                        <a class="text-teal-600 dark:text-teal-400 font-bold hover:underline" href="{{ $videoUrl->file_path }}" target="_blank">Open</a>
                                    </div>
                                @else
                                    <p class="text-gray-500 italic text-base">No video linked to this lesson yet.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Upload Inputs -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="block text-base font-bold text-gray-700 dark:text-gray-300">Option A: Upload Video File</label>
                                <div class="relative group">
                                    <input type="file" name="video_file" 
                                           class="block w-full text-sm text-gray-500 dark:text-gray-400
                                                  file:mr-4 file:py-2.5 file:px-4
                                                  file:rounded-xl file:border-0
                                                  file:text-sm file:font-bold
                                                  file:bg-teal-50 file:text-teal-700
                                                  dark:file:bg-teal-900/30 dark:file:text-teal-400
                                                  hover:file:bg-teal-100 transition-all
                                                  border border-gray-200 dark:border-gray-700 rounded-2xl p-2 bg-gray-50 dark:bg-gray-900">
                                </div>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest pl-2">MP4, WebM preferred (Max 100MB)</p>
                            </div>

                            <div class="space-y-3">
                                <label class="block text-base font-bold text-gray-700 dark:text-gray-300">Option B: YouTube / Vimeo URL</label>
                                <div class="relative group">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-teal-500 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.826a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                    </span>
                                    <input type="text" name="video_url" placeholder="https://youtube.com/watch?v=..."
                                           class="w-full pl-12 pr-4 py-3 rounded-2xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all outline-none">
                                </div>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest pl-2">Embed links are processed automatically</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================= DOCUMENT SECTION ================= -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 flex items-center gap-3">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <h3 class="font-bold text-lg text-gray-800 dark:text-gray-200">Supporting Notes (PDF)</h3>
                    </div>

                    <div class="p-8 space-y-6">
                        @if($pdf)
                            <div class="flex items-center justify-between p-5 bg-red-50/50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/30 rounded-2xl">
                                <div class="flex items-center gap-4">
                                    <div class="p-3 bg-red-100 dark:bg-red-900/50 rounded-xl text-red-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-gray-700 dark:text-gray-300 uppercase tracking-tighter">Current Notes File</p>
                                        <p class="text-xs text-gray-500 truncate max-w-xs font-mono">storage/{{ basename($pdf->file_path) }}</p>
                                    </div>
                                </div>
                                <a href="{{ asset('storage/'.$pdf->file_path) }}" target="_blank" 
                                   class="px-5 py-2 bg-white dark:bg-gray-700 text-red-600 dark:text-red-400 font-bold text-xs rounded-xl shadow-sm border border-red-100 dark:border-red-800 hover:bg-red-50 transition-colors uppercase tracking-widest">
                                    Download
                                </a>
                            </div>
                        @endif

                        <div class="space-y-3">
                            <label class="block text-base font-bold text-gray-700 dark:text-gray-300">Replace or Add Notes</label>
                            <input type="file" name="note_file" accept="application/pdf"
                                   class="block w-full text-sm text-gray-500 dark:text-gray-400
                                          file:mr-4 file:py-2.5 file:px-4
                                          file:rounded-xl file:border-0
                                          file:text-sm file:font-bold
                                          file:bg-teal-50 file:text-teal-700
                                          dark:file:bg-teal-900/30 dark:file:text-teal-400
                                          hover:file:bg-teal-100 transition-all
                                          border border-gray-200 dark:border-gray-700 rounded-2xl p-2 bg-gray-50 dark:bg-gray-900">
                        </div>
                    </div>
                </div>


                <!-- ================= FORM ACTIONS ================= -->
                <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-800">
                    <a href="{{ route('courses.content', ['course' => $lesson->course_id, 'lesson' => $lesson->id]) }}"
                       class="px-6 py-3 text-sm font-bold text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 uppercase tracking-widest">
                        Discard Changes
                    </a>
                    <button type="submit"
                            class="px-10 py-4 bg-teal-600 hover:bg-teal-700 text-white font-black uppercase text-sm tracking-[0.2em] rounded-2xl shadow-xl shadow-teal-500/20 transition-all transform hover:-translate-y-1 active:scale-95">
                        Update Material
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>