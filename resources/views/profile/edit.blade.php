<x-app-layout>
@if(session('success'))
<div
    x-data="{
        show: true,
        timer: null,
        start() {
            this.timer = setTimeout(() => this.show = false, 2000);
        },
        pause() {
            clearTimeout(this.timer);
        }
    }"
    x-init="start()"
    x-show="show"
    x-transition.opacity.duration.300ms
    class="fixed top-6 left-1/2 -translate-x-1/2 z-50"
>
    <div
        @mouseenter="pause()"
        @mouseleave="start()"
        @click="show = false"
        class="flex items-center gap-3
               bg-green-600 text-white
               px-6 py-3 rounded-lg shadow-lg
               cursor-pointer select-none"
    >
        <!-- Animated Check Icon -->
        <svg class="w-5 h-5 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M5 13l4 4L19 7"/>
        </svg>

        <span class="font-medium">
            {{ session('success') }}
        </span>

        <!-- Close hint -->
        <span class="text-sm opacity-70 ml-2">(click to dismiss)</span>
    </div>
</div>
@endif



    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- PROFILE HEADER -->
            <div class="bg-white rounded-2xl shadow p-6 flex items-center gap-6">
                <div class="relative">
                    <div class="w-24 h-24 rounded-full overflow-hidden border">
                        @if(Auth::user()->avatar)
                            <img
                                src="{{ asset('storage/' . Auth::user()->avatar) }}?v={{ time() }}"
                                class="w-full h-full object-cover"
                                alt="Avatar"
                            >
                        @else

                            <div class="w-full h-full flex items-center justify-center bg-gray-200 text-3xl font-bold">
                                {{ strtoupper(substr(Auth::user()->name,0,1)) }}
                            </div>
                        @endif
                    </div>
                </div>

                <div>
                    <h2 class="text-xl font-bold">{{ Auth::user()->name }}</h2>
                    <p class="text-gray-500">
                        {{ Auth::user()->role === 'instructor' ? 'Student | Instructor' : 'Student' }}
                    </p>
                    <p class="text-gray-500">{{ Auth::user()->address ?? 'Address not set' }}</p>
                </div>
            </div>

            <!-- PERSONAL DETAILS -->
            <form method="POST"
                  action="{{ route('profile.update') }}"
                  enctype="multipart/form-data"
                  class="bg-white rounded-2xl shadow p-6 space-y-6">
                @csrf
                @method('PATCH')

                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Personal Details</h3>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Save
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Avatar -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Profile Picture</label>
                        <input type="file" name="avatar" class="block w-full text-sm">
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Phone Number</label>
                        <input type="text" name="phone"
                               value="{{ old('phone', Auth::user()->phone) }}"
                               class="w-full rounded-lg border-gray-300">
                    </div>

                    <!-- Full Name -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Full Name</label>
                        <input type="text" name="name"
                               value="{{ old('name', Auth::user()->name) }}"
                               class="w-full rounded-lg border-gray-300">
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Date of Birth</label>
                        <input type="date" name="date_of_birth"
                               value="{{ old('date_of_birth', Auth::user()->date_of_birth) }}"
                               class="w-full rounded-lg border-gray-300">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Email Address</label>
                        <input type="email" value="{{ Auth::user()->email }}" disabled
                               class="w-full rounded-lg bg-gray-100 border-gray-300">
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Role</label>
                        <input type="text" value="{{ Auth::user()->role }}" disabled
                               class="w-full rounded-lg bg-gray-100 border-gray-300">
                    </div>

                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Address</label>
                        <input type="text" name="address"
                               value="{{ old('address', Auth::user()->address) }}"
                               class="w-full rounded-lg border-gray-300">
                    </div>

                    <!-- Bio -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Biography</label>
                        <textarea name="bio" rows="4"
                                  class="w-full rounded-lg border-gray-300">{{ old('bio', Auth::user()->bio) }}</textarea>
                    </div>
                </div>
            </form>

            <!-- ACCOUNT DELETION -->
            <div class="bg-white rounded-2xl shadow p-6 border border-red-200">
                <h3 class="text-lg font-semibold text-red-600 mb-2">
                    Delete Account
                </h3>

                <p class="text-sm text-gray-600 mb-4">
                    Once your account is deleted, all your data will be permanently removed.
                    This action cannot be undone.
                </p>

                <form method="POST" action="{{ route('profile.destroy') }}"
                    onsubmit="return confirm('Are you sure you want to delete your account? This cannot be undone.');">
                    @csrf
                    @method('DELETE')

                    <div class="flex items-center gap-4">
                        <input
                            type="password"
                            name="password"
                            required
                            placeholder="Confirm your password"
                            class="w-full max-w-sm rounded-lg border-gray-300"
                        >

                        <button
                            type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                        >
                            Delete Account
                        </button>
                    </div>
                </form>
            </div>


        </div>
    </div>
</x-app-layout>
