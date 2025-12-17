<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Reset Password – Learnify</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white min-h-screen">

{{-- HEADER --}}
<header class="w-full bg-white shadow-sm">
    <nav class="w-full mx-auto px-6 py-4 flex items-center justify-between">
        <x-application-logo class="h-20 w-auto" />

        <a href="{{ route('login') }}"
           class="px-5 py-2 rounded-xl border border-gray-300 text-base text-gray-700 font-medium
                  hover:bg-blue-500 hover:text-white transition">
            Back to login
        </a>
    </nav>
</header>

{{-- MAIN --}}
<main class="flex flex-col lg:flex-row h-[calc(100vh-80px)] overflow-hidden">

    {{-- LEFT --}}
    <div class="w-full lg:w-[60%] flex items-center justify-center p-12">
        <img
            src="{{ asset('images/learning-illustration.png') }}"
            class="w-full max-w-3xl object-contain"
            style="max-height: 70vh;"
        >
    </div>

    {{-- RIGHT --}}
    <div class="w-full lg:w-[40%] flex items-center justify-center
                p-6 sm:p-8 lg:pl-12 lg:pr-60 bg-white">

        <div class="w-full">

            <h1 class="text-4xl font-bold text-gray-900 mb-6 text-center">
                Create a new password
            </h1>

            <p class="text-gray-600 text-center mb-8">
                Choose a strong password to secure your Learnify account.
            </p>

            <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                {{-- Email --}}
                <div>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', $request->email) }}"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="Email address"
                        class="w-full px-4 py-4 border border-gray-300 rounded-xl
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    <x-input-error :messages="$errors->get('email')" class="mt-2"/>
                </div>

                {{-- Password --}}
                <div class="relative transition-all duration-300" id="passwordContainer">
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        placeholder="New password"
                        class="w-full px-4 py-4 pr-12 border border-gray-300 rounded-xl
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    <button
                        type="button"
                        id="togglePassword"
                        class="absolute right-4 top-4 text-gray-500 hover:text-gray-700 transition"
                    >
                        <i id="eyeIcon" class="fa-solid fa-eye"></i>
                    </button>
                    <x-input-error :messages="$errors->get('password')" class="mt-2"/>

                    {{-- Password Requirements --}}
                    <div id="passwordRequirements" class="absolute left-0 right-0 mt-2 space-y-1 text-sm hidden z-10">
                        <div id="allFulfilled" class="bg-green-50 border border-green-200 rounded-lg p-3 hidden">
                            <p class="text-green-700 font-medium">
                                <i class="fa-solid fa-circle-check text-green-500"></i>
                                All requirements fulfilled
                            </p>
                        </div>

                        <div id="requirementsList" class="bg-gray-50 border border-gray-200 rounded-lg p-3 space-y-1">
                            <p id="req-length"><i class="fa-solid fa-circle-xmark"></i> At least 8 characters</p>
                            <p id="req-uppercase"><i class="fa-solid fa-circle-xmark"></i> One uppercase letter</p>
                            <p id="req-lowercase"><i class="fa-solid fa-circle-xmark"></i> One lowercase letter</p>
                            <p id="req-number"><i class="fa-solid fa-circle-xmark"></i> One number</p>
                            <p id="req-special"><i class="fa-solid fa-circle-xmark"></i> One special character (!@#$%^&*)</p>
                        </div>
                    </div>
                </div>

                {{-- Confirm Password --}}
                <div class="relative transition-all duration-300" id="confirmPasswordContainer">
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Confirm new password"
                        class="w-full px-4 py-4 pr-12 border border-gray-300 rounded-xl
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    <button
                        type="button"
                        id="togglePasswordConfirm"
                        class="absolute right-4 top-4 text-gray-500 hover:text-gray-700 transition"
                    >
                        <i id="eyeIconConfirm" class="fa-solid fa-eye"></i>
                    </button>

                    <div id="passwordMatch" class="absolute left-0 right-0 mt-2 text-sm hidden z-10">
                        <div id="match-status" class="rounded-lg p-3"></div>
                    </div>
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="w-full mt-6 bg-blue-600 border border-gray-500 hover:bg-white
                           text-white hover:text-black font-medium py-3 rounded-lg transition">
                    Reset password
                </button>

                <div class="text-center pt-4 text-sm text-gray-600">
                    Remembered your password?
                    <a href="{{ route('login') }}" class="font-semibold hover:underline">Log in</a>
                </div>
            </form>
        </div>
    </div>
</main>

{{-- PASSWORD LOGIC (IDENTICAL TO REGISTER PAGE) --}}
<script>
const passwordInput = document.getElementById('password');
const passwordConfirmInput = document.getElementById('password_confirmation');
const passwordRequirements = document.getElementById('passwordRequirements');
const passwordContainer = document.getElementById('passwordContainer');

const reqLength = document.getElementById('req-length');
const reqUppercase = document.getElementById('req-uppercase');
const reqLowercase = document.getElementById('req-lowercase');
const reqNumber = document.getElementById('req-number');
const reqSpecial = document.getElementById('req-special');
const allFulfilled = document.getElementById('allFulfilled');
const requirementsList = document.getElementById('requirementsList');

function updateReq(el, valid) {
    el.className = valid ? 'text-green-600' : 'text-gray-500';
    el.querySelector('i').className = valid
        ? 'fa-solid fa-circle-check text-green-500'
        : 'fa-solid fa-circle-xmark text-red-500';
}

passwordInput.addEventListener('focus', () => {
    passwordRequirements.classList.remove('hidden');
    passwordContainer.style.marginBottom = '180px';
});

passwordInput.addEventListener('input', () => {
    const v = passwordInput.value;
    const checks = {
        length: v.length >= 8,
        upper: /[A-Z]/.test(v),
        lower: /[a-z]/.test(v),
        number: /\d/.test(v),
        special: /[!@#$%^&*]/.test(v)
    };

    updateReq(reqLength, checks.length);
    updateReq(reqUppercase, checks.upper);
    updateReq(reqLowercase, checks.lower);
    updateReq(reqNumber, checks.number);
    updateReq(reqSpecial, checks.special);

    const allValid = Object.values(checks).every(Boolean);
    allFulfilled.classList.toggle('hidden', !allValid);
    requirementsList.classList.toggle('hidden', allValid);
    passwordContainer.style.marginBottom = allValid ? '70px' : '180px';

    checkMatch();
});

const passwordMatchDiv = document.getElementById('passwordMatch');
const matchStatus = document.getElementById('match-status');
const confirmContainer = document.getElementById('confirmPasswordContainer');

function checkMatch() {
    if (!passwordConfirmInput.value) return;
    passwordMatchDiv.classList.remove('hidden');
    confirmContainer.style.marginBottom = '70px';

    const match = passwordInput.value === passwordConfirmInput.value;
    matchStatus.className = match
        ? 'bg-green-50 border border-green-200 text-green-700 p-3 rounded-lg'
        : 'bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg';
    matchStatus.innerHTML = match
        ? '<i class="fa-solid fa-circle-check"></i> Passwords match'
        : '<i class="fa-solid fa-circle-xmark"></i> Passwords do not match';
}

passwordConfirmInput.addEventListener('input', checkMatch);
</script>

</body>
</html>
