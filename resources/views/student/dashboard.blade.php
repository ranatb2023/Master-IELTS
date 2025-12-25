@extends('layouts.student')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-lg p-8 text-white">
            <h1 class="text-3xl font-bold">Welcome back, {{ auth()->user()->name }}!</h1>
            <p class="mt-2 text-indigo-100">Continue your learning journey</p>
        </div>

        {{-- Check user subscription/package status --}}
        @php
            $currentPlan = auth()->user()->getCurrentPlan();
            $lockedFeatures = auth()->user()->getLockedFeatures();
            
            // Check if user has ANY subscriptions, packages, or course purchases
            $hasActiveSubscription = auth()->user()->subscriptions()->active()->exists();
            $hasActivePackage = \App\Models\UserPackageAccess::where('user_id', auth()->id())
                ->where('is_active', true)
                ->exists();
            $hasCoursePurchase = \App\Models\Enrollment::where('user_id', auth()->id())
                ->whereNotNull('order_id')
                ->where('status', 'active')
                ->exists();
            $hasAnyAccess = $hasActiveSubscription || $hasActivePackage || $hasCoursePurchase;
        @endphp

        {{-- New Student Modal (no subscriptions or packages) --}}
        @if(!$hasAnyAccess && $lockedFeatures->count() > 0)
            <div x-data="{ showModal: true }" x-show="showModal" class="fixed inset-0 z-50"  x-cloak>
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"></div>

                {{-- Modal --}}
                <div class="fixed inset-0 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4">
                        <div @click.outside="showModal = false" class="relative transform overflow-hidden rounded-lg bg-white shadow-xl transition-all sm:w-full sm:max-w-2xl">
                            {{-- Header --}}
                            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-8 text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white/20 mb-4">
                                    <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </div>
                                <h3 class="text-3xl font-bold text-white">Welcome to MasterIELTS!</h3>
                                <p class="mt-2 text-lg text-indigo-100">Start your learning journey today</p>
                            </div>

                            {{-- Body --}}
                            <div class="px-6 py-8">
                                <p class="text-gray-700 text-center mb-6">
                                    You currently don't have any active subscription or package. To unlock all features and start learning,
                                    choose one of our plans:
                                </p>

                                {{-- Features Preview --}}
                                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                                    <h4 class="font-semibold text-gray-900 mb-3">🎯 What You'll Get:</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        @foreach($lockedFeatures->take(6) as $feature)
                                            <div class="flex items-center text-sm text-gray-700">
                                                <svg class="w-4 h-4 mr-2 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                {{ $feature->feature_name }}
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($lockedFeatures->count() > 6)
                                        <p class="text-xs text-gray-500 mt-2 text-center">+ {{ $lockedFeatures->count() - 6 }} more features</p>
                                    @endif
                                </div>

                                {{-- Action Buttons --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <a href="{{ route('student.packages.index') }}" class="flex flex-col items-center justify-center bg-gradient-to-br from-indigo-500 to-indigo-600 text-white rounded-lg p-6 hover:from-indigo-600 hover:to-indigo-700 transition shadow-lg group">
                                        <svg class="w-12 h-12 mb-3 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>
                                        <h4 class="font-bold text-lg mb-1">Browse Packages</h4>
                                        <p class="text-sm text-indigo-100 text-center">One-time purchase, lifetime access</p>
                                        <span class="mt-3 text-xs bg-white/20 px-3 py-1 rounded-full">Starting from $79</span>
                                    </a>

                                    <a href="{{ route('student.subscriptions.index') }}" class="flex flex-col items-center justify-center bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-lg p-6 hover:from-purple-600 hover:to-purple-700 transition shadow-lg group">
                                        <svg class="w-12 h-12 mb-3 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                                        </svg>
                                        <h4 class="font-bold text-lg mb-1">View Subscriptions</h4>
                                        <p class="text-sm text-purple-100 text-center">Flexible monthly plans</p>
                                        <span class="mt-3 text-xs bg-white/20 px-3 py-1 rounded-full">From $29/month</span>
                                    </a>
                                </div>

                                {{-- Close Button --}}
                                <div class="mt-6 text-center">
                                    <button @click="showModal = false" class="text-sm text-gray-500 hover:text-gray-700 underline">
                                        I'll decide later
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Upgrade Banner (for users with basic package/subscription) --}}
        @if($hasAnyAccess && $lockedFeatures->count() > 0)
            <div class="bg-gradient-to-r from-yellow-400 to-orange-500 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex-1">
                        <div class="flex items-center mb-2">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <h3 class="text-lg font-bold">Unlock More Features!</h3>
                        </div>
                        <p class="text-sm text-yellow-50">
                            You're missing out on <strong>{{ $lockedFeatures->count() }} premium features</strong> including 
                            @foreach($lockedFeatures->take(3) as $feature)
                                <strong>{{ $feature->feature_name }}</strong>{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                            @if($lockedFeatures->count() > 3)
                                <span class="font-semibold">and {{ $lockedFeatures->count() - 3 }} more</span>
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('student.subscriptions.index') }}" class="inline-flex items-center px-6 py-3 border-2 border-white text-base font-semibold rounded-lg text-yellow-600 bg-white hover:bg-yellow-50 transition shadow-lg">
                        Upgrade Now
                        <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @can('course.view')
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Enrolled Courses</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['enrolled_courses'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Completed</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['completed_courses'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan

            @if(auth()->user()->canAccessFeature('certificate_download'))
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Certificates</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['certificates_earned'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <x-locked-feature-card title="Certificates Locked" description="Upgrade to download certificates">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Certificates</p>
                                <p class="text-2xl font-semibold text-gray-900">🔒</p>
                            </div>
                        </div>
                    </div>
                </x-locked-feature-card>
            @endif

            @can('course.view')
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Avg. Progress</p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{ number_format($stats['average_progress'], 0) }}%</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan
        </div>

        <!-- Continue Learning -->
        @can('course.view')
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-gray-900">Continue Learning</h2>
                        <a href="{{ route('student.enrollments.index') }}"
                            class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">View All →</a>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($activeEnrollments as $enrollment)
                            <div class="border rounded-lg overflow-hidden hover:shadow-lg transition">
                                @if($enrollment->course->thumbnail)
                                    <img src="{{ asset('storage/' . $enrollment->course->thumbnail) }}"
                                        alt="{{ $enrollment->course->title }}" class="w-full h-40 object-cover">
                                @else
                                    <div
                                        class="w-full h-40 bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-2xl font-bold">
                                        {{ substr($enrollment->course->title, 0, 2) }}
                                    </div>
                                @endif
                                <div class="p-4">
                                    <h3 class="font-semibold text-lg mb-2 line-clamp-2">{{ $enrollment->course->title }}</h3>
                                    <div class="mb-3">
                                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                                            <span>Progress</span>
                                            <span>{{ number_format($enrollment->progress_percentage, 0) }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-indigo-600 h-2 rounded-full"
                                                style="width: {{ $enrollment->progress_percentage }}%"></div>
                                        </div>
                                    </div>
                                    <a href="{{ route('student.courses.learn', $enrollment->course) }}"
                                        class="block w-full text-center bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700 transition">
                                        Continue Learning
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-3 text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No courses yet</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by enrolling in a course.</p>
                                @can('course.enroll')
                                    <div class="mt-6">
                                        <a href="{{ route('student.courses.index') }}"
                                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                            Browse Courses
                                        </a>
                                    </div>
                                @endcan
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endcan

        <!-- Recent Activity & Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Activity -->
            @if(auth()->user()->canAccessFeature('quiz_access') || auth()->user()->canAccessFeature('assignment_submission'))
                <div class="lg:col-span-2 bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Recent Activity</h2>
                    </div>
                    <div class="p-6">
                        <div class="flow-root">
                            <ul class="-mb-8">
                                @php
                                    $activities = collect();

                                    if (auth()->user()->canAccessFeature('quiz_access')) {
                                        foreach ($recentQuizAttempts as $attempt) {
                                            $activities->push((object) [
                                                'type' => 'quiz',
                                                'title' => $attempt->quiz->title ?? 'Quiz',
                                                'score' => $attempt->percentage,
                                                'date' => $attempt->completed_at,
                                            ]);
                                        }
                                    }

                                    if (auth()->user()->canAccessFeature('assignment_submission')) {
                                        foreach ($recentAssignments as $submission) {
                                            $activities->push((object) [
                                                'type' => 'assignment',
                                                'title' => $submission->assignment->title ?? 'Assignment',
                                                'date' => $submission->submitted_at,
                                            ]);
                                        }
                                    }

                                    $activities = $activities->sortByDesc('date')->take(5);
                                @endphp

                                @forelse($activities as $activity)
                                    <li>
                                        <div class="relative pb-8">
                                            @if(!$loop->last)
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
                                                    aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    @if($activity->type === 'quiz')
                                                        <span
                                                            class="h-8 w-8 rounded-full bg-purple-500 flex items-center justify-center ring-8 ring-white">
                                                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        </span>
                                                    @else
                                                        <span
                                                            class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                    <div>
                                                        <p class="text-sm text-gray-900">
                                                            {{ $activity->type === 'quiz' ? 'Completed quiz:' : 'Submitted assignment:' }}
                                                            <span class="font-medium">{{ $activity->title }}</span>
                                                        </p>
                                                        @if($activity->type === 'quiz')
                                                            <p class="text-sm text-gray-500">Score:
                                                                {{ number_format($activity->score, 0) }}%</p>
                                                        @endif
                                                    </div>
                                                    <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                        {{ $activity->date ? $activity->date->diffForHumans() : 'Recently' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="text-center py-8 text-gray-500">
                                        No recent activity
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="space-y-6">
                @can('course.enroll')
                    <a href="{{ route('student.courses.index') }}"
                        class="block bg-white overflow-hidden shadow rounded-lg p-6 hover:shadow-md transition">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="font-semibold text-gray-900">Browse Courses</h3>
                                <p class="text-sm text-gray-500">Find new courses to learn</p>
                            </div>
                        </div>
                    </a>
                @endcan

                @can('course.view')
                    <a href="{{ route('student.enrollments.index') }}"
                        class="block bg-white overflow-hidden shadow rounded-lg p-6 hover:shadow-md transition">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="font-semibold text-gray-900">My Enrollments</h3>
                                <p class="text-sm text-gray-500">View all your courses</p>
                            </div>
                        </div>
                    </a>
                @endcan

                @if(auth()->user()->canAccessFeature('certificate_download'))
                    <a href="{{ route('student.certificates.index') }}"
                        class="block bg-white overflow-hidden shadow rounded-lg p-6 hover:shadow-md transition">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="font-semibold text-gray-900">My Certificates</h3>
                                <p class="text-sm text-gray-500">View earned certificates</p>
                            </div>
                        </div>
                    </a>
                @else
                    <div
                        class="block bg-gray-50 overflow-hidden shadow rounded-lg p-6 relative border-2 border-dashed border-gray-300">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center opacity-50">
                                <div class="p-3 rounded-full bg-gray-200 text-gray-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="font-semibold text-gray-700">My Certificates</h3>
                                    <p class="text-sm text-gray-500">Locked</p>
                                </div>
                            </div>
                            <a href="{{ route('student.subscriptions.index') }}"
                                class="text-xs bg-indigo-600 text-white px-3 py-1 rounded-md hover:bg-indigo-700">
                                Upgrade
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
