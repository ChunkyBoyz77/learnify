<x-app-layout>
<link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
/>

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

@if(session('password_success'))
<div
    x-data="{
        show: true,
        timer: null,
        start() {
            this.timer = setTimeout(() => this.show = false, 5000);
        },
        pause() {
            clearTimeout(this.timer);
        }
    }"
    x-init="start()"
    x-show="show"
    x-transition.opacity.duration.300ms
    class="fixed top-20 left-1/2 -translate-x-1/2 z-50"
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
            {{ session('password_success') }}
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
                        {{ Auth::user()->role === 'instructor' ? 'Instructor' : 'Student' }}
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
                        <input type="text" value="{{ Auth::user()->role === 'instructor' ? 'Instructor' : 'Student' }}" disabled
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

            <!-- CHANGE PASSWORD -->
            <form method="POST"
                action="{{ route('password.update') }}"
                class="bg-white rounded-2xl shadow p-6 space-y-6"
                id="passwordForm">
                @csrf
                @method('PUT')

                <h3 class="text-lg font-semibold">Change Password</h3>

                <!-- Current Password -->
                <div>
                    <label class="block text-sm font-medium mb-1">Current Password</label>
                    <div class="relative">
                    <input type="password"
                        id="passwordCurrent"
                        name="current_password"
                        required
                        class="w-full rounded-lg border-gray-300">
                    <button
                        type="button"
                        id="toggleCurrentPassword"
                        class="absolute right-4 top-2 text-gray-500 hover:text-gray-700 transition"
                    >
                        <i id="eyeCurrent" class="fa-solid fa-eye"></i>
                    </button>
                    </div>
                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2"/>
                </div>

                <!-- New Password -->
                <div id="passwordContainer">
                    <label class="block text-sm font-medium mb-1">New Password</label>
                    <div class="relative">
                    <input type="password"
                        id="passwordInput"
                        name="password"
                        required
                        class="w-full rounded-lg border-gray-300">
                    
                        <button
                        type="button"
                        id="togglePassword"
                        class="absolute right-4 top-2 text-gray-500 hover:text-gray-700 transition"
                    >
                        <i id="eyeIcon" class="fa-solid fa-eye"></i>
                    </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2"/>
                    

                    <!-- Password Requirements -->
                    <div id="passwordRequirements" class="mt-3 hidden">
                        <ul id="requirementsList" class="space-y-1 text-sm">
                            <li id="req-length" class="text-gray-500">
                                <i class="fa-solid fa-circle-xmark text-red-500 mr-2"></i> At least 8 characters
                            </li>
                            <li id="req-uppercase" class="text-gray-500">
                                <i class="fa-solid fa-circle-xmark text-red-500 mr-2"></i> One uppercase letter
                            </li>
                            <li id="req-lowercase" class="text-gray-500">
                                <i class="fa-solid fa-circle-xmark text-red-500 mr-2"></i> One lowercase letter
                            </li>
                            <li id="req-number" class="text-gray-500">
                                <i class="fa-solid fa-circle-xmark text-red-500 mr-2"></i> One number
                            </li>
                            <li id="req-special" class="text-gray-500">
                                <i class="fa-solid fa-circle-xmark text-red-500 mr-2"></i> One special character
                            </li>
                        </ul>

                        <div id="allFulfilled"
                            class="hidden text-green-600 text-sm font-medium mt-2">
                            <i class="fa-solid fa-circle-check mr-2"></i>Password meets all requirements
                        </div>
                    </div>

                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2"/>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-sm font-medium mb-1">Confirm New Password</label>
                    <div class="relative">
                    <input type="password"
                        id="confirmPasswordInput"
                        name="password_confirmation"
                        required
                        class="w-full rounded-lg border-gray-300">
                    <button
                        type="button"
                        id="togglePasswordConfirm"
                        class="absolute right-4 top-2 text-gray-500 hover:text-gray-700 transition"
                    >
                        <i id="eyeIconConfirm" class="fa-solid fa-eye"></i>
                    </button>
                    </div>

                    <p id="passwordMismatch"
                    class="hidden text-sm text-red-600 mt-1">
                        Passwords do not match
                    </p>
                </div>

                <!-- Submit -->
                <div class="flex justify-end">
                    <button id="passwordSubmit"
                            type="submit"
                            disabled
                            class="px-5 py-2 bg-blue-600 text-white rounded-lg
                                opacity-50 cursor-not-allowed">
                        Update Password
                    </button>
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

<script>
    
     // Password Toggle for Main Password
    const toggleCurrentPassword = document.getElementById('toggleCurrentPassword');
    const passwordCurrent = document.getElementById('passwordCurrent');
    const eyeCurrent = document.getElementById('eyeCurrent');

    toggleCurrentPassword.addEventListener('click', function() {
        const type = passwordCurrent.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordCurrent.setAttribute('type', type);
        
        if (type === 'password') {
            eyeCurrent.classList.remove('fa-eye-slash');
            eyeCurrent.classList.add('fa-eye');
        } else {
            eyeCurrent.classList.remove('fa-eye');
            eyeCurrent.classList.add('fa-eye-slash');
        }
    });
    
    // Password Toggle for Main Password
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('passwordInput');
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
    const passwordConfirmInput = document.getElementById('confirmPasswordInput');
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

    const confirmPasswordInput = document.getElementById('confirmPasswordInput');
    const passwordRequirements = document.getElementById('passwordRequirements');
    const passwordSubmit = document.getElementById('passwordSubmit');

    const reqLength = document.getElementById('req-length');
    const reqUppercase = document.getElementById('req-uppercase');
    const reqLowercase = document.getElementById('req-lowercase');
    const reqNumber = document.getElementById('req-number');
    const reqSpecial = document.getElementById('req-special');
    const allFulfilled = document.getElementById('allFulfilled');
    const requirementsList = document.getElementById('requirementsList');
    const passwordMismatch = document.getElementById('passwordMismatch');

    let isPasswordValid = false;
    let isMatch = false;

    function updateReq(el, valid) {
        el.className = valid ? 'text-green-600' : 'text-gray-500';
        el.querySelector('i').className = valid
            ? 'fa-solid fa-circle-check text-green-500'
            : 'fa-solid fa-circle-xmark text-red-500';
    }

    function validatePassword() {
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

        isPasswordValid = Object.values(checks).every(Boolean);

        allFulfilled.classList.toggle('hidden', !isPasswordValid);
        requirementsList.classList.toggle('hidden', isPasswordValid);

        validateMatch();
    }

    function validateMatch() {
        isMatch =
            passwordInput.value &&
            confirmPasswordInput.value &&
            passwordInput.value === confirmPasswordInput.value;

        passwordMismatch.classList.toggle('hidden', isMatch || !confirmPasswordInput.value);
        updateSubmit();
    }

    function updateSubmit() {
        const canSubmit = isPasswordValid && isMatch;
        passwordSubmit.disabled = !canSubmit;
        passwordSubmit.classList.toggle('opacity-50', !canSubmit);
        passwordSubmit.classList.toggle('cursor-not-allowed', !canSubmit);
    }

    passwordInput.addEventListener('focus', () => {
        passwordRequirements.classList.remove('hidden');
    });

    passwordInput.addEventListener('blur', () => {
        passwordRequirements.classList.add('hidden');
    });

    passwordInput.addEventListener('input', validatePassword);
    confirmPasswordInput.addEventListener('input', validateMatch);
</script>

</x-app-layout>
