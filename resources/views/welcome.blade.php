<x-public-layout>
    <!-- Hero Section -->
    <section id="home" class="relative min-h-screen flex items-end justify-center overflow-hidden pt-32 pb-[150px]">
        <!-- Background Image Integration -->
        <div class="absolute inset-0 z-0">
            <!-- Gradient Overlay matching Figma -->
            <div class="absolute inset-0 bg-gradient-to-b from-black/60 to-gray-800/90 mix-blend-multiply z-10">
            </div>
            <img src="{{ asset('images/home-hero-BGimage.webp') }}" alt="Background" class="w-full h-full object-cover">
        </div>

        <!-- Content -->
        <div class="relative z-20 text-center max-w-7xl px-4 animate-fade-in-up flex flex-col items-center">

            <h1
                class="reveal relative z-10 text-[40px] md:text-[64px] font-normal leading-[1] tracking-[-0.04em] mb-8 text-white text-center font-sans">
                Achieve your dream IELTS score <br />
                with a smarter study plan
            </h1>

            <p
                class="reveal text-lg md:text-xl text-gray-200 font-medium max-w-3xl mx-auto mb-12 leading-relaxed tracking-wide">
                Master IELTS offer personalised coaching and expert-led lessons <br class="hidden md:block" />
                designed to secure a 7+ band.
            </p>

            <button
                class="reveal px-10 py-4 bg-white text-[#0B2336] text-lg font-bold rounded-full hover:bg-gray-100 hover:scale-105 active:scale-95 transition-all shadow-xl inline-flex items-center gap-2 group">
                Let’s begin your prep
            </button>
        </div>
    </section>

    <!-- Statistics Section -->
    <section
        class="bg-[linear-gradient(90.81deg,#045F98_-14.55%,#011F32_105.37%)] border-t border-white/10 relative z-20"
        x-data="{
            animate(target, duration, isFloat = false) {
                let start = 0;
                const step = (timestamp) => {
                    if (!start) start = timestamp;
                    const progress = Math.min((timestamp - start) / duration, 1);
                    if (isFloat) {
                        return (progress * target).toFixed(1);
                    }
                    return Math.floor(progress * target);
                };
                return step;
            }
        }">
        <div
            class="max-w-7xl mx-auto grid grid-cols-2 md:grid-cols-4 divide-x divide-white/20 border-b border-white/20">
            <!-- Stat 1 -->
            <div class="reveal py-12 px-6 flex flex-col items-center justify-center text-center group hover:bg-white/5 transition"
                x-data="{ current: 0, target: 200 }" x-intersect.once="
                    let start = 0;
                    const step = (timestamp) => {
                        if (!start) start = timestamp;
                        const progress = Math.min((timestamp - start) / 2000, 1);
                        current = Math.floor(progress * target);
                        if (progress < 1) window.requestAnimationFrame(step);
                    };
                    window.requestAnimationFrame(step);
                ">
                <span
                    class="font-sans font-light text-[50px] md:text-[64px] leading-[0.92] tracking-normal text-white mb-2"
                    x-text="current + '+'">200+</span>
                <span
                    class="font-sans font-medium text-[16px] md:text-[18px] leading-[1.1] tracking-normal text-gray-300 text-center">Successful
                    students</span>
            </div>
            <!-- Stat 2 -->
            <div class="reveal py-12 px-6 flex flex-col items-center justify-center text-center group hover:bg-white/5 transition"
                x-data="{ current: 0.0, target: 7.5 }" x-intersect.once="
                    let start = 0;
                    const step = (timestamp) => {
                        if (!start) start = timestamp;
                        const progress = Math.min((timestamp - start) / 2000, 1);
                        current = (progress * target).toFixed(1);
                        if (progress < 1) window.requestAnimationFrame(step);
                    };
                    window.requestAnimationFrame(step);
                ">
                <span
                    class="font-sans font-light text-[50px] md:text-[64px] leading-[0.92] tracking-normal text-white mb-2"
                    x-text="current">7.5</span>
                <span
                    class="font-sans font-medium text-[16px] md:text-[18px] leading-[1.1] tracking-normal text-gray-300 text-center">Average
                    band score</span>
            </div>
            <!-- Stat 3 -->
            <div class="reveal py-12 px-6 flex flex-col items-center justify-center text-center group hover:bg-white/5 transition"
                x-data="{ current: 0, target: 250 }" x-intersect.once="
                    let start = 0;
                    const step = (timestamp) => {
                        if (!start) start = timestamp;
                        const progress = Math.min((timestamp - start) / 2000, 1);
                        current = Math.floor(progress * target);
                        if (progress < 1) window.requestAnimationFrame(step);
                    };
                    window.requestAnimationFrame(step);
                ">
                <span
                    class="font-sans font-light text-[50px] md:text-[64px] leading-[0.92] tracking-normal text-white mb-2"
                    x-text="current + '+'">250+</span>
                <span
                    class="font-sans font-medium text-[16px] md:text-[18px] leading-[1.1] tracking-normal text-gray-300 text-center">Videos
                    & quizzes</span>
            </div>
            <!-- Stat 4 -->
            <div class="reveal py-12 px-6 flex flex-col items-center justify-center text-center group hover:bg-white/5 transition"
                x-data="{ current: 0, target: 150 }" x-intersect.once="
                    let start = 0;
                    const step = (timestamp) => {
                        if (!start) start = timestamp;
                        const progress = Math.min((timestamp - start) / 2000, 1);
                        current = Math.floor(progress * target);
                        if (progress < 1) window.requestAnimationFrame(step);
                    };
                    window.requestAnimationFrame(step);
                ">
                <span
                    class="font-sans font-light text-[50px] md:text-[64px] leading-[0.92] tracking-normal text-white mb-2"
                    x-text="current + '+'">150+</span>
                <span
                    class="font-sans font-medium text-[16px] md:text-[18px] leading-[1.1] tracking-normal text-gray-300 text-center">Comprehensive
                    Notes</span>
            </div>
        </div>
    </section>

</x-public-layout>