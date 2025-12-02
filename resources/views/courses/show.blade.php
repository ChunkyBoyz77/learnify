<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                            
                            <!-- Enrollment Button Section -->
                            <div class="mt-6">
                                @auth
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
                                @else
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

