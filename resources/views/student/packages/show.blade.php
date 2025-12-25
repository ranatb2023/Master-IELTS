@extends('layouts.student')

@section('title', $package->name)

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('student.packages.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                    ← Back to Packages
                </a>
            </div>

            {{-- Package Upgrade Warning --}}
            @if($currentPackage && !$hasAccess)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">📦 Package Upgrade</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>
                                    You currently have the <strong class="font-semibold">{{ $currentPackage->name }}</strong> package.
                                </p>
                                <p class="mt-1">
                                    Purchasing this package will <strong>replace</strong> your current package. Your old features will be revoked and new features will be granted.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Active Subscription Warning --}}
            @if(isset($activeSubscription) && $activeSubscription && !$hasAccess)
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">⚠️ Active Subscription Will Be Cancelled</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p class="font-semibold">
                                    You currently have: <strong>{{ $activeSubscription->subscriptionPlan->name ?? 'Active Subscription' }}</strong>
                                </p>
                                <div class="mt-2 bg-red-100 border border-red-200 rounded p-2">
                                    <p class="font-bold text-red-900">⚠️ IMPORTANT - NO REFUND:</p>
                                    <ul class="mt-1 list-disc list-inside space-y-1">
                                        <li>Your subscription will be <strong>IMMEDIATELY CANCELLED</strong></li>
                                        <li>All subscription features will be <strong>REVOKED</strong></li>
                                        <li><strong>NO REFUND</strong> for remaining subscription time</li>
                                        <li>You will switch to package features only</li>
                                    </ul>
                                </div>
                                <p class="mt-2 text-xs">
                                    💡 Consider if you want to keep your subscription instead of purchasing this package.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content (Left Column - 2/3) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Package Header -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-8">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    @if($package->category)
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                            {{ ucfirst($package->category) }}
                                        </span>
                                    @endif
                                    @if($package->is_featured)
                                        <span
                                            class="ml-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                            ⭐ Featured
                                        </span>
                                    @endif
                                    @if($hasAccess)
                                        <span
                                            class="ml-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            ✓ You Own This
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $package->name }}</h1>
                            <p class="text-lg text-gray-600 mb-6">{{ $package->description }}</p>

                            <!-- Package Stats -->
                            <div class="flex flex-wrap gap-6 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    <span><strong>{{ $package->courses->count() }}</strong> Courses</span>
                                </div>

                                @if($package->is_lifetime)
                                    <div class="flex items-center text-green-600 font-medium">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Lifetime Access</span>
                                    </div>
                                @elseif($package->duration_days)
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span><strong>{{ $package->duration_days }}</strong> Days Access</span>
                                    </div>
                                @endif

                                @if($package->auto_enroll_courses)
                                    <div class="flex items-center text-green-600">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Auto-Enrollment</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Access Info for Purchased Packages -->
                            @if($accessDetails)
                                @php
                                    $hasRefundedEnrollment = $accessDetails->enrollments()
                                        ->where('payment_status', 'refunded')
                                        ->exists();

                                    $refundedEnrollment = $hasRefundedEnrollment
                                        ? $accessDetails->enrollments()->where('payment_status', 'refunded')->first()
                                        : null;
                                @endphp

                                @if($hasRefundedEnrollment)
                                    <!-- Refunded Status -->
                                    <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                                        <h4 class="font-semibold text-red-900 mb-2 flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            ⚠️ Access Revoked - Enrollment Refunded
                                        </h4>
                                        <div class="text-sm text-red-700 space-y-1">
                                            <p><strong>Refund Amount:</strong>
                                                ${{ number_format($refundedEnrollment->refund_amount ?? 0, 2) }}</p>
                                            <p><strong>Refunded On:</strong>
                                                {{ $refundedEnrollment->refunded_at?->format('M d, Y') }}</p>
                                            @if($refundedEnrollment->refund_reason)
                                                <p><strong>Reason:</strong> {{ $refundedEnrollment->refund_reason }}</p>
                                            @endif
                                            <p class="text-xs text-red-600 mt-2">⏱ Funds will appear in your account within 5-10
                                                business days</p>
                                        </div>
                                    </div>
                                @elseif($hasAccess)
                                    <!-- Active Access -->
                                    <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                                        <h4 class="font-semibold text-green-900 mb-2">✓ You have access to this package</h4>
                                        <div class="text-sm text-green-700 space-y-1">
                                            <p><strong>Purchased:</strong> {{ $accessDetails->created_at->format('M d, Y') }}
                                            </p>
                                            @if($accessDetails->expires_at)
                                                <p><strong>Expires:</strong> {{ $accessDetails->expires_at->format('M d, Y') }}</p>
                                                <p><strong>Days Remaining:</strong>
                                                    @php $daysRemaining = now()->diffInDays($accessDetails->expires_at, false); @endphp @if($daysRemaining < 0) <span class=\"text-red-600 font-semibold\">Expired</span> @elseif($daysRemaining == 0) <span class=\"text-orange-600 font-semibold\">Expires Today</span> @else {{ ceil($daysRemaining) }} days @endif</p>
                                            @else
                                                <p class="font-medium">Lifetime Access - Never Expires</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- Included Courses -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Included Courses</h3>

                            @if($package->courses->count() > 0)
                                <div class="space-y-4">
                                    @foreach($package->courses as $course)
                                        <div
                                            class="flex items-start p-4 border border-gray-200 rounded-lg hover:border-indigo-300 transition">
                                            <div
                                                class="flex-shrink-0 w-16 h-16 bg-indigo-100 rounded-lg flex items-center justify-center mr-4">
                                                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="text-lg font-semibold text-gray-900 mb-1">{{ $course->title }}</h4>
                                                <div class="text-sm text-gray-600 line-clamp-2">{!! $course->description !!}
                                                </div>
                                                <div class="mt-2 flex items-center gap-4 text-xs text-gray-500">
                                                    @if($course->level)
                                                        <span class="inline-flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                                            </svg>
                                                            {{ ucfirst($course->level) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($hasAccess)
                                                <a href="{{ route('student.courses.learn', $course) }}"
                                                    class="ml-4 px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 transition">
                                                    Start Learning
                                                </a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500">No courses included in this package.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Package Features -->
                    @if($package->features && count($package->features) > 0)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="p-8">
                                <h3 class="text-2xl font-bold text-gray-900 mb-4">What's Included</h3>
                                <ul class="space-y-3">
                                    @foreach($package->features as $feature)
                                        <li class="flex items-start">
                                            <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span class="text-gray-700">{{ $feature }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar (Right Column - 1/3) -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden sticky top-6">
                        <div class="p-6">
                            {{-- Price --}}
                            <div class="text-center mb-6">
                                @if($package->sale_price)
                                    {{-- Sale Price --}}
                                    <div class="text-5xl font-bold text-green-600 mb-1">
                                        ${{ number_format($package->sale_price, 2) }}
                                    </div>
                                    <div class="text-2xl text-gray-400 line-through mb-2">
                                        ${{ number_format($package->price, 2) }}
                                    </div>
                                @else
                                    {{-- Regular Price --}}
                                    <div class="text-5xl font-bold text-gray-900 mb-2">
                                        ${{ number_format($package->price, 2) }}
                                    </div>
                                @endif
                                @if(!$package->is_lifetime && $package->duration_days)
                                    <div class="text-sm text-gray-500">
                                        for {{ $package->duration_days }} days
                                    </div>
                                @elseif($package->is_lifetime)
                                    <div class="text-sm text-green-600 font-semibold">
                                        Lifetime Access
                                    </div>
                                @endif
                            </div>

                            <!-- Purchase Button -->
                            @if($hasAccess)
                                <a href="{{ route('student.enrollments.index') }}"
                                    class="block w-full px-6 py-3 bg-green-600 text-white text-center font-semibold rounded-lg hover:bg-green-700 transition mb-3">
                                    Access Your Courses
                                </a>
                            @else
                                @if($package->is_subscription_package)
                                    <a href="{{ route('student.subscriptions.index') }}"
                                        class="block w-full px-6 py-3 bg-indigo-600 text-white text-center font-semibold rounded-lg hover:bg-indigo-700 transition mb-3">
                                        View Subscription Plans
                                    </a>
                                    <p class="text-xs text-gray-500 text-center">This package requires a subscription</p>
                                @else
                                    <a href="{{ route('student.packages.checkout', $package) }}"
                                        class="block w-full px-6 py-3 bg-indigo-600 text-white text-center font-semibold rounded-lg hover:bg-indigo-700 transition mb-3">
                                        Purchase Now
                                    </a>
                                    <p class="text-xs text-gray-500 text-center">Secure payment via Stripe</p>
                                @endif
                            @endif

                            <!-- Package Highlights -->
                            <div class="mt-6 pt-6 border-t border-gray-200 space-y-3">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>{{ $package->courses->count() }} courses included</span>
                                </div>
                                @if($package->is_lifetime)
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Lifetime access</span>
                                    </div>
                                @endif
                                @if($package->auto_enroll_courses)
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Automatic course enrollment</span>
                                    </div>
                                @endif
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Instant access</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Secure payment</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Packages -->
            @if($relatedPackages->count() > 0)
                <div class="mt-12">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Related Packages</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($relatedPackages as $related)
                            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                                <div class="p-6">
                                    <h4 class="text-lg font-bold text-gray-900 mb-2">{{ $related->name }}</h4>
                                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $related->description }}</p>
                                    <div class="flex items-baseline mb-4">
                                        @if($related->sale_price)
                                            <span class="text-2xl font-bold text-green-600">${{ number_format($related->sale_price, 2) }}</span>
                                            <span class="ml-2 text-lg text-gray-400 line-through">${{ number_format($related->price, 2) }}</span>
                                        @else
                                            <span class="text-2xl font-bold text-gray-900">${{ number_format($related->price, 2) }}</span>
                                        @endif
                                        @if($related->is_lifetime)
                                            <span class="ml-2 text-sm text-green-600">Lifetime</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('student.packages.show', $related) }}"
                                        class="block w-full text-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                                        View Package
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

