@extends('layouts.student')

@section('title', 'Subscription Plans')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-3xl font-bold text-gray-900 mb-6">Subscription Plans</h1>

                    @if($currentSubscription)
                        <div class="mb-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-blue-900 mb-2">Active Subscription</h3>
                                    <p class="text-blue-700">You currently have an active
                                        <strong>{{ $currentSubscription->subscriptionPlan->name ?? 'Subscription' }}</strong>
                                        subscription.
                                    </p>
                                </div>
                                <a href="{{ route('student.subscriptions.manage') }}"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    Manage Subscription
                                </a>
                            </div>
                        </div>
                    @endif

                    <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                        @forelse($plans as $plan)
                            <div
                                class="border border-gray-200 rounded-lg shadow-sm divide-y divide-gray-200 {{ $plan->is_featured ? 'ring-2 ring-indigo-500' : '' }}">
                                @if($plan->is_featured)
                                    <div class="bg-indigo-500 text-white text-center py-2 rounded-t-lg">
                                        <span class="text-sm font-medium">⭐ Most Popular</span>
                                    </div>
                                @endif

                                <div class="p-6">
                                    <h2 class="text-2xl leading-6 font-semibold text-gray-900">{{ $plan->name }}</h2>
                                    @if($plan->description)
                                        <p class="mt-4 text-sm text-gray-500">{{ $plan->description }}</p>
                                    @endif
                                    <p class="mt-8">
                                        @if($plan->hasPromotionalPricing())
                                            <span
                                                class="text-4xl font-extrabold text-gray-900">${{ number_format($plan->first_month_price, 2) }}</span>
                                            <span class="text-base font-medium text-gray-500">/{{ $plan->interval }}</span>
                                            <span class="block mt-2 text-sm text-gray-500">Then
                                                ${{ number_format($plan->regular_price, 2) }}/{{ $plan->interval }}</span>
                                        @else
                                            <span
                                                class="text-4xl font-extrabold text-gray-900">${{ number_format($plan->price, 2) }}</span>
                                            <span class="text-base font-medium text-gray-500">/{{ $plan->interval }}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="pt-6 pb-8 px-6">
                                    <h3 class="text-xs font-medium text-gray-900 tracking-wide uppercase">What's included</h3>
                                    <ul class="mt-6 space-y-4">
                                        @foreach($plan->features as $feature)
                                            <li class="flex space-x-3">
                                                <svg class="flex-shrink-0 h-5 w-5 text-green-500" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                <span class="text-sm text-gray-500">{{ $feature }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="mt-8">
                                        @if($currentSubscription && $currentSubscription->subscription_plan_id == $plan->id)
                                            <button disabled
                                                class="w-full bg-gray-300 text-gray-600 px-4 py-2 rounded-md cursor-not-allowed">
                                                Current Plan
                                            </button>
                                        @elseif($currentSubscription && $currentSubscription->onGracePeriod())
                                            <!-- Cancelled - Cannot Switch Plans -->
                                            <div class="mb-3 p-3 bg-orange-50 border-l-4 border-orange-400 rounded">
                                                <div class="flex items-start">
                                                    <svg class="w-5 h-5 text-orange-600 mt-0.5 mr-2 flex-shrink-0"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    <div>
                                                        <p class="text-sm font-semibold text-orange-800">Subscription Cancelled</p>
                                                        <p class="text-xs text-orange-700 mt-1">
                                                            Resume your subscription to change plans
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <button disabled
                                                class="w-full bg-gray-300 text-gray-600 px-4 py-2 rounded-md cursor-not-allowed">
                                                Cannot Switch Plan
                                            </button>
                                        @elseif($currentSubscription)
                                            <!-- Warning Message -->
                                            <div class="mb-3 p-2 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                                                <div class="flex items-start">
                                                    <svg class="w-4 h-4 text-yellow-600 mt-0.5 mr-2 flex-shrink-0"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    <p class="text-xs text-yellow-700">
                                                        Switching plans will suspend access to courses from your current plan
                                                    </p>
                                                </div>
                                            </div>

                                            <a href="{{ route('student.subscriptions.preview-plan-change', $plan) }}"
                                                class="block w-full text-center bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                                Switch to {{ $plan->name }}
                                            </a>
                                        @else
                                            <a href="{{ route('student.subscriptions.checkout', $plan) }}"
                                                class="block w-full text-center bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                                Subscribe Now
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-3 text-center py-12">
                                <p class="text-gray-500">No subscription plans available at the moment.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmPlanChange(event, newPlan, currentPlan) {
            const message = '⚠️ Confirm Plan Change\n\n' +
                'You are about to switch from "' + currentPlan + '" to "' + newPlan + '"\n\n' +
                '⚠️ Important:\n' +
                '• Access to courses from your current plan will be SUSPENDED\n' +
                '• You will gain access to courses in the new plan\n' +
                '• Your learning progress will be preserved\n' +
                '• Billing will be updated immediately\n\n' +
                'Do you want to continue?';

            return confirm(message);
        }
    </script>
@endpush
