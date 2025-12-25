<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Master IELTS') }} - Registration</title>

    <!-- Fonts: Satoshi -->
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@300,400,500,700,900&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'Dark-Blue': '#0B2336',
                        'Light-Blue': '#4A90E2',
                        'Hover-Blue': '#0F3957',
                        'White': '#FFFFFF',
                        'Black': '#000000',
                    },
                    fontFamily: {
                        'sans': ['Satoshi', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Login/Register Page Styles */
        .login-bg-gradient {
            background: linear-gradient(90.81deg, #045F98 -14.55%, #011F32 105.37%);
        }

        .login-card-bg {
            background-image: url("{{ asset('images/home-page-contact-Bg.webp') }}");
        }

        .login-card-overlay {
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.32) -31.93%, #1B242F 100%);
        }
    </style>
</head>

<body class="font-sans antialiased flex items-center justify-center relative min-h-screen login-bg-gradient">

    <!-- Decorative Vector (Top Right) -->
    <img src="{{ asset('images/login Vector.png') }}" alt=""
        class="absolute top-0 right-0 pointer-events-none opacity-30 mix-blend-screen z-0">

    <div class="w-full h-full">
        <!-- Main Wrapper with Padding -->
        <div class="w-full h-full flex items-center justify-center p-6 lg:p-[60px_65px] relative z-10">
            <!-- Inner Container ensuring gap -->
            <div class="flex w-full max-w-7xl h-full gap-8 lg:gap-[48px]">

                <!-- Left Side: Floating Image Card -->
                <div
                    class="hidden lg:flex shrink-0 relative rounded-[24px] overflow-hidden bg-cover bg-center shadow-2xl lg:w-[645px] lg:h-[836px] login-card-bg">

                    <!-- Specific Gradient Overlay -->
                    <div class="absolute inset-0 login-card-overlay"></div>

                    <div class="relative z-10 flex flex-col justify-between h-full px-6 py-12">
                        <!-- Logo -->
                        <img src="{{ asset('images/loginpage_logo.png') }}" alt="Master IELTS"
                            class="h-10 w-auto object-contain self-start">

                        <!-- Bottom Text -->
                        <div class="max-w-xl">
                            <p
                                class="text-white text-[18px] md:text-[20px] font-medium leading-[1.4] tracking-wide drop-shadow-md">
                                Plan, draft, and refine essays with strategies designed to improve clarity, cohesion,
                                and task achievement. Our Writing Course trains you to plan effectively, express your
                                thoughts
                                clearly, and write with confidence under exam conditions.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Registration Form -->
                <div class="flex-1 relative flex flex-col justify-center text-white">

                    <!-- Form Container -->
                    <div class="relative z-10 flex flex-col w-full">
                        <h1 class="text-[32px] md:text-[40px] font-normal leading-tight mb-3 flex items-center gap-3">
                            Student Registration
                            <img src="{{ asset('images/hand_svg.svg') }}" alt="Wave"
                                class="w-[35px] h-auto inline-block">
                        </h1>
                        <p class="text-white/80 text-[16px] md:text-[18px] mb-8">Please fill the form to create an
                            account.</p>

                        <form class="flex flex-col gap-5" method="POST" action="{{ route('register') }}">
                            @csrf

                            <!-- Name -->
                            <div class="flex flex-col gap-2">
                                <label for="name" class="text-white text-[16px] font-medium">Full Name*</label>
                                <div class="relative">
                                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                        autofocus autocomplete="name"
                                        class="w-full bg-[#0B2336]/30 border border-[#4A90E2]/50 rounded-lg px-4 py-3.5 text-white placeholder-white/50 focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition-all backdrop-blur-sm">
                                    <!-- User Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="w-5 h-5 absolute right-4 top-1/2 -translate-y-1/2 text-white/70">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>
                                </div>
                                <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-300" />
                            </div>

                            <!-- Email -->
                            <div class="flex flex-col gap-2">
                                <label for="email" class="text-white text-[16px] font-medium">Email*</label>
                                <div class="relative">
                                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                        autocomplete="username"
                                        class="w-full bg-[#0B2336]/30 border border-[#4A90E2]/50 rounded-lg px-4 py-3.5 text-white placeholder-white/50 focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition-all backdrop-blur-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="w-5 h-5 absolute right-4 top-1/2 -translate-y-1/2 text-white/70">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>
                                </div>
                                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-300" />
                            </div>

                            <!-- Password -->
                            <div class="flex flex-col gap-2" x-data="{ showPassword: false }">
                                <label for="password" class="text-white text-[16px] font-medium">Password*</label>
                                <div class="relative">
                                    <input :type="showPassword ? 'text' : 'password'" id="password" name="password"
                                        required autocomplete="new-password"
                                        class="w-full bg-[#0B2336]/30 border border-[#4A90E2]/50 rounded-lg px-4 py-3.5 text-white placeholder-white/50 focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition-all backdrop-blur-sm">

                                    <button type="button" @click="showPassword = !showPassword"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-white/70 hover:text-white transition cursor-pointer focus:outline-none">
                                        <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                            class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <svg x-show="showPassword" x-cloak xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                            class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                        </svg>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-300" />
                            </div>

                            <!-- Confirm Password -->
                            <div class="flex flex-col gap-2">
                                <label for="password_confirmation" class="text-white text-[16px] font-medium">Confirm
                                    Password*</label>
                                <div class="relative">
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        required autocomplete="new-password"
                                        class="w-full bg-[#0B2336]/30 border border-[#4A90E2]/50 rounded-lg px-4 py-3.5 text-white placeholder-white/50 focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition-all backdrop-blur-sm">
                                    <!-- Person Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="w-5 h-5 absolute right-4 top-1/2 -translate-y-1/2 text-white/70">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>
                                </div>
                                <x-input-error :messages="$errors->get('password_confirmation')"
                                    class="mt-2 text-red-300" />
                            </div>

                            <!-- Submit Button -->
                            <button type="submit"
                                class="w-full bg-white text-[#0B2336] text-[18px] font-bold py-3.5 rounded-lg hover:bg-gray-100 hover:scale-[1.02] active:scale-[0.98] transition-all shadow-xl mt-4">
                                Create an account
                            </button>
                        </form>

                        <!-- Divider -->
                        <div class="w-full h-[1px] bg-white/20 mt-8 mb-6"></div>

                        <!-- Footer -->
                        <div class=" text-white/80 text-[16px]">
                            Already a member?
                            <a href="{{ route('login') }}"
                                class="text-white font-bold hover:underline underline-offset-4 decoration-white ml-1">Sign
                                in</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>