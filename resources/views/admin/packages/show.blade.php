@extends('layouts.admin')

@section('title', $package->name)
@section('page-title', $package->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">{{ $package->name }}</h1>
                @if($package->is_featured)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    Featured
                </span>
                @endif
                @if($package->status === 'published')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Published
                </span>
                @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    {{ ucfirst($package->status) }}
                </span>
                @endif
            </div>
            <p class="mt-1 text-sm text-gray-600">Created {{ $package->created_at->diffForHumans() }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.packages.edit', $package) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Package
            </a>
            <a href="{{ route('admin.packages.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                Back to Packages
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500">Total Courses</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_courses'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500">Active Users</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['active_users'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500">Price</p>
                    <p class="text-2xl font-semibold text-gray-900">${{ number_format($package->effective_price, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500">Access</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $package->is_lifetime ? 'Lifetime' : $package->duration_days . ' days' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Package Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Information -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Package Information</h2>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $package->description ?? 'No description provided' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Slug</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $package->slug }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Auto-Enroll</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $package->auto_enroll_courses ? 'Yes' : 'No' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Features -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Features</h2>
            @php
                $allFeatureKeys = array_merge(
                    $package->display_features ?? [],
                    $package->functional_features ?? []
                );
                $packageFeatures = \App\Models\PackageFeature::whereIn('feature_key', $allFeatureKeys)
                    ->orderBy('type')
                    ->orderBy('feature_name')
                    ->get();
            @endphp
            @if($packageFeatures->count() > 0)
            <div class="space-y-4">
                @if($packageFeatures->where('type', 'display')->count() > 0)
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Display Features</h3>
                    <ul class="space-y-2">
                        @foreach($packageFeatures->where('type', 'display') as $feature)
                        <li class="flex items-start text-sm text-gray-700">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <div class="flex-1">
                                <span class="font-medium">{{ $feature->feature_name }}</span>
                                @if($feature->description)
                                <p class="text-xs text-gray-500 mt-0.5">{{ $feature->description }}</p>
                                @endif
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if($packageFeatures->where('type', 'functional')->count() > 0)
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Functional Features</h3>
                    <ul class="space-y-2">
                        @foreach($packageFeatures->where('type', 'functional') as $feature)
                        <li class="flex items-start text-sm text-gray-700">
                            <svg class="w-5 h-5 text-indigo-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="flex-1">
                                <span class="font-medium">{{ $feature->feature_name }}</span>
                                <code class="text-xs text-gray-600 bg-gray-100 px-1 py-0.5 rounded ml-2">{{ $feature->feature_key }}</code>
                                @if($feature->description)
                                <p class="text-xs text-gray-500 mt-0.5">{{ $feature->description }}</p>
                                @endif
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
            @else
            <p class="text-sm text-gray-500">No features defined</p>
            @endif
        </div>
    </div>

    <!-- Included Courses -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Included Courses ({{ $package->courses->count() }})</h2>
        @if($package->courses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($package->courses as $course)
            <div class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-indigo-300 transition-colors">
                @if($course->thumbnail)
                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-16 h-16 object-cover rounded">
                @else
                <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                @endif
                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-medium text-gray-900">{{ $course->title }}</h3>
                    <p class="text-sm text-gray-500">{{ ucfirst($course->level) }} • {{ $course->duration_hours }}h</p>
                </div>
                <a href="{{ route('admin.courses.show', $course) }}" class="ml-4 text-indigo-600 hover:text-indigo-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-sm text-gray-500">No courses added to this package yet</p>
        @endif
    </div>
</div>
@endsection
