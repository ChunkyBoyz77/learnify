<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('courses.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                    ←
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('New Courses Form') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Error Message -->
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <form method="POST" action="{{ route('courses.store') }}" id="courseForm">
                        @csrf

                        <!-- Display Validation Errors -->
                        @if ($errors->any())
                            <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                            Please correct the following errors:
                                        </h3>
                                        <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                            <ul class="list-disc list-inside space-y-1">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Left Column - Course Details -->
                            <div class="space-y-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Course Details</h3>
                                
                                <!-- Course Title -->
                                <div>
                                    <x-input-label for="title" :value="__('Course Title')" />
                                    <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" placeholder="eg: Python Programming" :value="old('title')" required autofocus />
                                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                                </div>

                                <!-- Course Description -->
                                <div>
                                    <x-input-label for="description" :value="__('Course Description')" />
                                    <textarea id="description" name="description" rows="4" placeholder="eg: Learn Python coding" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 dark:focus:border-teal-600 focus:ring-teal-500 dark:focus:ring-teal-600 rounded-md shadow-sm">{{ old('description') }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>

                                <!-- What You Will Learn -->
                                <div>
                                    <x-input-label for="what_you_will_learn" :value="__('What you will learn')" />
                                    <textarea id="what_you_will_learn" name="what_you_will_learn" rows="4" placeholder="eg: Python Programming, Google Collab" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 dark:focus:border-teal-600 focus:ring-teal-500 dark:focus:ring-teal-600 rounded-md shadow-sm">{{ old('what_you_will_learn') }}</textarea>
                                    <x-input-error :messages="$errors->get('what_you_will_learn')" class="mt-2" />
                                </div>

                                <!-- Skills You Gain -->
                                <div>
                                    <x-input-label for="skills_gain" :value="__('Skill you gain')" />
                                    <textarea id="skills_gain" name="skills_gain" rows="4" placeholder="eg: Python Programming, Google Collab" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 dark:focus:border-teal-600 focus:ring-teal-500 dark:focus:ring-teal-600 rounded-md shadow-sm">{{ old('skills_gain') }}</textarea>
                                    <x-input-error :messages="$errors->get('skills_gain')" class="mt-2" />
                                </div>

                                <!-- Module/Assessment -->
                                <div>
                                    <x-input-label for="assessment_info" :value="__('Module/Assessment')" />
                                    <x-text-input id="assessment_info" class="block mt-1 w-full" type="text" name="assessment_info" placeholder="eg: 5 quiz, 5 assignment" :value="old('assessment_info')" />
                                    <x-input-error :messages="$errors->get('assessment_info')" class="mt-2" />
                                </div>

                                <!-- Duration -->
                                <div>
                                    <x-input-label for="duration" :value="__('Duration')" />
                                    <x-text-input id="duration" class="block mt-1 w-full" type="text" name="duration" placeholder="eg: 6 weeks" :value="old('duration', '6 weeks')" />
                                    <x-input-error :messages="$errors->get('duration')" class="mt-2" />
                                </div>

                                <!-- Price (RM) -->
                                <div>
                                    <x-input-label for="price" :value="__('Price (RM)')" />
                                    <x-text-input id="price" class="block mt-1 w-full" type="number" name="price" step="0.01" min="0" placeholder="50.00" :value="old('price', 50.00)" required />
                                    <x-input-error :messages="$errors->get('price')" class="mt-2" />
                                </div>

                                <!-- Level -->
                                <div>
                                    <x-input-label for="level" :value="__('Level')" />
                                    <select id="level" name="level" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 dark:focus:border-teal-600 focus:ring-teal-500 dark:focus:ring-teal-600 rounded-md shadow-sm">
                                        <option value="Beginner" {{ old('level', 'Beginner') == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                                        <option value="Intermediate" {{ old('level') == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                                        <option value="Advanced" {{ old('level') == 'Advanced' ? 'selected' : '' }}>Advanced</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('level')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Right Column - Module Details -->
                            <div class="space-y-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Module Details</h3>
                                
                                <!-- Module Title -->
                                <div>
                                    <x-input-label for="module_title" :value="__('Module Title')" />
                                    <x-text-input id="module_title" class="block mt-1 w-full" type="text" placeholder="eg: Python Programming" />
                                </div>

                                <!-- Module Description -->
                                <div>
                                    <x-input-label for="module_description" :value="__('Description')" />
                                    <textarea id="module_description" rows="4" placeholder="eg: Python Programming" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 dark:focus:border-teal-600 focus:ring-teal-500 dark:focus:ring-teal-600 rounded-md shadow-sm"></textarea>
                                </div>

                                <!-- Add Module Button -->
                                <div>
                                    <button type="button" id="addModuleBtn" class="w-full px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-md transition-colors">
                                        Add Module
                                    </button>
                                </div>

                                <!-- Modules List -->
                                <div id="modulesList" class="space-y-4 mt-6">
                                    <!-- Modules will be added here dynamically -->
                                </div>

                                <!-- Hidden input to store modules JSON -->
                                <input type="hidden" name="modules" id="modulesInput" value="">
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-8 flex items-center justify-end space-x-4">
                            <a href="{{ route('courses.index') }}" class="px-6 py-2 bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-400 dark:hover:bg-gray-600 transition-colors">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="px-8 py-2 text-lg bg-gradient-to-r from-teal-600 to-cyan-600 hover:from-teal-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                                {{ __('ADD') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables accessible to all functions
        let moduleCounter = 0;
        const modules = [];

        document.addEventListener('DOMContentLoaded', function() {
            const addModuleBtn = document.getElementById('addModuleBtn');
            if (addModuleBtn) {
                addModuleBtn.addEventListener('click', function() {
                    const titleInput = document.getElementById('module_title');
                    const descriptionInput = document.getElementById('module_description');
                    
                    const title = titleInput ? titleInput.value.trim() : '';
                    const description = descriptionInput ? descriptionInput.value.trim() : '';

                    if (!title) {
                        alert('Please enter a module title');
                        return;
                    }

                    const module = {
                        id: moduleCounter++,
                        title: title,
                        description: description
                    };

                    modules.push(module);

                    // Add to list
                    const modulesList = document.getElementById('modulesList');
                    if (modulesList) {
                        const moduleDiv = document.createElement('div');
                        moduleDiv.className = 'bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600';
                        moduleDiv.id = `module-${module.id}`;
                        moduleDiv.innerHTML = `
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100">${module.title}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">${module.description || 'No description'}</p>
                                </div>
                                <button type="button" onclick="removeModule(${module.id})" class="ml-4 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        `;
                        modulesList.appendChild(moduleDiv);
                    }

                    // Clear inputs
                    if (titleInput) titleInput.value = '';
                    if (descriptionInput) descriptionInput.value = '';

                    // Update hidden input
                    updateModulesInput();
                });
            }

            // Handle form submission
            const courseForm = document.getElementById('courseForm');
            if (courseForm) {
                courseForm.addEventListener('submit', function(e) {
                    updateModulesInput();
                });
            }
        });

        // Global function accessible from onclick
        function removeModule(id) {
            const index = modules.findIndex(m => m.id === id);
            if (index > -1) {
                modules.splice(index, 1);
                const moduleElement = document.getElementById(`module-${id}`);
                if (moduleElement) {
                    moduleElement.remove();
                }
                updateModulesInput();
            }
        }

        function updateModulesInput() {
            const modulesInput = document.getElementById('modulesInput');
            if (modulesInput) {
                modulesInput.value = JSON.stringify(modules);
            }
        }
    </script>
</x-app-layout>

