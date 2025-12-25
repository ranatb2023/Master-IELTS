<footer class="bg-[#003B5C] text-white pb-8 font-sans">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Main Content -->
        <div class="flex flex-col lg:flex-row gap-8 lg:gap-32">
            <!-- Left: Branding -->
            <div class="lg:w-[400px] shrink-0 pt-10 pb-8 lg:pb-16">
                <!-- Use asset helper for the footer logo -->
                <img src="{{ asset('images/footer logo.png') }}" alt="Master IELTS" class="h-[70px] w-auto mb-8">
                <p class="text-[18px] leading-[1.6] opacity-90 max-w-sm">
                    Discover expertly designed courses, personalised support from our qualified tutor, and extensive
                    study resources to build your confidence, sharpen your skills, and achieve the score you’re
                    aiming for.
                </p>
            </div>

            <!-- Right: Navigation & Info -->
            <div class="lg:flex-1 lg:border-l border-white/20 flex flex-col justify-between pt-10 pb-8 lg:pb-16">
                <!-- Vertical Links -->
                <nav class="flex flex-col gap-[20px] mb-12 pl-0 lg:pl-16">
                    <a href="{{ url('/') }}"
                        class="font-sans font-normal text-[36px] leading-[0.88] tracking-[-0.05em] hover:opacity-80 transition-opacity">Home</a>
                    <a href="{{ url('/blog') }}"
                        class="font-sans font-normal text-[36px] leading-[0.88] tracking-[-0.05em] hover:opacity-80 transition-opacity">Blog</a>
                    <a href="#"
                        class="font-sans font-normal text-[36px] leading-[0.88] tracking-[-0.05em] hover:opacity-80 transition-opacity">Courses</a>
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="font-sans font-normal text-[36px] leading-[0.88] tracking-[-0.05em] hover:opacity-80 transition-opacity">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}"
                            class="font-sans font-normal text-[36px] leading-[0.88] tracking-[-0.05em] hover:opacity-80 transition-opacity">Login</a>
                    @endauth
                </nav>

                <!-- Legal & Socials -->
                <div
                    class="flex flex-col md:flex-row justify-between items-center pt-8 border-t border-white/20 gap-6 pl-0 lg:pl-16">
                    <div class="flex flex-col gap-2 text-[16px] opacity-90 items-start">
                        <a href="#" class="hover:text-white transition">Privacy Policy</a>
                        <a href="#" class="hover:text-white transition">Terms & Condition</a>
                    </div>
                    <div class="flex gap-6 items-center">
                        <!-- Email -->
                        <a href="#" class="hover:opacity-80 transition">
                            <svg class="w-6 h-6" fill="white" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                            </svg>
                        </a>
                        <!-- Facebook -->
                        <a href="#" class="hover:opacity-80 transition">
                            <svg class="w-6 h-6" fill="white" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5c0-2 1.33-3 3-3 1.46 0 2.22.11 2.33.15v2.7h-1.6c-.96 0-1.14.46-1.14 1.13V12h3l-.4 3h-2.6v6.8c4.56-.93 8-4.96 8-9.8z" />
                            </svg>
                        </a>
                        <!-- Instagram -->
                        <a href="#" class="hover:opacity-80 transition">
                            <svg class="w-6 h-6" fill="white" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2m-.2 2A3.6 3.6 0 0 0 4 7.6v8.8C4 18.39 5.61 20 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6C20 5.61 18.39 4 16.4 4H7.6m9.65 1.5a1.25 1.25 0 0 1 1.25 1.25A1.25 1.25 0 0 1 17.25 8 1.25 1.25 0 0 1 16 6.75a1.25 1.25 0 0 1 1.25-1.25M12 7a5 5 0 0 1 5 5 5 5 0 0 1-5 5 5 5 0 0 1-5-5 5 5 0 0 1 5-5m0 2a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="border-t border-white/20 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm opacity-70">
            <div>Copyright {{ date('Y') }} Master IELTS Online</div>
            <div>Designed by <a href="https://trickleup.co.uk/" target="_blank"
                    class="font-bold underline hover:text-white transition">Trickle up</a></div>
        </div>
    </div>
</footer>