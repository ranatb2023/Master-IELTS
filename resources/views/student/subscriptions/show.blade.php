@extends('layouts.student')

@section('title', $plan->name)

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('student.subscriptions.index') }}"
                    class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                    ← Back to Plans
                </a>
            </div>

            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-6 py-8 sm:p-10">
                    <div class="text-center">
                        <h1 class="text-4xl font-extrabold text-gray-900">{{ $plan->name }}</h1>
                        @if($plan->description)
                            <p class="mt-4 text-lg text-gray-600">{{ $plan->description }}</p>
                        @endif
                    </div>

                    <!-- Pricing -->
                    <div class="mt-8 flex justify-center">
                        <div class="flex items-baseline text-6xl font-extrabold">
                            <span
                                class="text-gray-900">${{ number_format($plan->hasPromotionalPricing() ? $plan->first_month_price : $plan->price, 0) }}</span>
                            <span class="ml-1 text-2xl font-medium text-gray-500">/{{ $plan->interval }}</span>
                        </div>
                    </div>

                    @if($plan->hasPromotionalPricing())
                        <p class="mt-4 text-center text-gray-600">
                            First {{ $plan->promotional_months }} {{ Str::plural('month', $plan->promotional_months) }} at
                            ${{ number_format($plan->first_month_price, 2) }}, then
                            ${{ number_format($plan->regular_price, 2) }}/{{ $plan->interval }}
                        </p>
                    @endif

                    @if($plan->trial_days > 0)
                        <p class="mt-2 text-center text-green-600 font-medium">
                            {{ $plan->trial_days }}-day free trial included
                        </p>
                    @endif

                    <!-- Features -->
                    <div class="mt-10">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6">What's Included</h3>
                        <ul class="space-y-4">
                            @foreach($plan->features as $feature)
                                <li class="flex items-start">
                                    <svg class="flex-shrink-0 h-6 w-6 text-green-500 mr-3" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-gray-700">{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- CTA -->
                    <div class="mt-10">
                        @if($currentSubscription)
                            <p class="text-center text-gray-600 mb-4">You already have an active subscription</p>
                            <a href="{{ route('student.subscriptions.manage') }}"
                                class="block w-full text-center bg-indigo-600 border border-transparent rounded-md py-3 px-8 text-base font-medium text-white hover:bg-indigo-700">
                                Manage Subscription
                            </a>
                        @else
                            <a href="{{ route('student.subscriptions.checkout', $plan) }}"
                                class="block w-full text-center bg-indigo-600 border border-transparent rounded-md py-3 px-8 text-base font-medium text-white hover:bg-indigo-700">
                                Subscribe Now
                            </a>
                        @endif
                    </div>

                    <p class="mt-6 text-center text-xs text-gray-500">
                        Cancel anytime. Secure payment by Stripe.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection