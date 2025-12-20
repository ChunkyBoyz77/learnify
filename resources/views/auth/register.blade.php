<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sign Up - {{ config('app.name', 'Learnify') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white min-h-screen">

        {{-- HEADER --}}
    @include('layouts.auth-header')

    <!-- Main Content -->
    <main class="flex flex-col lg:flex-row h-[calc(100vh-80px)] overflow-hidden">

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

        <!-- RIGHT: Register Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center
                    p-6 sm:p-8 lg:pl-12 bg-white
                    order-1 lg:order-2">

            <div class="w-full max-w-md lg:max-w-lg xl:max-w-xl max-h-[calc(100vh-120px)] overflow-y-auto">

                <h1 class="text-4xl font-bold text-gray-900 mb-8 text-center">
                    Get started with Learnify.
                </h1>

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <!-- Full Name -->
                    <div>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Full Name"
                            required
                            class="w-full px-4 py-4 border border-gray-300 rounded-xl
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-inset"
                        >
                        <x-input-error :messages="$errors->get('name')" class="mt-2"/>
                    </div>

                    <!-- Email -->
                    <div id="emailContainer">
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Email"
                            required
                            class="peer w-full px-4 py-4 border border-gray-300 rounded-xl
                                    focus:outline-none focus:ring-2
                                    focus:ring-blue-500
                                    focus:border-transparent
                                    focus:ring-inset"
                                    
                        >
                        <x-input-error :messages="$errors->get('email')" class="mt-2"/>
                        <p id="emailError" class="mt-2 text-sm text-red-500 hidden">
                            Please enter a valid email address.
                        </p>

                    </div>

                    <!-- Password -->
                    <div class="relative transition-all duration-300" id="passwordContainer">
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            required 
                            autocomplete="new-password"
                            placeholder="Password"
                            class="w-full px-4 py-4 pr-12 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 
                            focus:ring-blue-500 focus:border-transparent focus:ring-inset bg-white text-gray-900 placeholder-gray-400 text-base"
                        />
                        <button 
                            type="button"
                            id="togglePassword"
                            class="absolute right-4 top-4 text-gray-500 hover:text-gray-700 transition"
                            aria-label="Toggle password visibility"
                        >
                            <i id="eyeIcon" class="fa-solid fa-eye"></i>
                        </button>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        
                        <!-- Password Requirements (Absolutely Positioned) -->
                        <div id="passwordRequirements" class="absolute left-0 right-0 mt-2 space-y-1 text-sm hidden z-10">
                            <div id="allFulfilled" class="bg-green-50 border border-green-200 rounded-lg p-3 hidden">
                                <p class="text-green-700 font-medium">
                                    <i class="fa-solid fa-circle-check text-green-500"></i> All requirements fulfilled
                                </p>
                            </div>
                            <div id="requirementsList" class="bg-gray-50 border border-gray-200 rounded-lg p-3 space-y-1">
                                <p id="req-length" class="text-gray-500">
                                    <i class="fa-solid fa-circle-xmark text-red-500"></i> At least 8 characters
                                </p>
                                <p id="req-uppercase" class="text-gray-500">
                                    <i class="fa-solid fa-circle-xmark text-red-500"></i> One uppercase letter
                                </p>
                                <p id="req-lowercase" class="text-gray-500">
                                    <i class="fa-solid fa-circle-xmark text-red-500"></i> One lowercase letter
                                </p>
                                <p id="req-number" class="text-gray-500">
                                    <i class="fa-solid fa-circle-xmark text-red-500"></i> One number
                                </p>
                                <p id="req-special" class="text-gray-500">
                                    <i class="fa-solid fa-circle-xmark text-red-500"></i> One special character (!@#$%^&*)
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="relative transition-all duration-300" id="confirmPasswordContainer">
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            placeholder="Confirm Password"
                            required
                            autocomplete="new-password"
                            class="w-full px-4 py-4 pr-12 border border-gray-300 rounded-xl focus:outline-none 
                            focus:ring-2 focus:ring-blue-500 focus:ring-inset"
                        >
                        <button 
                            type="button"
                            id="togglePasswordConfirm"
                            class="absolute right-4 top-4 text-gray-500 hover:text-gray-700 transition"
                            aria-label="Toggle password visibility"
                        >
                            <i id="eyeIconConfirm" class="fa-solid fa-eye"></i>
                        </button>
                        
                        <!-- Password Match Indicator (Absolutely Positioned) -->
                        <div id="passwordMatch" class="absolute left-0 right-0 mt-2 text-sm hidden z-10">
                            <div id="match-status" class="rounded-lg p-3"></div>
                        </div>
                    </div>
                    <!-- Role Selection -->
                    <div class="space-y-3 pt-2">
                        <label class="flex items-center gap-3 p-4 border rounded-xl cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="role" value="student" required>
                            <span class="font-medium">Student</span>
                        </label>

                        <label class="flex items-center gap-3 p-4 border rounded-xl cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="role" value="instructor" required>
                            <span class="font-medium">Instructor</span>
                        </label>

                        <x-input-error :messages="$errors->get('role')" class="mt-2"/>
                    </div>

                    <!-- Register Button -->
                    <button
                        type="submit"
                        id="registerButton"
                        class="w-full mt-6 bg-blue-600 border border-gray-500 hover:bg-white
                            text-white hover:text-black font-medium py-3 rounded-lg transition
                            flex items-center justify-center gap-2"
                    >
                        <svg id="registerSpinner"
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

                        <span id="registerText">Continue</span>
                    </button>


                    <!-- Login Link -->
                    <div class="text-center pt-4">
                        <p class="text-sm text-gray-600">
                            Already have an account?
                            <a href="{{ route('login') }}" class="font-semibold hover:underline">
                                Log In
                            </a>
                        </p>
                    </div>
                </form>

            </div>
        </div>

    </main>
</body>
<script>
    // Password Toggle for Main Password
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        if (type === 'password') {
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        } else {
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        }
    });

    // Password Toggle for Confirm Password
    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const eyeIconConfirm = document.getElementById('eyeIconConfirm');

    togglePasswordConfirm.addEventListener('click', function() {
        const type = passwordConfirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordConfirmInput.setAttribute('type', type);
        
        if (type === 'password') {
            eyeIconConfirm.classList.remove('fa-eye-slash');
            eyeIconConfirm.classList.add('fa-eye');
        } else {
            eyeIconConfirm.classList.remove('fa-eye');
            eyeIconConfirm.classList.add('fa-eye-slash');
        }
    });

    // Password Requirements Validation
    const passwordContainer = document.getElementById('passwordContainer');
    const passwordRequirements = document.getElementById('passwordRequirements');
    const allFulfilled = document.getElementById('allFulfilled');
    const requirementsList = document.getElementById('requirementsList');
    const reqLength = document.getElementById('req-length');
    const reqUppercase = document.getElementById('req-uppercase');
    const reqLowercase = document.getElementById('req-lowercase');
    const reqNumber = document.getElementById('req-number');
    const reqSpecial = document.getElementById('req-special');

    let isPasswordFullyValid = false;

    function updateRequirement(element, isValid) {
        const icon = element.querySelector('i');
        if (isValid) {
            icon.classList.remove('fa-circle-xmark', 'text-red-500');
            icon.classList.add('fa-circle-check', 'text-green-500');
            element.classList.remove('text-gray-500');
            element.classList.add('text-green-600');
        } else {
            icon.classList.remove('fa-circle-check', 'text-green-500');
            icon.classList.add('fa-circle-xmark', 'text-red-500');
            element.classList.remove('text-green-600');
            element.classList.add('text-gray-500');
        }
    }

    passwordInput.addEventListener('focus', function() {
        passwordRequirements.classList.remove('hidden');
        passwordContainer.style.marginBottom = '180px';

        if (isPasswordFullyValid) {
            allFulfilled.classList.remove('hidden');
            passwordContainer.style.marginBottom = '70px';
        } else {
            passwordContainer.style.marginBottom = '180px';
        }
    });

    passwordInput.addEventListener('blur', function() {
        passwordRequirements.classList.add('hidden');
        passwordContainer.style.marginBottom = '0px'; 
        // Optional: Keep requirements visible or hide them
        // setTimeout(() => {
        //     passwordRequirements.classList.add('hidden');
        //     passwordContainer.style.marginBottom = '0';
        // }, 200);
    });

    passwordInput.addEventListener('input', function() {
        const value = this.value;

        // Check all requirements
        const hasLength = value.length >= 8;
        const hasUppercase = /[A-Z]/.test(value);
        const hasLowercase = /[a-z]/.test(value);
        const hasNumber = /[0-9]/.test(value);
        const hasSpecial = /[!@#$%^&*]/.test(value);

        // Update individual requirements
        updateRequirement(reqLength, hasLength);
        updateRequirement(reqUppercase, hasUppercase);
        updateRequirement(reqLowercase, hasLowercase);
        updateRequirement(reqNumber, hasNumber);
        updateRequirement(reqSpecial, hasSpecial);

        // Check if all requirements are fulfilled
        isPasswordFullyValid = hasLength && hasUppercase && hasLowercase && hasNumber && hasSpecial;

        if (isPasswordFullyValid) {
            // Show "All requirements fulfilled" message
            allFulfilled.classList.remove('hidden');
            requirementsList.classList.add('hidden');
            // Adjust margin for smaller message
            passwordContainer.style.marginBottom = '70px';
        } else {
            // Show individual requirements
            allFulfilled.classList.add('hidden');
            requirementsList.classList.remove('hidden');
            // Adjust margin for full list
            passwordContainer.style.marginBottom = '180px';
        }

        // Check password match when password changes
        checkPasswordMatch();
    });

    // Password Match Validation
    const confirmPasswordContainer = document.getElementById('confirmPasswordContainer');
    const passwordMatchDiv = document.getElementById('passwordMatch');
    const matchStatus = document.getElementById('match-status');

    function checkPasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = passwordConfirmInput.value;

        if (confirmPassword.length === 0) {
            passwordMatchDiv.classList.add('hidden');
            confirmPasswordContainer.style.marginBottom = '0';
            return;
        }

        passwordMatchDiv.classList.remove('hidden');
        confirmPasswordContainer.style.marginBottom = '70px'; // Add space for match indicator

        if (password === confirmPassword) {
            matchStatus.className = 'rounded-lg p-3 bg-green-50 border border-green-200';
            matchStatus.innerHTML = '<span class="text-green-700 font-medium"><i class="fa-solid fa-circle-check text-green-500"></i> Passwords match</span>';
        } else {
            matchStatus.className = 'rounded-lg p-3 bg-red-50 border border-red-200';
            matchStatus.innerHTML = '<span class="text-red-700 font-medium"><i class="fa-solid fa-circle-xmark text-red-500"></i> Passwords do not match</span>';
        }
    }

    passwordConfirmInput.addEventListener('input', checkPasswordMatch);

    // Form Validation on Submit
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const password = passwordInput.value;
        const confirmPassword = passwordConfirmInput.value;

        // Check all requirements
        const hasLength = password.length >= 8;
        const hasUppercase = /[A-Z]/.test(password);
        const hasLowercase = /[a-z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSpecial = /[!@#$%^&*]/.test(password);
        const passwordsMatch = password === confirmPassword;

        if (!hasLength || !hasUppercase || !hasLowercase || !hasNumber || !hasSpecial) {
            e.preventDefault();
            alert('Please ensure your password meets all requirements.');
            passwordRequirements.classList.remove('hidden');
            return false;
        }

        if (!passwordsMatch) {
            e.preventDefault();
            alert('Passwords do not match.');
            return false;
        }
    });

    // Email Requirement Validation
    const emailInput = document.getElementById('email');
    const emailContainer = document.getElementById('emailContainer');
    const emailError = document.getElementById('emailError');

    function isEmailValid(value) {
        return value.includes('@') && value.includes('.com');
    }

    function updateEmailContainer(isValid) {

        if (isValid) {
            emailInput.classList.remove('focus:ring-red-500');
            emailInput.classList.add('focus:ring-green-500');
        } else {
            emailInput.classList.remove('focus:ring-blue-500');
            emailInput.classList.add('focus:ring-red-500');
        }
    }

    // Focus: show error ONLY if invalid
    emailInput.addEventListener('focus', () => {
        if (!isEmailValid(emailInput.value) && emailInput.value !== '') {
            emailError.classList.remove('hidden');
            updateEmailContainer(false);
        }
    });

    // Blur: hide error & reset ring if empty
    emailInput.addEventListener('blur', () => {
        emailError.classList.add('hidden');

        if (emailInput.value === '') {
            emailInput.classList.remove('ring-red-500', 'ring-green-500');
            emailInput.classList.add('ring-gray-300');
        }
    });

    // Realtime validation
    emailInput.addEventListener('input', () => {
        const valid = isEmailValid(emailInput.value);

        if (emailInput.value === '') {
            emailError.classList.add('hidden');
            emailInput.classList.remove('ring-red-500', 'ring-green-500');
            emailInput.classList.add('ring-gray-300');
            return;
        }

        updateEmailContainer(valid);

        if (!valid && document.activeElement === emailInput) {
            emailError.classList.remove('hidden');
        } else {
            emailError.classList.add('hidden');
        }
    });



</script>
</html>
