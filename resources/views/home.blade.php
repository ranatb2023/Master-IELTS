<x-public-layout>
    @section('title', 'Home - Master IELTS')

    {{-- Hero Section --}}
    <section id="home" class="relative min-h-screen flex items-end justify-center overflow-hidden pt-32 pb-[150px]">
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-b from-black/60 to-gray-800/90 mix-blend-multiply z-10"></div>
            <img src="{{ asset('images/home-hero-BGimage.webp') }}" alt="Background" class="w-full h-full object-cover">
        </div>

        <div class="relative z-20 text-center max-w-7xl px-4 animate-fade-in-up flex flex-col items-center">
            <h1 class="reveal relative z-10 text-[32px] sm:text-[40px] md:text-[64px] font-normal leading-[1.1] md:leading-[1] tracking-[-0.04em] mb-6 md:mb-8 text-white text-center font-sans px-4">
                Achieve your dream IELTS score <br /> with a smarter study plan
            </h1>

            <p class="reveal text-base md:text-lg lg:text-xl text-gray-200 font-medium max-w-3xl mx-auto mb-8 md:mb-12 leading-relaxed tracking-wide px-4">
                Master IELTS offer personalised coaching and expert-led lessons <br class="hidden md:block" /> designed to secure a 7+ band.
            </p>

            <div class="reveal flex flex-col sm:flex-row gap-4">
                @guest
                    <a href="{{ route('register') }}" class="px-10 py-4 bg-white text-[#0B2336] text-lg font-bold rounded-full hover:bg-gray-100 hover:scale-105 active:scale-95 transition-all shadow-xl inline-flex items-center gap-2 group">
                        Let's begin your prep
                    </a>
                @else
                    <a href="{{ route('student.dashboard') }}" class="px-10 py-4 bg-white text-[#0B2336] text-lg font-bold rounded-full hover:bg-gray-100 hover:scale-105 active:scale-95 transition-all shadow-xl inline-flex items-center gap-2 group">
                        Go to Dashboard
                    </a>
                @endguest
            </div>
        </div>
    </section>

    {{-- Statistics Section --}}
    <section class="bg-[linear-gradient(90.81deg,#045F98_-14.55%,#011F32_105.37%)] border-t border-white/10 relative z-20" x-data="{}">
        <div class="max-w-7xl mx-auto grid grid-cols-2 md:grid-cols-4 divide-x divide-white/20 border-b border-white/20">
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
                <span class="font-sans font-light text-[50px] md:text-[64px] leading-[0.92] tracking-normal text-white mb-2" x-text="current + '+'">200+</span>
                <span class="font-sans font-medium text-[16px] md:text-[18px] leading-[1.1] tracking-normal text-gray-300 text-center">Successful students</span>
            </div>

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
                <span class="font-sans font-light text-[50px] md:text-[64px] leading-[0.92] tracking-normal text-white mb-2" x-text="current">7.5</span>
                <span class="font-sans font-medium text-[16px] md:text-[18px] leading-[1.1] tracking-normal text-gray-300 text-center">Average band score</span>
            </div>

            <div class="reveal py-12 px-6 flex flex-col items-center justify-center text-center group hover:bg-white/5 transition"
                x-data="{ current: 0, target: 50 }" x-intersect.once="
                    let start = 0;
                    const step = (timestamp) => {
                        if (!start) start = timestamp;
                        const progress = Math.min((timestamp - start) / 2000, 1);
                        current = Math.floor(progress * target);
                        if (progress < 1) window.requestAnimationFrame(step);
                    };
                    window.requestAnimationFrame(step);
                ">
                <span class="font-sans font-light text-[50px] md:text-[64px] leading-[0.92] tracking-normal text-white mb-2" x-text="current + '+'">50+</span>
                <span class="font-sans font-medium text-[16px] md:text-[18px] leading-[1.1] tracking-normal text-gray-300 text-center">Courses & content</span>
            </div>

            <div class="reveal py-12 px-6 flex flex-col items-center justify-center text-center group hover:bg-white/5 transition"
                x-data="{ current: 0, target: 10 }" x-intersect.once="
                    let start = 0;
                    const step = (timestamp) => {
                        if (!start) start = timestamp;
                        const progress = Math.min((timestamp - start) / 2000, 1);
                        current = Math.floor(progress * target);
                        if (progress < 1) window.requestAnimationFrame(step);
                    };
                    window.requestAnimationFrame(step);
                ">
                <span class="font-sans font-light text-[50px] md:text-[64px] leading-[0.92] tracking-normal text-white mb-2" x-text="current + '+'">10+</span>
                <span class="font-sans font-medium text-[16px] md:text-[18px] leading-[1.1] tracking-normal text-gray-300 text-center">Expert Tutors</span>
            </div>
        </div>
    </section>

    {{-- Learn Anywhere Section --}}
    <section class="relative py-[200px] px-4 overflow-hidden bg-gray-900">
        <div class="absolute inset-0 opacity-40">
            <img src="{{ asset('images/home-page-learn-anywhere-Bgimage.webp') }}" class="w-full h-full object-cover">
        </div>

        <div class="relative z-10 max-w-7xl mx-auto flex flex-col gap-[10px]">
            <div class="reveal w-full text-left">
                <h2 class="font-sans font-normal text-[36px] md:text-[52px] lg:text-[64px] leading-[0.9] md:leading-[0.85] tracking-[-0.04em] text-white mb-4 md:mb-6">
                    Learn anywhere, <br /> anytime
                </h2>
                <p class="font-sans font-medium text-[18px] md:text-[20px] lg:text-[24px] leading-[1.2] md:leading-[1.1] tracking-[-0.04em] text-gray-300 max-w-md">
                    Worldwide access so you can learn without the location holding you back.
                </p>
            </div>

            <div class="reveal flex flex-col md:flex-row w-full gap-4 h-auto md:h-[400px] items-stretch" x-data="{ active: 1 }">
                {{-- Card 1 --}}
                <div class="relative overflow-hidden cursor-pointer transition-all duration-500 ease-in-out w-full md:w-auto"
                    :class="active === 1 ? 'md:flex-[1.34] h-[300px] md:h-[356px] bg-white rounded-[16px]' : 'md:flex-1 h-[200px] md:h-[265px] bg-white/10 backdrop-blur-md border border-white/20 hover:bg-white/20 rounded-[16px]'"
                    @mouseenter="active = 1"
                    @click="active = 1">
                    <div class="absolute inset-0 p-[20px] md:p-[32px] flex flex-col justify-between">
                        <div class="flex flex-col h-full justify-center transition-all duration-500">
                            <template x-if="active === 1">
                                <div class="animate-fade-in flex flex-col h-full justify-between">
                                    <h3 class="font-sans font-normal text-[32px] md:text-[48.34px] leading-[1] tracking-[-0.04em] text-[#082E4E] mb-4 md:mb-6">
                                        Bite-sized and <br /> structured <br /> content
                                    </h3>
                                    <p class="font-sans font-normal text-[18px] md:text-[26.86px] leading-[1.2] tracking-[-0.04em] text-[#082E4E]">
                                        Lots of free content on the internet, but it's better to get your tests graded to assess your knowledge
                                    </p>
                                </div>
                            </template>
                            <template x-if="active !== 1">
                                <div class="flex items-center justify-center h-full animate-fade-in">
                                    <h3 class="font-sans font-normal text-[24px] md:text-[36px] leading-[1] tracking-[-0.04em] text-white text-center">
                                        Bite-sized and <br /> structured <br /> content
                                    </h3>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Card 2 --}}
                <div class="relative overflow-hidden cursor-pointer transition-all duration-500 ease-in-out w-full md:w-auto"
                    :class="active === 2 ? 'md:flex-[1.34] h-[300px] md:h-[356px] bg-white rounded-[16px]' : 'md:flex-1 h-[200px] md:h-[265px] bg-white/10 backdrop-blur-md border border-white/20 hover:bg-white/20 rounded-[16px]'"
                    @mouseenter="active = 2"
                    @click="active = 2">
                    <div class="absolute inset-0 p-[20px] md:p-[32px] flex flex-col justify-between">
                        <div class="flex flex-col h-full justify-center transition-all duration-500">
                            <template x-if="active === 2">
                                <div class="animate-fade-in flex flex-col h-full justify-between">
                                    <h3 class="font-sans font-normal text-[32px] md:text-[48.34px] leading-[1] tracking-[-0.04em] text-[#082E4E] mb-4 md:mb-6">
                                        Flexible study <br /> plans
                                    </h3>
                                    <p class="font-sans font-normal text-[18px] md:text-[26.86px] leading-[1.2] tracking-[-0.04em] text-[#082E4E]">
                                        Study whenever it works for you, move at your own pace, and stay motivated with progress tracking.
                                    </p>
                                </div>
                            </template>
                            <template x-if="active !== 2">
                                <div class="flex items-center justify-center h-full animate-fade-in">
                                    <h3 class="font-sans font-normal text-[24px] md:text-[36px] leading-[1] tracking-[-0.04em] text-white text-center">
                                        Flexible study <br /> plans
                                    </h3>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Card 3 --}}
                <div class="relative overflow-hidden cursor-pointer transition-all duration-500 ease-in-out w-full md:w-auto"
                    :class="active === 3 ? 'md:flex-[1.34] h-[300px] md:h-[356px] bg-white rounded-[16px]' : 'md:flex-1 h-[200px] md:h-[265px] bg-white/10 backdrop-blur-md border border-white/20 hover:bg-white/20 rounded-[16px]'"
                    @mouseenter="active = 3"
                    @click="active = 3">
                    <div class="absolute inset-0 p-[20px] md:p-[32px] flex flex-col justify-between">
                        <div class="flex flex-col h-full justify-center transition-all duration-500">
                            <template x-if="active === 3">
                                <div class="animate-fade-in flex flex-col h-full justify-between">
                                    <h3 class="font-sans font-normal text-[32px] md:text-[48.34px] leading-[1] tracking-[-0.04em] text-[#082E4E] mb-4 md:mb-6">
                                        Cancel <br /> anytime
                                    </h3>
                                    <p class="font-sans font-normal text-[18px] md:text-[26.86px] leading-[1.2] tracking-[-0.04em] text-[#082E4E]">
                                        Upgrade your skills with full flexibility. Cancel anytime if life gets busy for a smoother learning experience.
                                    </p>
                                </div>
                            </template>
                            <template x-if="active !== 3">
                                <div class="flex items-center justify-center h-full animate-fade-in">
                                    <h3 class="font-sans font-normal text-[24px] md:text-[36px] leading-[1] tracking-[-0.04em] text-white text-center">
                                        Cancel <br /> anytime
                                    </h3>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Roadmap Section --}}
    <section class="py-[200px] px-4 text-white relative" style="background: linear-gradient(159.48deg, #045F98 -19.19%, #011F32 129.61%);">
        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-start justify-between gap-16 lg:gap-[20px]">
            <div class="reveal lg:w-[45%] lg:sticky lg:top-32 self-start animate-fade-in-up">
                <h2 class="font-sans font-normal text-[36px] md:text-[52px] lg:text-[64px] leading-[1.1] md:leading-[1] tracking-[-0.04em] text-white mb-[150px] md:mb-[200px] lg:mb-[300px]">
                    Your roadmap to <br /> 7+ IELTS band
                </h2>
                <p class="font-sans font-medium text-[20px] leading-[1.1] tracking-[-0.04em] text-white/80 mb-10 w-full max-w-sm">
                    The IELTS score that changes everything is closer than you think.
                </p>
                @guest
                    <a href="{{ route('register') }}" class="w-[177px] h-[56px] bg-white text-[#082E4E] font-sans font-bold text-[18px] leading-[1.1] tracking-normal rounded-full flex items-center justify-center gap-[10px] hover:shadow-lg hover:-translate-y-1 transition transform">
                        Join the course
                    </a>
                @else
                    <a href="{{ route('student.dashboard') }}" class="w-[177px] h-[56px] bg-white text-[#082E4E] font-sans font-bold text-[18px] leading-[1.1] tracking-normal rounded-full flex items-center justify-center gap-[10px] hover:shadow-lg hover:-translate-y-1 transition transform">
                        Go to Dashboard
                    </a>
                @endguest
            </div>

            <div class="flex-1 flex flex-col gap-6 w-full items-end pb-24">
                @foreach([
                        ['01', 'Join', 'quick signup, instant access', 'top-[80px] md:top-[140px] z-10'],
                        ['02', 'Learn', 'lessons for Reading, Listening, Writing & Speaking', 'top-[100px] md:top-[165px] z-20'],
                        ['03', 'Practice', 'quizzes, mock tests, and feedback', 'top-[120px] md:top-[190px] z-30'],
                        ['04', 'Improve', 'follow your personalised study plan', 'top-[140px] md:top-[215px] z-40'],
                        ['05', 'Score', 'walk into the exam ready to shine', 'top-[160px] md:top-[240px] z-50']
                    ] as $step)
                                    <div class="reveal sticky {{ $step[3] }} bg-white w-full md:w-[690px] h-auto min-h-[350px] md:h-[420px] p-[24px] md:p-[34px] rounded-[17px] border-[1.4px] border-transparent relative overflow-hidden flex flex-col justify-end items-start text-left shadow-[0px_-10px_40px_rgba(8,46,78,0.2)] transform transition duration-300">
                                        <div class="absolute top-[34px] left-[34px] w-[103.16px] h-[103.16px] bg-[rgba(4,95,152,0.2)] rounded-full pointer-events-none flex items-center justify-center font-sans font-bold text-[32px] text-[#045F98]">
                                            {{ $step[0] }}
                                        </div>
                                        <img src="{{ asset('images/curve Vector.png') }}" class="absolute top-[137px] left-0 w-full h-auto pointer-events-none" alt="Curve Decoration">
                                        <div class="relative z-10 flex flex-col gap-4">
                                            <span class="font-sans font-normal text-[32px] md:text-[50.87px] leading-[1.05] tracking-[-0.04em] text-[#082E4E]">{{ $step[1] }}</span>
                                            <span class="font-sans font-normal text-[33.91px] leading-[1.2] tracking-[-0.04em] text-[#082E4E]">{{ $step[2] }}</span>
                                        </div>
                                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Packages Section --}}
    <section id="courses" class="py-[61px] px-4 bg-[#f6fbff] text-[#082E4E] overflow-hidden relative">
        <div class="absolute top-[-135px] left-1/2 -translate-x-1/2 w-[1516px] h-[1498px] border-[2px] border-[#e8eff5] rounded-full pointer-events-none"></div>
        <div class="absolute top-[23px] left-1/2 -translate-x-1/2 w-[1196px] h-[1182px] border-[2px] border-[#e8eff5] rounded-full pointer-events-none"></div>
        <div class="absolute top-[229px] left-1/2 -translate-x-1/2 w-[780px] h-[770px] border-[2px] border-[#e8eff5] rounded-full pointer-events-none"></div>

        <div class="max-w-7xl mx-auto relative z-10">
            <div class="reveal text-center max-w-2xl mx-auto mb-16">
                <h2 class="font-sans font-normal text-[40px] md:text-[64px] leading-[1] tracking-[-0.04em] text-[#082E4E] mb-6">
                    Choose your package <br />and get started
                </h2>
                <p class="font-sans font-medium text-[20px] leading-[1.1] tracking-[-0.04em] text-[#082E4E] opacity-80">
                    Pick a plan that fits you and get everything you <br /> need to boost your IELTS score.
                </p>
            </div>

            <div class="grid lg:grid-cols-2 gap-8 items-stretch">
                @php
                    $packages = $featuredPackages ?? collect();
                    $subscriptions = $featuredSubscriptions ?? collect();
                    // Your DB has Premium first, Standard second, so we swap the assignment
                    // Standard plan (left - gradient card) - gets SECOND package from DB
                    $standardPlan = $packages->get(1) ?? $subscriptions->get(1) ?? $subscriptions->first();
                    // Premium plan (right - white card) - gets FIRST package from DB  
                    $premiumPlan = $packages->first() ?? $subscriptions->first();
                @endphp

                {{-- Standard Plan (Left, Gradient) --}}
                @if($standardPlan)
                    <x-package-card :plan="$standardPlan" variant="standard" position="left" />
                @endif

                {{-- Premium Plan (Right, White) --}}
                @if($premiumPlan)
                    <x-package-card :plan="$premiumPlan" variant="premium" position="right" />
                @endif

            </div>

        </div>
        </div>
    </section>

    {{-- Comparison Table Section (IELTS Success Plans) --}}
    <section class="pt-[64px] pb-0 px-4 bg-[#fff] text-[#082E4E]">
        <div class="max-w-7xl mx-auto">
            <h2 class="reveal font-sans font-normal text-[40px] md:text-[64px] leading-[0.76] tracking-[-0.04em] text-center mb-6 text-[#082E4E]">
                IELTS <br /> success plans
            </h2>
            <p class="reveal font-sans font-medium text-[20px] leading-[1.1] tracking-[-0.04em] text-center text-[#082E4E] mb-16 max-w-2xl mx-auto">
                Pick a plan that fits you and get everything you <br /> need to boost your IELTS score.
            </p>

            <div class="reveal overflow-x-auto pb-6">
                <table class="w-full md:min-w-[900px] text-left border-collapse">
                    <thead>
                        <tr class="bg-[#082E4E] text-white">
                            <th class="p-2 pl-2 md:p-6 md:pl-8 text-[12px] md:text-[20px] font-bold w-[34%]">Features</th>
                            <th class="p-2 md:p-6 text-[12px] md:text-[20px] font-bold w-[33%]">Standard Plan</th>
                            <th class="p-2 md:p-6 text-[12px] md:text-[20px] font-bold w-[33%]">Premium Plan</th>
                        </tr>
                    </thead>
                    <tbody class="text-[12px] md:text-[18px] font-sans font-medium">
                        @foreach([
                                ['Access Duration', '1 Month', '3 Months'],
                                ['Price', '$30', '$70 total'],
                                ['Full Access to Video Lessons', '✔', '✔'],
                                ['Mock Exams & Quizzes', '✔', '✔'],
                                ['Weekly Updated Learning Material', '✔', '✔'],
                                ['Q&A Support from Expert Tutor', '✔', '✔'],
                                ['Personal Progress Tracker', '✖ (Basic progress only)', '✔ (Detailed tracking)'],
                                ['Assignments & Personalised Feedback', '✖ (Self-study only)', '✔'],
                                ['Extra Practice Pack (Bonus PDFs)', '✖', '✔'],
                                ['Goal: Band 7+ Roadmap', '✔', '✔'],
                                ['One-to-one speaking test with an IELTS tutor', '✖', '✔']
                            ] as $row)
                                        <tr class="border-b border-gray-100">
                                            <td class="p-2 pl-2 md:p-6 md:pl-8 flex items-center gap-1 md:gap-3">
                                                <div class="w-2 h-2 md:w-3 md:h-3 bg-[#0C689F] rotate-45 shrink-0"></div>
                                                {{ $row[0] }}
                                            </td>
                                            <td class="p-2 md:p-6 {!! str_contains($row[1], '✔') ? 'text-[#15803D] font-bold' : (str_contains($row[1], '✖') ? 'text-[#EF4444] font-bold' : '') !!}">
                                                {!! str_replace(['✔', '✖'], ['<span class="text-[#15803D] font-bold mr-1 md:mr-2">✔</span>', '<span class="text-[#EF4444] font-bold mr-1 md:mr-2">✖</span>'], $row[1]) !!}
                                            </td>
                                            <td class="p-2 md:p-6 {!! str_contains($row[2], '✔') ? 'text-[#15803D] font-bold' : (str_contains($row[2], '✖') ? 'text-[#EF4444] font-bold' : '') !!}">
                                                {!! str_replace(['✔', '✖'], ['<span class="text-[#15803D] font-bold mr-1 md:mr-2">✔</span>', '<span class="text-[#EF4444] font-bold mr-1 md:mr-2">✖</span>'], $row[2]) !!}
                                            </td>
                                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- Your Progress Highlighted Section (Testimonials Carousel) --}}
    <section class="pt-[120px] pb-[60px] bg-[#f6fbff] text-[#082E4E] overflow-hidden" x-data="{ 
        current: 0, 
        total: 5, 
        autoPlayInterval: null,
        init() {
            this.startAutoPlay();
        },
        next() {
            this.stopAutoPlay();
            this.current = (this.current + 1) % this.total;
            this.startAutoPlay();
        },
        prev() {
            this.stopAutoPlay();
            this.current = (this.current - 1 + this.total) % this.total;
            this.startAutoPlay();
        },
        stopAutoPlay() {
            clearInterval(this.autoPlayInterval);
            this.autoPlayInterval = null;
        },
        startAutoPlay() {
            if (this.autoPlayInterval) return;
            this.autoPlayInterval = setInterval(() => {
                this.current = (this.current + 1) % this.total;
            }, 4000);
        },
        getStyle(index) {
            let rel = (index - this.current + this.total) % this.total;
            let bg = 'background: linear-gradient(153.36deg, #045F98 -11.75%, #011F32 105.72%); box-shadow: 0px 4px 24px rgba(0, 0, 0, 0.08);';
            
            if (rel === 0) {
                 return 'z-index: 30; opacity: 1; transform: translateX(0) scale(1); ' + bg;
            } else if (rel === 1) {
                 return 'z-index: 20; opacity: 1; transform: translateX(-40px) translateY(10px) scale(0.95) rotate(-3deg); ' + bg;
            } else if (rel === 2) {
                 return 'z-index: 10; opacity: 1; transform: translateX(-80px) translateY(20px) scale(0.90) rotate(-6deg); ' + bg;
            } else if (rel === 3) {
                 return 'z-index: 5; opacity: 1; transform: translateX(-120px) translateY(30px) scale(0.85) rotate(-9deg); ' + bg;
            } else {
                 return 'z-index: 1; opacity: 1; transform: translateX(-160px) translateY(40px) scale(0.80) rotate(-12deg); ' + bg;
            }
        }
    }" x-init="init()">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="reveal font-sans font-normal text-[64px] leading-[1] tracking-[-0.04em] text-center mb-16 text-[#082E4E]">
                Your progress <br /> highlighted
            </h2>

            {{-- Stacked Slider Container --}}
            <div class="reveal relative h-[450px] md:h-[550px] flex justify-center items-start mb-12">
                @foreach([
                        ['01', 'Omar', '"Enrolling in the IELTS self-preparation course was a game-changer for me. As a busy professional, finding time to study seemed impossible, but this course allowed me to adapt my study schedule to fit my hectic routine seamlessly"'],
                        ['02', 'Sarah', '"The mock exams were incredibly helpful. They gave me a real sense of what to expect on test day, and the detailed feedback helped me improve my writing score significantly."'],
                        ['03', 'Ahmed', '"I was struggling with the speaking section, but the one-on-one tutoring sessions gave me the confidence I needed. I ended up scoring an 8.0!"'],
                        ['04', 'Fatima', '"The weekly updated materials kept the learning fresh and relevant. I never felt bored or stuck with outdated content. Highly recommended!"'],
                        ['05', 'John', '"The community support was amazing. Being able to ask questions and get answers from experts and fellow students made a huge difference."']
                    ] as $index => $testimonial)
                        <div class="absolute w-[300px] md:w-[377.5px] h-[380px] md:h-[462px] p-5 md:p-6 rounded-[18.57px] flex flex-col justify-between overflow-hidden shadow-2xl origin-bottom-left transition-all duration-500 ease-in-out"
                            :style="getStyle({{ $index }})">
                            <img src="{{ asset('images/testimoni aVector.png') }}" class="absolute top-0 left-0 w-full pointer-events-none z-0" alt="">
                            <div class="relative z-10 flex flex-col justify-between h-full">
                                <div class="flex flex-col items-start gap-4">
                                    <div class="w-[48px] h-[48px] bg-white rounded-full flex items-center justify-center font-sans font-medium text-[20px] text-[#082E4E]">
                                        {{ $testimonial[0] }}
                                    </div>
                                    <div class="font-sans font-bold text-[24px] text-white">
                                        {{ $testimonial[1] }}
                                    </div>
                                </div>
                                <p class="font-sans font-normal text-[20px] leading-[1.3] tracking-[-0.04em] text-white">
                                    {{ $testimonial[2] }}
                                </p>
                            </div>
                        </div>
                @endforeach
            </div>

            {{-- Navigation Buttons --}}
            <div class="flex justify-center gap-4 relative z-50">
                <button @click="prev()" class="w-[48px] h-[48px] flex items-center justify-center hover:opacity-70 transition">
                    <svg width="67" height="16" viewBox="0 0 67 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="pointer-events-none">
                        <path d="M0.292892 7.29289C-0.0976334 7.68342 -0.0976334 8.31658 0.292892 8.70711L6.65685 15.0711C7.04738 15.4616 7.68054 15.4616 8.07107 15.0711C8.46159 14.6805 8.46159 14.0474 8.07107 13.6569L2.41421 8L8.07107 2.34315C8.46159 1.95262 8.46159 1.31946 8.07107 0.928932C7.68054 0.538408 7.04738 0.538408 6.65685 0.928932L0.292892 7.29289ZM67 7L1 7V9L67 9V7Z" fill="#082E4E" />
                    </svg>
                </button>
                <button @click="next()" class="w-[48px] h-[48px] flex items-center justify-center hover:opacity-70 transition">
                    <svg width="67" height="16" viewBox="0 0 67 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="pointer-events-none">
                        <path d="M66.7071 8.70711C67.0976 8.31658 67.0976 7.68342 66.7071 7.29289L60.3431 0.928932C59.9526 0.538408 59.3195 0.538408 58.9289 0.928932C58.5384 1.31946 58.5384 1.95262 58.9289 2.34315L64.5858 8L58.9289 13.6569C58.5384 14.0474 58.5384 14.6805 58.9289 15.0711C59.3195 15.4616 59.9526 15.4616 60.3431 15.0711L66.7071 8.70711ZM0 9H66V7H0V9Z" fill="#082E4E" />
                    </svg>
                </button>
            </div>
        </div>
    </section>

    {{-- Contact Section (Drop us your questions) --}}
    <section class="py-24 px-4 bg-[#011F32] relative overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/home-page-contact-Bg.webp') }}" class="w-full h-full object-cover">
            <div class="absolute inset-0" style="background: linear-gradient(180deg, rgba(0, 0, 0, 0.32) -31.93%, #1B242F 100%);"></div>
        </div>

        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-start gap-16 relative z-10 text-white">
            <div class="reveal lg:w-1/3 pt-12">
                <h2 class="font-sans font-normal text-[64px] leading-[0.9] tracking-[-0.04em] mb-8">
                    Drop us your <br /> questions
                </h2>
            </div>

            <div class="reveal lg:w-2/3 w-full flex justify-end">
                <div class="w-full max-w-[645px] h-auto bg-white/5 backdrop-blur-[20px] border border-white/10 rounded-[12px] p-6 lg:p-[24px] shadow-2xl flex flex-col justify-between">
                    <div>
                        <div class="mb-4">
                            <h3 class="font-sans font-medium text-[32px]">Let's talk</h3>
                        </div>

                        <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
                            <p class="font-sans text-[18px] text-white/80 leading-relaxed">
                                Fill out the form below and we'll get back to you<br>as soon as possible.
                            </p>

                            <div class="flex border border-white/30 rounded-[8px] overflow-hidden shrink-0">
                                <a href="mailto:{{ config('mail.from.address') }}" class="w-14 h-12 flex items-center justify-center hover:bg-white/10 transition border-r border-white/30" title="Email Us">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /><path d="M22 6L12 13L2 6" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                </a>
                                <a href="#" class="w-14 h-12 flex items-center justify-center hover:bg-white/10 transition border-r border-white/30" title="Facebook">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M18 2H15C13.6739 2 12.4021 2.52678 11.4645 3.46447C10.5268 4.40215 10 5.67392 10 7V10H7V14H10V22H14V14H17L18 10H14V7C14 6.73478 14.1054 6.48043 14.2929 6.29289C14.4804 6.10536 14.7348 6 15 6H18V2Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                </a>
                                <a href="#" class="w-14 h-12 flex items-center justify-center hover:bg-white/10 transition" title="Instagram">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M17 2H7C4.23858 2 2 4.23858 2 7V17C2 19.7614 4.23858 22 7 22H17C19.7614 22 22 19.7614 22 17V7C22 4.23858 19.7614 2 17 2Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /><path d="M16 11.37C16.1234 12.2022 15.9813 13.0522 15.5938 13.799C15.2063 14.5458 14.5931 15.1514 13.8416 15.5297C13.0901 15.9079 12.2384 16.0396 11.4078 15.9059C10.5771 15.7723 9.80976 15.3801 9.21484 14.7852C8.61991 14.1902 8.22773 13.4229 8.09406 12.5922C7.9604 11.7616 8.09206 10.9099 8.47032 10.1584C8.84858 9.40685 9.45418 8.79374 10.201 8.40624C10.9478 8.01874 11.7978 7.87658 12.63 8C13.5225 8.00511 14.3768 8.36195 15.0066 8.99175C15.6363 9.62156 15.9932 10.4759 15.9983 11.3683L16 11.37Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /><path d="M17.5 6.5H17.51" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                </a>
                            </div>
                        </div>

                        {{-- Success/Error Messages (AJAX) --}}
                        <div x-data="{
                            showSuccess: {{ session('success') ? 'true' : 'false' }},
                            showError: {{ session('error') ? 'true' : 'false' }},
                            successMessage: '{{ session('success') ?? '' }}',
                            errorMessage: '{{ session('error') ?? '' }}',
                            init() {
                                if (this.showSuccess) setTimeout(() => this.showSuccess = false, 5000);
                                if (this.showError) setTimeout(() => this.showError = false, 5000);
                            }
                        }">
                            <div x-show="showSuccess" x-transition class="mb-6 p-4 bg-green-500/20 border border-green-500/50 rounded-lg text-green-100">
                                <span x-text="successMessage"></span>
                            </div>

                            <div x-show="showError" x-transition class="mb-6 p-4 bg-red-500/20 border border-red-500/50 rounded-lg text-red-100">
                                <span x-text="errorMessage"></span>
                            </div>
                        </div>

                        <form 
                            x-data="{
                                submitting: false,
                                showSuccess: false,
                                showError: false,
                                successMessage: '',
                                errorMessage: '',
                                async submitForm(event) {
                                    this.submitting = true;
                                    this.showSuccess = false;
                                    this.showError = false;
                                    
                                    const formData = new FormData(event.target);
                                    
                                    try {
                                        const response = await fetch('{{ route('contact.submit') }}', {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'X-Requested-With': 'XMLHttpRequest',
                                                'Accept': 'application/json',
                                            },
                                            body: formData
                                        });
                                        
                                        const data = await response.json();
                                        
                                        if (response.ok) {
                                            this.successMessage = data.message || 'Your message has been sent successfully!';
                                            this.showSuccess = true;
                                            event.target.reset();
                                            setTimeout(() => this.showSuccess = false, 5000);
                                        } else {
                                            this.errorMessage = data.message || 'Something went wrong. Please try again.';
                                            this.showError = true;
                                            setTimeout(() => this.showError = false, 5000);
                                        }
                                    } catch (error) {
                                        this.errorMessage = 'Network error. Please check your connection and try again.';
                                        this.showError = true;
                                        setTimeout(() => this.showError = false, 5000);
                                    } finally {
                                        this.submitting = false;
                                    }
                                }
                            }"
                            @submit.prevent="submitForm($event)"
                            class="space-y-6"
                        >
                            {{-- AJAX Success/Error Messages --}}
                            <div x-show="showSuccess" x-transition class="p-4 bg-green-500/20 border border-green-500/50 rounded-lg text-green-100">
                                <span x-text="successMessage"></span>
                            </div>

                            <div x-show="showError" x-transition class="p-4 bg-red-500/20 border border-red-500/50 rounded-lg text-red-100">
                                <span x-text="errorMessage"></span>
                            </div>
                            @csrf
                            <div class="space-y-6">
                                <div class="space-y-2">
                                    <label class="block font-sans font-medium text-[16px] text-white/90">Name*</label>
                                    <input type="text" name="name" required class="w-full bg-white/5 border border-white/20 rounded-[12px] p-4 text-white placeholder-white/40 focus:outline-none focus:border-white focus:bg-white/10 transition-all font-sans" placeholder="Your Name">
                                </div>
                                <div class="space-y-2">
                                    <label class="block font-sans font-medium text-[16px] text-white/90">Email*</label>
                                    <input type="email" name="email" required class="w-full bg-white/5 border border-white/20 rounded-[12px] p-4 text-white placeholder-white/40 focus:outline-none focus:border-white focus:bg-white/10 transition-all font-sans" placeholder="name@example.com">
                                </div>
                                <div class="space-y-2">
                                    <label class="block font-sans font-medium text-[16px] text-white/90">Contact#</label>
                                    <input type="text" name="phone" class="w-full bg-white/5 border border-white/20 rounded-[12px] p-4 text-white placeholder-white/40 focus:outline-none focus:border-white focus:bg-white/10 transition-all font-sans" placeholder="Contact">
                                </div>
                                <div class="space-y-2">
                                    <label class="block font-sans font-medium text-[16px] text-white/90">Message</label>
                                    <textarea rows="4" name="message" required class="w-full bg-white/5 border border-white/20 rounded-[12px] p-4 text-white placeholder-white/40 focus:outline-none focus:border-white focus:bg-white/10 transition-all resize-none font-sans" placeholder="How can we help?"></textarea>
                                </div>
                            </div>

                            <button 
                                type="submit" 
                                :disabled="submitting"
                                :class="submitting ? 'opacity-70 cursor-not-allowed' : 'hover:-translate-y-1'"
                                class="w-full py-4 bg-white text-[#082E4E] border border-white hover:border-[#082E4E] hover:bg-[#082E4E] hover:text-white font-sans font-medium text-[20px] leading-[1] tracking-[-0.04em] rounded-[6px] transition-all transform mt-6 shadow-lg"
                            >
                                <span x-show="!submitting">Send Message</span>
                                <span x-show="submitting">Sending...</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FAQ Section --}}
    <section class="bg-[#F8FAFC] text-[#082E4E]">
        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-8 lg:gap-32">
            <div class="reveal lg:w-[415px] shrink-0 py-24 px-4">
                <h2 class="text-[36px] md:text-[56px] leading-[1.1] font-normal tracking-[-0.02em] font-sans text-[#082E4E]">
                    Frequently asked <br /> questions
                </h2>
            </div>

            <div class="reveal lg:flex-1 w-full border-l border-[#082E4E]/20 pl-0 py-24 px-4" x-data="{ active: 1 }">
                <div class="border border-[#082E4E]/20">
                    @foreach([
                            ['Can I access the course on a mobile or tablet device?', 'Yes, you can! Our course is fully accessible on mobile and tablet devices. We understand that many students have busy schedules and personal commitments, which can make IELTS prep challenging. That\'s why we prioritise flexibility, allowing you to learn on the go and fit your preparation around your life.'],
                            ['How is this course different from other IELTS preparation courses?', 'Our course takes IELTS prep to the next level with fun and interactive features. You\'ll get video lessons, quizzes and mock tests, plus personal feedback on your essays to help you improve fast.'],
                            ['What kind of support is available during the course?', 'We\'ve got your back with fast, friendly support! For any general questions, you\'ll hear from us within 24 hours. When it comes to your assignments, we take up to 48 hours to provide personalised feedback from our IELTS expert.'],
                            ['How do I know if this course is right for me?', 'This course is perfect for you if you\'re juggling a busy schedule and need flexibility, or if you prefer to study online at your own pace. If you want a flexible, budget-friendly way to prepare for IELTS, this course is for you!'],
                            ['Do I need to be at a certain English level to take this course?', 'This course is designed for non-native English speakers, so don\'t worry if English isn\'t your first language! If you have some academic English experience, you\'ll find it easy to navigate and follow along.']
                        ] as $index => $faq)
                                    <div class="border-b border-[#082E4E]/20 bg-white">
                                        <button @click="active = (active === {{ $index + 1 }} ? null : {{ $index + 1 }})"
                                            class="w-full p-6 lg:p-8 flex justify-between items-start text-left focus:outline-none group">
                                            <span class="text-[20px] font-medium text-[#082E4E] font-sans pr-8">{{ $faq[0] }}</span>
                                            <span class="text-2xl font-light text-[#082E4E] shrink-0" x-text="active === {{ $index + 1 }} ? '−' : '+'"></span>
                                        </button>
                                        <div x-show="active === {{ $index + 1 }}" x-collapse>
                                            <div class="px-6 lg:px-8 pb-8 pt-0 text-[18px] leading-relaxed text-[#4B5563] font-sans">
                                                {{ $faq[1] }}
                                            </div>
                                        </div>
                                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</x-public-layout>