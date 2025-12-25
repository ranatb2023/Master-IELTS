@extends('layouts.app')

@section('title', $course->title)

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Course Header -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <!-- Breadcrumb -->
                            <nav class="text-sm mb-4">
                                <ol class="list-none p-0 inline-flex">
                                    <li class="flex items-center">
                                        <a href="{{ route('home') }}" class="text-indigo-600 hover:text-indigo-800">Home</a>
                                        <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 320 512">
                                            <path
                                                d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z" />
                                        </svg>
                                    </li>
                                    <li class="flex items-center">
                                        <a href="{{ route('courses.index') }}"
                                            class="text-indigo-600 hover:text-indigo-800">Courses</a>
                                        <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 320 512">
                                            <path
                                                d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z" />
                                        </svg>
                                    </li>
                                    <li class="text-gray-500">{{ $course->title }}</li>
                                </ol>
                            </nav>

                            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $course->title }}</h1>

                            <p class="text-lg text-gray-600 mb-6">{{ $course->short_description }}</p>

                            <!-- Course Meta -->
                            <div class="flex flex-wrap items-center gap-4 text-sm">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">
                                    {{ ucfirst($course->level) }}
                                </span>

                                <div class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    {{ $course->total_enrollments ?? 0 }} students enrolled
                                </div>

                                <div class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $course->duration_hours ?? 0 }} hours
                                </div>

                                <div class="flex items-center text-yellow-500">
                                    <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    {{ number_format($course->average_rating, 1) }} ({{ $course->total_reviews }} reviews)
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Course Preview Video/Image -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        @if($course->preview_video)
                            @php
                                // Extract YouTube video ID from URL
                                $videoId = null;
                                if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $course->preview_video, $matches)) {
                                    $videoId = $matches[1];
                                }
                            @endphp
                            @if($videoId)
                                <div class="relative w-full" style="padding-bottom: 56.25%;">
                                    <iframe class="absolute top-0 left-0 w-full h-full"
                                        src="https://www.youtube.com/embed/{{ $videoId }}" frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen>
                                    </iframe>
                                </div>
                            @else
                                <!-- Fallback to thumbnail if video ID couldn't be extracted -->
                                @if($course->thumbnail)
                                    <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}"
                                        class="w-full h-96 object-cover">
                                @else
                                    <div class="w-full h-96 bg-gray-200 flex items-center justify-center">
                                        <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            @endif
                        @elseif($course->thumbnail)
                            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}"
                                class="w-full h-96 object-cover">
                        @else
                            <div class="w-full h-96 bg-gray-200 flex items-center justify-center">
                                <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Course Description -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">About This Course</h2>
                            <div class="prose prose-lg max-w-none text-gray-700">
                                {!! $course->description !!}
                            </div>
                        </div>
                    </div>

                    <!-- What You'll Learn -->
                    @if($course->learning_outcomes)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h2 class="text-2xl font-bold text-gray-900 mb-4">What You'll Learn</h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach(json_decode($course->learning_outcomes, true) ?? [] as $outcome)
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-gray-700">{{ $outcome }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Course Content -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6">Course Content</h2>
                            <div class="space-y-3">
                                @forelse($course->topics as $topic)
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
                                                <div class="flex-1">
                                                    <h3 class="font-semibold text-gray-900 text-lg">{{ $topic->title }}</h3>
                                                    @if($topic->description)
                                                        <p class="text-sm text-gray-600 mt-1">
                                                            {!! Str::limit(strip_tags($topic->description), 80) !!}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3 ml-4">
                                                @if($topic->lessons->count() > 0)
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-blue-100 text-blue-700 font-medium">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
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
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-green-100 text-green-700 font-medium">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        {{ $topic->quizzes->count() }}
                                                    </span>
                                                @endif
                                                @if($topic->assignments->count() > 0)
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-purple-100 text-purple-700 font-medium">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                        </svg>
                                                        {{ $topic->assignments->count() }}
                                                    </span>
                                                @endif
                                                <svg class="w-5 h-5 text-gray-400 transition-transform"
                                                    :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                        </div>

                                        <!-- Topic Content -->
                                        <div x-show="open" x-collapse>
                                            <div class="bg-white divide-y divide-gray-100">
                                                <!-- Lessons -->
                                                @foreach($topic->lessons as $lesson)
                                                    <div
                                                        class="group flex items-center justify-between p-4 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 transition-all">
                                                        <div class="flex items-center flex-1 min-w-0">
                                                            <div
                                                                class="p-2 rounded-lg bg-blue-50 group-hover:bg-blue-100 transition-colors mr-3">
                                                                @if($lesson->content_type === 'video')
                                                                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>
                                                                @elseif($lesson->content_type === 'text')
                                                                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                    </svg>
                                                                @elseif($lesson->content_type === 'audio')
                                                                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                                                                    </svg>
                                                                @else
                                                                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                                    </svg>
                                                                @endif
                                                            </div>
                                                            <div class="flex-1 min-w-0">
                                                                <h4
                                                                    class="text-sm font-medium text-gray-900 group-hover:text-indigo-600 transition-colors truncate">
                                                                    {{ $lesson->title }}
                                                                </h4>
                                                                <p class="text-xs text-gray-500 mt-0.5">
                                                                    Lesson • {{ ucfirst($lesson->content_type) }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center gap-3 ml-4">
                                                            @if($lesson->is_free_preview)
                                                                <span
                                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                                    Free Preview
                                                                </span>
                                                            @else
                                                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                                </svg>
                                                            @endif
                                                            @if($lesson->duration_minutes)
                                                                <span class="text-sm text-gray-500 font-medium whitespace-nowrap">
                                                                    {{ $lesson->duration_minutes }} min
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach

                                                <!-- Quizzes -->
                                                @foreach($topic->quizzes as $quiz)
                                                    <div
                                                        class="group flex items-center justify-between p-4 hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 transition-all">
                                                        <div class="flex items-center flex-1 min-w-0">
                                                            <div
                                                                class="p-2 rounded-lg bg-green-50 group-hover:bg-green-100 transition-colors mr-3">
                                                                <svg class="h-5 w-5 text-green-600" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                </svg>
                                                            </div>
                                                            <div class="flex-1 min-w-0">
                                                                <h4
                                                                    class="text-sm font-medium text-gray-900 group-hover:text-green-600 transition-colors truncate">
                                                                    {{ $quiz->title }}
                                                                </h4>
                                                                <p class="text-xs text-gray-500 mt-0.5">
                                                                    Quiz • {{ $quiz->questions->count() }} questions
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center gap-3 ml-4">
                                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                            </svg>
                                                            @if($quiz->time_limit)
                                                                <span class="text-sm text-gray-500 font-medium whitespace-nowrap">
                                                                    {{ $quiz->time_limit }} min
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach

                                                <!-- Assignments -->
                                                @foreach($topic->assignments as $assignment)
                                                    <div
                                                        class="group flex items-center justify-between p-4 hover:bg-gradient-to-r hover:from-purple-50 hover:to-violet-50 transition-all">
                                                        <div class="flex items-center flex-1 min-w-0">
                                                            <div
                                                                class="p-2 rounded-lg bg-purple-50 group-hover:bg-purple-100 transition-colors mr-3">
                                                                <svg class="h-5 w-5 text-purple-600" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                                </svg>
                                                            </div>
                                                            <div class="flex-1 min-w-0">
                                                                <h4
                                                                    class="text-sm font-medium text-gray-900 group-hover:text-purple-600 transition-colors truncate">
                                                                    {{ $assignment->title }}
                                                                </h4>
                                                                <p class="text-xs text-gray-500 mt-0.5">
                                                                    Assignment
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center gap-3 ml-4">
                                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-12">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No content yet</h3>
                                        <p class="mt-1 text-sm text-gray-500">Course content is coming soon.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Instructor -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">Your Instructor</h2>
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <img src="{{ $course->instructor->avatar ? asset('storage/' . $course->instructor->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($course->instructor->name) }}"
                                        alt="{{ $course->instructor->name }}"
                                        class="w-16 h-16 rounded-full object-cover border-2 border-gray-200">
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $course->instructor->name }}</h3>
                                    <p class="text-gray-600">
                                        {{ $course->instructor->profile->bio ?? 'Experienced IELTS Instructor' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="sticky top-4">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <!-- Price -->
                                @if($course->is_free || $course->package_only || ($course->allow_single_purchase ?? true))
                                    <div class="text-center mb-6">
                                        @if($course->is_free)
                                            <div class="text-4xl font-bold text-green-600">Free</div>
                                        @else
                                            @if($course->sale_price)
                                                <div class="text-4xl font-bold text-gray-900">
                                                    ${{ number_format($course->sale_price, 2) }}
                                                </div>
                                                <div class="text-sm text-gray-500 line-through">
                                                    ${{ number_format($course->price, 2) }}
                                                </div>
                                                <span
                                                    class="inline-block mt-2 px-3 py-1 bg-red-100 text-red-800 text-sm font-semibold rounded">
                                                    Save
                                                    {{ number_format((($course->price - $course->sale_price) / $course->price) * 100, 0) }}%
                                                </span>
                                            @else
                                                <div class="text-4xl font-bold text-gray-900">
                                                    @if($course->single_purchase_price)
                                                        ${{ number_format($course->single_purchase_price, 2) }}
                                                    @else
                                                        ${{ number_format($course->price, 2) }}
                                                    @endif
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                @endif

                                <!-- Enroll Button -->
                                @auth
                                    @if($enrollment)
                                        @if($enrollment->status === 'active')
                                            <a href="{{ route('student.courses.learn', $course) }}"
                                                class="block w-full text-center px-4 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                Continue Learning
                                            </a>
                                        @else
                                            <div class="text-center text-gray-600 font-semibold py-3">
                                                Enrollment: {{ ucfirst($enrollment->status) }}
                                            </div>
                                        @endif
                                    @else
                                        <!-- Purchase Status Warnings (only for non-enrolled users) -->
                                        @if($course->package_only)
                                            <!-- Package Only Warning -->
                                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                                                <div class="flex">
                                                    <div class="flex-shrink-0">
                                                        <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                        </svg>
                                                    </div>
                                                    <div class="ml-3">
                                                        <p class="text-sm text-yellow-700 font-medium">Package Only</p>
                                                        <p class="text-xs text-yellow-600 mt-1">This course is only available as part of
                                                            a package</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if($course->package_only)
                                            <!-- Package Only - Show Package Link -->
                                            <a href="{{ route('packages.index') }}"
                                                class="block w-full text-center px-4 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                                                View Packages
                                            </a>
                                        @elseif(($course->allow_single_purchase ?? true) && auth()->user()->hasRole('student'))
                                            <!-- Regular Enrollment -->
                                            @if($course->is_free)
                                                <!-- Free Course -->
                                                <form method="POST" action="{{ route('student.courses.enroll', $course) }}">
                                                    @csrf
                                                    <x-primary-button class="w-full justify-center py-3">
                                                        Enroll Now - Free
                                                    </x-primary-button>
                                                </form>
                                            @else
                                                <!-- Paid Course -->
                                                <form method="POST" action="{{ route('student.courses.purchase', $course) }}">
                                                    @csrf
                                                    <x-primary-button class="w-full justify-center py-3">
                                                        Purchase Now -
                                                        ${{ number_format($course->single_purchase_price ?? $course->sale_price ?? $course->price, 2) }}
                                                    </x-primary-button>
                                                </form>
                                            @endif
                                        @else
                                            <p class="text-center text-sm text-gray-600">Only students can enroll in courses</p>
                                        @endif
                                    @endif
                                @else
                                    <!-- Not Logged In -->
                                    @if($course->package_only)
                                        <a href="{{ route('login') }}"
                                            class="block w-full text-center px-4 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                                            Login to View Packages
                                        </a>
                                    @elseif(!($course->allow_single_purchase ?? true))
                                        <a href="{{ route('login') }}"
                                            class="block w-full text-center px-4 py-3 bg-gray-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 transition">
                                            Login for More Info
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}"
                                            class="block w-full text-center px-4 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Login to Enroll
                                        </a>
                                    @endif
                                    <p class="mt-2 text-center text-sm text-gray-600">
                                        New here? <a href="{{ route('register') }}"
                                            class="text-indigo-600 hover:text-indigo-800">Create an account</a>
                                    </p>
                                @endauth

                                <!-- Course Includes -->
                                <div class="mt-6 pt-6 border-t border-gray-200">
                                    <h3 class="font-semibold text-gray-900 mb-3">This course includes:</h3>
                                    <ul class="space-y-3 text-sm text-gray-600">
                                        <li class="flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            {{ $course->duration_hours ?? 0 }} hours of video content
                                        </li>
                                        <li class="flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            {{ $course->topics->count() }} sections
                                        </li>
                                        <li class="flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            {{ $course->quizzes->count() }} quizzes
                                        </li>
                                        @if($course->certificate_available)
                                            <li class="flex items-center">
                                                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                                Certificate of completion
                                            </li>
                                        @endif
                                        @if($course->has_lifetime_access)
                                            <li class="flex items-center">
                                                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                                Lifetime access
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection