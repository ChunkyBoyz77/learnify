<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-teal-50 via-cyan-50 to-blue-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
            <!-- Decorative background elements -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute top-20 left-10 w-72 h-72 bg-teal-200 dark:bg-teal-900/20 rounded-full mix-blend-multiply dark:mix-blend-soft-light filter blur-xl opacity-30 animate-blob"></div>
                <div class="absolute top-40 right-10 w-72 h-72 bg-cyan-200 dark:bg-cyan-900/20 rounded-full mix-blend-multiply dark:mix-blend-soft-light filter blur-xl opacity-30 animate-blob animation-delay-2000"></div>
                <div class="absolute -bottom-8 left-1/2 w-72 h-72 bg-blue-200 dark:bg-blue-900/20 rounded-full mix-blend-multiply dark:mix-blend-soft-light filter blur-xl opacity-30 animate-blob animation-delay-4000"></div>
            </div>
            
            <div class="relative z-10">
                <a href="/" class="flex flex-col items-center space-y-2 group">
                    <x-application-logo class="w-24 h-24 transition-transform group-hover:scale-110" />
                    <div class="text-center">
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-teal-600 to-cyan-600 dark:from-teal-400 dark:to-cyan-400 bg-clip-text text-transparent">LEARNIFY</h1>
                        <p class="text-xs text-amber-600 dark:text-amber-400 font-medium mt-1">LEARN MORE.BE MORE</p>
                    </div>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-8 px-6 py-8 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-xl border border-teal-100 dark:border-gray-700 overflow-hidden sm:rounded-2xl relative z-10">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
