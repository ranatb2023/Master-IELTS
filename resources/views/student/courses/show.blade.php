@extends('layouts.student')

@section('title', $course->title)
@section('page-title', $course->title)

@section('content')
<div>
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="lg:grid lg:grid-cols-12 lg:gap-8">
                <div class="lg:col-span-8">
                    <!-- Breadcrumb -->
                    <nav class="flex mb-6" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-2 text-sm">
                            <li><a href="{{ route('student.courses.index') }}" class="text-indigo-200 hover:text-white">Courses</a></li>
                            <li class="text-indigo-300">/</li>
                            @if($course->category)
                            <li><a href="{{ route('student.courses.index', ['category' => $course->category_id]) }}" class="text-indigo-200 hover:text-white">{{ $course->category->name }}</a></li>
                            <li class="text-indigo-300">/</li>
                            @endif
                            <li class="text-white">{{ Str::limit($course->title, 40) }}</li>
                        </ol>
                    </nav>

                    <!-- Course Title & Info -->
                    <h1 class="text-3xl font-extrabold text-white sm:text-4xl">{{ $course->title }}</h1>
                    @if($course->subtitle)
                    <p class="mt-3 text-xl text-indigo-100">{{ $course->subtitle }}</p>
                    @endif

                    <p class="mt-4 text-lg text-indigo-100">{{ $course->short_description }}</p>

                    <!-- Rating & Stats -->
                    <div class="mt-6 flex flex-wrap items-center gap-4 text-sm text-white">
                        <div class="flex items-center">
                            <span class="font-semibold mr-1">{{ number_format($course->average_rating, 1) }}</span>
                            <div class="flex">
                                @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= $course->average_rating ? 'text-yellow-400' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                @endfor
                            </div>
                            <span class="ml-1">({{ $course->reviews_count }} {{ Str::plural('review', $course->reviews_count) }})</span>
                        </div>
                        <span>|</span>
                        <span>{{ $course->enrollments_count }} {{ Str::plural('student', $course->enrollments_count) }} enrolled</span>
                        @if($course->duration_hours)
                        <span>|</span>
                        <span>{{ $course->duration_hours }} hours</span>
                        @endif
                        <span>|</span>
                        <span>{{ ucfirst($course->level) }}</span>
                    </div>

                    <!-- Instructor Info -->
                    <div class="mt-6 flex items-center">
                        <img src="{{ $course->instructor->avatar ? asset('storage/' . $course->instructor->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($course->instructor->name) }}" alt="{{ $course->instructor->name }}" class="w-12 h-12 rounded-full border-2 border-white">
                        <div class="ml-3">
                            <p class="text-sm text-indigo-100">Instructor</p>
                            <p class="text-white font-medium">{{ $course->instructor->name }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="lg:grid lg:grid-cols-12 lg:gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-8 space-y-8">
                <!-- Preview Video -->
                @if($course->preview_video)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    @php
                        // Extract YouTube video ID from URL
                        $videoId = null;
                        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $course->preview_video, $matches)) {
                            $videoId = $matches[1];
                        }
                    @endphp
                    @if($videoId)
                        <div class="relative w-full" style="padding-bottom: 56.25%;">
                            <iframe
                                class="absolute top-0 left-0 w-full h-full"
                                src="https://www.youtube.com/embed/{{ $videoId }}"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                            </iframe>
                        </div>
                    @else
                        <div class="aspect-w-16 aspect-h-9">
                            <iframe src="{{ $course->preview_video }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-96"></iframe>
                        </div>
                    @endif
                </div>
                @elseif($course->thumbnail)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-96 object-cover">
                </div>
                @endif

                <!-- What You'll Learn -->
                @if($course->learning_outcomes)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">What you'll learn</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach(json_decode($course->learning_outcomes) as $outcome)
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-sm text-gray-700">{{ $outcome }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Course Content -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Course Content</h2>
                    <div class="mb-6 text-sm text-gray-600">
                        {{ $course->topics->count() }} {{ Str::plural('section', $course->topics->count()) }} •
                        {{ $course->topics->sum(function($topic) { return $topic->lessons->count() + $topic->quizzes->count() + $topic->assignments->count(); }) }} lectures •
                        {{ $course->duration_hours ?? 0 }}h total length
                    </div>

                    <div class="space-y-3">
                        @foreach($course->topics->where('is_published', true)->sortBy('order') as $index => $topic)
                        <div class="border border-gray-200 rounded-xl overflow-hidden hover:shadow-md transition-shadow" x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
                            <!-- Topic Header -->
                            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 flex items-center justify-between cursor-pointer hover:from-indigo-50 hover:to-purple-50 transition-colors" @click="open = !open">
                                <div class="flex items-center flex-1">
                                    <div class="p-2 bg-white rounded-lg mr-4 shadow-sm">
                                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900 text-lg">{{ $topic->title }}</h3>
                                        @if($topic->description)
                                        <p class="text-sm text-gray-600 mt-1">{!! Str::limit(strip_tags($topic->description), 80) !!}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center gap-3 text-sm">
                                        @if($topic->lessons->count() > 0)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-medium">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            {{ $topic->lessons->count() }}
                                        </span>
                                        @endif
                                        @if($topic->quizzes->count() > 0)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $topic->quizzes->count() }}
                                        </span>
                                        @endif
                                        @if($topic->assignments->count() > 0)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-purple-100 text-purple-700 font-medium">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            {{ $topic->assignments->count() }}
                                        </span>
                                        @endif
                                    </div>
                                    <svg class="w-5 h-5 text-gray-500 transition-transform flex-shrink-0" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Topic Content -->
                            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white">
                                <div class="px-6 py-2 space-y-1">
                                    @foreach($topic->lessons->where('is_published', true)->sortBy('order') as $lesson)
                                    <!-- Lesson Item -->
                                    <div class="group flex items-center justify-between p-4 rounded-lg hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 transition-all">
                                        <div class="flex items-center flex-1">
                                            <div class="p-2 bg-gray-100 rounded-lg mr-4 group-hover:bg-indigo-100 transition-colors">
                                                @if($lesson->content_type === 'video')
                                                <svg class="h-5 w-5 text-gray-500 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                @elseif($lesson->content_type === 'text')
                                                <svg class="h-5 w-5 text-gray-500 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                @elseif($lesson->content_type === 'audio')
                                                <svg class="h-5 w-5 text-gray-500 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                                                </svg>
                                                @else
                                                <svg class="h-5 w-5 text-gray-500 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                </svg>
                                                @endif
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="text-sm font-medium text-gray-900 group-hover:text-indigo-600 transition-colors">
                                                    {{ $lesson->title }}
                                                </h4>
                                                @if($lesson->is_preview)
                                                <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Free Preview
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3 text-sm text-gray-500">
                                            @if($lesson->duration_minutes)
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $lesson->duration_minutes }}m
                                            </span>
                                            @endif
                                            @if(!$lesson->is_preview)
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach

                                    @foreach($topic->quizzes->where('is_published', true)->sortBy('order') as $quiz)
                                    <!-- Quiz Item -->
                                    <div class="group flex items-center justify-between p-4 rounded-lg hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 transition-all">
                                        <div class="flex items-center flex-1">
                                            <div class="p-2 bg-green-100 rounded-lg mr-4">
                                                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="text-sm font-medium text-gray-900 group-hover:text-green-600 transition-colors">
                                                    {{ $quiz->title }}
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3 text-sm text-gray-500">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $quiz->questions->count() }} questions
                                            </span>
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    @endforeach

                                    @foreach($topic->assignments->where('is_published', true)->sortBy('order') as $assignment)
                                    <!-- Assignment Item -->
                                    <div class="group flex items-center justify-between p-4 rounded-lg hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 transition-all">
                                        <div class="flex items-center flex-1">
                                            <div class="p-2 bg-purple-100 rounded-lg mr-4">
                                                <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="text-sm font-medium text-gray-900 group-hover:text-purple-600 transition-colors">
                                                    {{ $assignment->title }}
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3 text-sm text-gray-500">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                                </svg>
                                                {{ $assignment->max_points }} points
                                            </span>
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Requirements -->
                @if($course->requirements)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Requirements</h2>
                    <ul class="space-y-2">
                        @foreach(json_decode($course->requirements) as $requirement)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-gray-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            <span class="text-sm text-gray-700">{{ $requirement }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Description -->
                @if($course->description)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Description</h2>
                    <div class="prose prose-lg max-w-none text-gray-700">
                        {!! $course->description !!}
                    </div>
                </div>
                @endif

                <!-- Instructor Section -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Instructor</h2>
                    <div class="flex items-start">
                        <img src="{{ $course->instructor->avatar ? asset('storage/' . $course->instructor->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($course->instructor->name) }}" alt="{{ $course->instructor->name }}" class="w-24 h-24 rounded-full">
                        <div class="ml-6 flex-1">
                            <h3 class="text-xl font-semibold text-gray-900">{{ $course->instructor->name }}</h3>
                            @if($course->instructor->profile && $course->instructor->profile->bio)
                            <p class="mt-2 text-gray-600">{{ $course->instructor->profile->bio }}</p>
                            @endif
                            <div class="mt-4 flex items-center space-x-6 text-sm text-gray-500">
                                <span>{{ $course->instructor->createdCourses->count() }} courses</span>
                                <span>{{ $course->instructor->createdCourses->sum('enrolled_count') }} students</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Reviews -->
                @if($course->reviews->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Student Reviews</h2>

                    <!-- Rating Summary -->
                    <div class="flex items-center mb-6">
                        <div class="text-center mr-8">
                            <div class="text-5xl font-bold text-gray-900">{{ number_format($course->average_rating, 1) }}</div>
                            <div class="flex justify-center mt-2">
                                @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= $course->average_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                @endfor
                            </div>
                            <div class="text-sm text-gray-500 mt-1">{{ $course->reviews_count }} reviews</div>
                        </div>
                    </div>

                    <!-- Reviews List -->
                    <div class="space-y-6">
                        @foreach($course->reviews->take(5) as $review)
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex items-start">
                                <img src="{{ $review->student->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($review->student->name) }}" alt="{{ $review->student->name }}" class="w-10 h-10 rounded-full">
                                <div class="ml-4 flex-1">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-medium text-gray-900">{{ $review->student->name }}</h4>
                                        <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="flex mt-1">
                                        @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        @endfor
                                    </div>
                                    @if($review->comment)
                                    <p class="mt-2 text-gray-700">{{ $review->comment }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar (Sticky) -->
            <div class="mt-8 lg:mt-0 lg:col-span-4">
                <div class="sticky top-4">
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <!-- Course Image -->
                        @if($course->thumbnail)
                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-48 object-cover">
                        @endif

                        <div class="p-6">
                            <!-- Price -->
                            @if($course->is_free || $course->package_only || ($course->allow_single_purchase ?? true))
                                <div class="mb-6">
                                    @if($course->is_free)
                                    <div class="text-3xl font-bold text-green-600">Free</div>
                                    @else
                                    <div class="flex items-baseline">
                                        @if($course->sale_price)
                                        <span class="text-3xl font-bold text-gray-900">{{ $course->currency }}{{ number_format($course->sale_price, 2) }}</span>
                                        <span class="ml-2 text-lg text-gray-500 line-through">{{ $course->currency }}{{ number_format($course->price, 2) }}</span>
                                        @else
                                        <span class="text-3xl font-bold text-gray-900">{{ $course->currency }}{{ number_format($course->price, 2) }}</span>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Enroll/Purchase Button -->
                            @auth
                                @if(auth()->user()->enrollments()->where('course_id', $course->id)->exists())
                                <a href="{{ route('student.courses.learn', $course->slug) }}" class="block w-full text-center px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 mb-3">
                                    Continue Learning
                                </a>
                                @else
                                    <!-- Package warning only for non-enrolled users -->
                                    @if($course->package_only)
                                        <!-- Package Only Badge -->
                                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                                            <div class="flex">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm text-yellow-700 font-medium">Package Only</p>
                                                    <p class="text-xs text-yellow-600 mt-1">This course is only available as part of a package</p>
                                                </div>
                                            </div>
                                        </div>

                                        @php
                                            $availablePackages = $course->availablePackages()->get();
                                        @endphp

                                        @if($availablePackages->count() > 0)
                                            <a href="#available-packages" class="block w-full text-center px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 mb-3">
                                                View Available Packages
                                            </a>
                                        @else
                                            <button disabled class="w-full px-6 py-3 bg-gray-400 text-white rounded-md cursor-not-allowed mb-3">
                                                Not Available for Purchase
                                            </button>
                                        @endif
                                    @elseif(($course->allow_single_purchase ?? true) && $course->is_free)
                                        <!-- Free course enrollment -->
                                        <form action="{{ route('student.courses.enroll', $course) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 mb-3">
                                                Enroll for Free
                                            </button>
                                        </form>
                                    @elseif(($course->allow_single_purchase ?? true) && !$course->is_free)
                                        <!-- Paid course - show purchase button -->
                                        <form action="{{ route('student.courses.purchase', $course) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 mb-3">
                                                Purchase Course
                                            </button>
                                        </form>

                                        <!-- Show package options if available -->
                                        @if($course->allowed_in_packages && count($course->allowed_in_packages) > 0)
                                            <p class="text-center text-sm text-gray-600 mb-3">or</p>
                                            <a href="#available-packages" class="block w-full text-center px-6 py-3 bg-white border-2 border-indigo-600 text-indigo-600 rounded-md hover:bg-indigo-50 mb-3">
                                                View Package Options
                                            </a>
                                        @endif
                                    @endif
                                @endif
                            @else
                            <a href="{{ route('login') }}" class="block w-full text-center px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 mb-3">
                                Login to Enroll
                            </a>
                            @endauth

                            <!-- Course Includes -->
                            <div class="border-t border-gray-200 pt-6 space-y-3">
                                <h3 class="font-semibold text-gray-900 mb-3">This course includes:</h3>
                                @if($course->duration_hours)
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $course->duration_hours }} hours on-demand video
                                </div>
                                @endif
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    {{ $course->topics->sum(function($topic) { return $topic->assignments->count(); }) }} assignments
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    Access on mobile and desktop
                                </div>
                                @if($course->certificate_enabled)
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                    </svg>
                                    Certificate of completion
                                </div>
                                @endif
                            </div>

                            <!-- Share Buttons -->
                            <div class="border-t border-gray-200 pt-6 mt-6">
                                <h3 class="font-semibold text-gray-900 mb-3">Share this course:</h3>
                                <div class="flex space-x-2">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('student.courses.show', $course)) }}" target="_blank" rel="noopener noreferrer" class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50 transition-colors" title="Share on Facebook">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('student.courses.show', $course)) }}&text={{ urlencode($course->title) }}" target="_blank" rel="noopener noreferrer" class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50 transition-colors" title="Share on Twitter">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                                    </a>
                                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(route('student.courses.show', $course)) }}&title={{ urlencode($course->title) }}" target="_blank" rel="noopener noreferrer" class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50 transition-colors" title="Share on LinkedIn">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Packages Section -->
        @if($course->package_only || ($course->allowed_in_packages && count($course->allowed_in_packages) > 0))
        <div id="available-packages" class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Available Packages</h2>
            <p class="text-gray-600 mb-6">
                @if($course->package_only)
                This course is only available as part of the following packages:
                @else
                Get better value by purchasing a package that includes this course:
                @endif
            </p>

            @php
                $packages = $course->package_only
                    ? $course->availablePackages()->get()
                    : \App\Models\Package::whereJsonContains('allowed_in_packages', $course->id)
                        ->orWhereHas('courses', function($q) use ($course) {
                            $q->where('course_id', $course->id);
                        })
                        ->published()
                        ->get();
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($packages as $package)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="p-6">
                        <!-- Package Header -->
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="text-xl font-bold text-gray-900">{{ $package->name }}</h3>
                            @if($package->is_featured)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                Featured
                            </span>
                            @endif
                        </div>

                        <!-- Description -->
                        @if($package->description)
                        <p class="text-sm text-gray-600 mb-4">{{ Str::limit($package->description, 100) }}</p>
                        @endif

                        <!-- Price -->
                        <div class="mb-4">
                            @if($package->has_sale)
                            <div class="flex items-baseline">
                                <span class="text-2xl font-bold text-gray-900">{{ $package->currency ?? 'USD' }} {{ number_format($package->sale_price, 2) }}</span>
                                <span class="ml-2 text-lg text-gray-500 line-through">{{ $package->currency ?? 'USD' }} {{ number_format($package->price, 2) }}</span>
                            </div>
                            <span class="inline-block mt-1 px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded">
                                Save {{ number_format((($package->price - $package->sale_price) / $package->price) * 100, 0) }}%
                            </span>
                            @else
                            <span class="text-2xl font-bold text-gray-900">{{ $package->currency ?? 'USD' }} {{ number_format($package->effective_price, 2) }}</span>
                            @endif
                        </div>

                        <!-- Package Info -->
                        <div class="border-t border-gray-200 pt-4 mb-4 space-y-2 text-sm text-gray-600">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                {{ $package->courses()->count() }} courses included
                            </div>
                            @if($package->is_lifetime)
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Lifetime access
                            </div>
                            @elseif($package->duration_days)
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $package->duration_days }} days access
                            </div>
                            @endif
                        </div>

                        <!-- Features List -->
                        @if($package->display_features && count($package->display_features) > 0)
                        <div class="border-t border-gray-200 pt-4 mb-4">
                            <ul class="space-y-2">
                                @foreach(array_slice($package->display_features, 0, 3) as $feature)
                                <li class="flex items-start text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>{{ $feature }}</span>
                                </li>
                                @endforeach
                                @if(count($package->display_features) > 3)
                                <li class="text-sm text-indigo-600 font-medium">
                                    + {{ count($package->display_features) - 3 }} more features
                                </li>
                                @endif
                            </ul>
                        </div>
                        @endif

                        <!-- CTA Button -->
                        <a href="#" class="block w-full text-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors">
                            View Package Details
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-8 text-gray-500">
                    No packages available at this time.
                </div>
                @endforelse
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
