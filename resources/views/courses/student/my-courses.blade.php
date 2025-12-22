<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight tracking-tight">
                {{ __('My Learning Journey') }}
            </h2>
            <div class="hidden sm:flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <svg class="w-4 h-4 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>{{ $courses->count() }} Enrolled Courses</span>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($courses->count() === 0)
                {{-- EMPTY STATE --}}
                <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-200 dark:border-gray-700 p-12 text-center shadow-sm">
                    <div class="w-24 h-24 bg-teal-50 dark:bg-teal-900/20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 tracking-tighter uppercase">No Enrollments Found</h3>
                    <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto mb-8 text-base">
                        Your learning library is empty. Discover high-quality courses and start building your skills today.
                    </p>
                    <a href="{{ route('courses.index') }}"
                       class="inline-flex items-center px-8 py-3.5 bg-teal-600 hover:bg-teal-700 text-white font-black uppercase text-xs tracking-widest rounded-2xl shadow-xl shadow-teal-500/20 transition-all transform active:scale-95">
                        Explore Courses
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            @else

                {{-- COURSE GRID --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($courses as $course)
                        <div class="group bg-white dark:bg-gray-800 rounded-[2rem] shadow-sm hover:shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 flex flex-col h-full relative">
                            
                            {{-- THUMBNAIL --}}
                            <div class="relative h-52 overflow-hidden">
                                @if($course->image)
                                    <img src="{{ asset('storage/' . $course->image) }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-90 group-hover:opacity-100">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-teal-500 via-indigo-600 to-cyan-600 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-white opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    </div>
                                @endif

                                {{-- FLOATING OVERLAY INFO --}}
                                <div class="absolute bottom-4 left-4 right-4 flex justify-between items-center">
                                    <span class="px-3 py-1 bg-white/10 backdrop-blur-md text-white text-[10px] font-black uppercase tracking-widest rounded-lg border border-white/20">
                                        {{ $course->level ?? 'Standard' }}
                                    </span>
                                </div>
                            </div>

                            {{-- CONTENT --}}
                            <div class="p-8 flex flex-col flex-grow">
                                <div class="flex-grow">
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 leading-tight mb-3 tracking-tight group-hover:text-teal-600 transition-colors">
                                        {{ $course->title }}
                                    </h3>

                                    <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed line-clamp-3 mb-6">
                                        {{ $course->description }}
                                    </p>
                                </div>

                                {{-- ACTION AREA --}}
                                <div class="pt-6 border-t border-gray-100 dark:border-gray-700">
                                    <a href="{{ route('student.course.content', $course->id) }}"
                                       class="flex items-center justify-center w-full bg-teal-600 hover:bg-teal-700 text-white font-black uppercase text-xs tracking-widest py-3.5 rounded-xl transition-all shadow-lg shadow-teal-500/10 active:scale-95">
                                        Continue Learning
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>
                                </div>
                            </div>

                            {{-- PROGRESS BORDER ACCENT (Visual only for now) --}}
                            <div class="absolute bottom-0 left-0 h-1 bg-teal-500 transition-all duration-500" style="width: 100%"></div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>