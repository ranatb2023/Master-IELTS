<nav x-data="{ mobileMenuOpen: false, scrolled: false, dropdownOpen: null }"
    @scroll.window="scrolled = (window.pageYOffset > 20)"
    class="fixed top-6 left-0 right-0 z-[100] flex justify-center px-4 transition-all duration-300">

    <div
        class="bg-[#0B2336] w-full max-w-[1300px] rounded-full border border-white/10 px-8 py-3.5 flex justify-between items-center shadow-2xl transition-all duration-300 relative">

        <!-- Logo -->
        <a href="{{ url('/') }}" class="flex items-center gap-2 group shrink-0">
            <img src="{{ asset('images/master-ilets-logo.webp') }}" alt="Master IELTS"
                class="h-10 w-auto object-contain">
        </a>

        <!-- Desktop Menu -->
        <div class="hidden md:flex items-center gap-10">
            <!-- Home -->
            <a href="{{ url('/') }}" class="text-white/80 text-[16px] font-medium hover:text-white transition">Home</a>

            <!-- Courses Dropdown -->
            <div class="relative group" @mouseenter="dropdownOpen = 'courses'" @mouseleave="dropdownOpen = null">
                <button
                    class="text-white text-[16px] font-medium border-b-2 border-transparent hover:border-white pb-0.5 hover:opacity-100 transition flex items-center gap-1">
                    Courses
                    <svg class="w-3.5 h-3.5 transition-transform group-hover:rotate-180" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                        </path>
                    </svg>
                </button>
                <!-- Dropdown Content -->
                <div x-show="dropdownOpen === 'courses'" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-2"
                    class="absolute top-full left-1/2 -translate-x-1/2 mt-4 w-48 bg-white rounded-xl shadow-xl overflow-hidden py-2 text-black"
                    style="display: none;">
                    <a href="{{ route('reading') }}"
                        class="block px-5 py-2.5 {{ request()->routeIs('reading') ? 'bg-[#0B2336] text-white' : 'hover:bg-[#0B2336] hover:text-white' }} transition-colors duration-200 text-sm font-medium">Reading</a>
                    <a href="{{ route('listening') }}"
                        class="block px-5 py-2.5 {{ request()->routeIs('listening') ? 'bg-[#0B2336] text-white' : 'hover:bg-[#0B2336] hover:text-white' }} transition-colors duration-200 text-sm font-medium">Listening</a>
                    <a href="{{ route('writing') }}"
                        class="block px-5 py-2.5 {{ request()->routeIs('writing') ? 'bg-[#0B2336] text-white' : 'hover:bg-[#0B2336] hover:text-white' }} transition-colors duration-200 text-sm font-medium">Writing</a>
                    <a href="{{ route('speaking') }}"
                        class="block px-5 py-2.5 {{ request()->routeIs('speaking') ? 'bg-[#0B2336] text-white' : 'hover:bg-[#0B2336] hover:text-white' }} transition-colors duration-200 text-sm font-medium">Speaking</a>
                </div>
            </div>

            <!-- Practice Tests -->
            <a href="#" class="text-white/80 text-[16px] font-medium hover:text-white transition">Practice Tests</a>

            <!-- Blog -->
            <a href="{{ url('/blog') }}"
                class="text-white/80 text-[16px] font-medium hover:text-white transition">Blogs</a>
        </div>

        <!-- Login / Mobile Toggle -->
        <div class="flex items-center gap-4 shrink-0">
            @auth
                <a href="{{ url('/dashboard') }}"
                    class="hidden md:flex px-6 py-2.5 bg-transparent border border-white/30 text-white text-[16px] font-bold rounded-full hover:bg-white/10 transition shadow-sm tracking-wide">
                    Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}" class="hidden md:block">
                    @csrf
                    <button type="submit"
                        class="px-8 py-2.5 bg-white text-[#0B2336] text-[16px] font-bold rounded-full hover:bg-gray-100 transition shadow-lg hover:scale-105 active:scale-95 tracking-wide">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}"
                    class="hidden md:flex px-6 py-2.5 bg-transparent border border-white/30 text-white text-[16px] font-bold rounded-full hover:bg-white/10 transition shadow-sm tracking-wide">
                    Login
                </a>
                <a href="{{ route('register') }}"
                    class="hidden md:flex px-8 py-2.5 bg-white text-[#0B2336] text-[16px] font-bold rounded-full hover:bg-gray-100 transition shadow-lg hover:scale-105 active:scale-95 tracking-wide">
                    Register
                </a>
            @endauth

            <!-- Mobile Menu Button -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 text-white">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"></path>
                    <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu Dropdown -->
    <div class="relative z-[200]" aria-labelledby="slide-over-title" role="dialog" aria-modal="true"
        x-show="mobileMenuOpen" style="display: none;">
        <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" x-show="mobileMenuOpen"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <div class="pointer-events-auto w-screen max-w-[300px]" x-show="mobileMenuOpen"
                        x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500"
                        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                        x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500"
                        x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                        @click.away="mobileMenuOpen = false">

                        <div
                            class="flex h-full flex-col overflow-y-auto bg-[#0B2336] shadow-2xl py-6 px-6 border-l border-white/10">
                            <div class="flex items-center justify-between mb-10 border-b border-white/10 pb-6">
                                <div class="flex items-center gap-2">
                                    <img src="{{ asset('images/master-ilets-logo.webp') }}" alt="Logo"
                                        class="h-8 w-auto">
                                </div>
                                <button type="button"
                                    class="rounded-md text-gray-300 hover:text-white focus:outline-none transition"
                                    @click="mobileMenuOpen = false">
                                    <span class="sr-only">Close panel</span>
                                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="flex flex-col gap-2 font-sans" x-data="{ open: null }">
                                <a href="{{ url('/') }}" @click="mobileMenuOpen = false"
                                    class="text-[20px] font-medium py-3 border-b border-white/5 text-white/70 hover:text-blue-400 transition">Home</a>

                                <!-- Courses Dropdown -->
                                <div>
                                    <button @click="open = (open === 'courses' ? null : 'courses')"
                                        class="w-full flex items-center justify-between text-[20px] font-medium py-3 border-b border-white/5 text-white hover:text-blue-400 transition outline-none">
                                        Courses
                                        <svg class="w-4 h-4 transition-transform duration-200"
                                            :class="open === 'courses' ? 'rotate-180' : ''" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div x-show="open === 'courses'" x-collapse
                                        class="pl-4 flex flex-col gap-2 mt-2 mb-2 border-l border-white/10">
                                        <a href="{{ route('reading') }}"
                                            class="text-[18px] {{ request()->routeIs('reading') ? 'text-white font-bold' : 'text-white/70 hover:text-white' }} py-1">Reading</a>
                                        <a href="{{ route('listening') }}"
                                            class="text-[18px] {{ request()->routeIs('listening') ? 'text-white font-bold' : 'text-white/70 hover:text-white' }} py-1">Listening</a>
                                        <a href="{{ route('writing') }}"
                                            class="text-[18px] {{ request()->routeIs('writing') ? 'text-white font-bold' : 'text-white/70 hover:text-white' }} py-1">Writing</a>
                                        <a href="{{ route('speaking') }}"
                                            class="text-[18px] {{ request()->routeIs('speaking') ? 'text-white font-bold' : 'text-white/70 hover:text-white' }} py-1">Speaking</a>
                                    </div>
                                </div>

                                <!-- Practice Tests -->
                                <a href="#" @click="mobileMenuOpen = false"
                                    class="text-[20px] font-medium py-3 border-b border-white/5 text-white/90 hover:text-blue-400 transition">Free Practice Tests</a>

                                <a href="{{ url('/blog') }}" @click="mobileMenuOpen = false"
                                    class="text-[20px] font-medium py-3 border-b border-white/5 text-white/90 hover:text-blue-400 transition">Blogs</a>

                                <div class="flex flex-col gap-4 mt-8">
                                    @auth
                                        <a href="{{ url('/dashboard') }}"
                                            class="w-full py-3.5 bg-transparent border border-white/30 text-white text-center text-[18px] font-bold rounded-full hover:bg-white/10 transition shadow-sm">Dashboard</a>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit"
                                                class="w-full py-3.5 bg-white text-[#0B2336] text-center text-[18px] font-bold rounded-full hover:bg-gray-100 transition shadow-lg">Logout</button>
                                        </form>
                                    @else
                                        <a href="{{ route('login') }}"
                                            class="w-full py-3.5 bg-transparent border border-white/30 text-white text-center text-[18px] font-bold rounded-full hover:bg-white/10 transition shadow-sm">Login</a>
                                        <a href="{{ route('register') }}"
                                            class="w-full py-3.5 bg-white text-[#0B2336] text-center text-[18px] font-bold rounded-full hover:bg-gray-100 transition shadow-lg">Register</a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>