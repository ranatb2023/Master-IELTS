<x-public-layout>
    <!-- Hero Section -->
    <section
        class="relative min-h-[90vh] flex items-center justify-start overflow-hidden pt-24 pb-[60px] lg:pt-32 lg:pb-[100px]">
        <!-- Background -->
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-black/20 z-10"></div>
            <!-- Gradient Overlay for text readability -->
            <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/40 to-transparent z-10">
            </div>

            <img src="{{ asset('images/speaking hero section BG.webp') }}" alt="Speaking Hero Background"
                class="w-full h-full object-cover">
        </div>

        <!-- Content -->
        <div class="relative z-20 container mx-auto px-4 lg:px-16">
            <!-- Text Container -->
            <div class="flex flex-col items-start text-left max-w-4xl pt-10">
                <!-- Top Content: Heading -->
                <div>
                    <h1 class="reveal font-sans text-white">
                        <span
                            class="block text-[24px] lg:text-[48px] font-normal leading-[1] tracking-[-0.04em] mb-2 opacity-90">Speaking:</span>
                        <span class="block text-[36px] lg:text-[64px] font-normal leading-[1] tracking-[-0.04em]">
                            Your Complete Guide to the <br /> IELTS Speaking Test
                        </span>
                    </h1>
                </div>

                <!-- Bottom Content: Paragraph & Button -->
                <div class="mt-[250px] lg:mt-[350px]">
                    <p
                        class="reveal delay-100 font-sans font-medium text-[16px] md:text-[20px] leading-[1.1] tracking-normal text-gray-200 mb-8 max-w-2xl">
                        Master vocabulary, grammar, and pronunciation while practising real exam scenarios. Our
                        speaking course helps you structure answers effectively and develop the fluency needed to
                        achieve your target Band score.
                    </p>

                    <a href="{{ Auth::check() ? route('student.dashboard') : route('register') }}"
                        class="inline-block reveal delay-200 px-10 py-3.5 bg-white text-[#0B2336] text-[18px] md:text-[20px] font-medium leading-[1.1] tracking-[-0.04em] text-center rounded-full hover:bg-gray-100 hover:scale-105 active:scale-95 transition-all shadow-xl">
                        Join now
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Why the IELTS Speaking Test Matters -->
    <section class="py-[100px] bg-[#F6FBFF] text-[#082E4E]">
        <div class="container mx-auto px-4 lg:px-16">
            <!-- Header -->
            <div class="text-center max-w-7xl mx-auto mb-16">
                <h2
                    class="reveal font-sans font-normal text-[42px] md:text-[64px] leading-[1.1] tracking-[-0.04em] text-[#082E4E] mb-6">
                    Why the IELTS Speaking Test Matters
                </h2>
                <p
                    class="reveal delay-100 font-sans font-medium text-[20px] leading-[1.2] tracking-[-0.04em] text-[#082E4E] max-w-3xl mx-auto">
                    The IELTS Speaking is about speaking smart. You need to:
                </p>
            </div>

            <!-- Cards Grid -->
            <div x-data="{ activeCard: 1 }" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
                <!-- Card 1 -->
                <div @mouseenter="activeCard = 1"
                    :class="activeCard === 1 ? 'bg-[#0B2336] text-white scale-105 shadow-2xl border-transparent' : 'bg-white text-[#082E4E] border-gray-200 shadow-sm'"
                    class="reveal delay-200 border p-8 rounded-[24px] flex flex-col justify-between h-[380px] transition-all duration-300 cursor-pointer">
                    <!-- Number -->
                    <div :class="activeCard === 1 ? 'bg-white text-[#0B2336]' : 'bg-[#0B2336] text-white'"
                        class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-[18px] transition-colors duration-300">
                        01
                    </div>
                    <!-- Content -->
                    <div>
                        <h3 :class="activeCard === 1 ? 'text-white' : 'text-[#082E4E]'"
                            class="font-sans font-medium text-[24px] leading-[1] tracking-[-0.04em] mb-4 transition-colors duration-300">
                            Express ideas
                        </h3>
                        <p :class="activeCard === 1 ? 'text-gray-300' : 'text-[#082E4E]'"
                            class="font-sans font-normal text-[20px] leading-[1.2] tracking-[-0.04em] transition-colors duration-300">
                            clearly and coherently
                        </p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div @mouseenter="activeCard = 2"
                    :class="activeCard === 2 ? 'bg-[#0B2336] text-white scale-105 shadow-2xl border-transparent' : 'bg-white text-[#082E4E] border-gray-200 shadow-sm'"
                    class="reveal delay-300 border p-8 rounded-[24px] flex flex-col justify-between h-[380px] transition-all duration-300 cursor-pointer">
                    <!-- Number -->
                    <div :class="activeCard === 2 ? 'bg-white text-[#0B2336]' : 'bg-[#0B2336] text-white'"
                        class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-[18px] transition-colors duration-300">
                        02
                    </div>
                    <!-- Content -->
                    <div>
                        <h3 :class="activeCard === 2 ? 'text-white' : 'text-[#082E4E]'"
                            class="font-sans font-medium text-[24px] leading-[1] tracking-[-0.04em] mb-4 transition-colors duration-300">
                            Use a wide range
                        </h3>
                        <p :class="activeCard === 2 ? 'text-gray-300' : 'text-[#082E4E]'"
                            class="font-sans font-normal text-[20px] leading-[1.2] tracking-[-0.04em] transition-colors duration-300">
                            of vocabulary and grammar accurately
                        </p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div @mouseenter="activeCard = 3"
                    :class="activeCard === 3 ? 'bg-[#0B2336] text-white scale-105 shadow-2xl border-transparent' : 'bg-white text-[#082E4E] border-gray-200 shadow-sm'"
                    class="reveal delay-400 border p-8 rounded-[24px] flex flex-col justify-between h-[380px] transition-all duration-300 cursor-pointer">
                    <!-- Number -->
                    <div :class="activeCard === 3 ? 'bg-white text-[#0B2336]' : 'bg-[#0B2336] text-white'"
                        class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-[18px] transition-colors duration-300">
                        03
                    </div>
                    <!-- Content -->
                    <div>
                        <h3 :class="activeCard === 3 ? 'text-white' : 'text-[#082E4E]'"
                            class="font-sans font-medium text-[24px] leading-[1] tracking-[-0.04em] mb-4 transition-colors duration-300">
                            Respond naturally
                        </h3>
                        <p :class="activeCard === 3 ? 'text-gray-300' : 'text-[#082E4E]'"
                            class="font-sans font-normal text-[20px] leading-[1.2] tracking-[-0.04em] transition-colors duration-300">
                            to different question types and follow-ups
                        </p>
                    </div>
                </div>

                <!-- Card 4 -->
                <div @mouseenter="activeCard = 4"
                    :class="activeCard === 4 ? 'bg-[#0B2336] text-white scale-105 shadow-2xl border-transparent' : 'bg-white text-[#082E4E] border-gray-200 shadow-sm'"
                    class="reveal delay-500 border p-8 rounded-[24px] flex flex-col justify-between h-[380px] transition-all duration-300 cursor-pointer">
                    <!-- Number -->
                    <div :class="activeCard === 4 ? 'bg-white text-[#0B2336]' : 'bg-[#0B2336] text-white'"
                        class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-[18px] transition-colors duration-300">
                        04
                    </div>
                    <!-- Content -->
                    <div>
                        <h3 :class="activeCard === 4 ? 'text-white' : 'text-[#082E4E]'"
                            class="font-sans font-medium text-[24px] leading-[1] tracking-[-0.04em] mb-4 transition-colors duration-300">
                            Manage time
                        </h3>
                        <p :class="activeCard === 4 ? 'text-gray-300' : 'text-[#082E4E]'"
                            class="font-sans font-normal text-[20px] leading-[1.2] tracking-[-0.04em] transition-colors duration-300">
                            across three parts: introduction, individual long turn, and discussion
                        </p>
                    </div>
                </div>
            </div>

            <!-- Bottom Text -->
            <div class="text-center">
                <p
                    class="reveal delay-600 font-sans font-medium text-[20px] leading-[1.1] tracking-[-0.04em] text-[#082E4E] max-w-3xl mx-auto">
                    That’s why a structured, well-practised strategy is the key, and Master IELTS gives you exactly
                    that.
                </p>
            </div>
        </div>
    </section>

    <!-- IELTS Speaking Test Format -->
    <section class="relative pt-[100px] lg:pt-[350px] pb-[60px] px-4 overflow-hidden bg-[#0B2336] text-white">
        <!-- Background -->
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/IELTS Reading Test Format (Academic) section BG.webp') }}"
                alt="Test Format Background" class="w-full h-full object-cover">
            <div class="absolute inset-0 z-10"
                style="background: linear-gradient(180deg, rgba(0, 0, 0, 0.2688) -31.93%, rgba(8, 46, 78, 0.84) 100%);">
            </div>
        </div>

        <div class="relative z-20 max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="reveal font-sans font-normal text-[36px] md:text-[64px] leading-[1] mb-6 tracking-[-0.04em]">
                    IELTS Speaking Test Format
                </h2>
                <p class="reveal font-sans text-[16px] md:text-[20px] text-gray-300 tracking-wide max-w-3xl mx-auto">
                    The Speaking test lasts 11–14 minutes and is divided into three parts. Every part tests a
                    specific skill:
                </p>
            </div>

            <!-- Glass Table Container -->
            <div class="reveal delay-200 bg-white/10 backdrop-blur-md border border-white/10 rounded-[32px] p-4 md:p-8">
                <!-- Header Row -->
                <div
                    class="bg-white rounded-full px-8 py-5 mb-8 hidden md:grid grid-cols-12 gap-4 text-[#0B2336] font-medium text-[18px]">
                    <div class="col-span-2">Part</div>
                    <div class="col-span-4 text-center">Description</div>
                    <div class="col-span-6 text-left pl-8">What to Expect</div>
                </div>

                <!-- Row 1: Part 1 -->
                <div
                    class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center py-6 border-b border-white/10 last:border-0 hover:bg-white/5 transition-colors duration-300 rounded-xl px-4 md:px-8">
                    <div class="col-span-2 font-medium text-[18px] md:text-[20px]">Part 1</div>
                    <div class="col-span-4 text-gray-300 text-center text-[16px] md:text-[18px]">Introduction &
                        Interview</div>
                    <div class="col-span-6 text-gray-300 text-left pl-0 md:pl-8 text-[16px] md:text-[18px]">
                        Short questions about yourself, your life, and familiar topics
                    </div>
                </div>

                <!-- Row 2: Part 2 -->
                <div
                    class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center py-6 border-b border-white/10 last:border-0 hover:bg-white/5 transition-colors duration-300 rounded-xl px-4 md:px-8">
                    <div class="col-span-2 font-medium text-[18px] md:text-[20px]">Part 2</div>
                    <div class="col-span-4 text-gray-300 text-center text-[16px] md:text-[18px]">Long Turn</div>
                    <div class="col-span-6 text-gray-300 text-left pl-0 md:pl-8 text-[16px] md:text-[18px]">
                        Speak for 1–2 minutes on a given topic; 1 minute to prepare
                    </div>
                </div>

                <!-- Row 3: Part 3 -->
                <div
                    class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center py-6 border-b border-white/10 last:border-0 hover:bg-white/5 transition-colors duration-300 rounded-xl px-4 md:px-8">
                    <div class="col-span-2 font-medium text-[18px] md:text-[20px]">Part 3</div>
                    <div class="col-span-4 text-gray-300 text-center text-[16px] md:text-[18px]">Discussion</div>
                    <div class="col-span-6 text-gray-300 text-left pl-0 md:pl-8 text-[16px] md:text-[18px]">
                        Follow-up questions exploring abstract ideas, opinions, and issues
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- IELTS Speaking Question Types -->
    <section class="py-[100px] bg-[#F6FBFF] text-[#082E4E]">
        <div class="container mx-auto px-4 lg:px-16">
            <!-- Header -->
            <div class="text-center max-w-4xl mx-auto mb-16">
                <h2
                    class="reveal font-sans font-normal text-[42px] md:text-[64px] leading-[1.1] tracking-[-0.04em] mb-6">
                    IELTS Speaking Question Types
                </h2>
                <p class="reveal delay-100 font-sans font-medium text-[20px] text-[#082E4E] leading-relaxed">
                    Prepare for key question types, each testing fluency, vocabulary, and pronunciation:
                </p>
            </div>

            <!-- Table Container -->
            <div
                class="reveal delay-200 max-w-7xl mx-auto overflow-hidden rounded-t-xl bg-white shadow-sm border border-gray-200">
                <!-- Table Header -->
                <div
                    class="bg-[#0B2336] text-white py-6 px-4 md:px-8 hidden md:grid grid-cols-1 md:grid-cols-12 gap-4 font-bold text-[18px]">
                    <div class="col-span-1 md:col-span-4 pl-2">Question Type</div>
                    <div class="col-span-1 md:col-span-3">Skill Tested</div>
                    <div class="col-span-1 md:col-span-5">Quick Tip</div>
                </div>

                <!-- Row 1: Personal Questions -->
                <div
                    class="bg-white border-b border-gray-100 py-6 px-4 md:px-8 grid grid-cols-1 md:grid-cols-12 gap-4 items-center hover:bg-[#F0F7FF] transition-colors duration-200 group">
                    <div class="col-span-1 md:col-span-4 flex items-center gap-3">
                        <div class="w-2.5 h-2.5 rotate-45 bg-[#045F98] group-hover:scale-110 transition-transform">
                        </div>
                        <span class="font-medium text-[18px]">Personal Questions</span>
                    </div>
                    <div class="col-span-1 md:col-span-3 text-[16px] md:text-[18px] text-gray-600">Fluency & coherence
                    </div>
                    <div class="col-span-1 md:col-span-5 text-[16px] md:text-[18px] text-[#082E4E]">Answer naturally,
                        don’t memorize; expand answers</div>
                </div>

                <!-- Row 2: Long Turn / Cue Card -->
                <div
                    class="bg-white border-b border-gray-100 py-6 px-4 md:px-8 grid grid-cols-1 md:grid-cols-12 gap-4 items-center hover:bg-[#F0F7FF] transition-colors duration-200 group">
                    <div class="col-span-1 md:col-span-4 flex items-center gap-3">
                        <div class="w-2.5 h-2.5 rotate-45 bg-[#045F98] group-hover:scale-110 transition-transform">
                        </div>
                        <span class="font-medium text-[18px]">Long Turn / Cue Card</span>
                    </div>
                    <div class="col-span-1 md:col-span-3 text-[16px] md:text-[18px] text-gray-600">Extended speaking
                    </div>
                    <div class="col-span-1 md:col-span-5 text-[16px] md:text-[18px] text-[#082E4E]">Structure your
                        speech: intro, main points, conclusion</div>
                </div>

                <!-- Row 3: Follow-up / Discussion -->
                <div
                    class="bg-white border-b border-gray-100 py-6 px-4 md:px-8 grid grid-cols-1 md:grid-cols-12 gap-4 items-center hover:bg-[#F0F7FF] transition-colors duration-200 group">
                    <div class="col-span-1 md:col-span-4 flex items-center gap-3">
                        <div class="w-2.5 h-2.5 rotate-45 bg-[#045F98] group-hover:scale-110 transition-transform">
                        </div>
                        <span class="font-medium text-[18px]">Follow-up / Discussion</span>
                    </div>
                    <div class="col-span-1 md:col-span-3 text-[16px] md:text-[18px] text-gray-600">Opinions & reasoning
                    </div>
                    <div class="col-span-1 md:col-span-5 text-[16px] md:text-[18px] text-[#082E4E]">Justify your ideas
                        with examples and explanations</div>
                </div>

                <!-- Row 4: Hypothetical Questions -->
                <div
                    class="bg-white border-b border-gray-100 py-6 px-4 md:px-8 grid grid-cols-1 md:grid-cols-12 gap-4 items-center hover:bg-[#F0F7FF] transition-colors duration-200 group">
                    <div class="col-span-1 md:col-span-4 flex items-center gap-3">
                        <div class="w-2.5 h-2.5 rotate-45 bg-[#045F98] group-hover:scale-110 transition-transform">
                        </div>
                        <span class="font-medium text-[18px]">Hypothetical Questions</span>
                    </div>
                    <div class="col-span-1 md:col-span-3 text-[16px] md:text-[18px] text-gray-600">Critical thinking
                    </div>
                    <div class="col-span-1 md:col-span-5 text-[16px] md:text-[18px] text-[#082E4E]">Use conditional
                        structures (“If…then…”) confidently</div>
                </div>

                <!-- Row 5: Compare & Contrast -->
                <div
                    class="bg-white border-b border-gray-100 py-6 px-4 md:px-8 grid grid-cols-1 md:grid-cols-12 gap-4 items-center hover:bg-[#F0F7FF] transition-colors duration-200 group">
                    <div class="col-span-1 md:col-span-4 flex items-center gap-3">
                        <div class="w-2.5 h-2.5 rotate-45 bg-[#045F98] group-hover:scale-110 transition-transform">
                        </div>
                        <span class="font-medium text-[18px]">Compare & Contrast</span>
                    </div>
                    <div class="col-span-1 md:col-span-3 text-[16px] md:text-[18px] text-gray-600">Vocabulary & clarity
                    </div>
                    <div class="col-span-1 md:col-span-5 text-[16px] md:text-[18px] text-[#082E4E]">Highlight
                        differences and similarities clearly</div>
                </div>

                <!-- Row 6: Describing & Narrating -->
                <div
                    class="bg-white py-6 px-4 md:px-8 grid grid-cols-1 md:grid-cols-12 gap-4 items-center hover:bg-[#F0F7FF] transition-colors duration-200 group">
                    <div class="col-span-1 md:col-span-4 flex items-center gap-3">
                        <div class="w-2.5 h-2.5 rotate-45 bg-[#045F98] group-hover:scale-110 transition-transform">
                        </div>
                        <span class="font-medium text-[18px]">Describing & Narrating</span>
                    </div>
                    <div class="col-span-1 md:col-span-3 text-[16px] md:text-[18px] text-gray-600">Pronunciation &
                        intonation</div>
                    <div class="col-span-1 md:col-span-5 text-[16px] md:text-[18px] text-[#082E4E]">Speak clearly and
                        emphasise key words</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Band Score Conversion & Interactive Cards -->
    <section class="py-[60px] lg:py-[100px] px-4 mobile-section-fix relative"
        style="background: linear-gradient(90.81deg, #045F98 -14.55%, #011F32 105.37%);">

        <!-- Main Heading Area -->
        <div class="max-w-7xl mx-auto text-center mb-10 lg:mb-20">
            <h2 class="reveal font-sans font-normal text-[32px] lg:text-[64px] leading-[1] text-white mb-6">
                Band Score Conversion
            </h2>
            <p class="reveal delay-100 font-sans font-medium text-[18px] lg:text-[20px] text-white/80">
                Below is an approximate score conversion for Speaking:
            </p>
        </div>

        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-start justify-between gap-[20px]">

            <!-- Left: Sticky Score Table -->
            <div class="reveal lg:w-[40%] lg:sticky lg:top-32 self-start animate-fade-in-up w-full">
                <h3 class="font-sans font-normal text-[32px] md:text-[40px] text-white mb-8">Score Conversion</h3>

                <div class="overflow-hidden rounded-2xl border border-white/20 shadow-2xl bg-white/5 backdrop-blur-sm">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-white"
                                style="background: linear-gradient(90.81deg, #045F98 -14.55%, #011F32 105.37%);">
                                <th class="p-6 text-[18px] md:text-[20px] font-bold">Description</th>
                                <th class="p-6 text-[18px] md:text-[20px] font-bold text-right">Band</th>
                            </tr>
                        </thead>
                        <tbody class="text-[18px] font-sans font-medium bg-white text-[#082E4E]">
                            <tr class="border-b border-gray-100">
                                <td class="p-5 pl-6">
                                    <div class="block text-[20px] font-medium leading-[1.4] tracking-[-0.04em]">
                                        Expert speaker:</div>
                                    <div
                                        class="block text-[16px] font-medium leading-[1.4] tracking-[-0.04em] mt-1 opacity-70">
                                        fluent, accurate, fully coherent</div>
                                </td>
                                <td class="p-5 pr-6 text-right font-bold">9</td>
                            </tr>
                            <tr class="border-b border-gray-100">
                                <td class="p-5 pl-6">
                                    <div class="block text-[20px] font-medium leading-[1.4] tracking-[-0.04em]">Very
                                        good:</div>
                                    <div
                                        class="block text-[16px] font-medium leading-[1.4] tracking-[-0.04em] mt-1 opacity-70">
                                        occasional minor mistakes, natural speech</div>
                                </td>
                                <td class="p-5 pr-6 text-right font-bold">8</td>
                            </tr>
                            <tr class="border-b border-gray-100">
                                <td class="p-5 pl-6">
                                    <div class="block text-[20px] font-medium leading-[1.4] tracking-[-0.04em]">
                                        Good:
                                    </div>
                                    <div
                                        class="block text-[16px] font-medium leading-[1.4] tracking-[-0.04em] mt-1 opacity-70">
                                        some errors, but meaning is clear and fluent</div>
                                </td>
                                <td class="p-5 pr-6 text-right font-bold">7</td>
                            </tr>
                            <tr class="border-b border-gray-100">
                                <td class="p-5 pl-6">
                                    <div class="block text-[20px] font-medium leading-[1.4] tracking-[-0.04em]">
                                        Competent:</div>
                                    <div
                                        class="block text-[16px] font-medium leading-[1.4] tracking-[-0.04em] mt-1 opacity-70">
                                        noticeable errors, but communication effective</div>
                                </td>
                                <td class="p-5 pr-6 text-right font-bold">6.5</td>
                            </tr>
                            <tr class="border-b border-gray-100">
                                <td class="p-5 pl-6">
                                    <div class="block text-[20px] font-medium leading-[1.4] tracking-[-0.04em]">
                                        Modest:</div>
                                    <div
                                        class="block text-[16px] font-medium leading-[1.4] tracking-[-0.04em] mt-1 opacity-70">
                                        frequent mistakes, limited flexibility</div>
                                </td>
                                <td class="p-5 pr-6 text-right font-bold">6</td>
                            </tr>
                            <tr>
                                <td class="p-5 pl-6">
                                    <div class="block text-[20px] font-medium leading-[1.4] tracking-[-0.04em]">
                                        Limited:</div>
                                    <div
                                        class="block text-[16px] font-medium leading-[1.4] tracking-[-0.04em] mt-1 opacity-70">
                                        frequent breakdowns, difficulty expressing ideas</div>
                                </td>
                                <td class="p-5 pr-6 text-right font-bold">5.5</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right: Stacked Cards (How to Approach) -->
            <div class="flex-1 w-full flex flex-col gap-6 lg:gap-[10px] pb-24">
                <div
                    class="reveal mb-4 lg:mb-0 flex flex-col lg:flex-row justify-between items-end gap-6 text-left w-full">
                    <h3 class="font-sans font-normal text-[32px] md:text-[40px] text-white leading-[1.1]">
                        How to Approach the <br class="hidden md:block" /> IELTS Speaking Test
                    </h3>
                    <p class="font-sans font-medium text-[18px] text-white/80 max-w-[220px] text-left pb-1">
                        Speaking improves faster when you know what to practise and how.
                    </p>
                </div>

                <!-- Stack Card 1 -->
                <div id="stack-card-1"
                    class="reveal sticky top-[222px] z-10 bg-white w-full max-w-[794px] h-auto min-h-[350px] lg:h-[460px] p-6 lg:p-[32px] rounded-[16px] border-b border-[#121212]/20 shadow-xl text-[#082E4E] transition-all duration-500 ease-out origin-top flex flex-col">
                    <div
                        class="w-12 h-12 bg-[#DCE9F5] rounded-full flex items-center justify-center font-bold text-[20px] text-[#0B2336] mb-4">
                        01
                    </div>
                    <h4
                        class="font-sans font-normal text-[32px] lg:text-[56px] tracking-[-0.04em] leading-[1] text-[#0B2336] mb-auto">
                        Prepare for Common Topics</h4>

                    <div class="mt-8">
                        <div class="flex flex-col gap-3">
                            <div
                                class="bg-[#045F98] text-white rounded-xl py-3 px-4 text-left font-medium text-[16px] flex items-center">
                                <span class="mr-2">•</span> Personal experiences, family, hobbies, work/study, travel
                            </div>
                            <div
                                class="bg-[#045F98] text-white rounded-xl py-3 px-4 text-left font-medium text-[16px] flex items-center">
                                <span class="mr-2">•</span> Current issues like environment, technology, and society
                            </div>
                            <div
                                class="bg-[#045F98] text-white rounded-xl py-3 px-4 text-left font-medium text-[16px] flex items-center">
                                <span class="mr-2">•</span> Practice answers naturally, don’t memorize scripts
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stack Card 2 -->
                <div id="stack-card-2"
                    class="reveal sticky top-[242px] z-20 bg-white w-full max-w-[794px] h-auto min-h-[350px] lg:h-[460px] p-6 lg:p-[32px] rounded-[16px] border-b border-[#121212]/20 shadow-xl text-[#082E4E] transition-all duration-500 ease-out origin-top flex flex-col">
                    <div
                        class="w-12 h-12 bg-[#DCE9F5] rounded-full flex items-center justify-center font-bold text-[20px] text-[#0B2336] mb-4">
                        02
                    </div>
                    <h4
                        class="font-sans font-normal text-[32px] lg:text-[56px] tracking-[-0.04em] leading-[1] text-[#0B2336] mb-auto">
                        Use Structured Answers</h4>

                    <div class="mt-8">
                        <div class="flex flex-col gap-3">
                            <div
                                class="bg-[#045F98] text-white rounded-xl py-3 px-4 text-left font-medium text-[16px] flex items-center">
                                <span class="mr-2">•</span> Start with a clear statement
                            </div>
                            <div
                                class="bg-[#045F98] text-white rounded-xl py-3 px-4 text-left font-medium text-[16px] flex items-center">
                                <span class="mr-2">•</span> Add 2–3 supporting details or examples
                            </div>
                            <div
                                class="bg-[#045F98] text-white rounded-xl py-3 px-4 text-left font-medium text-[16px] flex items-center">
                                <span class="mr-2">•</span> Conclude briefly to wrap up the answer
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stack Card 3 -->
                <div id="stack-card-3"
                    class="reveal sticky top-[262px] z-30 bg-white w-full max-w-[794px] h-auto min-h-[350px] lg:h-[460px] p-6 lg:p-[32px] rounded-[16px] border-b border-[#121212]/20 shadow-xl text-[#082E4E] transition-all duration-500 ease-out origin-top flex flex-col">
                    <div
                        class="w-12 h-12 bg-[#DCE9F5] rounded-full flex items-center justify-center font-bold text-[20px] text-[#0B2336] mb-4">
                        03
                    </div>
                    <h4
                        class="font-sans font-normal text-[32px] lg:text-[56px] tracking-[-0.04em] leading-[1] text-[#0B2336] mb-auto">
                        Speak Actively & Confidently</h4>

                    <div class="mt-8">
                        <div class="flex flex-col gap-3">
                            <div
                                class="bg-[#045F98] text-white rounded-xl py-3 px-4 text-left font-medium text-[16px] flex items-center">
                                <span class="mr-2">•</span> Maintain clear pronunciation and rhythm
                            </div>
                            <div
                                class="bg-[#045F98] text-white rounded-xl py-3 px-4 text-left font-medium text-[16px] flex items-center">
                                <span class="mr-2">•</span> Use a variety of sentence structures
                            </div>
                            <div
                                class="bg-[#045F98] text-white rounded-xl py-3 px-4 text-left font-medium text-[16px] flex items-center">
                                <span class="mr-2">•</span> Link ideas with connectors (however, moreover, in addition)
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stack Card 4 (Detailed Time Management) -->
                <div id="stack-card-4"
                    class="reveal sticky top-[282px] z-40 bg-white w-full max-w-[794px] h-auto min-h-[350px] lg:h-[460px] p-6 lg:p-[32px] rounded-[16px] border-b border-[#121212]/20 shadow-xl text-[#082E4E] transition-all duration-500 ease-out origin-top flex flex-col">
                    <div
                        class="w-12 h-12 bg-[#DCE9F5] rounded-full flex items-center justify-center font-bold text-[20px] text-[#0B2336] mb-4">
                        04
                    </div>
                    <h4
                        class="font-sans font-normal text-[32px] lg:text-[56px] tracking-[-0.04em] leading-[1] text-[#0B2336] mb-auto">
                        Master Time Management</h4>

                    <div class="mt-8">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-[#045F98] rounded-xl p-4 text-center flex flex-col justify-center">
                                <div class="text-[14px] text-white/80 uppercase tracking-wider mb-1">Part 1</div>
                                <div class="text-[24px] font-bold text-white">4–5 min</div>
                            </div>
                            <div class="bg-[#045F98] rounded-xl p-4 text-center flex flex-col justify-center">
                                <div class="text-[14px] text-white/80 uppercase tracking-wider mb-1">Part 2</div>
                                <div class="text-[24px] font-bold text-white leading-none">3–4 min</div>
                                <div class="text-[12px] text-white/70 mt-1">(incl. 1m prep)</div>
                            </div>
                            <div class="bg-[#045F98] rounded-xl p-4 text-center flex flex-col justify-center">
                                <div class="text-[14px] text-white/80 uppercase tracking-wider mb-1">Part 3</div>
                                <div class="text-[24px] font-bold text-white">4–5 min</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tips to Improve Your IELTS Speaking Score -->
    <section
        class="relative pt-[200px] pb-[60px] px-4 overflow-hidden bg-[#0B2336] min-h-[800px] flex flex-col justify-end">
        <!-- Background -->
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/Tips to Improve Your IELTS Reading Score- section-Bg.webp') }}"
                alt="Tips Background" class="w-full h-full object-cover object-top">
            <!-- Overlay for text readability -->
            <div class="absolute inset-0 z-10"
                style="background: linear-gradient(197.63deg, rgba(0, 0, 0, 0) -15.88%, rgba(27, 36, 47, 0.83) 89.02%);">
            </div>
        </div>

        <div class="relative z-20 max-w-[1440px] mx-auto w-full h-full flex flex-col justify-between">
            <!-- Section Header -->
            <div class="text-center mb-[60px] md:mb-[250px] pt-10">
                <h2
                    class="reveal font-sans font-normal text-[40px] md:text-[64px] leading-[0.98] tracking-[-0.04em] text-white">
                    Tips to Improve Your IELTS <br /> Speaking Score
                </h2>
            </div>

            <!-- Cards Grid -->
            <!-- Mobile: Vertical Stack (1 Col), Desktop: 5 Cols -->
            <div
                class="grid grid-cols-1 md:grid-cols-5 h-auto md:h-[300px] w-full border-t border-white/20 gap-4 md:gap-[5px]">

                <!-- Card 1 (White) -->
                <div
                    class="group relative bg-white border border-white p-6 md:p-8 flex flex-col justify-end transition-all duration-300 hover:bg-gray-50 h-auto min-h-[220px] md:h-full">
                    <p class="font-sans font-medium text-[20px] text-[#0B2336] leading-[1.4] tracking-[-0.04em]">
                        Practice speaking daily with a partner or tutor
                    </p>
                </div>

                <!-- Card 2 (Glassmorphic) -->
                <div
                    class="group relative bg-white/10 backdrop-blur-md border border-white p-6 md:p-8 flex flex-col justify-end transition-all duration-300 hover:bg-white/20 h-auto min-h-[220px] md:h-full">
                    <p class="font-sans font-medium text-[20px] text-white leading-[1.4] tracking-[-0.04em]">
                        Record yourself and listen for mistakes and filler words
                    </p>
                </div>

                <!-- Card 3 (White) -->
                <div
                    class="group relative bg-white border border-white p-6 md:p-8 flex flex-col justify-end transition-all duration-300 hover:bg-gray-50 h-auto min-h-[220px] md:h-full">
                    <p class="font-sans font-medium text-[20px] text-[#0B2336] leading-[1.4] tracking-[-0.04em]">
                        Expand vocabulary for common IELTS topics
                    </p>
                </div>

                <!-- Card 4 (Glassmorphic) -->
                <div
                    class="group relative bg-white/10 backdrop-blur-md border border-white p-6 md:p-8 flex flex-col justify-end transition-all duration-300 hover:bg-white/20 h-auto min-h-[220px] md:h-full">
                    <p class="font-sans font-medium text-[20px] text-white leading-[1.4] tracking-[-0.04em]">
                        Focus on pronunciation, intonation, and natural pacing
                    </p>
                </div>

                <!-- Card 5 (White) -->
                <div
                    class="group relative bg-white border border-white p-6 md:p-8 flex flex-col justify-end transition-all duration-300 hover:bg-gray-50 h-auto min-h-[220px] md:h-full">
                    <p class="font-sans font-medium text-[20px] text-[#0B2336] leading-[1.4] tracking-[-0.04em]">
                        Review and learn from errors instead of just repeating practice
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Learn with Master IELTS -->
    <section class="py-[150px] bg-[#F6FBFF] text-[#082E4E]">
        <div class="container mx-auto px-4 lg:px-16" x-data="{ activeCard: 1 }">
            <!-- Header -->
            <div class="text-center max-w-5xl mx-auto mb-16">
                <h2
                    class="reveal font-sans font-normal text-[32px] lg:text-[64px] leading-[1.1] tracking-[-0.04em] text-[#082E4E] mb-6">
                    Why Learn with Master IELTS?
                </h2>
                <p
                    class="reveal delay-100 font-sans font-medium text-[18px] lg:text-[20px] leading-[1.2] tracking-[-0.04em] text-[#082E4E] max-w-3xl mx-auto">
                    Whether you're a beginner or aiming for Band 7+, we've got a structured path for you.
                </p>
            </div>

            <!-- Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
                <!-- Card 1 -->
                <div @mouseenter="activeCard = 1"
                    :class="activeCard === 1 ? 'bg-[#0B2336] text-white scale-105 shadow-2xl border-transparent' : 'bg-white text-[#082E4E] border-gray-200 shadow-sm'"
                    class="reveal delay-200 border p-8 rounded-[24px] flex flex-col justify-between h-[420px] transition-all duration-300 cursor-pointer">
                    <!-- Number -->
                    <div :class="activeCard === 1 ? 'bg-white text-[#0B2336]' : 'bg-[#0B2336] text-white'"
                        class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-[18px] transition-colors duration-300">
                        01
                    </div>
                    <!-- Content -->
                    <div>
                        <h3 :class="activeCard === 1 ? 'text-white' : 'text-[#082E4E]'"
                            class="font-sans font-medium text-[24px] leading-[1] tracking-[-0.04em] mb-4 transition-colors duration-300">
                            Smart learning
                        </h3>
                        <p :class="activeCard === 1 ? 'text-gray-300' : 'text-[#082E4E]'"
                            class="font-sans font-normal text-[18px] leading-[1.4] tracking-[-0.04em] transition-colors duration-300">
                            Our lessons follow a clear, organised structure that makes every topic easy to
                            understand. You always know what to study next and how to improve step by step.
                        </p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div @mouseenter="activeCard = 2"
                    :class="activeCard === 2 ? 'bg-[#0B2336] text-white scale-105 shadow-2xl border-transparent' : 'bg-white text-[#082E4E] border-gray-200 shadow-sm'"
                    class="reveal delay-300 border p-8 rounded-[24px] flex flex-col justify-between h-[420px] transition-all duration-300 cursor-pointer">
                    <!-- Number -->
                    <div :class="activeCard === 2 ? 'bg-white text-[#0B2336]' : 'bg-[#0B2336] text-white'"
                        class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-[18px] transition-colors duration-300">
                        02
                    </div>
                    <!-- Content -->
                    <div>
                        <h3 :class="activeCard === 2 ? 'text-white' : 'text-[#082E4E]'"
                            class="font-sans font-medium text-[24px] leading-[1] tracking-[-0.04em] mb-4 transition-colors duration-300">
                            Proven methods
                        </h3>
                        <p :class="activeCard === 2 ? 'text-gray-300' : 'text-[#082E4E]'"
                            class="font-sans font-normal text-[18px] leading-[1.4] tracking-[-0.04em] transition-colors duration-300">
                            We focus on practical exam techniques that actually reflect how IELTS questions work.
                            Every strategy is designed to save time and reduce mistakes.
                        </p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div @mouseenter="activeCard = 3"
                    :class="activeCard === 3 ? 'bg-[#0B2336] text-white scale-105 shadow-2xl border-transparent' : 'bg-white text-[#082E4E] border-gray-200 shadow-sm'"
                    class="reveal delay-400 border p-8 rounded-[24px] flex flex-col justify-between h-[420px] transition-all duration-300 cursor-pointer">
                    <!-- Number -->
                    <div :class="activeCard === 3 ? 'bg-white text-[#0B2336]' : 'bg-[#0B2336] text-white'"
                        class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-[18px] transition-colors duration-300">
                        03
                    </div>
                    <!-- Content -->
                    <div>
                        <h3 :class="activeCard === 3 ? 'text-white' : 'text-[#082E4E]'"
                            class="font-sans font-medium text-[24px] leading-[1] tracking-[-0.04em] mb-4 transition-colors duration-300">
                            Guided support
                        </h3>
                        <p :class="activeCard === 3 ? 'text-gray-300' : 'text-[#082E4E]'"
                            class="font-sans font-normal text-[18px] leading-[1.4] tracking-[-0.04em] transition-colors duration-300">
                            You receive helpful guidance and resources tailored to your progress. This includes
                            personalised support and one-to-one speaking sessions with an IELTS tutor.
                        </p>
                    </div>
                </div>
            </div>

            <!-- CTA Button -->
            <div class="text-center reveal delay-500">
                <a href="{{ Auth::check() ? route('student.dashboard') : route('register') }}"
                    class="inline-block bg-[#0B2336] text-white font-sans font-medium text-[16px] md:text-[18px] py-4 px-8 rounded-full hover:scale-105 transition-transform duration-300 shadow-lg">
                    Start your IELTS journey today
                </a>
            </div>
        </div>
    </section>

    <!-- Stacked Cards Script -->
    <script>
        document.addEventListener('scroll', () => {
            const card1 = document.getElementById('stack-card-1');
            const card2 = document.getElementById('stack-card-2');
            const card3 = document.getElementById('stack-card-3');
            const card4 = document.getElementById('stack-card-4');

            if (!card1 || !card2 || !card3 || !card4) return;

            const offset1 = 222; // Sticky top of Card 1
            const offset2 = 242; // Sticky top of Card 2
            const offset3 = 262; // Sticky top of Card 3
            // offset4 is 282, but not needed for trigger (Card 4 is top)

            const triggerOffset = 230; // Half of 460px height

            const rect2 = card2.getBoundingClientRect();
            const rect3 = card3.getBoundingClientRect();
            const rect4 = card4.getBoundingClientRect();

            // Card 1 Logic: When Card 2 overlaps half of Card 1
            if (rect2.top <= offset1 + triggerOffset) {
                // Apply "Card 1" Stacked Style
                card1.style.opacity = '0.16';
                card1.style.borderRadius = '16.96px';
                card1.style.padding = '33.91px';
                card1.style.borderWidth = '1.41px';
                card1.style.transform = 'scale(0.92) translateY(-10px)';
            } else {
                // Reset to Default
                card1.style.opacity = '1';
                card1.style.borderRadius = '';
                card1.style.padding = '';
                card1.style.borderWidth = '';
                card1.style.transform = 'scale(1) translateY(0)';
            }

            // Card 2 Logic: When Card 3 overlaps half of Card 2
            if (rect3.top <= offset2 + triggerOffset) {
                // Apply "Card 2" Stacked Style
                card2.style.opacity = '0.32';
                card2.style.borderRadius = '16.96px';
                card2.style.padding = '33.91px';
                card2.style.borderWidth = '1.41px';
                card2.style.transform = 'scale(0.95) translateY(-5px)';
            } else {
                // Reset if not overlapped sufficiently by Card 3
                card2.style.opacity = '1';
                card2.style.borderRadius = '';
                card2.style.padding = '';
                card2.style.borderWidth = '';
                card2.style.transform = 'scale(1) translateY(0)';
            }

            // Card 3 Logic: When Card 4 overlaps half of Card 3
            if (rect4.top <= offset3 + triggerOffset) {
                // Apply "Card 3" Stacked Style
                card3.style.opacity = '0.46';
                card3.style.borderRadius = '16.96px';
                card3.style.padding = '33.91px';
                card3.style.borderWidth = '1.41px';
                card3.style.transform = 'scale(0.98) translateY(-2px)';
            } else {
                card3.style.opacity = '1';
                card3.style.borderRadius = '';
                card3.style.padding = '';
                card3.style.borderWidth = '';
                card3.style.transform = 'scale(1) translateY(0)';
            }
        });
    </script>
</x-public-layout>