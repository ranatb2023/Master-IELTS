@extends('layouts.admin')

@section('title', 'Reports Dashboard')
@section('page-title', 'Reports & Analytics')

@section('content')
<div class="space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                        <dd class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_users']) }}</dd>
                    </dl>
                </div>
            </div>
            <div class="mt-4 text-sm text-gray-600">
                <span class="text-green-600 font-medium">{{ number_format($stats['new_users_this_month']) }}</span> new this month
            </div>
        </div>

        <!-- Total Courses -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Courses</dt>
                        <dd class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_courses']) }}</dd>
                    </dl>
                </div>
            </div>
            <div class="mt-4 text-sm text-gray-600">
                <span class="text-green-600 font-medium">{{ number_format($stats['published_courses']) }}</span> published
            </div>
        </div>

        <!-- Total Enrollments -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Enrollments</dt>
                        <dd class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_enrollments']) }}</dd>
                    </dl>
                </div>
            </div>
            <div class="mt-4 text-sm text-gray-600">
                <span class="text-green-600 font-medium">{{ number_format($stats['enrollments_this_month']) }}</span> this month
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Revenue</dt>
                        <dd class="text-2xl font-semibold text-gray-900">${{ number_format($stats['total_revenue'], 2) }}</dd>
                    </dl>
                </div>
            </div>
            <div class="mt-4 text-sm text-gray-600">
                <span class="text-green-600 font-medium">${{ number_format($stats['revenue_this_month'], 2) }}</span> this month
            </div>
        </div>
    </div>

    <!-- Secondary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Students</h3>
            <p class="text-3xl font-bold text-indigo-600">{{ number_format($stats['total_students']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Tutors</h3>
            <p class="text-3xl font-bold text-green-600">{{ number_format($stats['total_tutors']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Active Enrollments</h3>
            <p class="text-3xl font-bold text-yellow-600">{{ number_format($stats['active_enrollments']) }}</p>
        </div>
    </div>

    <!-- Charts & Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue Trend (Last 12 Months)</h3>
            <div class="space-y-2">
                @forelse($revenueChart as $item)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">{{ $item->month }}</span>
                    <div class="flex items-center flex-1 mx-4">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $revenueChart->max('revenue') > 0 ? ($item->revenue / $revenueChart->max('revenue') * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    <span class="text-sm font-semibold text-gray-900">${{ number_format($item->revenue, 2) }}</span>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">No revenue data available</p>
                @endforelse
            </div>
        </div>

        <!-- Popular Courses -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Popular Courses</h3>
            <div class="space-y-3">
                @forelse($popularCourses as $course)
                <div class="flex items-center justify-between border-b border-gray-200 pb-2">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">{{ Str::limit($course->title, 40) }}</p>
                        <p class="text-xs text-gray-500">{{ $course->instructor->name ?? 'N/A' }}</p>
                    </div>
                    <span class="ml-2 px-3 py-1 bg-indigo-100 text-indigo-800 text-xs font-semibold rounded-full">
                        {{ $course->enrollments_count }} enrollments
                    </span>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">No courses available</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Enrollments -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Recent Enrollments</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($recentEnrollments as $enrollment)
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $enrollment->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ Str::limit($enrollment->course->title, 40) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500">{{ $enrollment->enrolled_at ? $enrollment->enrolled_at->diffForHumans() : 'N/A' }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $enrollment->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($enrollment->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-4">
                    <p class="text-gray-500 text-center">No recent enrollments</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Recent Orders</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($recentOrders as $order)
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $order->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $order->order_number }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900">${{ number_format($order->total, 2) }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $order->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}
                            ">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-4">
                    <p class="text-gray-500 text-center">No recent orders</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('admin.reports.revenue') }}" class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow">
            <h4 class="font-semibold text-gray-900">Revenue Report</h4>
            <p class="text-sm text-gray-600 mt-1">View detailed revenue analytics</p>
        </a>
        <a href="{{ route('admin.reports.enrollments') }}" class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow">
            <h4 class="font-semibold text-gray-900">Enrollments Report</h4>
            <p class="text-sm text-gray-600 mt-1">Track enrollment trends</p>
        </a>
        <a href="{{ route('admin.reports.course-performance') }}" class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow">
            <h4 class="font-semibold text-gray-900">Course Performance</h4>
            <p class="text-sm text-gray-600 mt-1">Analyze course metrics</p>
        </a>
        <a href="{{ route('admin.reports.student-progress') }}" class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow">
            <h4 class="font-semibold text-gray-900">Student Progress</h4>
            <p class="text-sm text-gray-600 mt-1">Monitor student learning</p>
        </a>
    </div>
</div>
@endsection
