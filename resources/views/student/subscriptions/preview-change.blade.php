@extends('layouts.student')

@section('title', 'Preview Plan Change')
@section('page-title', 'Preview Plan Change')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('student.subscriptions.index') }}"
                class="text-indigo-600 hover:text-indigo-800 flex items-center mb-4">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Plans
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Confirm Plan Change</h1>
            <p class="mt-2 text-gray-600">Review the details before switching your subscription</p>
        </div>

        <!-- Plan Comparison -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Plan Change Summary</h2>

            <div class="grid grid-cols-2 gap-6">
                <!-- Current Plan -->
                <div class="border-2 border-gray-200 rounded-lg p-4">
                    <div class="text-sm text-gray-500 mb-1">Current Plan</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $currentPlan->name ?? 'Unknown' }}</div>
                    <div class="text-lg text-gray-600 mt-2">${{ number_format($currentPlan->price ?? 0, 2) }}/month</div>
                </div>

                <!-- Arrow -->
                <div class="flex items-center justify-center text-indigo-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </div>

                <!-- New Plan - moved to correct position -->
            </div>

            <div class="grid grid-cols-2 gap-6 mt-[-80px] ml-[50%]">
                <div class="border-2 border-indigo-500 rounded-lg p-4 bg-indigo-50">
                    <div class="text-sm text-indigo-600 mb-1">New Plan</div>
                    <div class="text-2xl font-bold text-indigo-900">{{ $plan->name }}</div>
                    <div class="text-lg text-indigo-700 mt-2">${{ number_format($plan->price, 2) }}/month</div>
                    @if($isUpgrade)
                        <span
                            class="inline-block mt-2 px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Upgrade</span>
                    @else
                        <span
                            class="inline-block mt-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">Downgrade</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Proration Details -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Billing Details</h2>

            <!-- Charge Amount -->
            <div
                class="bg-{{ $prorationAmount >= 0 ? 'blue' : 'green' }}-50 border border-{{ $prorationAmount >= 0 ? 'blue' : 'green' }}-200 rounded-lg p-6 mb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-{{ $prorationAmount >= 0 ? 'blue' : 'green' }}-600 mb-1">
                            @if($prorationAmount >= 0)
                                Amount to Charge Now
                            @else
                                Credit to Your Account
                            @endif
                        </div>
                        <div class="text-4xl font-bold text-{{ $prorationAmount >= 0 ? 'blue' : 'green' }}-900">
                            ${{ number_format(abs($prorationAmount), 2) }}
                        </div>
                        <div class="text-sm text-{{ $prorationAmount >= 0 ? 'blue' : 'green' }}-700 mt-1">
                            Charged to your default payment method immediately
                        </div>
                    </div>
                    <svg class="w-16 h-16 text-{{ $prorationAmount >= 0 ? 'blue' : 'green' }}-400" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                        <path fill-rule="evenodd"
                            d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
            </div>

            <!-- Line Items Breakdown -->
            <div class="space-y-3">
                <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Breakdown:</h3>
                @foreach($lineItems as $item)
                    <div class="flex items-center justify-between py-2 border-b border-gray-200">
                        <div class="flex-1">
                            <div class="text-sm text-gray-900">{{ $item['description'] }}</div>
                            @if($item['proration'])
                                <span class="text-xs text-indigo-600">(Prorated adjustment)</span>
                            @endif
                        </div>
                        <div class="text-sm font-semibold {{ $item['amount'] < 0 ? 'text-green-600' : 'text-gray-900' }}">
                            {{ $item['amount'] < 0 ? '-' : '' }}${{ number_format(abs($item['amount']), 2) }}
                        </div>
                    </div>
                @endforeach

                <div class="flex items-center justify-between py-3 pt-4 border-t-2 border-gray-300">
                    <div class="text-base font-bold text-gray-900">Total Due Today:</div>
                    <div class="text-xl font-bold text-{{ $prorationAmount >= 0 ? 'blue' : 'green' }}-900">
                        ${{ number_format(abs($prorationAmount), 2) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Important Information -->
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <svg class="w-6 h-6 text-yellow-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
                <div>
                    <h3 class="text-sm font-semibold text-yellow-800 mb-2">Important Information:</h3>
                    <ul class="text-sm text-yellow-700 space-y-1">
                        <li>• Your access to courses from your current plan will be <strong>suspended</strong></li>
                        <li>• You will gain immediate access to courses in the new plan</li>
                        <li>• Your billing cycle remains the same (next billing:
                            {{ $subscription->asStripeSubscription()->current_period_end ? \Carbon\Carbon::createFromTimestamp($subscription->asStripeSubscription()->current_period_end)->format('M d, Y') : 'N/A' }})
                        </li>
                        <li>• Future charges will be ${{ number_format($plan->price, 2) }}/month</li>
                        <li>• Your learning progress will be preserved</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4">
            <a href="{{ route('student.subscriptions.index') }}"
                class="flex-1 text-center px-6 py-3 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 font-semibold">
                Cancel
            </a>
            <form action="{{ route('student.subscriptions.confirm-plan-change', $plan) }}" method="POST" class="flex-1">
                @csrf
                <button type="submit"
                    class="w-full px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-semibold">
                    Confirm Plan Change
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-500 mt-4">
            By confirming, you agree to the plan change and authorize the charge above.
        </p>
    </div>
@endsection