@extends('layouts.student')

@section('title', 'My Packages')

@section('content')
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                My Packages
            </h2>
            <a href="{{ route('student.packages.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                Browse More Packages →
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Active Packages -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-2xl font-bold text-gray-900">Active Packages</h3>

                    <!-- Sort Filter -->
                    @if($activePackages->count() > 0)
                        <form method="GET" action="{{ route('student.packages.my-packages') }}" id="sortForm">
                            <div class="flex items-center gap-2">
                                <label for="sort" class="text-sm font-medium text-gray-700">Sort By:</label>
                                <select name="sort" id="sort"
                                    class="text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    onchange="document.getElementById('sortForm').submit()">
                                    <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>
                                        Newest First</option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First
                                    </option>
                                    <option value="expires_soon" {{ request('sort') == 'expires_soon' ? 'selected' : '' }}>
                                        Expires Soon</option>
                                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Package Name (A-Z)
                                    </option>
                                </select>
                            </div>
                        </form>
                    @endif
                </div>

                @if($activePackages->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($activePackages as $userAccess)
                            @php
                                $package = $userAccess->package;
                            @endphp
                            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                                <div class="p-6">
                                    <!-- Status Badge -->
                                    <div class="flex items-start justify-between mb-3">
                                        @php
                                            $hasRefundedEnrollment = $userAccess->enrollments()
                                                ->where('payment_status', 'refunded')
                                                ->exists();
                                        @endphp
                                        
                                        @if($hasRefundedEnrollment)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                ✗ Refunded
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                ✓ Active
                                            </span>
                                        @endif
                                        
                                        @if(!$userAccess->expires_at)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                Lifetime
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Package Name -->
                                    <h4 class="text-lg font-bold text-gray-900 mb-2">{{ $package->name }}</h4>

                                    <!-- Description -->
                                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $package->description }}</p>

                                    <!-- Purchase Info -->
                                    <div class="space-y-2 mb-4 text-sm">
                                        <div class="flex justify-between text-gray-600">
                                            <span>Purchased:</span>
                                            <span class="font-medium">{{ $userAccess->created_at->format('M d, Y') }}</span>
                                        </div>
                                        @if($userAccess->expires_at)
                                            <div class="flex justify-between text-gray-600">
                                                <span>Expires:</span>
                                                <span class="font-medium">{{ $userAccess->expires_at->format('M d, Y') }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Days Left:</span>
                                                @php
                                                    $daysLeft = now()->diffInDays($userAccess->expires_at, false);
                                                @endphp
                                                @if($daysLeft < 0)
                                                    <span class="font-semibold text-red-600">Expired</span>
                                                @elseif($daysLeft == 0)
                                                    <span class="font-semibold text-orange-600">Expires Today</span>
                                                @elseif($daysLeft <= 7)
                                                    <span class="font-semibold text-orange-600">{{ ceil($daysLeft) }} days</span>
                                                @else
                                                    <span class="font-semibold text-green-600">{{ ceil($daysLeft) }} days</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Courses Count -->
                                    <div class="flex items-center text-sm text-gray-600 mb-4">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                        <strong>{{ $package->courses->count() }}</strong>&nbsp;courses
                                    </div>

                                    @if($hasRefundedEnrollment)
                                        @php
                                            $refundedEnrollment = $userAccess->enrollments()
                                                ->where('payment_status', 'refunded')
                                                ->first();
                                        @endphp
                                        <!-- Refund Information -->
                                        <div class="bg-red-50 border border-red-200 rounded-md p-3 mb-4">
                                            <div class="flex items-start">
                                                <svg class="w-5 h-5 text-red-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                </svg>
                                                <div class="flex-1">
                                                    <h5 class="text-sm font-semibold text-red-900 mb-1">Refund Processed</h5>
                                                    <p class="text-xs text-red-700 mb-2">
                                                        <strong>${{ number_format($refundedEnrollment->refund_amount ?? 0, 2) }}</strong> refunded on 
                                                        {{ $refundedEnrollment->refunded_at?->format('M d, Y') }}
                                                    </p>
                                                    @if($refundedEnrollment->refund_reason)
                                                        <p class="text-xs text-red-600">Reason: {{ $refundedEnrollment->refund_reason }}</p>
                                                    @endif
                                                    <p class="text-xs text-red-500 mt-2">
                                                        ⏱ Funds will appear in 5-10 business days
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Action Buttons -->
                                    <div class="space-y-2">
                                        @if($hasRefundedEnrollment)
                                            <!-- Refunded - Limited Actions -->
                                            <div class="flex gap-2">
                                                <a href="{{ route('student.packages.show', $package) }}"
                                                    class="flex-1 text-center px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition text-sm">
                                                    View Details
                                                </a>
                                            </div>
                                            <div class="text-center px-4 py-2 bg-red-50 text-red-700 rounded-md text-sm border border-red-200">
                                                🔒 Access Revoked - Enrollment Canceled
                                            </div>
                                        @else
                                            <!-- Active - Full Actions -->
                                            <div class="flex gap-2">
                                                <a href="{{ route('student.packages.show', $package) }}"
                                                    class="flex-1 text-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition text-sm">
                                                    View Details
                                                </a>
                                                <a href="{{ route('student.enrollments.index') }}"
                                                    class="flex-1 text-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition text-sm">
                                                    Start Learning
                                                </a>
                                            </div>
                                        @endif
                                        
                                        @if($userAccess->order && $userAccess->order->invoices->first())
                                            <a href="{{ route('student.invoices.download', $userAccess->order->invoices->first()) }}"
                                                class="block text-center px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition text-sm">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                Download Invoice
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow-md p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No active packages</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by purchasing a package.</p>
                        <div class="mt-6">
                            <a href="{{ route('student.packages.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Browse Packages
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Expired Packages -->
            @if($expiredPackages->count() > 0)
                <div class="mt-12">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Expired Packages</h3>

                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Package Name
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Purchased
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Expired
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Amount Paid
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($expiredPackages as $userAccess)
                                        @php
                                            $package = $userAccess->package;
                                        @endphp
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $package->name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">{{ $package->courses->count() }}
                                                            courses</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $userAccess->purchased_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-red-600">
                                                    {{ $userAccess->expires_at->format('M d, Y') }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                ${{ number_format($userAccess->amount_paid ?? $package->price, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <a href="{{ route('student.packages.show', $package) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 font-medium">
                                                    Renew
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
