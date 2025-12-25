@extends('layouts.tutor')

@section('title', 'Topic Details')
@section('page-title', $topic->title)

@section('content')
    <div class="space-y-6">
        <!-- Header with Back Button -->
        <div class="flex justify-between items-center">
            <a href="{{ route('tutor.topics.all') }}"
                class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Topics
            </a>
            <div class="flex space-x-3">
                @can('course.update')
                    <a href="{{ route('tutor.courses.topics.edit', [$topic->course, $topic]) }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Topic
                    </a>
                @endcan
            </div>
        </div>

        <!-- Topic Info Card -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h2 class="text-3xl font-bold text-gray-900">{{ $topic->title }}</h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Course: <a href="{{ route('tutor.courses.show', $topic->course) }}"
                            class="text-indigo-600 hover:text-indigo-900">{{ $topic->course->title }}</a>
                    </p>
                    @if($topic->description)
                        <div class="mt-4 prose max-w-none">
                            {!! $topic->description !!}
                        </div>
                    @endif
                </div>
                <div class="ml-6">
                    @if($topic->is_published)
                        <span
                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Published</span>
                    @else
                        <span
                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Lessons</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $topic->lessons->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Quizzes</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $topic->quizzes->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Assignments</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $topic->assignments->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lessons List -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Lessons in this Topic</h3>
                @can('course.create')
                    <a href="{{ route('tutor.courses.topics.lessons.create', [$topic->course, $topic]) }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Lesson
                    </a>
                @endcan
            </div>

            @if($topic->lessons->count() > 0)
                <div class="space-y-4">
                    @foreach($topic->lessons as $lesson)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                            <div class="flex items-center flex-1">
                                <div class="flex-shrink-0 text-gray-400 font-semibold">
                                    #{{ $lesson->order }}
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $lesson->title }}</h4>
                                    <div class="mt-1 flex items-center space-x-3 text-xs text-gray-500">
                                        @if($lesson->duration_minutes)
                                            <span>{{ $lesson->duration_minutes }} min</span>
                                        @endif
                                        @if($lesson->is_preview)
                                            <span class="text-blue-600">Preview</span>
                                        @endif
                                        <span class="{{ $lesson->is_published ? 'text-green-600' : 'text-gray-600' }}">
                                            {{ $lesson->is_published ? 'Published' : 'Draft' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                @can('course.update')
                                    <a href="{{ route('tutor.courses.topics.lessons.edit', [$topic->course, $topic, $lesson]) }}"
                                        class="text-indigo-600 hover:text-indigo-900 text-sm">
                                        Edit
                                    </a>
                                @endcan
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-500 py-8">No lessons in this topic yet.</p>
            @endif
        </div>
    </div>
@endsection