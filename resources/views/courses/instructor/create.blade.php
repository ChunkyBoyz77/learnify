<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ url()->previous() }}" class="text-gray-600 hover:text-teal-600 dark:text-gray-300">
                ←
            </a>
            <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100 leading-tight">
                New Course Form
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto px-4">

            <form action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                    <!-- ================= LEFT SIDE — COURSE DETAILS ================= -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-teal-200">

                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Course Information</h3>

                        <!-- Course Title -->
                        <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Course Title</label>
                        <input type="text" name="title" placeholder="e.g. Python Programming"
                               class="w-full mb-4 px-4 py-2 rounded-lg border-gray-300" required>

                        <!-- Course Description -->
                        <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Course Description</label>
                        <textarea name="description" placeholder="e.g. Learn Python coding"
                                  class="w-full mb-4 px-4 py-2 rounded-lg border-gray-300" rows="3"></textarea>

                        <!-- What You Will Learn -->
                        <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">What you will learn</label>
                        <input type="text" name="what_you_will_learn" placeholder="e.g. Python basics, Google Colab"
                               class="w-full mb-4 px-4 py-2 rounded-lg border-gray-300">

                        <!-- Skills -->
                        <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Skills you gain</label>
                        <input type="text" name="skills_gain" placeholder="e.g. Problem solving, coding"
                               class="w-full mb-4 px-4 py-2 rounded-lg border-gray-300">

                        <!-- Assessment -->
                        <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Assessment Summary</label>
                        <input type="text" name="assessment_info" placeholder="e.g. 5 quizzes, 2 assignments"
                               class="w-full mb-4 px-4 py-2 rounded-lg border-gray-300">

                        <!-- Duration & Price -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Duration</label>
                                <input type="text" name="duration" placeholder="e.g. 6 weeks"
                                       class="w-full px-4 py-2 rounded-lg border-gray-300">
                            </div>

                            <div>
                                <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Price (RM)</label>
                                <input type="number" step="0.01" name="price" placeholder="50.00"
                                       class="w-full px-4 py-2 rounded-lg border-gray-300">
                            </div>
                        </div>

                        <!-- Level -->
                        <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Level</label>
                        <select name="level" class="w-full px-4 py-2 rounded-lg border-gray-300 mb-4">
                            <option value="Beginner">Beginner</option>
                            <option value="Intermediate">Intermediate</option>
                            <option value="Advanced">Advanced</option>
                        </select>

                        <!-- Image Upload -->
                        <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-1">Course Image</label>
                        <input type="file" name="image" accept="image/*"
                               class="w-full px-4 py-2 rounded-lg border-gray-300 mb-4">

                    </div>


                    <!-- ================= RIGHT SIDE — ADD LESSONS ================= -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-300">

                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
                            Add Lessons
                        </h3>

                        <!-- Default Lesson (Lesson 1 always visible) -->
                        <div id="lessons-container">

                            <div class="lesson-box mb-6 p-4 rounded-xl bg-gray-100 dark:bg-gray-700 border border-gray-300">
                                <h4 class="font-semibold mb-3">Lesson 1</h4>

                                <label class="block mb-1">Lesson Title</label>
                                <input type="text" name="lessons[0][title]"
                                    class="w-full mb-3 px-4 py-2 rounded-lg border-gray-300" required>
                            </div>

                        </div>

                        <!-- Add Lesson Button -->
                        <button type="button" onclick="addLesson()"
                            class="mt-4 px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">
                            + Add Lesson
                        </button>

                    </div>

                </div>

                <!-- ✅ Submit Button (Create Course) -->
                <div class="flex justify-end mt-10">
                    <button type="submit"
                            class="px-8 py-3 bg-teal-600 text-white font-semibold rounded-lg hover:bg-teal-700 shadow-lg">
                        Create Course
                    </button>
                </div>

            </form>
        </div>
    </div>

    <script>
        let lessonCount = 1; // Already have Lesson 1

        function addLesson() {
            const index = lessonCount++;

            const container = document.getElementById('lessons-container');

            const lessonHTML = `
                <div class="lesson-box mb-6 p-4 rounded-xl bg-gray-100 dark:bg-gray-700 border border-gray-300">
                    <h4 class="font-semibold mb-3">Lesson ${index + 1}</h4>

                    <label class="block mb-1">Lesson Title</label>
                    <input type="text" name="lessons[${index}][title]"
                        class="w-full mb-3 px-4 py-2 rounded-lg border-gray-300" required>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', lessonHTML);
        }
    </script>

</x-app-layout>
