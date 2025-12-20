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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>


    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white min-h-screen overflow-hidden">
    {{-- HEADER --}}
    @include('layouts.auth-header')



    <!-- Main Content -->
    <main class="flex flex-col lg:flex-row h-[calc(100vh-96px)] overflow-hidden">
        <!-- Left Side - Illustration (60% width on desktop, full width on mobile) -->
        <div class="w-full lg:w-[60%] bg-white flex items-center justify-center p-6 sm:p-8 lg:p-12 order-2 lg:order-1">
            <div class="w-full max-w-3xl flex items-center justify-center h-full">
                <img 
                    src="{{ asset('images/learning-illustration.png') }}?v={{ time() }}" 
                    alt="Online Learning Illustration" 
                    class="w-full h-auto object-contain"
                    style="max-height: 60vh;"
                >

            </div>
        </div>

        <!-- Right Side - Login Form (40% width on desktop, full width on mobile) -->
        <div class="w-full lg:w-1/2 flex items-start lg:items-center justify-center pt-10 lg:pt-0 p-6 sm:p-8 lg:p-12 bg-white order-1 lg:order-2 overflow-y-auto">
            <div class="w-full max-w-md lg:max-w-lg xl:max-w-xl">
                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <h1 class="text-4xl xl:text-5xl font-bold leading-tight text-gray-900 mb-6 sm:mb-8">Log in to continue your learning <span id="rotating-word">journey</span>.</h1>

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
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
                            class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-gray-900 placeholder-gray-400 text-base"
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="relative">
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            required 
                            autocomplete="current-password"
                            placeholder="Password"
                            class="w-full px-4 py-4 pr-12 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-gray-900 placeholder-gray-400 text-base"
                        />
                        <button 
                            type="button"
                            id="togglePassword"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 transition"
                            aria-label="Toggle password visibility"
                        >
                            <i id="eyeIcon" class="fa-solid fa-eye"></i>
                        </button>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input 
                            id="remember_me" 
                            type="checkbox" 
                            name="remember" 
                            class="rounded border-gray-300 text-blur-600 focus:ring-blue-500 w-5 h-5"
                        >
                        <label for="remember_me" class="ml-2 text-sm text-gray-600">
                            Remember me
                        </label>
                    </div>

                    @php
                        $lockoutSeconds = session('lockout_seconds');
                    @endphp
                    
                    <button 
                        type="submit"
                        id="loginButton"
                        class="w-full mt-4 border font-medium py-3 px-4 rounded-lg shadow-sm transition
                            bg-white hover:bg-blue-600 text-black hover:text-white border-gray-500
                            flex items-center justify-center gap-2"
                    >
                        <svg id="loginSpinner"
                            class="hidden h-5 w-5 animate-spin text-current"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"></circle>
                            <path class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>

                        <span id="loginText">Log In</span>
                    </button>

                    @if($lockoutSeconds)
                        <p id="lockoutMessage" class="mt-3 text-sm text-red-600 text-center">
                            Too many failed attempts. Please wait
                            <span id="countdown">{{ $lockoutSeconds }}</span>
                            seconds.
                        </p>
                    @endif


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

<script>
    //Typing Effect
    const words = ['journey', 'adventure', 'quest', 'experience'];
    let currentIndex = 0;
    let currentText = '';
    let isDeleting = false; // Fixed: was "isDeleteing"
    const wordElement = document.getElementById('rotating-word');

    function typeEffect() {
        const currentWord = words[currentIndex];

        if (isDeleting) {
            currentText = currentWord.substring(0, currentText.length - 1);
        } else {
            currentText = currentWord.substring(0, currentText.length + 1);
        }

        wordElement.textContent = currentText; // Fixed: was "textContext"

        let typeSpeed = isDeleting ? 50 : 100;
        
        if (!isDeleting && currentText === currentWord) {
            typeSpeed = 2000; // Pause at complete word
            isDeleting = true;
        } else if (isDeleting && currentText === '') {
            isDeleting = false;
            currentIndex = (currentIndex + 1) % words.length;
            typeSpeed = 500; // Pause before typing next word
        }
        
        setTimeout(typeEffect, typeSpeed);
    }

    // Start the effect
    typeEffect();

    // Password Toggle
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    togglePassword.addEventListener('click', function() {
        // Toggle the type attribute
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Toggle the eye icon
        if (type === 'password') {
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        } else {
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        }
    });

    const input = document.getElementById('courseSearch');
    const resultsBox = document.getElementById('searchResults');
    let controller;

    input.addEventListener('input', async () => {
        const query = input.value.trim();

        if (query.length < 2) {
            resultsBox.classList.add('hidden');
            resultsBox.innerHTML = '';
            return;
        }

        // cancel previous request
        if (controller) controller.abort();
        controller = new AbortController();

        try {
            const res = await fetch(`/search/courses?q=${encodeURIComponent(query)}`, {
                signal: controller.signal
            });

            const data = await res.json();

            if (!data.length) {
                resultsBox.innerHTML = `
                    <div class="px-4 py-3 text-sm text-gray-500">
                        No results found
                    </div>`;
            } else {
                resultsBox.innerHTML = data.map(course => `
                    <a href="/courses/${course.id}"
                    class="block px-4 py-3 hover:bg-gray-100 transition text-sm">
                        ${course.title}
                    </a>
                `).join('');
            }

            resultsBox.classList.remove('hidden');
        } catch (e) {
            // request aborted, do nothing
        }
    });

    // Hide when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#courseSearch')) {
            resultsBox.classList.add('hidden');
        }
    });

</script>
<script>
    const loginForm = document.querySelector('form');
    const loginButn = document.getElementById('loginButton');
    const loginSpinner = document.getElementById('loginSpinner');
    const loginText = document.getElementById('loginText');

    loginForm.addEventListener('submit', (e) => {
        e.preventDefault(); //stop immediate submit

        // Disable button
        loginButn.disabled = true;
        loginButn.setAttribute('disabled', 'disabled');

        // Show spinner
        loginSpinner.classList.remove('hidden');
        loginText.textContent = 'Signing in...';
        loginButn.classList.add('cursor-not-allowed', 'opacity-80');

        // Allow browser to paint, then submit
        requestAnimationFrame(() => {
            loginForm.submit();
        });
    });
</script>


@if(session('lockout_seconds'))
<script>
    let secondsLeft = {{ session('lockout_seconds') }};
    const countdownEl = document.getElementById('countdown');
    const loginBtn = document.getElementById('loginButton');
    const lockoutMsg = document.getElementById('lockoutMessage');

    // LOCK immediately
    loginBtn.disabled = true;
    loginBtn.setAttribute('disabled', 'disabled');

    if (secondsLeft <= 0) {
        unlock();
    } else {
        const timer = setInterval(() => {
            secondsLeft--;

            if (secondsLeft <= 0) {
                clearInterval(timer);
                unlock();
                return;
            }

            countdownEl.textContent = secondsLeft;
        }, 1000);
    }

    function unlock() {
        // UNLOCK properly
        loginBtn.disabled = false;
        loginBtn.removeAttribute('disabled');

        loginBtn.classList.remove(
        'bg-gray-300',
        'text-gray-500',
        'cursor-not-allowed'
        );

        loginBtn.classList.add(
            'bg-white',
            'hover:bg-blue-600',
            'text-black',
            'hover:text-white',
            'border-gray-500'
        );


        lockoutMsg.classList.add('hidden');
    }
</script>
@endif




</html>
