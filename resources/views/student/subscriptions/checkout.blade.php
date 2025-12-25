@extends('layouts.app')

@section('title', 'Checkout - ' . $plan->name)

@section('content')
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <!-- Back Link -->
            <div class="mb-6">
                <a href="{{ route('student.subscriptions.index') }}"
                    class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900">
                    ← Back to Plans
                </a>
            </div>

            {{-- Active Package Warning --}}
            @if(isset($activePackage) && $activePackage)
                <div class="bg-red-50 border-l-4 border-red-400 p-6 mb-6 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-base font-bold text-red-900 mb-2">
                                ⚠️ ACTIVE PACKAGE WILL BE DEACTIVATED
                            </h3>
                            <div class="space-y-2 text-sm text-red-800">
                                <p class="font-semibold">
                                    You currently own: <strong class="text-lg">{{ $activePackage->package->name }}</strong>
                                    @if($activePackage->package->is_lifetime)
                                        <span
                                            class="inline-block px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs font-bold ml-2">LIFETIME
                                            ACCESS</span>
                                    @endif
                                </p>
                                <div class="mt-3 bg-red-100 border-2 border-red-300 rounded-lg p-4">
                                    <p class="font-bold text-red-900 text-base mb-2">🚨 CRITICAL WARNING:</p>
                                    <ul class="list-disc list-inside space-y-1.5 ml-2">
                                        <li class="font-semibold">Package will be <strong class="underline">IMMEDIATELY
                                                DEACTIVATED</strong></li>
                                        <li class="font-semibold">All features will be <strong
                                                class="underline">REVOKED</strong></li>
                                        <li class="font-semibold">If subscription ends, package will <strong
                                                class="underline">NOT BE REACTIVATED</strong></li>
                                    </ul>
                                </div>
                                <p
                                    class="mt-3 text-sm font-bold text-red-900 bg-yellow-100 border border-yellow-400 rounded p-2">
                                    💡 If you paid for lifetime access, consider keeping your package!
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Order Summary (Right - shown first on mobile) -->
                <div class="lg:col-span-1 order-first lg:order-last">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden sticky top-6">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Order Summary</h3>
                        </div>
                        <div class="p-6">
                            <!-- Plan Details -->
                            <div class="mb-4">
                                <h4 class="font-semibold text-gray-900 mb-2">{{ $plan->name }}</h4>
                                <p class="text-sm text-gray-600">{{ ucfirst($plan->interval) }}ly subscription</p>
                            </div>

                            <!-- Price Breakdown -->
                            <div class="border-t border-gray-200 pt-4 space-y-3">
                                @if($plan->hasPromotionalPricing())
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">First {{ $plan->promotional_months }}
                                            {{ Str::plural('month', $plan->promotional_months) }}</span>
                                        <span
                                            class="font-medium text-gray-900">${{ number_format($plan->first_month_price, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">After that</span>
                                        <span
                                            class="font-medium text-gray-900">${{ number_format($plan->regular_price, 2) }}/{{ $plan->interval }}</span>
                                    </div>
                                @else
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Subscription</span>
                                        <span
                                            class="font-medium text-gray-900">${{ number_format($plan->price, 2) }}/{{ $plan->interval }}</span>
                                    </div>
                                @endif

                                @if($plan->trial_days > 0)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Free Trial</span>
                                        <span class="font-medium text-green-600">{{ $plan->trial_days }} days</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Total -->
                            <div class="border-t border-gray-200 mt-4 pt-4">
                                <div class="flex justify-between">
                                    <span class="text-lg font-semibold text-gray-900">Total Due Today</span>
                                    <span class="text-2xl font-bold text-gray-900">
                                        @if($plan->trial_days > 0)
                                            $0.00
                                        @else
                                            ${{ number_format($plan->hasPromotionalPricing() ? $plan->first_month_price : $plan->price, 2) }}
                                        @endif
                                    </span>
                                </div>
                                @if($plan->trial_days > 0)
                                    <p class="text-xs text-gray-500 mt-1">You'll be charged after your trial ends</p>
                                @endif
                            </div>

                            <!-- Benefits -->
                            @if(!empty($plan->features))
                                <div class="border-t border-gray-200 mt-4 pt-4">
                                    <div class="text-sm font-medium text-gray-900 mb-3">What's included:</div>
                                    <ul class="space-y-2">
                                        @foreach(array_slice($plan->features ?? [], 0, 5) as $feature)
                                            <li class="flex items-start text-sm text-gray-600">
                                                <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                                <span>{{ $feature }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Security Note -->
                            <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0 mt-0.5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    <div class="text-xs text-blue-800">
                                        <p class="font-semibold mb-1">Secure Payment</p>
                                        <p>Your payment is processed securely through Stripe. We never store your card
                                            details.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Form (Left) -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Payment Information</h3>
                        </div>
                        <div class="p-6">
                            <form id="subscription-form" action="{{ route('student.subscriptions.subscribe', $plan) }}"
                                method="POST">
                                @csrf

                                @if($defaultPaymentMethod)
                                    <!-- Payment Method Selection -->
                                    <div class="space-y-4 mb-6">
                                        <!-- Saved Payment Method Option -->
                                        <label
                                            class="relative flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition-colors has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50">
                                            <input type="radio" name="payment_choice" value="saved" class="mt-1" checked>
                                            <input type="hidden" name="saved_payment_method_id"
                                                value="{{ $defaultPaymentMethod->id }}">
                                            <div class="ml-3 flex-1">
                                                <div class="flex items-center justify-between">
                                                    <span class="block text-sm font-medium text-gray-900">Use saved payment
                                                        method</span>
                                                    <div class="flex items-center gap-2">
                                                        @if($defaultPaymentMethod->card->brand === 'visa')
                                                            <svg class="h-6 w-10" viewBox="0 0 48 32" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <rect width="48" height="32" rx="4" fill="#1434CB" />
                                                                <path
                                                                    d="M21.3 21.8l1.9-11.7h3l-1.9 11.7h-3zm13.6-11.4c-.6-.2-1.5-.5-2.6-.5-2.9 0-4.9 1.5-4.9 3.7 0 1.6 1.5 2.5 2.6 3 1.1.6 1.5.9 1.5 1.4 0 .8-.9 1.1-1.8 1.1-1.2 0-1.8-.2-2.8-.6l-.4-.2-.4 2.4c.7.3 2 .6 3.3.6 3.1 0 5.1-1.5 5.1-3.8 0-1.3-.8-2.2-2.5-3-1-.5-1.6-.9-1.6-1.4 0-.5.5-1 1.6-1 .9 0 1.6.2 2.1.4l.3.1.4-2.2zm5.8-1.3h-2.3c-.7 0-1.3.2-1.6.9L32 21.8h3.1s.5-1.4.6-1.7h3.7c.1.4.4 1.7.4 1.7H43l-2.3-11.7zm-3.5 7.6c.2-.6 1.2-3.2 1.2-3.2s.3-.7.4-1.1l.2 1.1s.6 2.8.7 3.4h-2.5v-.2zm-17.7-7.6L16.7 18l-.3-1.6c-.5-1.7-2.1-3.6-3.9-4.5l2.7 10h3.1l4.6-11.7h-3.1z"
                                                                    fill="white" />
                                                                <path
                                                                    d="M13.2 10.1H8.9l-.1.4c3.7.9 6.1 3.1 7.1 5.8l-1-4.9c-.2-.7-.7-1.2-1.4-1.3z"
                                                                    fill="#F7B600" />
                                                            </svg>
                                                        @elseif($defaultPaymentMethod->card->brand === 'mastercard')
                                                            <svg class="h-6 w-10" viewBox="0 0 48 32" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <rect width="48" height="32" rx="4" fill="#000000" />
                                                                <circle cx="18" cy="16" r="8" fill="#EB001B" />
                                                                <circle cx="30" cy="16" r="8" fill="#F79E1B" />
                                                                <path
                                                                    d="M24 9.6c-1.7 1.4-2.8 3.6-2.8 6s1.1 4.6 2.8 6c1.7-1.4 2.8-3.6 2.8-6s-1.1-4.6-2.8-6z"
                                                                    fill="#FF5F00" />
                                                            </svg>
                                                        @elseif($defaultPaymentMethod->card->brand === 'amex')
                                                            <svg class="h-6 w-10" viewBox="0 0 48 32" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <rect width="48" height="32" rx="4" fill="#006FCF" />
                                                            </svg>
                                                        @else
                                                            <svg class="h-6 w-10" viewBox="0 0 48 32" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <rect width="48" height="32" rx="4" fill="#6B7280" />
                                                            </svg>
                                                        @endif
                                                    </div>
                                                </div>
                                                <p class="mt-1 text-sm text-gray-600">
                                                    {{ ucfirst($defaultPaymentMethod->card->brand) }} ending in
                                                    {{ $defaultPaymentMethod->card->last4 }}
                                                    <span class="mx-1">•</span>
                                                    Expires
                                                    {{ $defaultPaymentMethod->card->exp_month }}/{{ $defaultPaymentMethod->card->exp_year }}
                                                </p>
                                            </div>
                                        </label>

                                        <!-- New Payment Method Option -->
                                        <label
                                            class="relative flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition-colors has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50">
                                            <input type="radio" name="payment_choice" value="new" class="mt-1">
                                            <div class="ml-3">
                                                <span class="block text-sm font-medium text-gray-900">Use a different
                                                    card</span>
                                                <p class="mt-1 text-sm text-gray-600">Enter new payment details</p>
                                            </div>
                                        </label>
                                    </div>

                                    <!-- New Card Section (hidden by default) -->
                                    <div id="new-card-section" class="hidden space-y-6 mb-6">
                                        <!-- Card Element -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Card Details</label>
                                            <div id="card-element" class="p-3 border border-gray-300 rounded-md bg-white"></div>
                                            <div id="card-errors" class="mt-2 text-sm text-red-600"></div>
                                        </div>

                                        <!-- Name on Card -->
                                        <div>
                                            <label for="billing_name" class="block text-sm font-medium text-gray-700 mb-2">Name
                                                on Card</label>
                                            <input type="text" id="billing_name" name="billing_name"
                                                value="{{ Auth::user()->name }}" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                    </div>

                                    <!-- Hidden field for saved payment method -->
                                    <input type="hidden" name="saved_payment_method" value="{{ $defaultPaymentMethod->id }}">
                                @else
                                    <!-- No saved payment method - show card element directly -->
                                    <div class="space-y-6 mb-6">
                                        <!-- Card Element -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Card Details</label>
                                            <div id="card-element" class="p-3 border border-gray-300 rounded-md bg-white"></div>
                                            <div id="card-errors" class="mt-2 text-sm text-red-600"></div>
                                        </div>

                                        <!-- Name on Card -->
                                        <div>
                                            <label for="billing_name" class="block text-sm font-medium text-gray-700 mb-2">Name
                                                on Card</label>
                                            <input type="text" id="billing_name" name="billing_name"
                                                value="{{ Auth::user()->name }}" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                    </div>
                                @endif

                                <!-- Hidden payment method field -->
                                <input type="hidden" name="payment_method" id="payment-method">

                                <!-- Terms Agreement -->
                                <div class="mb-6">
                                    <label class="flex items-start">
                                        <input type="checkbox" required
                                            class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <span class="ml-2 text-sm text-gray-600">
                                            I agree to the <a href="#" class="text-indigo-600 hover:text-indigo-800">Terms
                                                of Service</a> and
                                            <a href="#" class="text-indigo-600 hover:text-indigo-800">Privacy Policy</a>
                                        </span>
                                    </label>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" id="subscribe-button"
                                    class="w-full px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span id="button-text">
                                        @if($plan->trial_days > 0)
                                            Start Free Trial
                                        @else
                                            Subscribe Now -
                                            ${{ number_format($plan->hasPromotionalPricing() ? $plan->first_month_price : $plan->price, 2) }}
                                        @endif
                                    </span>
                                    <span id="button-spinner" class="hidden">
                                        <svg class="animate-spin inline h-5 w-5 text-white"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Processing...
                                    </span>
                                </button>
                            </form>

                            <!-- Payment Methods -->
                            <div class="mt-8 pt-6 border-t border-gray-200">
                                <p class="text-xs text-gray-500 text-center mb-3">We accept</p>
                                <div class="flex justify-center items-center gap-3 flex-wrap">
                                    <!-- Visa -->
                                    <div class="bg-white border border-gray-200 rounded px-3 py-2 shadow-sm">
                                        <svg class="h-6 w-10" viewBox="0 0 48 32" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect width="48" height="32" rx="4" fill="#1434CB" />
                                            <path
                                                d="M21.3 21.8l1.9-11.7h3l-1.9 11.7h-3zm13.6-11.4c-.6-.2-1.5-.5-2.6-.5-2.9 0-4.9 1.5-4.9 3.7 0 1.6 1.5 2.5 2.6 3 1.1.6 1.5.9 1.5 1.4 0 .8-.9 1.1-1.8 1.1-1.2 0-1.8-.2-2.8-.6l-.4-.2-.4 2.4c.7.3 2 .6 3.3.6 3.1 0 5.1-1.5 5.1-3.8 0-1.3-.8-2.2-2.5-3-1-.5-1.6-.9-1.6-1.4 0-.5.5-1 1.6-1 .9 0 1.6.2 2.1.4l.3.1.4-2.2zm5.8-1.3h-2.3c-.7 0-1.3.2-1.6.9L32 21.8h3.1s.5-1.4.6-1.7h3.7c.1.4.4 1.7.4 1.7H43l-2.3-11.7zm-3.5 7.6c.2-.6 1.2-3.2 1.2-3.2s.3-.7.4-1.1l.2 1.1s.6 2.8.7 3.4h-2.5v-.2zm-17.7-7.6L16.7 18l-.3-1.6c-.5-1.7-2.1-3.6-3.9-4.5l2.7 10h3.1l4.6-11.7h-3.1z"
                                                fill="white" />
                                            <path
                                                d="M13.2 10.1H8.9l-.1.4c3.7.9 6.1 3.1 7.1 5.8l-1-4.9c-.2-.7-.7-1.2-1.4-1.3z"
                                                fill="#F7B600" />
                                        </svg>
                                    </div>
                                    <!-- Mastercard -->
                                    <div class="bg-white border border-gray-200 rounded px-3 py-2 shadow-sm">
                                        <svg class="h-6 w-10" viewBox="0 0 48 32" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect width="48" height="32" rx="4" fill="#000000" />
                                            <circle cx="18" cy="16" r="8" fill="#EB001B" />
                                            <circle cx="30" cy="16" r="8" fill="#F79E1B" />
                                            <path
                                                d="M24 9.6c-1.7 1.4-2.8 3.6-2.8 6s1.1 4.6 2.8 6c1.7-1.4 2.8-3.6 2.8-6s-1.1-4.6-2.8-6z"
                                                fill="#FF5F00" />
                                        </svg>
                                    </div>
                                    <!-- American Express -->
                                    <div class="bg-white border border-gray-200 rounded px-3 py-2 shadow-sm">
                                        <svg class="h-6 w-10" viewBox="0 0 48 32" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect width="48" height="32" rx="4" fill="#006FCF" />
                                        </svg>
                                    </div>
                                    <!-- Discover -->
                                    <div class="bg-white border border-gray-200 rounded px-3 py-2 shadow-sm">
                                        <svg class="h-6 w-10" viewBox="0 0 48 32" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect width="48" height="32" rx="4" fill="#FF6000" />
                                            <path d="M32 6h16v20c-5.3 0-10.7-6.7-16-20z" fill="#F7981D" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex justify-center mt-4">
                                    <div class="flex items-center gap-2 px-3 py-1.5 bg-indigo-50 rounded-full">
                                        <svg class="h-4" viewBox="0 0 60 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M59.2 13.2v-1.5c0-.3-.2-.5-.5-.5h-1.3c-.3 0-.5.2-.5.5v1.5c0 .3.2.5.5.5h1.3c.3 0 .5-.2.5-.5z"
                                                fill="#635BFF" />
                                            <path
                                                d="M54.4 13.2v-1.5c0-.3-.2-.5-.5-.5h-1.3c-.3 0-.5.2-.5.5v1.5c0 .3.2.5.5.5h1.3c.3 0 .5-.2.5-.5z"
                                                fill="#635BFF" />
                                            <path
                                                d="M3.7 8.5c0-1.2.9-2.1 2.1-2.1.7 0 1.3.3 1.7.7l1-1c-.7-.7-1.6-1.1-2.7-1.1-2 0-3.6 1.6-3.6 3.6s1.6 3.6 3.6 3.6c1 0 2-.4 2.7-1.1l-1-1c-.4.4-1 .7-1.7.7-1.2-.1-2.1-1-2.1-2.3zm8.8 3.2c-1.7 0-3-1.3-3-3s1.3-3 3-3 3 1.3 3 3-1.3 3-3 3zm0-4.5c-.8 0-1.5.7-1.5 1.5s.7 1.5 1.5 1.5 1.5-.7 1.5-1.5-.7-1.5-1.5-1.5zm8.3 4.5c-1 0-1.8-.5-2.3-1.3l1.3-.8c.3.5.7.7 1.1.7.7 0 1.2-.5 1.2-1.2V5.9h1.5v3.2c0 1.6-1.2 2.6-2.8 2.6zm9.4 0c-1.7 0-3-1.3-3-3s1.3-3 3-3 3 1.3 3 3-1.3 3-3 3zm0-4.5c-.8 0-1.5.7-1.5 1.5s.7 1.5 1.5 1.5 1.5-.7 1.5-1.5-.7-1.5-1.5-1.5zm7.3-1.3v6h-1.5v-6h-1.7V5.9h4.9v1.5h-1.7zm8.9 5.8h-1.5V8.9c0-.6-.4-1-1-1s-1 .4-1 1v2.8h-1.5V5.9h1.5v.5c.4-.4.9-.6 1.5-.6 1.2 0 2 .8 2 2v3.9zm7 0h-1.5V8.9c0-.6-.4-1-1-1s-1 .4-1 1v2.8h-1.5V5.9h1.5v.5c.4-.4.9-.6 1.5-.6 1.2 0 2 .8 2 2v3.9z"
                                                fill="#635BFF" />
                                        </svg>
                                        <span class="text-xs font-medium text-indigo-700">Powered by Stripe</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            // Initialize Stripe
            const stripe = Stripe('{{ config("services.stripe.key") }}');
            const elements = stripe.elements();
            let cardElement = null;

            @if($defaultPaymentMethod)
                // Payment method selection handling
                const paymentChoiceRadios = document.querySelectorAll('input[name="payment_choice"]');
                const newCardSection = document.getElementById('new-card-section');

                paymentChoiceRadios.forEach(radio => {
                    radio.addEventListener('change', function () {
                        if (this.value === 'new') {
                            newCardSection.classList.remove('hidden');
                            if (!cardElement) {
                                initializeCardElement();
                            }
                        } else {
                            newCardSection.classList.add('hidden');
                        }
                    });
                });

                function initializeCardElement() {
                    cardElement = elements.create('card', {
                        style: {
                            base: {
                                fontSize: '16px',
                                color: '#32325d',
                                fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                                '::placeholder': {
                                    color: '#aab7c4'
                                }
                            },
                            invalid: {
                                color: '#fa755a',
                                iconColor: '#fa755a'
                            }
                        }
                    });
                    cardElement.mount('#card-element');

                    cardElement.on('change', function (event) {
                        const displayError = document.getElementById('card-errors');
                        if (event.error) {
                            displayError.textContent = event.error.message;
                        } else {
                            displayError.textContent = '';
                        }
                    });
                }
            @else
                // No saved payment method - initialize card element immediately
                cardElement = elements.create('card', {
                    style: {
                        base: {
                            fontSize: '16px',
                            color: '#32325d',
                            fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                            '::placeholder': {
                                color: '#aab7c4'
                            }
                        },
                        invalid: {
                            color: '#fa755a',
                            iconColor: '#fa755a'
                        }
                    }
                });
                cardElement.mount('#card-element');

                cardElement.on('change', function (event) {
                    const displayError = document.getElementById('card-errors');
                    if (event.error) {
                        displayError.textContent = event.message;
                    } else {
                        displayError.textContent = '';
                    }
                });
            @endif

                                    // Handle form submission
                                    const form = document.getElementById('subscription-form');
            const submitButton = document.getElementById('subscribe-button');
            const buttonText = document.getElementById('button-text');
            const buttonSpinner = document.getElementById('button-spinner');

            form.addEventListener('submit', async function (event) {
                event.preventDefault();

                // Disable submit button
                submitButton.disabled = true;
                buttonText.classList.add('hidden');
                buttonSpinner.classList.remove('hidden');

                try {
                    @if($defaultPaymentMethod)
                        const paymentChoice = document.querySelector('input[name="payment_choice"]:checked').value;

                        if (paymentChoice === 'saved') {
                            // Use saved payment method
                            const savedPaymentMethodElement = document.querySelector('input[name="saved_payment_method_id"');
                            if (!savedPaymentMethodElement) {
                                throw new Error('Saved payment method not found');
                            }
                            const savedPaymentMethodId = savedPaymentMethodElement.value;
                            document.getElementById('payment-method').value = savedPaymentMethodId;
                            console.log('Submitting with saved payment method:', savedPaymentMethodId);
                            form.submit();
                            return;
                        }
                    @endif

                                    // Create new payment method
                                    const billingName = document.getElementById('billing_name').value;
                    const { paymentMethod, error } = await stripe.createPaymentMethod({
                        type: 'card',
                        card: cardElement,
                        billing_details: {
                            name: billingName,
                            email: '{{ Auth::user()->email }}'
                        }
                    });

                    if (error) {
                        // Show error
                        document.getElementById('card-errors').textContent = error.message;
                        submitButton.disabled = false;
                        buttonText.classList.remove('hidden');
                        buttonSpinner.classList.add('hidden');
                    } else {
                        // Set payment method and submit form
                        document.getElementById('payment-method').value = paymentMethod.id;
                        console.log('Submitting with new payment method:', paymentMethod.id);
                        form.submit();
                    }
                } catch (error) {
                    console.error('Form submission error:', error);
                    alert('Error: ' + error.message);
                    submitButton.disabled = false;
                    buttonText.classList.remove('hidden');
                    buttonSpinner.classList.add('hidden');
                }
            });
        </script>
    @endpush
@endsection