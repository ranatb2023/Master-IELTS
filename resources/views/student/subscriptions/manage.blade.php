@extends('layouts.student')

@section('title', 'Manage Subscription')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Manage Subscription</h1>
                <a href="{{ route('student.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Dashboard
                </a>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Current Plan Card -->
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-2xl font-bold">{{ $subscription->subscriptionPlan->name }}</h2>
                                    <p class="text-indigo-100 mt-1 text-lg">
                                        ${{ number_format($subscription->subscriptionPlan->price, 2) }} /
                                        {{ $subscription->subscriptionPlan->interval }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    @if($subscription->onTrial())
                                        <span
                                            class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-blue-500 text-white">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Trial Active
                                        </span>
                                    @elseif($subscription->active())
                                        <span
                                            class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-500 text-white">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Active
                                        </span>
                                    @elseif($subscription->onGracePeriod())
                                        <span
                                            class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-yellow-500 text-white">
                                            ⚠ Cancelled
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-gray-500 text-white">
                                            {{ ucfirst($subscription->stripe_status) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <!-- Trial Days Left Alert -->
                            @if($subscription->onTrial())
                                @php
                                    $trialEndsAt = $subscription->trial_ends_at;
                                    $daysLeft = $trialEndsAt ? $trialEndsAt->diffInDays(now()) : 0;
                                    $hoursLeft = $trialEndsAt ? $trialEndsAt->diffInHours(now()) % 24 : 0;
                                @endphp
                                <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                                    <div class="flex items-start">
                                        <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <div class="flex-1">
                                            <h3 class="text-blue-800 font-semibold text-lg">Free Trial Active</h3>
                                            <p class="text-blue-700 mt-1">
                                                @if($daysLeft > 0)
                                                    <span class="text-2xl font-bold">{{ $daysLeft }}</span>
                                                    <span class="text-base">{{ Str::plural('day', $daysLeft) }}</span>
                                                    @if($hoursLeft > 0)
                                                        and <span class="font-semibold">{{ $hoursLeft }}</span>
                                                        {{ Str::plural('hour', $hoursLeft) }}
                                                    @endif
                                                    left
                                                @else
                                                    Less than 1 hour remaining
                                                @endif
                                            </p>
                                            <p class="text-sm text-blue-600 mt-2">
                                                Trial ends on <strong>{{ $trialEndsAt->format('F j, Y \a\t g:i A') }}</strong>
                                            </p>
                                            <p class="text-xs text-blue-600 mt-1">
                                                You will be charged
                                                ${{ number_format($subscription->subscriptionPlan->price, 2) }} on
                                                {{ $trialEndsAt->format('M d, Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Cancellation Notice -->
                            @if($subscription->onGracePeriod())
                                <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                                    <div class="flex items-start">
                                        <svg class="w-6 h-6 text-yellow-600 mr-3 flex-shrink-0" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <div class="flex-1">
                                            <h3 class="text-yellow-800 font-semibold">Subscription Scheduled for Cancellation
                                            </h3>
                                            <p class="text-yellow-700 mt-1">
                                                Your subscription will end on
                                                <strong>{{ $subscription->ends_at->format('F j, Y') }}</strong>.
                                                You'll retain access until then.
                                            </p>
                                            <form action="{{ route('student.subscriptions.resume') }}" method="POST"
                                                class="mt-3">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                    </svg>
                                                    Resume Subscription
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Subscription Details Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <p class="text-sm text-gray-600 mb-1">Current Period</p>
                                    <p class="font-semibold text-gray-900">
                                        {{ $billingDates['current_period_start'] ? $billingDates['current_period_start']->format('M d, Y') : 'N/A' }}
                                    </p>
                                    <p class="text-xs text-gray-500">to</p>
                                    <p class="font-semibold text-gray-900">
                                        {{ $billingDates['current_period_end'] ? $billingDates['current_period_end']->format('M d, Y') : 'N/A' }}
                                    </p>
                                </div>

                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <p class="text-sm text-gray-600 mb-1">Started On</p>
                                    <p class="font-semibold text-gray-900">
                                        {{ $subscription->created_at->format('F j, Y') }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-2">
                                        {{ $subscription->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Plan Features -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Plan Features</h2>
                        <ul class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($subscription->subscriptionPlan->features as $feature)
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor"
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

                    <!-- Included Packages -->
                    @if($packageAccess->count() > 0)
                        <div class="bg-white shadow rounded-lg p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Included Packages
                                ({{ $packageAccess->count() }})</h2>
                            <div class="space-y-3">
                                @foreach($packageAccess as $access)
                                    <div
                                        class="flex items-center justify-between border border-gray-200 rounded-lg p-4 hover:border-indigo-300 hover:shadow-md transition">
                                        <div class="flex items-center">
                                            <div class="p-3 bg-indigo-100 rounded-lg mr-4">
                                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="font-semibold text-gray-900">{{ $access->package->name }}</h3>
                                                <p class="text-sm text-gray-600">{{ $access->package->courses->count() }}
                                                    {{ Str::plural('course', $access->package->courses->count()) }}
                                                </p>
                                            </div>
                                        </div>
                                        <a href="{{ route('student.packages.show', $access->package) }}"
                                            class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium">
                                            View Courses
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Next Billing Card -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Next Billing</h3>
                        @if($subscription->onGracePeriod())
                            <div class="text-center py-4">
                                <p class="text-gray-600 text-sm">No upcoming charges</p>
                                <p class="text-xs text-gray-500 mt-2">Subscription ends
                                    {{ $subscription->ends_at->format('M d, Y') }}
                                </p>
                            </div>
                        @elseif($subscription->onTrial())
                            <div class="text-center">
                                <p class="text-sm text-gray-600 mb-2">First charge after trial</p>
                                <p class="text-3xl font-bold text-indigo-600">
                                    ${{ number_format($subscription->subscriptionPlan->price, 2) }}</p>
                                <p class="text-sm text-gray-600 mt-3">on</p>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ $subscription->trial_ends_at->format('M d, Y') }}
                                </p>
                                <p class="text-xs text-gray-500 mt-2">
                                    ({{ $subscription->trial_ends_at->diffForHumans() }})
                                </p>
                            </div>
                        @else
                            <div class="text-center">
                                <p class="text-3xl font-bold text-gray-900">
                                    ${{ number_format($subscription->subscriptionPlan->price, 2) }}</p>
                                <p class="text-sm text-gray-600 mt-3">on</p>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ $billingDates['current_period_end'] ? $billingDates['current_period_end']->format('F j, Y') : 'N/A' }}
                                </p>
                                @if($billingDates['current_period_end'])
                                    <p class="text-xs text-gray-500 mt-2">
                                        ({{ $billingDates['current_period_end']->diffForHumans() }})
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Payment Method</h3>
                            <a href="{{ route('student.subscriptions.payment-method') }}"
                                class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                Update
                            </a>
                        </div>
                        @if(auth()->user()->hasDefaultPaymentMethod())
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                <svg class="w-10 h-10 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                <div>
                                    <p class="text-sm text-gray-900 font-medium">
                                        {{ ucfirst(auth()->user()->pm_type) }} •••• {{ auth()->user()->pm_last_four }}
                                    </p>
                                    <p class="text-xs text-gray-600">Default card</p>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4 bg-yellow-50 rounded-lg">
                                <p class="text-sm text-yellow-800">⚠ No payment method</p>
                                <a href="{{ route('student.subscriptions.payment-method') }}"
                                    class="text-xs text-yellow-700 underline mt-1 inline-block">
                                    Add one now
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Manage</h3>
                        <div class="space-y-3">
                            <a href="{{ route('student.subscriptions.index') }}"
                                class="flex items-center justify-between w-full px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition">
                                <span class="text-sm font-medium">Change Plan</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                            <a href="{{ route('student.subscriptions.invoices') }}"
                                class="flex items-center justify-between w-full px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition">
                                <span class="text-sm font-medium">View Invoices</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </a>
                            <a href="{{ route('student.subscriptions.payment-method') }}"
                                class="flex items-center justify-between w-full px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition">
                                <span class="text-sm font-medium">Payment Method</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </a>
                            @if(!$subscription->onGracePeriod())
                                <button
                                    onclick="if(confirm('Are you sure you want to cancel your subscription?\n\nYou will retain access until {{ $billingDates['current_period_end'] ? $billingDates['current_period_end']->format('M d, Y') : 'the end of your billing period' }}.\n\nYou can resume anytime before then.')) { document.getElementById('cancel-form').submit(); }"
                                    class="flex items-center justify-between w-full px-4 py-2 border-2 border-red-300 rounded-md text-red-700 hover:bg-red-50 transition font-medium">
                                    <span class="text-sm">Cancel Subscription</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                <form id="cancel-form" action="{{ route('student.subscriptions.cancel') }}" method="POST"
                                    class="hidden">
                                    @csrf
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection