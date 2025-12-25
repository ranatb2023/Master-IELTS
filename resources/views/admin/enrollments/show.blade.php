@extends('layouts.admin')

@section('title', 'Enrollment Details')
@section('page-title', 'Enrollment Details')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Enrollment Details</h2>
                <p class="mt-1 text-sm text-gray-600">Enrollment #{{ $enrollment->id }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.enrollments.edit', $enrollment) }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                    Edit
                </a>
                <a href="{{ route('admin.enrollments.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back
                </a>
            </div>
        </div>

        <!-- Status Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Status</p>
                        <p class="text-lg font-semibold text-gray-900 capitalize">{{ $enrollment->status }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Payment</p>
                        <p class="text-lg font-semibold text-gray-900 capitalize">{{ $enrollment->payment_status }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Progress</p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ number_format($enrollment->progress_percentage, 1) }}%</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Duration</p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $enrollment->enrolled_at->diffForHumans(now(), true) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Student & Course Information -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Enrollment Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Student Details</h4>
                            <div class="space-y-2">
                                <p class="text-sm"><span class="font-medium">Name:</span> {{ $enrollment->user->name }}</p>
                                <p class="text-sm"><span class="font-medium">Email:</span> {{ $enrollment->user->email }}
                                </p>
                                <a href="{{ route('admin.users.show', $enrollment->user) }}"
                                    class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                                    View Profile
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                        </path>
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Course Details</h4>
                            <div class="space-y-2">
                                <p class="text-sm"><span class="font-medium">Title:</span> {{ $enrollment->course->title }}
                                </p>
                                <p class="text-sm"><span class="font-medium">Instructor:</span>
                                    {{ $enrollment->course->instructor->name ?? 'N/A' }}</p>
                                <a href="{{ route('admin.courses.show', $enrollment->course) }}"
                                    class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                                    View Course
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                        </path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Learning Progress -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Learning Progress</h3>

                    <!-- Progress Bar -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Overall Progress</span>
                            <span
                                class="text-sm font-medium text-gray-700">{{ number_format($enrollment->progress_percentage, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-indigo-600 h-3 rounded-full transition-all duration-300"
                                style="width: {{ $enrollment->progress_percentage }}%"></div>
                        </div>
                    </div>

                    <!-- Activity Stats -->
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['completed_lessons'] ?? 0 }}</p>
                            <p class="text-sm text-gray-600">Lessons Completed</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['quiz_attempts'] ?? 0 }}</p>
                            <p class="text-sm text-gray-600">Quiz Attempts</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['assignments_submitted'] ?? 0 }}</p>
                            <p class="text-sm text-gray-600">Assignments</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                @if($enrollment->payment_status !== 'free')
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Information</h3>

                        @if($enrollment->packageAccess)
                            <!-- Package-based Enrollment -->
                            <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-md">
                                <div class="flex items-center text-blue-800 mb-2">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    <span class="font-medium">This enrollment is part of a package purchase</span>
                                </div>
                                <p class="text-sm text-blue-700">
                                    Package: <a href="{{ route('admin.packages.show', $enrollment->packageAccess->package) }}"
                                        class="font-medium underline hover:text-blue-900">{{ $enrollment->packageAccess->package->name }}</a>
                                </p>
                            </div>

                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($enrollment->packageAccess->order)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Total Package Amount</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            ${{ number_format($enrollment->packageAccess->order->total, 2) }}</dd>
                                    </div>
                                @endif
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Pro-rated Course Amount</dt>
                                    <dd class="mt-1 text-sm text-gray-900">${{ number_format($enrollment->amount_paid, 2) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Payment Status</dt>
                                    <dd class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($enrollment->payment_status === 'completed') bg-green-100 text-green-800
                                            @elseif($enrollment->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($enrollment->payment_status === 'failed') bg-red-100 text-red-800
                                            @elseif($enrollment->payment_status === 'refunded') bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($enrollment->payment_status) }}
                                        </span>
                                    </dd>
                                </div>
                                @if($enrollment->packageAccess->order)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                                        <dd class="mt-1 text-sm text-gray-900 capitalize">
                                            {{ $enrollment->packageAccess->order->payment_method ?? 'N/A' }}</dd>
                                    </div>
                                    <div class="md:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500">Order Reference</dt>
                                        <dd class="mt-1 text-sm text-gray-900 font-medium">
                                            Order #{{ $enrollment->packageAccess->order->id }}
                                        </dd>
                                    </div>
                                @endif
                                @if($enrollment->refunded_at)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Refund Amount</dt>
                                        <dd class="mt-1 text-sm text-gray-900">${{ number_format($enrollment->refund_amount, 2) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Refunded On</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $enrollment->refunded_at->format('M d, Y H:i') }}</dd>
                                    </div>
                                    @if($enrollment->refund_reason)
                                        <div class="md:col-span-2">
                                            <dt class="text-sm font-medium text-gray-500">Refund Reason</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $enrollment->refund_reason }}</dd>
                                        </div>
                                    @endif
                                @endif
                            </dl>
                        @else
                            <!-- Direct Course Enrollment -->
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Amount Paid</dt>
                                    <dd class="mt-1 text-sm text-gray-900">${{ number_format($enrollment->amount_paid, 2) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Payment Status</dt>
                                    <dd class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($enrollment->payment_status === 'completed') bg-green-100 text-green-800
                                            @elseif($enrollment->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($enrollment->payment_status === 'failed') bg-red-100 text-red-800
                                            @elseif($enrollment->payment_status === 'refunded') bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($enrollment->payment_status) }}
                                        </span>
                                    </dd>
                                </div>
                                @if($enrollment->refunded_at)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Refund Amount</dt>
                                        <dd class="mt-1 text-sm text-gray-900">${{ number_format($enrollment->refund_amount, 2) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Refunded On</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $enrollment->refunded_at->format('M d, Y H:i') }}</dd>
                                    </div>
                                    @if($enrollment->refund_reason)
                                        <div class="md:col-span-2">
                                            <dt class="text-sm font-medium text-gray-500">Refund Reason</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $enrollment->refund_reason }}</dd>
                                        </div>
                                    @endif
                                @endif
                            </dl>
                        @endif
                    </div>
                @endif

                <!-- Notes -->
                @if($enrollment->notes)
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Notes</h3>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $enrollment->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-2">
                        <a href="{{ route('admin.enrollments.edit', $enrollment) }}"
                            class="block w-full text-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Edit Enrollment
                        </a>

                        @if($enrollment->status === 'active' && !$enrollment->isExpired())
                            <button type="button" onclick="document.getElementById('extend-modal').classList.remove('hidden')"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Extend Access
                            </button>
                        @endif

                        <form action="{{ route('admin.enrollments.reset-progress', $enrollment) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to reset this enrollment progress?')">
                            @csrf
                            <button type="submit"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Reset Progress
                            </button>
                        </form>

                        @if($enrollment->payment_status === 'completed' && !$enrollment->refunded_at)
                            <button type="button" onclick="document.getElementById('refund-modal').classList.remove('hidden')"
                                class="w-full px-4 py-2 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                                Process Refund
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Dates -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Important Dates</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Enrolled On</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $enrollment->enrolled_at->format('M d, Y H:i') }}</dd>
                        </div>
                        @if($enrollment->expires_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Expires On</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $enrollment->expires_at->format('M d, Y H:i') }}
                                    @if($enrollment->isExpired())
                                        <span
                                            class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Expired</span>
                                    @endif
                                </dd>
                            </div>
                        @else
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Access Duration</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Lifetime</span>
                                </dd>
                            </div>
                        @endif
                        @if($enrollment->last_accessed_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Last Accessed</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $enrollment->last_accessed_at->diffForHumans() }}</dd>
                            </div>
                        @endif
                        @if($enrollment->completed_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Completed On</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $enrollment->completed_at->format('M d, Y H:i') }}
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <!-- Additional Info -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Info</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Enrollment Source</dt>
                            <dd class="mt-1 text-sm text-gray-900 capitalize">{{ $enrollment->enrollment_source }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Certificate</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($enrollment->certificate_issued)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Issued</span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">Not
                                        Issued</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Extend Access Modal -->
    <div id="extend-modal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <form action="{{ route('admin.enrollments.extend', $enrollment) }}" method="POST">
                @csrf
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Extend Access Period</h3>

                    <div class="space-y-4">
                        <div>
                            <label for="extend_days" class="block text-sm font-medium text-gray-700">Number of Days
                                *</label>
                            <input type="number" min="1" max="365" id="extend_days" name="extend_days" value="30" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-sm text-gray-500">Enter the number of days to extend (1-365 days)</p>
                        </div>

                        @if($enrollment->expires_at)
                            <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                                <p class="text-sm text-blue-800">
                                    <strong>Current Expiry:</strong> {{ $enrollment->expires_at->format('M d, Y') }}<br>
                                    <strong>New Expiry:</strong> <span
                                        id="new-expiry-date">{{ $enrollment->expires_at->addDays(30)->format('M d, Y') }}</span>
                                </p>
                            </div>
                        @else
                            <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                                <p class="text-sm text-blue-800">
                                    <strong>Current Expiry:</strong> Never<br>
                                    <strong>New Expiry:</strong> <span
                                        id="new-expiry-date">{{ now()->addDays(30)->format('M d, Y') }}</span>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('extend-modal').classList.add('hidden')"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        Extend Access
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Refund Modal -->
    <div id="refund-modal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <form action="{{ route('admin.enrollments.refund', $enrollment) }}" method="POST">
                @csrf
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Process Refund</h3>

                    <div class="space-y-4">
                        <div>
                            <label for="refund_amount" class="block text-sm font-medium text-gray-700">Refund Amount
                                *</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" step="0.01" min="0" max="{{ $enrollment->amount_paid }}"
                                    id="refund_amount" name="refund_amount" value="{{ $enrollment->amount_paid }}" required
                                    class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Maximum: ${{ number_format($enrollment->amount_paid, 2) }}
                            </p>
                        </div>

                        <div>
                            <label for="refund_reason" class="block text-sm font-medium text-gray-700">Reason *</label>
                            <textarea id="refund_reason" name="refund_reason" rows="3" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Enter refund reason..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('refund-modal').classList.add('hidden')"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                        Process Refund
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Update new expiry date preview when days input changes
        document.getElementById('extend_days')?.addEventListener('input', function (e) {
            const days = parseInt(e.target.value) || 0;
            @if($enrollment->expires_at)
                const currentExpiry = new Date('{{ $enrollment->expires_at->format('Y-m-d') }}');
            @else
                const currentExpiry = new Date();
            @endif
            const newExpiry = new Date(currentExpiry);
            newExpiry.setDate(newExpiry.getDate() + days);

            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            document.getElementById('new-expiry-date').textContent = newExpiry.toLocaleDateString('en-US', options);
        });
    </script>
@endsection