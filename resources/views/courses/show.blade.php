<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            @if($course->image)
                                <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" class="w-full rounded-lg mb-4">
                            @else
                                <div class="w-full h-64 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center mb-4">
                                    <span class="text-gray-400">No Image</span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                                {{ $course->title }}
                            </h1>
                            <div class="mb-4">
                                <span class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                                    ${{ number_format($course->price, 2) }}
                                </span>
                            </div>
                            <div class="mb-6">
                                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">
                                    {{ $course->description }}
                                </p>
                            </div>
                            
                            <!-- Enrollment/Instructor Actions Section -->
                            <div class="mt-6 space-y-4">
                                @auth
                                    @if(auth()->user()->role === 'instructor' && auth()->id() === $course->instructor_id)
                                        <!-- Instructor Actions -->
                                        <div class="bg-teal-50 dark:bg-teal-900/20 border border-teal-200 dark:border-teal-800 px-4 py-3 rounded mb-4">
                                            <p class="font-semibold text-teal-800 dark:text-teal-300 mb-3">This is your course</p>
                                            <div class="flex gap-3">
                                                <a href="{{ route('courses.edit', $course) }}" class="bg-teal-600 hover:bg-teal-700 text-white font-semibold py-2 px-4 rounded transition-colors">
                                                    ✏️ Edit Course
                                                </a>
                                                <form method="POST" action="{{ route('courses.destroy', $course) }}" onsubmit="return confirm('Are you sure you want to delete this course? This action cannot be undone.');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded transition-colors">
                                                        🗑️ Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @elseif(auth()->user()->role === 'student')
                                        <!-- Student Enrollment -->
                                        @if($isEnrolled)
                                            <div class="bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-4">
                                                <p class="font-semibold">✓ You are enrolled in this course!</p>
                                            </div>
                                            <a href="{{ route('enrollments.index') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-6 rounded inline-block">
                                                View My Enrollments
                                            </a>
                                        @else
                                            <a href="{{ route('payments.checkout', $course) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded inline-block text-lg w-full text-center">
                                                🎓 Enroll Now - ${{ number_format($course->price, 2) }}
                                            </a>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Secure payment via Stripe</p>
                                        @endif
                                    @endif
                                @else
                                    <!-- Guest Enrollment -->
                                    <a href="{{ route('payments.checkout', $course) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded inline-block text-lg w-full text-center">
                                        🎓 Enroll Now - ${{ number_format($course->price, 2) }}
                                    </a>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                        <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Login</a> or 
                                        <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Register</a> to enroll
                                    </p>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

