<header class="w-full bg-white shadow-sm">
    <nav class="w-full mx-auto px-6 py-4 flex items-center">

        <!-- LEFT: Logo + Explore + Search -->
        <div class="flex items-center gap-6 flex-1">
            <x-application-logo class="h-20 w-auto shrink-0" />

            <a href="{{ route('courses.index') }}"
               class="text-base text-gray-500 font-medium hover:text-cyan-900 transition">
                Explore
            </a>

            <div class="relative w-full max-w-3xl">
                <form action="{{ route('courses.index') }}" method="GET">
                    <input
                        id="courseSearch"
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Learn anything..."
                        class="w-full rounded-full border border-gray-300 px-6 py-4 pr-14 text-base
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >

                    <button
                        type="submit"
                        class="absolute right-2 top-1/2 -translate-y-1/2
                               h-10 w-10 flex items-center justify-center
                               rounded-full text-gray-500 hover:bg-gray-100 transition">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>

                <!-- Live results -->
                <div id="searchResults"
                     class="absolute top-full left-0 right-0 bg-white border rounded-xl shadow-lg mt-2 hidden z-50">
                </div>
            </div>
        </div>

        <!-- RIGHT -->
        <div class="flex items-center gap-2 ml-auto shrink-0">
            <a href="#"
               class="text-base text-gray-500 font-medium hover:text-blue-900 transition mr-10">
                Teach on Learnify
            </a>

            @guest
                <a href="{{ route('login') }}"
                   class="px-5 py-2 rounded-xl border border-gray-300 text-gray-700 font-medium
                          hover:bg-blue-500 hover:text-white transition">
                    Log In
                </a>

                <a href="{{ route('register') }}"
                   class="px-5 py-2 rounded-xl bg-blue-500 text-white font-medium
                          hover:bg-white hover:text-gray-700 transition shadow-sm">
                    Sign Up
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="font-medium">
                    Dashboard
                </a>
            @endguest
        </div>

    </nav>
</header>
