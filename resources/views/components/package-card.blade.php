@props([
    'plan',
    'variant' => 'standard', // 'standard' or 'premium'
    'position' => 'left' // 'left' or 'right'
])

@php
    $isStandard = $variant === 'standard';
    $isPremium = $variant === 'premium';
    $positionClass = $position === 'left' ? 'justify-self-end' : 'justify-self-start';
    
    // Get features
    $features = [];
    if (isset($plan->features) && is_array($plan->features)) {
        $features = $isStandard ? array_slice($plan->features, 0, 4) : $plan->features;
    } else {
        // Default features
        $features = $isStandard ? [
            'Limited access to video lessons',
            'Mock exams & quizzes',
            'Weekly updated learning material',
            'Basic personal progress tracker (automated tracking only)'
        ] : [
            'Full access to video lessons',
            'Mock exams & quizzes',
            'Personal progress tracker',
            'Weekly updated learning material',
            'Q&A support from expert tutor',
            'Access to the private community',
            'Assignments with personalised feedback'
        ];
    }
@endphp

{{-- Standard Plan (Gradient Card) --}}
@if($isStandard)
    <div class="reveal {{ $positionClass }} w-full md:w-[470px] min-h-[500px] md:h-[584px] p-[20px] md:p-[24px] rounded-[20px] md:rounded-[24px] relative overflow-hidden flex flex-col justify-between group hover:-translate-y-2 transition duration-300"
        style="background: linear-gradient(137.2deg, #045F98 -1.43%, #011F32 97.08%); box-shadow: 0px 4px 24px rgba(0, 0, 0, 0.08);">
        <img src="{{ asset('images/package card 1 curve .png') }}" alt="" class="absolute left-0 top-0 w-full h-auto pointer-events-none z-0">
        <div class="relative z-10">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-start mb-6 md:mb-8 gap-2">
                <h3 class="font-sans font-bold text-[28px] md:text-[36px] leading-[1.1] md:leading-[1.05] tracking-[-0.04em] text-white">{{ $plan->title ?? $plan->name }}</h3>
                <div class="font-sans font-medium text-white text-left sm:text-right shrink-0">
                    <span class="text-[26px] md:text-[32px] leading-[1] tracking-[-0.02em] font-bold">${{ number_format($plan->price ?? 30, 0) }}</span>
                    <span class="text-[14px] md:text-[16px] opacity-80 font-normal">{{ isset($plan->billing_period) ? '/' . $plan->billing_period : '/month' }}</span>
                </div>
            </div>
        </div>
        <div class="mb-4"></div>
        <ul class="space-y-3 md:space-y-4 mb-8 md:mb-12 mt-auto">
            @foreach($features as $feature)
                <li class="flex items-start gap-3">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="shrink-0">
                        <path d="M7 12L10.5 15.5L17 9" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="font-sans font-medium text-[16px] md:text-[20px] leading-[1.2] md:leading-[1.05] tracking-[-0.04em] text-white">{{ $feature }}</span>
                </li>
            @endforeach
        </ul>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 md:gap-6 mt-6 md:mt-8">
            <a href="{{ route('admin.subscription-plans.show', $plan) }}"
                class="w-full sm:w-[161px] h-[50px] md:h-[56px] bg-white text-[#082E4E] font-sans font-bold text-[16px] md:text-[18px] leading-[1.1] tracking-normal rounded-[100px] flex items-center justify-center gap-2 hover:shadow-lg hover:-translate-y-1 transition transform group">
                Join Now
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="transition-transform duration-300 group-hover:translate-x-1">
                    <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="#082E4E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <a href="#" class="flex items-center justify-center sm:justify-start gap-2 font-sans font-medium text-[14px] md:text-[16px] text-white opacity-80 hover:opacity-100 transition-opacity whitespace-nowrap group">
                Cancel anytime
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="transition-transform duration-300 group-hover:translate-x-1">
                    <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
        </div>
    </div>
@endif

{{-- Premium Plan (White Card) --}}
@if($isPremium)
    <div class="reveal {{ $positionClass }} w-full md:w-[488px] min-h-[500px] md:h-[584px] p-[20px] md:p-[24px] bg-white rounded-[20px] md:rounded-[24px] border border-[#00000066] relative overflow-hidden flex flex-col justify-between group hover:-translate-y-2 transition duration-300 shadow-xl hover:shadow-2xl">
        <img src="{{ asset('images/package 2 curve.png') }}" alt="" class="absolute left-0 top-[100px] w-full h-auto pointer-events-none z-0">
        <div class="relative z-10">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-start mb-2 gap-2">
                <h3 class="font-sans font-bold text-[28px] md:text-[36px] leading-[1.1] md:leading-[1.05] tracking-[-0.04em] text-[#082E4E]">{{ $plan->title ?? $plan->name }}</h3>
                <div class="font-sans font-bold text-[#082E4E] text-left sm:text-right shrink-0">
                    @if($plan->first_month_price)
                        {{-- Promotional pricing format --}}
                        <div class="text-[26px] md:text-[32px] leading-[1] tracking-[-0.02em]">${{ number_format($plan->first_month_price, 2) }} <span class="text-[14px] md:text-[18px] font-normal opacity-80">→ first month</span></div>
                    @else
                        {{-- Regular pricing format --}}
                        <div class="text-[26px] md:text-[32px] leading-[1] tracking-[-0.02em]">${{ number_format($plan->price, 2) }}<span class="text-[14px] md:text-[18px] font-normal opacity-80">/month</span></div>
                    @endif
                </div>
            </div>
            @if($plan->first_month_price)
                {{-- Show promotional description only when promotional pricing is active --}}
                @if(isset($plan->description) && $plan->description)
                    <div class="mb-6 md:mb-8 font-sans font-normal text-[16px] md:text-[18px] text-[#082E4E] underline decoration-gray-300 underline-offset-4">
                        {{ Str::limit($plan->description, 50) }}
                    </div>
                @else
                    {{-- Default promotional description with dynamic regular price --}}
                    <div class="mb-6 md:mb-8 font-sans font-normal text-[16px] md:text-[18px] text-[#082E4E] underline decoration-gray-300 underline-offset-4">
                        ${{ number_format($plan->regular_price ?? $plan->price ?? 20, 2) }}/month for the next {{ $plan->promo_duration ?? 2 }} months
                    </div>
                @endif
            @else
                {{-- Show description only if it exists when no promotional pricing --}}
                @if(isset($plan->description) && $plan->description)
                    <div class="mb-6 md:mb-8 font-sans font-normal text-[16px] md:text-[18px] text-[#082E4E]">
                        {{ Str::limit($plan->description, 80) }}
                    </div>
                @endif
            @endif
        </div>

        <ul class="space-y-3 md:space-y-4 mb-8 md:mb-12 mt-auto relative z-10">
            @foreach($features as $feature)
                <li class="flex items-start gap-3">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="shrink-0">
                        <path d="M7 12L10.5 15.5L17 9" stroke="#15803D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="font-sans font-medium text-[16px] md:text-[20px] leading-[1.2] md:leading-[1.05] tracking-[-0.04em] text-[#082E4E]">{{ $feature }}</span>
                </li>
            @endforeach
        </ul>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 md:gap-6 mt-6 md:mt-8 relative z-10">
            <a href="{{ route('admin.subscription-plans.show', $plan) }}"
                class="w-full sm:w-[161px] h-[50px] md:h-[56px] bg-[#082E4E] text-white font-sans font-bold text-[16px] md:text-[18px] leading-[1.1] tracking-normal rounded-[100px] flex items-center justify-center gap-2 hover:shadow-lg hover:bg-opacity-90 transition transform group">
                Join Now
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="transition-transform duration-300 group-hover:translate-x-1">
                    <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <a href="#" class="flex items-center justify-center sm:justify-start gap-2 font-sans font-medium text-[14px] md:text-[16px] text-[#082E4E] hover:opacity-80 transition-opacity whitespace-nowrap group">
                Cancel anytime
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="transition-transform duration-300 group-hover:translate-x-1">
                    <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="#082E4E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
        </div>
    </div>
@endif
