@extends('layouts.tutor')

@section('title', 'Manage Course')
@section('page-title', $course->title)

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $course->title }}</h2>
                <p class="mt-1 text-sm text-gray-600">Course Management Dashboard</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('tutor.courses.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Courses
                </a>
                <a href="{{ route('tutor.courses.edit', $course) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Course
                </a>
            </div>
        </div>

        <!-- Status Bar -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
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
                    @if($course->status === 'draft')
                        <form action="{{ route('tutor.courses.submitForReview', $course) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-900">Submit for
                                Review</button>
                        </form>
                    @endif
                </div>

                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Quick Actions
                        <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                        <div class="py-1">
                            <a href="{{ route('tutor.courses.analytics', $course) }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">View Analytics</a>
                            <a href="{{ route('tutor.courses.students', $course) }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">View Students</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Preview Course</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Students</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $course->enrolled_count }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Rating</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($course->average_rating, 1) }}</p>
                        <p class="text-xs text-gray-500">{{ $course->total_reviews }} reviews</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Completion</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($course->completion_rate, 1) }}%</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Content</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $course->total_lectures }}</p>
                        <p class="text-xs text-gray-500">lessons</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Content Management -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Course Content</h3>
                    <p class="mt-1 text-sm text-gray-600">Organize your course into topics and lessons</p>
                </div>
                <a href="{{ route('tutor.courses.topics.create', $course) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Topic
                </a>
            </div>

            <div class="px-6 py-4">
                @if($course->topics->count() > 0)
                    <div class="space-y-4" x-data="{ expandedTopic: null }">
                        @foreach($course->topics as $index => $topic)
                            <div class="border border-gray-200 rounded-lg">
                                <!-- Topic Header -->
                                <div class="p-4 bg-gray-50 flex items-center justify-between cursor-pointer"
                                    @click="expandedTopic = expandedTopic === {{ $index }} ? null : {{ $index }}">
                                    <div class="flex items-center space-x-4 flex-1">
                                        <div class="flex-shrink-0">
                                            <span
                                                class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-indigo-600 text-white text-sm font-medium">
                                                {{ $index + 1 }}
                                            </span>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-sm font-medium text-gray-900">{{ $topic->title }}</h4>
                                            <p class="text-xs text-gray-500">{{ $topic->lessons->count() }} lessons •
                                                {{ $topic->quizzes->count() }} quizzes • {{ $topic->assignments->count() }}
                                                assignments
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $topic->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $topic->is_published ? 'Published' : 'Draft' }}
                                        </span>
                                        <svg class="h-5 w-5 text-gray-400 transition-transform"
                                            :class="{ 'rotate-180': expandedTopic === {{ $index }} }" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Topic Content -->
                                <div x-show="expandedTopic === {{ $index }}" x-collapse class="border-t border-gray-200">
                                    <div class="p-4">
                                        <!-- Topic Actions -->
                                        <div class="flex justify-end space-x-2 mb-4">
                                            <a href="{{ route('tutor.courses.topics.lessons.create', [$course, $topic]) }}"
                                                class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md text-xs font-medium text-gray-700 hover:bg-gray-50">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v16m8-8H4" />
                                                </svg>
                                                Add Lesson
                                            </a>
                                            <a href="{{ route('tutor.quizzes.create', ['topic_id' => $topic->id]) }}"
                                                class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md text-xs font-medium text-gray-700 hover:bg-gray-50">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v16m8-8H4" />
                                                </svg>
                                                Add Quiz
                                            </a>
                                            <a href="{{ route('tutor.assignments.create', ['topic_id' => $topic->id]) }}"
                                                class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md text-xs font-medium text-gray-700 hover:bg-gray-50">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v16m8-8H4" />
                                                </svg>
                                                Add Assignment
                                            </a>
                                            <a href="{{ route('tutor.courses.topics.edit', [$course, $topic]) }}"
                                                class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md text-xs font-medium text-gray-700 hover:bg-gray-50">
                                                Edit Topic
                                            </a>
                                        </div>

                                        <!-- Lessons List -->
                                        @if($topic->lessons->count() > 0)
                                            <div class="space-y-2">
                                                @foreach($topic->lessons as $lesson)
                                                    <div
                                                        class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-md hover:bg-gray-50">
                                                        <div class="flex items-center space-x-3">
                                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                            </svg>
                                                            <div>
                                                                <p class="text-sm font-medium text-gray-900">{{ $lesson->title }}</p>
                                                                <p class="text-xs text-gray-500">{{ ucfirst($lesson->content_type) }} •
                                                                    {{ $lesson->duration_minutes }} min
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            <span
                                                                class="text-xs text-gray-500">{{ $lesson->is_published ? 'Published' : 'Draft' }}</span>
                                                            <a href="{{ route('tutor.courses.topics.lessons.edit', [$course, $topic, $lesson]) }}"
                                                                class="text-indigo-600 hover:text-indigo-900">
                                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                </svg>
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-sm text-gray-500 text-center py-4">No lessons added yet</p>
                                        @endif

                                        <!-- Quizzes -->
                                        @if($topic->quizzes->count() > 0)
                                            <div class="mt-4 space-y-2">
                                                <h5 class="text-xs font-medium text-gray-700 uppercase">Quizzes</h5>
                                                @foreach($topic->quizzes as $quiz)
                                                    <div
                                                        class="flex items-center justify-between p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                                                        <div class="flex items-center space-x-3">
                                                            <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                            </svg>
                                                            <div>
                                                                <p class="text-sm font-medium text-gray-900">{{ $quiz->title }}</p>
                                                                <p class="text-xs text-gray-500">{{ $quiz->questions_count ?? 0 }} questions
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <a href="{{ route('tutor.quizzes.edit', $quiz) }}"
                                                            class="text-yellow-600 hover:text-yellow-900">
                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <!-- Assignments -->
                                        @if($topic->assignments->count() > 0)
                                            <div class="mt-4 space-y-2">
                                                <h5 class="text-xs font-medium text-gray-700 uppercase">Assignments</h5>
                                                @foreach($topic->assignments as $assignment)
                                                    <div
                                                        class="flex items-center justify-between p-3 bg-purple-50 border border-purple-200 rounded-md">
                                                        <div class="flex items-center space-x-3">
                                                            <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                            <div>
                                                                <p class="text-sm font-medium text-gray-900">{{ $assignment->title }}</p>
                                                                <p class="text-xs text-gray-500">Due:
                                                                    {{ $assignment->due_date ? $assignment->due_date->format('M d, Y') : 'No due date' }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <a href="{{ route('tutor.assignments.edit', $assignment) }}"
                                                            class="text-purple-600 hover:text-purple-900">
                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No topics yet</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating your first topic.</p>
                        <div class="mt-6">
                            <a href="{{ route('tutor.courses.topics.create', $course) }}"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Create First Topic
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection