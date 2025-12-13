<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Log In - {{ config('app.name', 'Learnify') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white min-h-screen">
    <!-- Header -->
    <header class="w-full bg-white">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center space-x-3 sm:space-x-4">
                <x-application-logo class="w-12 h-12 sm:w-16 sm:h-16" />
                <div>
                    <div class="text-xl sm:text-2xl font-bold text-teal-600">LEARNIFY</div>
                    <div class="text-xs sm:text-sm text-amber-600 font-medium">LEARN MORE. BE MORE</div>
                </div>
            </div>

            <!-- Auth Buttons -->
            <div class="flex items-center space-x-3 sm:space-x-4">
                <a href="{{ route('login') }}" class="inline-block text-gray-700 hover:text-gray-900 font-medium text-base px-4 py-2 transition-colors">Log In</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium text-base px-5 py-2.5 rounded-lg shadow-sm transition-colors">Sign Up</a>
                @endif
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="flex flex-col lg:flex-row min-h-[calc(100vh-80px)]">
        <!-- Left Side - Illustration (60% width on desktop, full width on mobile) -->
        <div class="w-full lg:w-[60%] bg-gradient-to-br from-teal-50 via-cyan-50 to-blue-50 flex items-center justify-center p-6 sm:p-8 lg:p-12 order-2 lg:order-1">
            <div class="w-full max-w-2xl flex items-center justify-center h-full">
                <img 
                    src="{{ asset('images/learning-illustration.png') }}?v={{ time() }}" 
                    alt="Online Learning Illustration" 
                    class="w-full h-auto max-w-full object-contain mx-auto"
                    style="max-height: 70vh; display: block;"
                >
            </div>
        </div>

        <!-- Right Side - Login Form (40% width on desktop, full width on mobile) -->
        <div class="w-full lg:w-[40%] flex items-center justify-center p-6 sm:p-8 lg:p-12 bg-white order-1 lg:order-2">
            <div class="w-full max-w-md">
                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6 sm:mb-8">Log in with email</h1>

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus 
                            autocomplete="username"
                            placeholder="Email"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-gray-900 placeholder-gray-400 text-base"
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            required 
                            autocomplete="current-password"
                            placeholder="Password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-gray-900 placeholder-gray-400 text-base"
                        />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input 
                            id="remember_me" 
                            type="checkbox" 
                            name="remember" 
                            class="rounded border-gray-300 text-teal-600 focus:ring-teal-500 w-4 h-4"
                        >
                        <label for="remember_me" class="ml-2 text-sm text-gray-600">
                            Remember me
                        </label>
                    </div>

                    <!-- Log In Button -->
                    <button 
                        type="submit"
                        class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Log In
                    </button>

                    <!-- Forgot Password Link -->
                    @if (Route::has('password.request'))
                        <div class="text-center mt-3">
                            <a href="{{ route('password.request') }}" class="text-sm text-gray-600 hover:text-teal-600 transition">
                                Forgot your password?
                            </a>
                        </div>
                    @endif

                    <!-- Sign Up Link -->
                    @if (Route::has('register'))
                        <div class="text-center pt-4">
                            <p class="text-sm text-gray-600">
                                Don't have an account? 
                                <a href="{{ route('register') }}" class="font-semibold text-gray-900 hover:text-teal-600 transition">
                                    Sign Up
                                </a>
                            </p>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </main>
</body>
</html>
