@extends('layouts.admin')

@section('title', 'Subscription Plan Details')
@section('page-title', 'Subscription Plan Details')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $subscriptionPlan->name }}</h1>
                <p class="mt-1 text-sm text-gray-600">View subscription plan details and analytics</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.subscription-plans.edit', $subscriptionPlan) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Plan
                </a>
                <a href="{{ route('admin.subscription-plans.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Plans
                </a>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Total Subscriptions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Subscribers</dt>
                            <dd class="text-2xl font-semibold text-gray-900">
                                {{ number_format($stats['total_subscriptions']) }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Active Subscriptions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Subscribers</dt>
                            <dd class="text-2xl font-semibold text-gray-900">
                                {{ number_format($stats['active_subscriptions']) }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Revenue</dt>
                            <dd class="text-2xl font-semibold text-gray-900">
                                ${{ number_format($stats['total_revenue'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Average Duration -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Avg. Duration</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ number_format($stats['average_duration']) }}
                                days</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column (2/3 width) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Plan Name</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $subscriptionPlan->name }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Slug</h3>
                            <p class="mt-1 text-sm text-gray-900 font-mono">{{ $subscriptionPlan->slug }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <h3 class="text-sm font-medium text-gray-500">Description</h3>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $subscriptionPlan->description ?? 'No description provided' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Pricing Details -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Pricing Details</h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Regular Price</h3>
                            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $subscriptionPlan->formatted_price }}</p>
                            <p class="text-sm text-gray-500">per {{ $subscriptionPlan->interval }}</p>
                        </div>

                        @if($subscriptionPlan->first_month_price)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">First Month Price</h3>
                                <p class="mt-1 text-2xl font-bold text-green-600">
                                    ${{ number_format($subscriptionPlan->first_month_price, 2) }}</p>
                                <p class="text-sm text-gray-500">Promotional pricing</p>
                            </div>
                        @endif

                        @if($subscriptionPlan->regular_price)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Price After Promo</h3>
                                <p class="mt-1 text-2xl font-bold text-gray-900">
                                    ${{ number_format($subscriptionPlan->regular_price, 2) }}</p>
                                <p class="text-sm text-gray-500">After {{ $subscriptionPlan->promotional_months ?? 'N/A' }}
                                    months</p>
                            </div>
                        @endif

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Currency</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $subscriptionPlan->currency }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Billing Interval</h3>
                            <p class="mt-1 text-sm text-gray-900 capitalize">{{ $subscriptionPlan->interval }}ly</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Free Trial</h3>
                            <p class="mt-1 text-sm text-gray-900">
                                @if($subscriptionPlan->trial_days)
                                    {{ $subscriptionPlan->trial_days }} days
                                @else
                                    No trial
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Stripe Integration -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Integration</h2>

                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Stripe Price ID</h3>
                            <p class="mt-1 text-sm text-gray-900 font-mono">
                                {{ $subscriptionPlan->stripe_price_id ?? 'Not configured' }}
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Stripe Product ID</h3>
                            <p class="mt-1 text-sm text-gray-900 font-mono">
                                {{ $subscriptionPlan->stripe_product_id ?? 'Not configured' }}
                            </p>
                        </div>

                        @if($subscriptionPlan->paypal_plan_id)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">PayPal Plan ID</h3>
                                <p class="mt-1 text-sm text-gray-900 font-mono">{{ $subscriptionPlan->paypal_plan_id }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Plan Features -->
                @if($subscriptionPlan->features && count($subscriptionPlan->features) > 0)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Plan Features</h2>

                        <ul class="space-y-3">
                            @foreach($subscriptionPlan->features as $feature)
                                <li class="flex items-start">
                                    <svg class="h-6 w-6 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-sm text-gray-900">{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Included Packages -->
                @if($subscriptionPlan->included_package_ids && count($subscriptionPlan->included_package_ids) > 0)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Included Packages</h2>

                        <div class="space-y-3">
                            @foreach($subscriptionPlan->includedPackages() as $package)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-indigo-500 mr-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                        <div>
                                            <h3 class="text-sm font-medium text-gray-900">{{ $package->name }}</h3>
                                            <p class="text-xs text-gray-500">{{ $package->courses()->count() }} courses included</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.packages.show', $package) }}"
                                        class="text-sm text-indigo-600 hover:text-indigo-900">
                                        View →
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Included Individual Courses -->
                @if($subscriptionPlan->included_course_ids && count($subscriptionPlan->included_course_ids) > 0)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Included Individual Courses</h2>

                        <div class="space-y-3">
                            @foreach($subscriptionPlan->includedCourses() as $course)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-green-500 mr-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                        <div>
                                            <h3 class="text-sm font-medium text-gray-900">{{ $course->title }}</h3>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.courses.show', $course) }}"
                                        class="text-sm text-indigo-600 hover:text-indigo-900">
                                        View →
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Recent Subscribers -->
                @if($subscriptionPlan->subscriptions && $subscriptionPlan->subscriptions->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-gray-900">Recent Subscribers</h2>
                            <a href="{{ route('admin.subscription-plans.subscribers', $subscriptionPlan) }}"
                                class="text-sm text-indigo-600 hover:text-indigo-900">
                                View All →
                            </a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            User</th>
                                        <th scope="col"
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th scope="col"
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Started</th>
                                        <th scope="col"
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($subscriptionPlan->subscriptions->take(10) as $subscription)
                                        <tr>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div>
                                                        @if($subscription->user)
                                                            <div class="text-sm font-medium text-gray-900">
                                                                {{ $subscription->user->name }}</div>
                                                            <div class="text-sm text-gray-500">{{ $subscription->user->email }}</div>
                                                        @else
                                                            <div class="text-sm font-medium text-gray-400 italic">Deleted User</div>
                                                            <div class="text-sm text-gray-400 italic">User no longer exists</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                                @if($subscription->status === 'active') bg-green-100 text-green-800
                                                                @elseif($subscription->status === 'canceled') bg-red-100 text-red-800
                                                                @else bg-gray-100 text-gray-800
                                                                @endif">
                                                    {{ ucfirst($subscription->status) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                {{ $subscription->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex items-center justify-end space-x-3">
                                                    @if($subscription->user)
                                                        <a href="{{ route('admin.users.show', $subscription->user) }}"
                                                            class="text-indigo-600 hover:text-indigo-900">View User</a>
                                                    @else
                                                        <span class="text-gray-400">N/A</span>
                                                    @endif

                                                    <form
                                                        action="{{ route('admin.subscription-plans.subscriptions.destroy', $subscription->id) }}"
                                                        method="POST" class="inline-block"
                                                        onsubmit="return confirm('Are you sure you want to delete this subscription? This will remove all enrollments and package access for this user.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900 font-medium">
                                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column (1/3 width) -->
            <div class="space-y-6">
                <!-- Status -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Status</h2>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Active</span>
                            <span
                                class="px-3 py-1 text-sm rounded-full {{ $subscriptionPlan->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $subscriptionPlan->is_active ? 'Yes' : 'No' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Created</span>
                            <span class="text-sm text-gray-900">{{ $subscriptionPlan->created_at->format('M d, Y') }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Last Updated</span>
                            <span class="text-sm text-gray-900">{{ $subscriptionPlan->updated_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>

                    <div class="space-y-3">
                        <form action="{{ route('admin.subscription-plans.toggle-status', $subscriptionPlan) }}"
                            method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-gray-700 hover:bg-gray-50">
                                @if($subscriptionPlan->is_active)
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                    Deactivate Plan
                                @else
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Activate Plan
                                @endif
                            </button>
                        </form>

                        <a href="{{ route('admin.subscription-plans.edit', $subscriptionPlan) }}"
                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Plan
                        </a>

                        @if($subscriptionPlan->subscriptions && $subscriptionPlan->subscriptions->count() > 0)
                            <a href="{{ route('admin.subscription-plans.subscribers', $subscriptionPlan) }}"
                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-gray-700 hover:bg-gray-50">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                View All Subscribers
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="bg-white rounded-lg shadow-sm p-6 border-2 border-red-200">
                    <h2 class="text-lg font-semibold text-red-900 mb-2">Danger Zone</h2>
                    <p class="text-sm text-gray-600 mb-4">Once you delete this subscription plan, there is no going back.
                        Please be certain.</p>

                    <form action="{{ route('admin.subscription-plans.destroy', $subscriptionPlan) }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this subscription plan? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete Plan
                        </button>
                    </form>
                </div>

                <!-- Info Card -->
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Subscription Management</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>Subscriptions are managed through Stripe. View detailed billing information in your
                                    Stripe Dashboard.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection