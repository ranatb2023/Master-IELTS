@extends('layouts.tutor')

@section('title', 'Analytics Dashboard')
@section('page-title', 'Analytics Overview')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">Analytics Dashboard</h2>
            <p class="mt-1 text-sm text-gray-500">Comprehensive overview of your teaching performance</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="bg-white shadow rounded-lg p-4">
                <p class="text-xs text-gray-500 uppercase">Total Courses</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_courses'] }}</p>
            </div>
            <div class="bg-white shadow rounded-lg p-4">
                <p class="text-xs text-gray-500 uppercase">Published</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['published_courses'] }}</p>
            </div>
            <div class="bg-white shadow rounded-lg p-4">
                <p class="text-xs text-gray-500 uppercase">Total Students</p>
                <p class="text-2xl font-bold text-indigo-600">{{ $stats['total_students'] }}</p>
            </div>
            <div class="bg-white shadow rounded-lg p-4">
                <p class="text-xs text-gray-500 uppercase">Enrollments</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['total_enrollments'] }}</p>
            </div>
            <div class="bg-white shadow rounded-lg p-4">
                <p class="text-xs text-gray-500 uppercase">Active Students</p>
                <p class="text-2xl font-bold text-purple-600">{{ $stats['active_students'] }}</p>
            </div>
            <div class="bg-white shadow rounded-lg p-4">
                <p class="text-xs text-gray-500 uppercase">Total Revenue</p>
                <p class="text-2xl font-bold text-green-600">${{ number_format($stats['total_revenue'], 2) }}</p>
            </div>
        </div>

        <!-- Recent Enrollments Chart -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Enrollment Trends (Last 30 Days)</h3>
            <div class="h-64">
                <!-- Simple bar chart representation -->
                <div class="flex items-end justify-between h-full space-x-2">
                    @foreach($recentEnrollments as $enrollment)
                        <div class="flex-1 bg-indigo-200 hover:bg-indigo-300 rounded-t transition-colors"
                            style="height: {{ $enrollment->count * 10 }}%"
                            title="{{ $enrollment->date }}: {{ $enrollment->count }} enrollments">
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="mt-4 text-sm text-gray-500">
                Total enrollments in the last 30 days: {{ $recentEnrollments->sum('count') }}
            </div>
        </div>

        <!-- Top Performing Courses -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Performing Courses</h3>
            <div class="space-y-4">
                @foreach($topCourses as $course)
                    <div class="flex items-center justify-between border-b pb-3">
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">{{ $course->title }}</h4>
                            <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                                <span>{{ $course->enrollments_count }} students</span>
                                <span>•</span>
                                <span>{{ number_format($course->avg_rating, 1) }} ⭐</span>
                                <span>•</span>
                                <span>${{ number_format($course->revenue, 2) }} revenue</span>
                            </div>
                        </div>
                        <a href="{{ route('tutor.courses.analytics', $course) }}" class="text-indigo-600 hover:text-indigo-900">
                            View Details →
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Quick Links -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('tutor.analytics.course-performance') }}"
                class="bg-white shadow rounded-lg p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-900">Course Performance</h3>
                        <p class="text-xs text-gray-500">View detailed course metrics</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('tutor.analytics.student-engagement') }}"
                class="bg-white shadow rounded-lg p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-900">Student Engagement</h3>
                        <p class="text-xs text-gray-500">Track student activity</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('tutor.analytics.revenue') }}"
                class="bg-white shadow rounded-lg p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-900">Revenue</h3>
                        <p class="text-xs text-gray-500">View earnings and payouts</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
@endsection