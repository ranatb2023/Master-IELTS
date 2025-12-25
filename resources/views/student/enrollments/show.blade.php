@extends('layouts.student')

@section('title', 'Course Details - ' . $enrollment->course->title)
@section('page-title', $enrollment->course->title)

@section('content')
    <div class="space-y-6">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('student.enrollments.index') }}"
                class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to My Courses
            </a>
        </div>

        <!-- Course Header -->
        <div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
            <div class="md:flex">
                <!-- Course Preview Video/Image -->
                <div class="md:w-1/3 bg-gradient-to-br from-indigo-500 to-purple-600">
                    @if($enrollment->course->preview_video)
                        @php
                            // Extract YouTube video ID from URL
                            $videoId = null;
                            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $enrollment->course->preview_video, $matches)) {
                                $videoId = $matches[1];
                            }
                        @endphp
                        @if($videoId)
                            <div class="relative w-full h-64 md:h-full">
                                <iframe class="absolute top-0 left-0 w-full h-full"
                                    src="https://www.youtube.com/embed/{{ $videoId }}" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen>
                                </iframe>
                            </div>
                        @else
                            <!-- Fallback to thumbnail if video ID couldn't be extracted -->
                            @if($enrollment->course->thumbnail)
                                <img src="{{ asset('storage/' . $enrollment->course->thumbnail) }}"
                                    alt="{{ $enrollment->course->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="flex items-center justify-center h-64 md:h-full">
                                    <svg class="h-24 w-24 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                        </path>
                                    </svg>
                                </div>
                            @endif
                        @endif
                    @elseif($enrollment->course->thumbnail)
                        <img src="{{ asset('storage/' . $enrollment->course->thumbnail) }}"
                            alt="{{ $enrollment->course->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="flex items-center justify-center h-64 md:h-full">
                            <svg class="h-24 w-24 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Course Info -->
                <div class="md:w-2/3 p-8">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $enrollment->course->title }}</h1>
                            <p class="text-gray-600">{{ $enrollment->course->subtitle }}</p>
                        </div>
                        @if($enrollment->status == 'active')
                            <span class="px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Active</span>
                        @elseif($enrollment->status == 'completed')
                            <span
                                class="px-3 py-1 text-xs font-semibold text-purple-800 bg-purple-100 rounded-full">Completed</span>
                        @elseif($enrollment->status == 'expired')
                            <span class="px-3 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Expired</span>
                        @endif
                    </div>

                    <!-- Instructor -->
                    <div class="flex items-center text-sm text-gray-600 mb-4">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Instructor: <span
                                class="font-medium text-gray-900">{{ $enrollment->course->instructor->name }}</span></span>
                    </div>

                    <!-- Progress -->
                    <div class="mb-6">
                        <div class="flex justify-between text-sm text-gray-600 mb-2">
                            <span class="font-medium">Your Progress</span>
                            <span
                                class="font-semibold text-indigo-600">{{ number_format($enrollment->progress_percentage, 0) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-indigo-600 h-3 rounded-full transition-all duration-300"
                                style="width: {{ $enrollment->progress_percentage }}%"></div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3">
                        @if($enrollment->status == 'active')
                            <a href="{{ route('student.courses.learn', $enrollment->course) }}"
                                class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-medium">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $enrollment->progress_percentage > 0 ? 'Continue Learning' : 'Start Course' }}
                            </a>
                        @endif
                        <a href="{{ route('student.enrollments.progress', $enrollment) }}"
                            class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 font-medium">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                            View Progress
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Overview Stats -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Course Overview</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-indigo-600">{{ $stats['topics_count'] }}</div>
                            <div class="text-sm text-gray-600">Topics</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $stats['lessons_count'] }}</div>
                            <div class="text-sm text-gray-600">Lessons</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ $stats['quizzes_count'] }}</div>
                            <div class="text-sm text-gray-600">Quizzes</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600">{{ $stats['assignments_count'] }}</div>
                            <div class="text-sm text-gray-600">Assignments</div>
                        </div>
                    </div>
                </div>

                <!-- Course Description -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">About This Course</h2>
                    <div class="prose max-w-none text-gray-600">
                        {!! $enrollment->course->description !!}
                    </div>
                </div>

                <!-- Course Curriculum -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Course Curriculum</h2>
                    <div class="space-y-4">
                        @forelse($enrollment->course->topics as $topic)
                            <div class="border border-gray-200 rounded-xl overflow-hidden hover:shadow-md transition-shadow"
                                x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
                                <!-- Topic Header -->
                                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 flex items-center justify-between cursor-pointer hover:from-indigo-50 hover:to-purple-50 transition-colors"
                                    @click="open = !open">
                                    <div class="flex items-center flex-1">
                                        <div class="p-2 bg-white rounded-lg mr-4 shadow-sm">
                                            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-900 text-lg">{{ $topic->title }}</h3>
                                            @if($topic->description)
                                                <p class="text-sm text-gray-600 mt-1">{{ Str::limit($topic->description, 80) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div class="flex items-center gap-3 text-sm">
                                            @if($topic->lessons->count() > 0)
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-medium">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    {{ $topic->lessons->count() }}
                                                </span>
                                            @endif
                                            @if($topic->quizzes->count() > 0)
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                                    </svg>
                                                    {{ $topic->quizzes->count() }}
                                                </span>
                                            @endif
                                            @if($topic->assignments->count() > 0)
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full bg-purple-100 text-purple-700 font-medium">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    {{ $topic->assignments->count() }}
                                                </span>
                                            @endif
                                        </div>
                                        <svg class="h-6 w-6 text-gray-400 transition-transform duration-200"
                                            :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Topic Content -->
                                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform scale-95"
                                    x-transition:enter-end="opacity-100 transform scale-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 transform scale-100"
                                    x-transition:leave-end="opacity-0 transform scale-95" class="bg-white">

                                    <!-- Lessons -->
                                    @if($topic->lessons->count() > 0)
                                        <div class="px-6 py-4 space-y-2">
                                            @foreach($topic->lessons as $lesson)
                                                @php
                                                    $lessonProgress = auth()->user()->progress()
                                                        ->where('progressable_type', 'App\\Models\\Lesson')
                                                        ->where('progressable_id', $lesson->id)
                                                        ->first();
                                                    $isCompleted = $lessonProgress && $lessonProgress->status == 'completed';
                                                    $isInProgress = $lessonProgress && $lessonProgress->status == 'in_progress';
                                                @endphp
                                                <a href="{{ route('student.courses.view-lesson', [$enrollment->course, $topic->id, $lesson->id]) }}"
                                                    class="group flex items-center justify-between p-4 rounded-lg hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 transition-all">
                                                    <div class="flex items-center flex-1">
                                                        @if($isCompleted)
                                                            <div class="p-2 bg-green-100 rounded-lg mr-4">
                                                                <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd"
                                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                        clip-rule="evenodd" />
                                                                </svg>
                                                            </div>
                                                        @elseif($isInProgress)
                                                            <div class="p-2 bg-blue-100 rounded-lg mr-4">
                                                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                        d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                            </div>
                                                        @else
                                                            <div
                                                                class="p-2 bg-gray-100 rounded-lg mr-4 group-hover:bg-indigo-100 transition-colors">
                                                                <svg class="h-5 w-5 text-gray-400 group-hover:text-indigo-600 transition-colors"
                                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                        d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                            </div>
                                                        @endif
                                                        <div class="flex-1">
                                                            <h4
                                                                class="text-sm font-medium {{ $isCompleted ? 'text-gray-500 line-through' : 'text-gray-900 group-hover:text-indigo-600' }} transition-colors">
                                                                {{ $lesson->title }}
                                                            </h4>
                                                            @if($lesson->content_type)
                                                                <p class="text-xs text-gray-500 mt-1">
                                                                    <span class="inline-flex items-center">
                                                                        @if($lesson->content_type === 'video')
                                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                                                viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                                    stroke-width="2"
                                                                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                                            </svg>
                                                                        @elseif($lesson->content_type === 'text')
                                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                                                viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                                    stroke-width="2"
                                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                            </svg>
                                                                        @elseif($lesson->content_type === 'audio')
                                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                                                viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                                    stroke-width="2"
                                                                                    d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                                                                            </svg>
                                                                        @else
                                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                                                viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                                    stroke-width="2"
                                                                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                                            </svg>
                                                                        @endif
                                                                        {{ ucfirst($lesson->content_type) }}
                                                                    </span>
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-3">
                                                        @if($lesson->duration_minutes)
                                                            <span
                                                                class="text-xs text-gray-500 font-medium px-2 py-1 bg-gray-100 rounded-md">
                                                                {{ $lesson->duration_minutes }} min
                                                            </span>
                                                        @endif
                                                        @if($isCompleted)
                                                            <span class="inline-flex items-center text-xs font-medium text-green-600">
                                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd"
                                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                        clip-rule="evenodd" />
                                                                </svg>
                                                                Completed
                                                            </span>
                                                        @elseif($isInProgress)
                                                            <span class="inline-flex items-center text-xs font-medium text-blue-600">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                                In Progress
                                                            </span>
                                                        @endif
                                                        <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600 transition-colors"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M9 5l7 7-7 7" />
                                                        </svg>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Quizzes -->
                                    @if($topic->quizzes->count() > 0)
                                        <div class="px-6 py-4 border-t border-gray-100">
                                            <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                                </svg>
                                                Quizzes
                                            </h4>
                                            <div class="space-y-2">
                                                @foreach($topic->quizzes as $quiz)
                                                    @php
                                                        $bestAttempt = auth()->user()->quizAttempts()
                                                            ->where('quiz_id', $quiz->id)
                                                            ->orderBy('score', 'desc')
                                                            ->first();
                                                    @endphp
                                                    <a href="{{ route('student.quizzes.show', $quiz) }}"
                                                        class="group flex items-center justify-between p-3 rounded-lg hover:bg-green-50 transition-colors">
                                                        <div class="flex items-center flex-1">
                                                            <div
                                                                class="p-2 bg-green-100 rounded-lg mr-3 group-hover:bg-green-200 transition-colors">
                                                                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <h5
                                                                    class="text-sm font-medium text-gray-900 group-hover:text-green-600">
                                                                    {{ $quiz->title }}</h5>
                                                                <p class="text-xs text-gray-500 mt-1">
                                                                    {{ $quiz->questions()->count() }} questions
                                                                    @if($quiz->time_limit)
                                                                        • {{ $quiz->time_limit }} min
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        </div>
                                                        @if($bestAttempt)
                                                            <span
                                                                class="text-xs font-semibold px-3 py-1 rounded-full {{ $bestAttempt->passed ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                                                Best: {{ round($bestAttempt->score) }}%
                                                            </span>
                                                        @endif
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Assignments -->
                                    @if($topic->assignments->count() > 0)
                                        <div class="px-6 py-4 border-t border-gray-100">
                                            <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Assignments
                                            </h4>
                                            <div class="space-y-2">
                                                @foreach($topic->assignments as $assignment)
                                                    @php
                                                        $submission = auth()->user()->assignmentSubmissions()
                                                            ->where('assignment_id', $assignment->id)
                                                            ->latest()
                                                            ->first();
                                                    @endphp
                                                    <a href="{{ route('student.assignments.show', $assignment) }}"
                                                        class="group flex items-center justify-between p-3 rounded-lg hover:bg-purple-50 transition-colors">
                                                        <div class="flex items-center flex-1">
                                                            <div
                                                                class="p-2 bg-purple-100 rounded-lg mr-3 group-hover:bg-purple-200 transition-colors">
                                                                <svg class="h-4 w-4 text-purple-600" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <h5
                                                                    class="text-sm font-medium text-gray-900 group-hover:text-purple-600">
                                                                    {{ $assignment->title }}</h5>
                                                                <p class="text-xs text-gray-500 mt-1">
                                                                    @if($assignment->due_date)
                                                                        Due: {{ $assignment->due_date->format('M d, Y') }}
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        </div>
                                                        @if($submission)
                                                                                <span
                                                                                    class="text-xs font-semibold px-3 py-1 rounded-full
                                                                                    {{ $submission->status === 'graded' ? 'bg-green-100 text-green-700' :
                                                            ($submission->status === 'submitted' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                                                                                    {{ ucfirst($submission->status) }}
                                                                                </span>
                                                        @endif
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12 bg-gray-50 rounded-xl">
                                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <p class="text-gray-500 text-lg font-medium">No topics available yet</p>
                                <p class="text-gray-400 text-sm mt-2">Course content will be added soon</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Enrollment Info -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase mb-4">Enrollment Details</h3>
                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="text-gray-500">Enrolled On</dt>
                            <dd class="font-medium text-gray-900">{{ $enrollment->enrolled_at->format('M d, Y') }}</dd>
                        </div>
                        @if($enrollment->expires_at)
                            <div>
                                <dt class="text-gray-500">Expires On</dt>
                                <dd class="font-medium {{ $enrollment->isExpired() ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ $enrollment->expires_at->format('M d, Y') }}
                                </dd>
                            </div>
                        @endif
                        @if($enrollment->completed_at)
                            <div>
                                <dt class="text-gray-500">Completed On</dt>
                                <dd class="font-medium text-gray-900">{{ $enrollment->completed_at->format('M d, Y') }}</dd>
                            </div>
                        @endif
                        @if($enrollment->last_accessed_at)
                            <div>
                                <dt class="text-gray-500">Last Accessed</dt>
                                <dd class="font-medium text-gray-900">{{ $enrollment->last_accessed_at->diffForHumans() }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <!-- Learning Stats -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase mb-4">Your Activity</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Lessons Completed</span>
                            <span class="font-semibold text-gray-900">{{ $stats['completed_lessons'] }} /
                                {{ $stats['lessons_count'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Quizzes Taken</span>
                            <span class="font-semibold text-gray-900">{{ $stats['quizzes_taken'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Assignments Submitted</span>
                            <span class="font-semibold text-gray-900">{{ $stats['assignments_submitted'] }}</span>
                        </div>
                        @if($stats['average_quiz_score'])
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Avg. Quiz Score</span>
                                <span
                                    class="font-semibold text-gray-900">{{ number_format($stats['average_quiz_score'], 0) }}%</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Certificate -->
                @if($enrollment->status == 'completed' && $enrollment->certificate_issued)
                    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg shadow p-6 border border-yellow-200">
                        <div class="flex items-center mb-3">
                            <svg class="h-6 w-6 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z">
                                </path>
                            </svg>
                            <h3 class="text-sm font-semibold text-gray-900 uppercase">Certificate Available</h3>
                        </div>
                        <p class="text-sm text-gray-700 mb-4">Congratulations! You've completed this course.</p>
                        <a href="{{ route('student.certificates.download', $enrollment) }}"
                            class="block text-center px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 font-medium text-sm">
                            Download Certificate
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @endpush
@endsection