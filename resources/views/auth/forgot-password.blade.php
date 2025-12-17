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

<header class="w-full bg-white shadow-sm">
    <nav class="w-full mx-auto px-6 py-4 flex items-center justify-between">
        <x-application-logo class="h-20 w-auto shrink-0" />

        <a href="{{ route('login') }}"
           class="px-5 py-2 rounded-xl border border-gray-300 text-gray-700 font-medium
                  hover:bg-blue-500 hover:text-white transition">
            Back to login
        </a>
    </nav>
</header>

<main class="flex flex-col lg:flex-row min-h-[calc(100vh-80px)] overflow-hidden">

    {{-- LEFT --}}
    <div class="w-full lg:w-[60%] flex items-center justify-center p-12">
        <img
            src="{{ asset('images/learning-illustration.png') }}"
            class="w-full max-w-3xl object-contain"
            style="max-height: 70vh;"
        >
    </div>

    {{-- RIGHT --}}
    <div class="w-full lg:w-[40%] flex items-center justify-center p-12 lg:pr-60">
        <div class="w-full max-w-md">

            {{-- SUCCESS STATE --}}
            @if (session('status'))
                <div class="animate-fade-in">
                    <div class="flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mx-auto mb-6">
                        <i class="fa-solid fa-check text-green-600 text-2xl"></i>
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900 text-center mb-4">
                        Check your email
                    </h1>

                    <p class="text-gray-600 text-center mb-6">
                        We’ve sent a password reset link to your email.
                    </p>

                    <form method="POST" action="{{ route('password.email') }}" class="text-center">
                        @csrf
                        <input type="hidden" name="email" value="{{ old('email') }}">
                        <button class="text-blue-600 font-medium hover:underline">
                            Didn’t receive the email? Resend
                        </button>
                    </form>
                </div>

            @else
                {{-- FORM --}}
                <h1 class="text-4xl font-bold text-gray-900 mb-4">
                    Forgot your password?
                </h1>

                <p class="text-gray-600 mb-8">
                    Enter your email and we’ll send you a link to reset your password.
                </p>

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5" id="resetForm">
                    @csrf

                    <div>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            placeholder="Email address"
                            class="w-full px-4 py-4 border border-gray-300 rounded-xl
                                   focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <button
                        type="submit"
                        id="submitBtn"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white
                               font-medium py-3 rounded-xl transition shadow-sm
                               flex items-center justify-center gap-2"
                    >
                        <span id="btnText">Send reset link</span>
                        <i id="spinner" class="fa-solid fa-spinner animate-spin hidden"></i>
                    </button>

                    <p class="text-xs text-gray-400 text-center">
                        Too many attempts may be temporarily blocked.
                    </p>
                </form>
            @endif

            <div class="text-center mt-6 text-sm text-gray-600">
                Remembered your password?
                <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:underline">
                    Log in
                </a>
            </div>

        </div>
    </div>
</main>

{{-- JS --}}
<script>
document.getElementById('resetForm')?.addEventListener('submit', () => {
    document.getElementById('btnText').textContent = 'Sending...';
    document.getElementById('spinner').classList.remove('hidden');
});
</script>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fadeIn 0.4s ease-out;
}
</style>

</body>
</html>
