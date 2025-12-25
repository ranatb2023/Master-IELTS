@extends('layouts.admin')

@section('title', 'Course Details')
@section('page-title', $course->title)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $course->title }}</h2>
            <p class="mt-1 text-sm text-gray-600">Course ID: #{{ $course->id }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
            <a href="{{ route('admin.courses.edit', $course) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Course
            </a>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed top-4 right-4 z-50 hidden">
        <div class="bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span id="toast-message">Changes saved!</span>
        </div>
    </div>

    <!-- Status & Quick Actions -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <!-- Status Badge -->
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($course->status === 'published') bg-green-100 text-green-800
                        @elseif($course->status === 'review') bg-yellow-100 text-yellow-800
                        @elseif($course->status === 'draft') bg-gray-100 text-gray-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ ucfirst($course->status) }}
                    </span>
                </div>

                <!-- Visibility Badge -->
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        {{ ucfirst($course->visibility) }}
                    </span>
                </div>

                <!-- Free/Paid Badge -->
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($course->is_free) bg-teal-100 text-teal-800
                        @else bg-purple-100 text-purple-800
                        @endif">
                        {{ $course->is_free ? 'Free' : 'Paid' }}
                    </span>
                </div>
            </div>

            <!-- Quick Actions Dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Actions
                    <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                    <div class="py-1">
                        <a href="{{ route('admin.reports.course-performance', ['course_id' => $course->id]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <svg class="inline-block w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            View Analytics
                        </a>
                        <div class="border-t border-gray-100"></div>
                        <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" class="block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100" onclick="return confirm('Are you sure you want to delete this course?')">
                                <svg class="inline-block w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete Course
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Topics -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-indigo-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Topics</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['topics_count'] }}</p>
                </div>
            </div>
        </div>

        <!-- Total Lessons -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Lessons</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['lessons_count'] }}</p>
                </div>
            </div>
        </div>

        <!-- Total Enrollments -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Students</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['enrollments_count'] }}</p>
                </div>
            </div>
        </div>

        <!-- Revenue -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $course->currency }} {{ $course->is_free ? '0' : number_format($course->price * $course->enrolled_count, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Content with Drag & Drop -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content: Curriculum -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Course Curriculum</h3>
                            <p class="mt-1 text-sm text-gray-500">Drag items to reorder</p>
                        </div>
                        <a href="{{ route('admin.topics.create', ['course_id' => $course->id]) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Topic
                        </a>
                    </div>
                </div>

                <div class="p-6">
                    @if($course->topics->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No topics yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new topic for this course.</p>
                            <div class="mt-6">
                                <a href="{{ route('admin.topics.create', ['course_id' => $course->id]) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Create Topic
                                </a>
                            </div>
                        </div>
                    @else
                        <!-- Topics List (Sortable) -->
                        <div id="topics-list" class="space-y-4">
                            @foreach($course->topics->sortBy('order') as $topic)
                                <div class="topic-item border border-gray-200 rounded-lg bg-gray-50" data-topic-id="{{ $topic->id }}">
                                    <!-- Topic Header -->
                                    <div class="p-4" x-data="{ open: false }">
                                        <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
                                            <div class="flex items-center flex-1">
                                                <div class="topic-drag-handle cursor-move mr-3" @click.stop>
                                                    <svg class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M7 2a2 2 0 10-4 0 2 2 0 004 0zM7 10a2 2 0 10-4 0 2 2 0 004 0zM7 18a2 2 0 10-4 0 2 2 0 004 0zM17 2a2 2 0 10-4 0 2 2 0 004 0zM17 10a2 2 0 10-4 0 2 2 0 004 0zM17 18a2 2 0 10-4 0 2 2 0 004 0z"></path>
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <h4 class="text-base font-medium text-gray-900">{{ $topic->title }}</h4>
                                                    @if($topic->description)
                                                        <p class="text-sm text-gray-500 mt-1">{{ Str::limit($topic->description, 100) }}</p>
                                                    @endif
                                                </div>
                                                <div class="ml-4 flex items-center space-x-4 text-sm text-gray-500">
                                                    <span class="flex items-center" title="Lessons">
                                                        <svg class="w-4 h-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"></path>
                                                        </svg>
                                                        {{ $topic->lessons->count() }}
                                                    </span>
                                                    <span class="flex items-center" title="Quizzes">
                                                        <svg class="w-4 h-4 mr-1 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        {{ $topic->quizzes->count() }}
                                                    </span>
                                                    <span class="flex items-center" title="Assignments">
                                                        <svg class="w-4 h-4 mr-1 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        {{ $topic->assignments->count() }}
                                                    </span>
                                                </div>
                                                <svg class="w-5 h-5 ml-4 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </div>
                                        </div>

                                        <!-- Topic Content (Collapsible, Sortable) -->
                                        <div x-show="open" x-collapse class="mt-4 pt-4 border-t border-gray-200">
                                            @php
                                                // Combine all content items
                                                $contentItems = collect();

                                                foreach ($topic->lessons as $lesson) {
                                                    $contentItems->push([
                                                        'id' => $lesson->id,
                                                        'type' => 'lesson',
                                                        'title' => $lesson->title,
                                                        'order' => $lesson->order,
                                                        'duration' => $lesson->duration,
                                                        'is_free' => $lesson->is_free,
                                                        'model' => $lesson
                                                    ]);
                                                }

                                                foreach ($topic->quizzes as $quiz) {
                                                    $contentItems->push([
                                                        'id' => $quiz->id,
                                                        'type' => 'quiz',
                                                        'title' => $quiz->title,
                                                        'order' => $quiz->order,
                                                        'questions_count' => $quiz->questions->count(),
                                                        'time_limit' => $quiz->time_limit,
                                                        'model' => $quiz
                                                    ]);
                                                }

                                                foreach ($topic->assignments as $assignment) {
                                                    $contentItems->push([
                                                        'id' => $assignment->id,
                                                        'type' => 'assignment',
                                                        'title' => $assignment->title,
                                                        'order' => $assignment->order,
                                                        'max_points' => $assignment->max_points,
                                                        'due_date' => $assignment->due_date,
                                                        'model' => $assignment
                                                    ]);
                                                }

                                                $contentItems = $contentItems->sortBy('order');
                                            @endphp

                                            @if($contentItems->isEmpty())
                                                <div class="text-center py-8">
                                                    <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    <p class="mt-2 text-sm text-gray-500">No content in this topic yet</p>
                                                    <div class="mt-4 flex justify-center space-x-2">
                                                        <a href="{{ route('admin.lessons.create', ['topic_id' => $topic->id]) }}" class="text-xs text-indigo-600 hover:text-indigo-800">Add Lesson</a>
                                                        <span class="text-gray-300">|</span>
                                                        <a href="{{ route('admin.quizzes.create', ['topic_id' => $topic->id]) }}" class="text-xs text-indigo-600 hover:text-indigo-800">Add Quiz</a>
                                                        <span class="text-gray-300">|</span>
                                                        <a href="{{ route('admin.assignments.create', ['topic_id' => $topic->id]) }}" class="text-xs text-indigo-600 hover:text-indigo-800">Add Assignment</a>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="content-list space-y-2" data-topic-id="{{ $topic->id }}">
                                                    @foreach($contentItems as $item)
                                                        <div class="content-item flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200 hover:border-gray-300 transition" data-content-id="{{ $item['id'] }}" data-content-type="{{ $item['type'] }}">
                                                            <div class="flex items-center flex-1">
                                                                <div class="content-drag-handle cursor-move mr-3">
                                                                    <svg class="w-4 h-4 text-gray-400 hover:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path d="M7 2a2 2 0 10-4 0 2 2 0 004 0zM7 10a2 2 0 10-4 0 2 2 0 004 0zM7 18a2 2 0 10-4 0 2 2 0 004 0zM17 2a2 2 0 10-4 0 2 2 0 004 0zM17 10a2 2 0 10-4 0 2 2 0 004 0zM17 18a2 2 0 10-4 0 2 2 0 004 0z"></path>
                                                                    </svg>
                                                                </div>

                                                                @if($item['type'] === 'lesson')
                                                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                                                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"></path>
                                                                        </svg>
                                                                    </div>
                                                                    <div class="flex-1">
                                                                        <h5 class="text-sm font-medium text-gray-900">{{ $item['title'] }}</h5>
                                                                        <p class="text-xs text-gray-500 mt-0.5">
                                                                            @if($item['duration'])
                                                                                {{ $item['duration'] }} min
                                                                            @endif
                                                                            @if($item['is_free'])
                                                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Free</span>
                                                                            @endif
                                                                        </p>
                                                                    </div>
                                                                @elseif($item['type'] === 'quiz')
                                                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                                                        <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                                                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                                                                        </svg>
                                                                    </div>
                                                                    <div class="flex-1">
                                                                        <h5 class="text-sm font-medium text-gray-900">{{ $item['title'] }}</h5>
                                                                        <p class="text-xs text-gray-500 mt-0.5">
                                                                            {{ $item['questions_count'] }} questions
                                                                            @if($item['time_limit'])
                                                                                · {{ $item['time_limit'] }} min
                                                                            @endif
                                                                        </p>
                                                                    </div>
                                                                @else
                                                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center mr-3">
                                                                        <svg class="w-4 h-4 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path>
                                                                        </svg>
                                                                    </div>
                                                                    <div class="flex-1">
                                                                        <h5 class="text-sm font-medium text-gray-900">{{ $item['title'] }}</h5>
                                                                        <p class="text-xs text-gray-500 mt-0.5">
                                                                            {{ $item['max_points'] }} points
                                                                            @if($item['due_date'])
                                                                                · Due: {{ \Carbon\Carbon::parse($item['due_date'])->format('M d, Y') }}
                                                                            @endif
                                                                        </p>
                                                                    </div>
                                                                @endif

                                                                <div class="flex items-center space-x-2 ml-4">
                                                                    <a href="{{ route('admin.' . Str::plural($item['type']) . '.show', $item['id']) }}" class="text-indigo-600 hover:text-indigo-800" title="View">
                                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                                        </svg>
                                                                    </a>
                                                                    <a href="{{ route('admin.' . Str::plural($item['type']) . '.edit', $item['id']) }}" class="text-gray-600 hover:text-gray-800" title="Edit">
                                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                        </svg>
                                                                    </a>
                                                                    <form action="{{ route('admin.' . Str::plural($item['type']) . '.destroy', $item['id']) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this {{ $item['type'] }}?')">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                            </svg>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar: Course Info -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg overflow-hidden sticky top-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Course Information</h3>

                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Category</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($course->category)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $course->category->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400">Not set</span>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Level</dt>
                            <dd class="mt-1 text-sm text-gray-900 capitalize">{{ $course->level ?? 'Not set' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Price</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($course->is_free)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Free
                                    </span>
                                @else
                                    ${{ number_format($course->price, 2) }}
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Duration</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $course->duration_hours ? $course->duration_hours . ' hours' : 'Not set' }}</dd>
                        </div>

                        @if($course->instructor)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Instructor</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $course->instructor->name }}</dd>
                            </div>
                        @endif

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $course->created_at->format('M d, Y') }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $course->updated_at->diffForHumans() }}</dd>
                        </div>

                        @if($course->subtitle)
                            <div class="pt-3 border-t border-gray-200">
                                <dt class="text-sm font-medium text-gray-500">Subtitle</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $course->subtitle }}</dd>
                            </div>
                        @endif

                        @if($course->published_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Published</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $course->published_at->format('M d, Y') }}
                                    @if($course->published_at->isFuture())
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Scheduled</span>
                                    @endif
                                </dd>
                            </div>
                        @endif

                        <div class="pt-3 border-t border-gray-200">
                            <dt class="text-sm font-medium text-gray-500 mb-2">Purchase Options</dt>
                            <dd class="space-y-1">
                                @if($course->allow_single_purchase ?? true)
                                    <div class="flex items-center text-xs">
                                        <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-gray-700">Individual purchase allowed</span>
                                    </div>
                                @endif
                                
                                @if($course->package_only)
                                    <div class="flex items-center text-xs">
                                        <svg class="w-4 h-4 text-orange-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-gray-700 font-medium">Package only</span>
                                    </div>
                                @endif
                                
                                @if($course->single_purchase_price)
                                    <div class="text-xs text-gray-600 mt-1">
                                        Individual price: ${{ number_format($course->single_purchase_price, 2) }}
                                    </div>
                                @endif
                            </dd>
                        </div>
                    </dl>

                    <!-- Quick Actions -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Quick Actions</h4>
                        <div class="space-y-2">
                            <a href="{{ route('admin.topics.create', ['course_id' => $course->id]) }}" class="block w-full text-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Add Topic
                            </a>
                            <a href="{{ route('admin.enrollments.create', ['course_id' => $course->id]) }}" class="block w-full text-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Enroll Student
                            </a>
                            @if($course->slug)
                                <a href="{{ route('courses.show', $course->slug) }}" target="_blank" class="block w-full text-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    View Public Page
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SortableJS Library -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
    // Toast notification helper
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toast-message');

        toastMessage.textContent = message;

        // Change color based on type
        const toastDiv = toast.querySelector('div');
        if (type === 'error') {
            toastDiv.classList.remove('bg-green-500');
            toastDiv.classList.add('bg-red-500');
        } else {
            toastDiv.classList.remove('bg-red-500');
            toastDiv.classList.add('bg-green-500');
        }

        toast.classList.remove('hidden');

        setTimeout(() => {
            toast.classList.add('hidden');
        }, 3000);
    }

    // Initialize SortableJS for Topics
    const topicsList = document.getElementById('topics-list');
    if (topicsList) {
        new Sortable(topicsList, {
            handle: '.topic-drag-handle',
            animation: 150,
            ghostClass: 'opacity-50',
            onEnd: function(evt) {
                // Collect new order
                const topics = [];
                document.querySelectorAll('.topic-item').forEach((item, index) => {
                    topics.push({
                        id: parseInt(item.dataset.topicId),
                        order: index
                    });
                });

                // Save to server
                fetch('{{ route('admin.courses.reorder-topics', $course) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ topics: topics })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Topics reordered successfully!');
                    } else {
                        showToast(data.message || 'Failed to reorder topics', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Failed to save order', 'error');
                });
            }
        });
    }

    // Initialize SortableJS for Content Items within each topic
    document.querySelectorAll('.content-list').forEach(contentList => {
        new Sortable(contentList, {
            handle: '.content-drag-handle',
            animation: 150,
            ghostClass: 'opacity-50',
            onEnd: function(evt) {
                const topicId = contentList.dataset.topicId;

                // Collect new order
                const items = [];
                contentList.querySelectorAll('.content-item').forEach((item, index) => {
                    items.push({
                        id: parseInt(item.dataset.contentId),
                        type: item.dataset.contentType,
                        order: index
                    });
                });

                // Save to server
                fetch('{{ route('admin.courses.reorder-content', $course) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        topic_id: topicId,
                        items: items
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Content reordered successfully!');
                    } else {
                        showToast(data.message || 'Failed to reorder content', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Failed to save order', 'error');
                });
            }
        });
    });
</script>
@endsection
