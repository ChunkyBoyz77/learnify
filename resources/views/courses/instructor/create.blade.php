<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <!-- Back Button -->
             <a href="{{ route('courses.my') }}"
               class="flex items-center text-gray-600 dark:text-gray-400
                      hover:text-teal-600 dark:hover:text-teal-400 transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>

            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Create New Course') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Error Message Alert -->
            <div id="formError"
                 class="hidden mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800
                        text-red-700 dark:text-red-400 px-4 py-3 rounded-xl shadow-sm flex items-center gap-3">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">Please fill in all required fields marked with (*).</span>
            </div>

            <form id="courseForm"
                  action="{{ route('courses.store') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  novalidate
                  class="space-y-8">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                    <!-- ================= LEFT: COURSE DETAILS ================= -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-teal-100 dark:border-gray-700 p-8">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="p-2 bg-teal-100 dark:bg-teal-900/50 rounded-lg">
                                <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">Course Information</h3>
                        </div>

                        <!-- Course Title -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Course Title <span class="text-red-500 ml-1">*</span></label>
                            <div class="relative group">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-teal-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </span>
                                <input type="text" 
                                       name="title" 
                                       placeholder="e.g. Master Software Quality Assurance" 
                                       class="required-field w-full pl-11 pr-4 py-2.5 rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all outline-none">
                            </div>
                        </div>

                        <!-- Description Area -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                Course Description <span class="text-red-500">*</span>
                            </label>
                            <div class="relative group">
                                <span class="absolute left-4 top-4 text-gray-400 group-focus-within:text-teal-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                                    </svg>
                                </span>
                                <textarea name="description" rows="4" placeholder="Provide a detailed overview of the course content..."
                                          class="required-field w-full pl-11 pr-4 py-3 rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all outline-none"></textarea>
                            </div>
                        </div>

                        <!-- What You Will Learn -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">What You Will Learn <span class="text-red-500 ml-1">*</span></label>
                            <div class="relative group">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-teal-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                                </span>
                                <input type="text" 
                                       name="what_you_will_learn" 
                                       placeholder="Key outcomes separated by commas" 
                                       class="required-field w-full pl-11 pr-4 py-2.5 rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all outline-none">
                            </div>
                        </div>

                        <!-- Skills Gained -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Skills Gained <span class="text-red-500 ml-1">*</span></label>
                            <div class="relative group">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-teal-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </span>
                                <input type="text" 
                                       name="skills_gain" 
                                       placeholder="e.g. Critical Thinking, Automation" 
                                       class="required-field w-full pl-11 pr-4 py-2.5 rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all outline-none">
                            </div>
                        </div>

                        <!-- Assessment Info -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Assessment Info</label>
                            <div class="relative group">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-teal-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                </span>
                                <input type="text" 
                                       name="assessment_info" 
                                       placeholder="Quizzes, Exams, Assignments" 
                                       class="w-full pl-11 pr-4 py-2.5 rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all outline-none">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Duration -->
                            <div class="mb-6">
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Duration <span class="text-red-500 ml-1">*</span></label>
                                <div class="relative group">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-teal-500 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </span>
                                    <input type="text" 
                                           name="duration" 
                                           placeholder="e.g. 12 Hours" 
                                           class="required-field w-full pl-11 pr-4 py-2.5 rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all outline-none">
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="mb-6">
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Price (RM) <span class="text-red-500 ml-1">*</span></label>
                                <div class="relative group">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-teal-500 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </span>
                                    <input type="number" 
                                           name="price" 
                                           placeholder="0.00" 
                                           class="required-field w-full pl-11 pr-4 py-2.5 rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- Level -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                Course Level <span class="text-red-500">*</span>
                            </label>
                            <div class="relative group">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-teal-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                </span>
                                <select name="level" 
                                        class="required-field w-full pl-11 pr-4 py-2.5 rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 appearance-none transition-all outline-none">
                                    <option value="">Select Level</option>
                                    <option value="Beginner">Beginner</option>
                                    <option value="Intermediate">Intermediate</option>
                                    <option value="Advanced">Advanced</option>
                                </select>
                            </div>
                        </div>

                        <!-- Image Upload -->
                        <div class="mb-2">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                Thumbnail Image <span class="text-red-500">*</span>
                            </label>
                            <div id="image-dropzone" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-700 border-dashed rounded-xl hover:border-teal-500 transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                        <label class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-teal-600 hover:text-teal-500">
                                            <span>Upload a file</span>
                                            <input type="file" name="image" class="required-field sr-only">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG up to 10MB</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ================= RIGHT: LESSON DETAILS ================= -->
                    <div class="space-y-6">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-teal-100 dark:border-gray-700 p-8 h-fit">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="p-2 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">Course Lesson</h3>
                            </div>

                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                                Start by defining the module titles. You can add more detailed content to these modules later. <span class="text-red-500 font-bold">*At least one module is required.</span>
                            </p>

                            <div id="lessons-container" class="space-y-4">
                                <div class="relative group">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-teal-500 font-bold">1</span>
                                    <input type="text" 
                                           name="lessons[0][title]" 
                                           placeholder="e.g. Introduction to SQA" 
                                           class="required-field w-full pl-11 pr-4 py-3 rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all outline-none">
                                </div>
                            </div>

                            <button type="button" 
                                    onclick="addLesson()"
                                    class="mt-6 w-full rounded-xl border-2 border-dashed border-teal-600 text-teal-600 dark:text-teal-400 font-bold hover:bg-teal-50 dark:hover:bg-teal-900/20 transition-all py-3 flex items-center justify-center gap-2 group">
                                <svg class="w-5 h-5 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Add Module
                            </button>
                        </div>

                        <!-- Final Submit Card -->
                        <div class="bg-teal-600 dark:bg-teal-700 rounded-2xl shadow-xl p-8 text-white">
                            <h4 class="font-bold text-lg mb-2">Ready to Publish?</h4>
                            <p class="text-teal-100 text-sm mb-6">Ensure all information is accurate.</p>
                            <button type="submit" 
                                    class="w-full bg-white text-teal-700 font-black py-4 rounded-xl shadow-lg hover:bg-teal-50 transition-colors transform active:scale-95 duration-150 uppercase tracking-widest text-sm">
                                Create Course
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        let lessonCount = 1;
        const form = document.getElementById('courseForm');
        const errorBox = document.getElementById('formError');

        /**
         * Logic to update UI styles based on field validity
         */
        function updateFieldState(field) {
            const isFile = field.type === 'file';
            const value = isFile ? field.files.length > 0 : field.value.trim() !== '';
            const targetEl = isFile ? document.getElementById('image-dropzone') : field;

            if (value) {
                // Success: Green State
                targetEl.classList.remove('border-red-500', 'ring-red-500', 'border-gray-300', 'dark:border-gray-700');
                targetEl.classList.add('border-green-500', 'ring-1', 'ring-green-500');
            } else {
                // Failure: Red State (Only if form was attempted or box already has an error state)
                if (!errorBox.classList.contains('hidden') || targetEl.classList.contains('border-red-500')) {
                    targetEl.classList.remove('border-green-500', 'ring-green-500', 'border-gray-300', 'dark:border-gray-700');
                    targetEl.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                }
            }
        }

        // Live validation listeners via delegation
        form.addEventListener('input', (e) => {
            if (e.target.classList.contains('required-field')) {
                updateFieldState(e.target);
            }
        });

        form.addEventListener('change', (e) => {
            if (e.target.classList.contains('required-field')) {
                updateFieldState(e.target);
            }
        });

        form.addEventListener('submit', function (e) {
            let valid = true;
            errorBox.classList.add('hidden');

            document.querySelectorAll('.required-field').forEach(field => {
                const isFile = field.type === 'file';
                const hasValue = isFile ? field.files.length > 0 : field.value.trim() !== '';
                const targetEl = isFile ? document.getElementById('image-dropzone') : field;

                if (!hasValue) {
                    targetEl.classList.remove('border-green-500', 'ring-green-500', 'border-gray-300', 'dark:border-gray-700');
                    targetEl.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                    valid = false;
                } else {
                    targetEl.classList.remove('border-red-500', 'ring-red-500', 'border-gray-300', 'dark:border-gray-700');
                    targetEl.classList.add('border-green-500', 'ring-1', 'ring-green-500');
                }
            });

            if (!valid) {
                e.preventDefault();
                errorBox.classList.remove('hidden');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });

        function addLesson() {
            const index = lessonCount++;
            const container = document.getElementById('lessons-container');
            const newLesson = `
                <div class="relative group opacity-0 translate-y-2 transition-all duration-300 animate-in" id="lesson-row-${index}">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-teal-500 font-bold">${index + 1}</span>
                    <input type="text" 
                           name="lessons[${index}][title]" 
                           placeholder="Module title (optional)" 
                           class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all outline-none">
                </div>
            `;
            container.insertAdjacentHTML('beforeend', newLesson);
            
            // Smooth entrance animation
            setTimeout(() => {
                const el = document.getElementById(`lesson-row-${index}`);
                if (el) {
                    el.classList.remove('opacity-0', 'translate-y-2');
                }
            }, 10);
        }
    </script>

    <style>
        /* Styling custom file upload area */
        input[type="file"]::file-selector-button {
            display: none;
        }
        
        .animate-in {
            transition: all 0.3s ease-out;
        }

        /* Focus styles for the custom input wrapper */
        .group:focus-within svg {
            color: #0d9488; /* teal-600 */
        }
    </style>
</x-app-layout>