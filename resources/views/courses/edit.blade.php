<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit Course') }}
            </h2>
            <a href="{{ route('courses.show', $course) }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                ← Back to Course
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('courses.update', $course) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Title -->
                        <div>
                            <x-input-label for="title" :value="__('Course Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $course->title)" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="5" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 dark:focus:border-teal-600 focus:ring-teal-500 dark:focus:ring-teal-600 rounded-md shadow-sm">{{ old('description', $course->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- What You Will Learn -->
                        <div>
                            <x-input-label for="what_you_will_learn" :value="__('What You Will Learn')" />
                            <textarea id="what_you_will_learn" name="what_you_will_learn" rows="4" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 dark:focus:border-teal-600 focus:ring-teal-500 dark:focus:ring-teal-600 rounded-md shadow-sm">{{ old('what_you_will_learn', $course->what_you_will_learn) }}</textarea>
                            <x-input-error :messages="$errors->get('what_you_will_learn')" class="mt-2" />
                        </div>

                        <!-- Skills Gain -->
                        <div>
                            <x-input-label for="skills_gain" :value="__('Skills You Will Gain')" />
                            <textarea id="skills_gain" name="skills_gain" rows="4" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 dark:focus:border-teal-600 focus:ring-teal-500 dark:focus:ring-teal-600 rounded-md shadow-sm">{{ old('skills_gain', $course->skills_gain) }}</textarea>
                            <x-input-error :messages="$errors->get('skills_gain')" class="mt-2" />
                        </div>

                        <!-- Assessment Info -->
                        <div>
                            <x-input-label for="assessment_info" :value="__('Assessment Information')" />
                            <textarea id="assessment_info" name="assessment_info" rows="4" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 dark:focus:border-teal-600 focus:ring-teal-500 dark:focus:ring-teal-600 rounded-md shadow-sm">{{ old('assessment_info', $course->assessment_info) }}</textarea>
                            <x-input-error :messages="$errors->get('assessment_info')" class="mt-2" />
                        </div>

                        <!-- Price and Duration Row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Price -->
                            <div>
                                <x-input-label for="price" :value="__('Price ($)')" />
                                <x-text-input id="price" class="block mt-1 w-full" type="number" name="price" step="0.01" min="0" :value="old('price', $course->price)" required />
                                <x-input-error :messages="$errors->get('price')" class="mt-2" />
                            </div>

                            <!-- Duration -->
                            <div>
                                <x-input-label for="duration" :value="__('Duration (e.g., 4 weeks, 2 months)')" />
                                <x-text-input id="duration" class="block mt-1 w-full" type="text" name="duration" :value="old('duration', $course->duration)" />
                                <x-input-error :messages="$errors->get('duration')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Level -->
                        <div>
                            <x-input-label for="level" :value="__('Course Level')" />
                            <select id="level" name="level" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-teal-500 dark:focus:border-teal-600 focus:ring-teal-500 dark:focus:ring-teal-600 rounded-md shadow-sm">
                                <option value="">Select Level</option>
                                <option value="Beginner" {{ old('level', $course->level) == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="Intermediate" {{ old('level', $course->level) == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="Advanced" {{ old('level', $course->level) == 'Advanced' ? 'selected' : '' }}>Advanced</option>
                            </select>
                            <x-input-error :messages="$errors->get('level')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('courses.show', $course) }}" class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-400 dark:hover:bg-gray-600 transition-colors">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Update Course') }}
                            </x-primary-button>
                        </div>
                    </form>

                    <!-- Delete Course Form -->
                    <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Danger Zone</h3>
                        <form method="POST" action="{{ route('courses.destroy', $course) }}" onsubmit="return confirm('Are you sure you want to delete this course? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <x-danger-button>
                                {{ __('Delete Course') }}
                            </x-danger-button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

