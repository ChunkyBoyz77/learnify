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
    
    <!-- Additional Styles to Ensure Buttons are Always Visible -->
    <style>
        /* Force ALL buttons and links to be visible always */
        header nav a,
        header nav a[href],
        header nav a[href*="login"],
        header nav a[href*="register"],
        form button,
        form button[type="submit"],
        button[type="submit"] {
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        /* Sign Up button - always visible with blue background */
        header nav a[href*="register"],
        a.bg-blue-600 {
            background-color: #2563eb !important;
            color: #ffffff !important;
            opacity: 1 !important;
            visibility: visible !important;
            display: inline-block !important;
            border: none !important;
        }
        
        /* Log In link - always visible */
        header nav a[href*="login"] {
            color: #374151 !important;
            opacity: 1 !important;
            visibility: visible !important;
            display: inline-block !important;
        }
        
        /* Form submit button - always visible */
        form button[type="submit"],
        button[type="submit"] {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            background-color: #2563eb !important;
            color: #ffffff !important;
            cursor: pointer !important;
        }
        
        /* Override ANY opacity or visibility rules */
        header nav a:not(:hover),
        header nav a:hover,
        form button:not(:hover),
        form button:hover {
            opacity: 1 !important;
            visibility: visible !important;
        }
    </style>
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
                <a href="{{ route('login') }}" style="display: inline-block !important; visibility: visible !important; opacity: 1 !important; color: #374151 !important; font-weight: 500 !important; font-size: 1rem !important; text-decoration: none !important; padding: 0.5rem 1rem !important;">Log In</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" style="display: inline-block !important; visibility: visible !important; opacity: 1 !important; background-color: #2563eb !important; color: #ffffff !important; padding: 0.625rem 1.25rem !important; border-radius: 0.5rem !important; font-weight: 500 !important; font-size: 1rem !important; text-decoration: none !important; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;">Sign Up</a>
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
                        style="display: block !important; visibility: visible !important; opacity: 1 !important; width: 100% !important; background-color: #2563eb !important; color: #ffffff !important; padding: 0.75rem 1rem !important; border-radius: 0.5rem !important; font-weight: 500 !important; font-size: 1rem !important; border: none !important; cursor: pointer !important; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important; margin-top: 1rem !important;"
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
    
    <!-- Script to Force Buttons to be Visible -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Force header buttons to be visible
            const headerLinks = document.querySelectorAll('header nav a');
            headerLinks.forEach(function(link) {
                link.style.display = 'inline-block';
                link.style.visibility = 'visible';
                link.style.opacity = '1';
            });
            
            // Force form button to be visible
            const submitButton = document.querySelector('form button[type="submit"]');
            if (submitButton) {
                submitButton.style.display = 'block';
                submitButton.style.visibility = 'visible';
                submitButton.style.opacity = '1';
                submitButton.style.backgroundColor = '#2563eb';
                submitButton.style.color = '#ffffff';
            }
        });
        
        // Also ensure visibility after page load
        window.addEventListener('load', function() {
            const headerLinks = document.querySelectorAll('header nav a');
            headerLinks.forEach(function(link) {
                link.style.display = 'inline-block';
                link.style.visibility = 'visible';
                link.style.opacity = '1';
            });
            
            const submitButton = document.querySelector('form button[type="submit"]');
            if (submitButton) {
                submitButton.style.display = 'block';
                submitButton.style.visibility = 'visible';
                submitButton.style.opacity = '1';
            }
        });
    </script>
</body>
</html>
