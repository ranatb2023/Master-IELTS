@extends('layouts.learning')

@section('content')
<div class="bg-gray-100">
    <div class="flex" x-data="{ sidebarOpen: true, noteModal: false }">
        <!-- Sidebar -->
        <div :class="sidebarOpen ? 'w-80' : 'w-0'"
            class="bg-white shadow-lg transition-all duration-300 overflow-hidden">
            <div class="overflow-y-auto" style="max-height: 100vh;">
                <!-- Course Header -->
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 line-clamp-2">{{ $course->title }}</h2>
                    <div class="mt-3">
                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                            <span>Course Progress</span>
                            <span>{{ $progressPercentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-indigo-600 h-2 rounded-full transition-all"
                                style="width: {{ $progressPercentage }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Course Content -->
                <div class="p-4">
                    @foreach($course->topics as $topicIndex => $topic)
                        @php
                            // Auto-expand the topic that contains the current lesson
                            $containsCurrentLesson = $topic->lessons->contains('id', $currentLesson->id);
                        @endphp
                        <div class="mb-4" x-data="{ expanded: {{ $containsCurrentLesson ? 'true' : 'false' }} }">
                            <button @click="expanded = !expanded"
                                class="w-full flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-gray-600 transition-transform"
                                        :class="expanded ? 'rotate-90' : ''" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                    <span class="font-medium text-gray-900">{{ $topic->title }}</span>
                                </div>
                                <span class="text-xs text-gray-500">
                                    @php
                                        $itemCount = $topic->lessons->count();
                                        if (auth()->user()->canAccessFeature('quiz_access')) {
                                            $itemCount += $topic->quizzes->count();
                                        }
                                        if (auth()->user()->canAccessFeature('assignment_submission')) {
                                            $itemCount += $topic->assignments->count();
                                        }
                                    @endphp
                                    {{ $itemCount }} items
                                </span>
                            </button>

                            <div x-show="expanded" x-collapse class="mt-2 space-y-1">
                                @php
                                    $contentItems = collect();

                                    foreach ($topic->lessons as $lesson) {
                                        $contentItems->push([
                                            'type' => 'lesson',
                                            'order' => $lesson->order ?? 0,
                                            'item' => $lesson
                                        ]);
                                    }

                                    // Only include quizzes if user has access
                                    if (auth()->user()->canAccessFeature('quiz_access')) {
                                        foreach ($topic->quizzes as $quiz) {
                                            $contentItems->push([
                                                'type' => 'quiz',
                                                'order' => $quiz->order ?? 0,
                                                'item' => $quiz
                                            ]);
                                        }
                                    }

                                    // Only include assignments if user has access
                                    if (auth()->user()->canAccessFeature('assignment_submission')) {
                                        foreach ($topic->assignments as $assignment) {
                                            $contentItems->push([
                                                'type' => 'assignment',
                                                'order' => $assignment->order ?? 0,
                                                'item' => $assignment
                                            ]);
                                        }
                                    }

                                    $contentItems = $contentItems->sortBy('order');
                                @endphp

                                @foreach($contentItems as $content)
                                    @if($content['type'] === 'lesson')
                                        @php $lesson = $content['item']; @endphp
                                        <a href="{{ route('student.courses.view-lesson', [$course, $topic, $lesson]) }}"
                                            class="flex items-center justify-between p-3 ml-7 rounded-lg transition
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           {{ isset($currentLesson) && $currentLesson->id == $lesson->id ? 'bg-indigo-50 border-l-4 border-indigo-600' : 'hover:bg-gray-50' }}">
                                            <div class="flex items-center flex-1">
                                                <div class="mr-3">
                                                    @if($lesson->content_type === 'video')
                                                        <svg class="w-5 h-5 {{ isset($currentLesson) && $currentLesson->id == $lesson->id ? 'text-indigo-600' : 'text-gray-400' }}"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    @elseif($lesson->content_type === 'reading')
                                                        <svg class="w-5 h-5 {{ isset($currentLesson) && $currentLesson->id == $lesson->id ? 'text-indigo-600' : 'text-gray-400' }}"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                        </svg>
                                                    @else
                                                        <svg class="w-5 h-5 {{ isset($currentLesson) && $currentLesson->id == $lesson->id ? 'text-indigo-600' : 'text-gray-400' }}"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="flex-1">
                                                    <p
                                                        class="text-sm {{ isset($currentLesson) && $currentLesson->id == $lesson->id ? 'text-indigo-900 font-medium' : 'text-gray-700' }}">
                                                        {{ $lesson->title }}
                                                    </p>
                                                    @if($lesson->duration_minutes)
                                                        <p class="text-xs text-gray-500">{{ $lesson->duration_minutes }} min</p>
                                                    @endif
                                                </div>
                                            </div>
                                            @if(isset($completedLessons) && in_array($lesson->id, $completedLessons))
                                                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            @endif
                                        </a>

                                    @elseif($content['type'] === 'quiz')
                                        @php $quiz = $content['item']; @endphp
                                        <a href="{{ route('student.quizzes.show', $quiz) }}"
                                            class="flex items-center justify-between p-3 ml-7 rounded-lg transition hover:bg-gray-50">
                                            <div class="flex items-center flex-1">
                                                <div class="mr-3">
                                                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-sm text-gray-700">{{ $quiz->title }}</p>
                                                    <p class="text-xs text-gray-500">
                                                        {{ $quiz->questions_count ?? $quiz->questions->count() }} questions
                                                        @if($quiz->time_limit)
                                                            • {{ $quiz->time_limit }} min
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </a>

                                    @elseif($content['type'] === 'assignment')
                                        @php $assignment = $content['item']; @endphp
                                        <a href="{{ route('student.assignments.show', $assignment) }}"
                                            class="flex items-center justify-between p-3 ml-7 rounded-lg transition hover:bg-gray-50">
                                            <div class="flex items-center flex-1">
                                                <div class="mr-3">
                                                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-sm text-gray-700">{{ $assignment->title }}</p>
                                                    @if($assignment->due_date)
                                                        <p class="text-xs text-gray-500">Due:
                                                            {{ \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y') }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col">
            <!-- Top Bar -->
            <div class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg hover:bg-gray-100 mr-4">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <h1 class="text-lg font-semibold text-gray-900">{{ $currentLesson->title }}</h1>
                    </div>


                    <div class="flex items-center gap-3">
                        {{-- Navigation Buttons --}}
                        <a href="{{ route('student.courses.show', $course->slug) }}"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-150">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Course
                        </a>
                        <a href="{{ route('student.dashboard') }}"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-150">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Dashboard
                        </a>

                        {{-- Add Note Button --}}
                        <button @click="$dispatch('open-note-modal')"
                            class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 transition"
                            title="Add a note for this lesson">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Add Note
                        </button>

                        <!-- Previous Button -->
                        @if($previousLesson)
                            <a href="{{ route('student.courses.view-lesson', [$course, $previousLesson->topic_id_for_nav, $previousLesson]) }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Previous
                            </a>
                        @elseif($previousItem)
                            @if($previousItem['type'] === 'quiz')
                                <a href="{{ route('student.quizzes.show', $previousItem['item']) }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                    Previous Quiz
                                </a>
                            @elseif($previousItem['type'] === 'assignment')
                                <a href="{{ route('student.assignments.show', $previousItem['item']) }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                    Previous Assignment
                                </a>
                            @endif
                        @endif

                        <!-- Next Button -->
                        @if($nextLesson)
                            <a href="{{ route('student.courses.view-lesson', [$course, $nextLesson->topic_id_for_nav, $nextLesson]) }}"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Next
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @elseif($nextItem)
                            @if($nextItem['type'] === 'quiz')
                                <a href="{{ route('student.quizzes.show', $nextItem['item']) }}"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                    Next Quiz
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            @elseif($nextItem['type'] === 'assignment')
                                <a href="{{ route('student.assignments.show', $nextItem['item']) }}"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                    Next Assignment
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            @endif
                        @elseif(!$nextLesson && !$nextItem)
                            <a href="{{ route('student.dashboard') }}"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                Complete Course
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Lesson Content -->
            <div class="flex-1 bg-gray-50 pb-8">
                <!-- Lesson Hero Section -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                    <div class="max-w-7xl mx-auto px-8 py-12">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 backdrop-blur-sm">
                                        @if($currentLesson->content_type === 'video')
                                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
                                            </svg>
                                            Video Lesson
                                        @elseif($currentLesson->content_type === 'text')
                                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Reading
                                        @elseif($currentLesson->content_type === 'audio')
                                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z" />
                                            </svg>
                                            Audio
                                        @elseif($currentLesson->content_type === 'document')
                                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Document
                                        @elseif($currentLesson->content_type === 'presentation')
                                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Presentation
                                        @else
                                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ ucfirst($currentLesson->content_type) }}
                                        @endif
                                    </span>
                                    @if($currentLesson->duration_minutes)
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 backdrop-blur-sm">
                                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $currentLesson->duration_minutes }} min
                                        </span>
                                    @endif
                                </div>
                                <h1 class="text-3xl font-bold mb-3">{{ $currentLesson->title }}</h1>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="max-w-7xl mx-auto px-8 py-8"
                    x-data="{ activeTab: window.location.hash === '#comments' ? 'comments' : 'content' }">
                    <!-- Tabs -->
                    <div class="mb-8 border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8">
                            <button @click="activeTab = 'content'"
                                :class="activeTab === 'content' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                Lesson Content
                            </button>
                            <button @click="activeTab = 'overview'"
                                :class="activeTab === 'overview' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Overview
                            </button>
                            @if($currentLesson->resources && $currentLesson->resources->count() > 0)
                                <button @click="activeTab = 'resources'"
                                    :class="activeTab === 'resources' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Resources
                                    <span
                                        class="ml-2 bg-indigo-100 text-indigo-600 py-0.5 px-2 rounded-full text-xs font-medium">{{ $currentLesson->resources->count() }}</span>
                                </button>
                            @endif
                            <button @click="activeTab = 'comments'"
                                :class="activeTab === 'comments' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                </svg>
                                Private Comments
                                @if($lessonComments->count() > 0)
                                    <span
                                        class="ml-2 bg-indigo-100 text-indigo-600 py-0.5 px-2 rounded-full text-xs font-medium">{{ $lessonComments->count() }}</span>
                                @endif
                            </button>
                        </nav>
                    </div>

                    <!-- Two Column Layout -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Main Content Column (2/3 width) -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Overview Tab -->
                            <div x-show="activeTab === 'overview'" x-transition
                                class="bg-white rounded-xl shadow-sm p-8">
                                <h2 class="text-2xl font-bold text-gray-900 mb-6">Lesson Overview</h2>
                                <div class="prose prose-lg max-w-none">
                                    @if($currentLesson->description)
                                        <div class="text-gray-700 leading-relaxed">
                                            {!! $currentLesson->description !!}
                                        </div>
                                    @else
                                        <p class="text-gray-500 italic">No description available for this lesson.
                                        </p>
                                    @endif
                                </div>

                                <!-- Lesson Metadata -->
                                <div class="mt-8 pt-6 border-t border-gray-200">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Lesson Details</h3>
                                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <dt class="text-sm font-medium text-gray-500 mb-1">Content Type</dt>
                                            <dd class="text-lg font-semibold text-gray-900">
                                                {{ ucfirst($currentLesson->content_type) }}
                                            </dd>
                                        </div>
                                        @if($currentLesson->duration_minutes)
                                            <div class="bg-gray-50 rounded-lg p-4">
                                                <dt class="text-sm font-medium text-gray-500 mb-1">Estimated
                                                    Duration
                                                </dt>
                                                <dd class="text-lg font-semibold text-gray-900">
                                                    {{ $currentLesson->duration_minutes }} minutes
                                                </dd>
                                            </div>
                                        @endif
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <dt class="text-sm font-medium text-gray-500 mb-1">Status</dt>
                                            <dd class="text-lg font-semibold">
                                                @if(in_array($currentLesson->id, $completedLessons))
                                                    <span class="text-green-600 flex items-center">
                                                        <svg class="w-5 h-5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        Completed
                                                    </span>
                                                @else
                                                    <span class="text-gray-600">Not completed</span>
                                                @endif
                                            </dd>
                                        </div>
                                        @if($currentLesson->resources && $currentLesson->resources->count() > 0)
                                            <div class="bg-gray-50 rounded-lg p-4">
                                                <dt class="text-sm font-medium text-gray-500 mb-1">Resources</dt>
                                                <dd class="text-lg font-semibold text-gray-900">
                                                    {{ $currentLesson->resources->count() }}
                                                    {{ Str::plural('file', $currentLesson->resources->count()) }}
                                                </dd>
                                            </div>
                                        @endif
                                    </dl>
                                </div>
                            </div>

                            <!-- Content Tab -->
                            <div x-show="activeTab === 'content'" x-transition class="space-y-6">
                                <!-- Video Content -->
                                @if($currentLesson->content_type === 'video' && $currentLesson->contentable)
                                    <div class="bg-black rounded-lg overflow-hidden mb-6" style="aspect-ratio: 16/9;">
                                        <div class="w-full h-full flex items-center justify-center">
                                            @php
                                                $videoContent = $currentLesson->contentable;
                                                $isUploadedVideo = ($videoContent->source ?? 'url') === 'upload' && $videoContent->file_path;

                                                // Helper function to convert YouTube URL to embed URL
                                                $getYouTubeEmbedUrl = function ($url) {
                                                    // Extract video ID from various YouTube URL formats
                                                    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/', $url, $matches)) {
                                                        return 'https://www.youtube.com/embed/' . $matches[1];
                                                    }
                                                    return $url;
                                                };
                                            @endphp

                                            @if($isUploadedVideo)
                                                <!-- Uploaded Video File (Private Storage) -->
                                                <video id="lessonVideo" controls class="w-full h-full"
                                                    controlsList="nodownload">
                                                    <source src="{{ route('lessons.video.stream', $currentLesson) }}"
                                                        type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                            @elseif($videoContent->vimeo_id)
                                                <!-- Vimeo Player -->
                                                <iframe src="https://player.vimeo.com/video/{{ $videoContent->vimeo_id }}"
                                                    class="w-full h-full" frameborder="0"
                                                    allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
                                            @elseif($videoContent->url)
                                                <!-- External Video URL -->
                                                @php
                                                    $isYouTube = Str::contains($videoContent->url, 'youtube.com') || Str::contains($videoContent->url, 'youtu.be');
                                                @endphp

                                                @if($isYouTube)
                                                    <!-- YouTube Player -->
                                                    <iframe src="{{ $getYouTubeEmbedUrl($videoContent->url) }}"
                                                        class="w-full h-full" frameborder="0"
                                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                        allowfullscreen></iframe>
                                                @else
                                                    <!-- Direct Video URL -->
                                                    <video id="lessonVideo" controls class="w-full h-full">
                                                        <source src="{{ $videoContent->url }}" type="video/mp4">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                @endif
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Video Transcript (if available) -->
                                    @if($currentLesson->contentable->transcript)
                                        <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
                                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Transcript</h3>
                                            <div class="prose max-w-none text-gray-700">
                                                {!! nl2br(e($currentLesson->contentable->transcript)) !!}
                                            </div>
                                        </div>
                                    @endif
                                @endif

                                <!-- Text Content -->
                                @if($currentLesson->content_type === 'text' && $currentLesson->contentable)
                                    <div class="bg-white rounded-lg shadow-sm p-8">
                                        @if($currentLesson->contentable->reading_time)
                                            <div class="flex items-center text-sm text-gray-600 mb-4">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span>{{ $currentLesson->contentable->reading_time }} min read</span>
                                            </div>
                                        @endif
                                        <div class="prose max-w-none">
                                            {!! $currentLesson->contentable->body !!}
                                        </div>
                                    </div>
                                @endif

                                <!-- Document Content -->
                                @if($currentLesson->content_type === 'document' && $currentLesson->contentable)
                                    @php
                                        $fileType = strtolower($currentLesson->contentable->file_type);
                                        $isPdf = $fileType === 'pdf';
                                    @endphp

                                    @if($isPdf)
                                        <!-- PDF Viewer -->
                                        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                                            <div
                                                class="bg-indigo-50 px-6 py-4 border-b border-indigo-100 flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <svg class="h-6 w-6 text-indigo-600 mr-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    <div>
                                                        <p class="font-medium text-gray-900">
                                                            {{ $currentLesson->contentable->file_name }}
                                                        </p>
                                                        <p class="text-xs text-gray-600">PDF Document</p>
                                                    </div>
                                                </div>
                                                <a href="{{ route('lessons.document.stream', $currentLesson) }}" download
                                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    Download
                                                </a>
                                            </div>
                                            <!-- PDF Viewer Iframe -->
                                            <div class="bg-gray-100" style="height: 800px;">
                                                <iframe src="{{ route('lessons.document.stream', $currentLesson) }}"
                                                    class="w-full h-full" frameborder="0">
                                                </iframe>
                                            </div>
                                        </div>
                                    @else
                                        <!-- Other Document Types (DOC, DOCX, PPT, PPTX) -->
                                        <div class="bg-white rounded-lg shadow-sm p-8">
                                            <div class="flex items-center p-6 bg-indigo-50 rounded-lg">
                                                <svg class="h-12 w-12 text-indigo-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <div class="ml-4 flex-1">
                                                    <p class="text-lg font-medium text-gray-900">
                                                        {{ $currentLesson->contentable->file_name }}
                                                    </p>
                                                    <p class="text-sm text-gray-600 mt-1">
                                                        {{ strtoupper($currentLesson->contentable->file_type) }}
                                                        Document
                                                    </p>
                                                </div>
                                                <a href="{{ route('lessons.document.stream', $currentLesson) }}" target="_blank"
                                                    class="ml-4 inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    Download
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                @endif

                                <!-- Audio Content -->
                                @if($currentLesson->content_type === 'audio' && $currentLesson->contentable)
                                    <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Audio Lesson</h3>
                                        <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-lg p-6">
                                            <audio id="lessonAudio" controls class="w-full" controlsList="nodownload">
                                                <source src="{{ route('lessons.audio.stream', $currentLesson) }}"
                                                    type="audio/mpeg">
                                                Your browser does not support the audio element.
                                            </audio>
                                        </div>
                                    </div>

                                    <!-- Audio Transcript (if available) -->
                                    @if($currentLesson->contentable->transcript)
                                        <div class="bg-white rounded-lg shadow-sm p-8">
                                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Transcript</h3>
                                            <div class="prose max-w-none text-gray-700 bg-gray-50 rounded-lg p-6">
                                                {!! nl2br(e($currentLesson->contentable->transcript)) !!}
                                            </div>
                                        </div>
                                    @endif
                                @endif

                                <!-- Presentation Content -->
                                @if($currentLesson->content_type === 'presentation' && $currentLesson->contentable)
                                    <div class="bg-white rounded-lg shadow-sm p-8">
                                        <div class="flex items-center p-6 bg-yellow-50 rounded-lg">
                                            <svg class="h-12 w-12 text-yellow-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                                            </svg>
                                            <div class="ml-4 flex-1">
                                                <p class="text-lg font-medium text-gray-900">Presentation Slides</p>
                                                <p class="text-sm text-gray-600 mt-1">
                                                    {{ basename($currentLesson->contentable->file_path) }}
                                                </p>
                                            </div>
                                            <a href="{{ route('lessons.presentation.stream', $currentLesson) }}"
                                                target="_blank"
                                                class="ml-4 inline-flex items-center px-6 py-3 bg-yellow-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-yellow-700 transition">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                View Presentation
                                            </a>
                                        </div>
                                    </div>
                                @endif

                                <!-- Embed Content -->
                                @if($currentLesson->content_type === 'embed' && $currentLesson->contentable)
                                    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                                        <div class="aspect-video">
                                            @if($currentLesson->contentable->metadata && isset($currentLesson->contentable->metadata['embed_code']))
                                                {!! $currentLesson->contentable->metadata['embed_code'] !!}
                                            @else
                                                <iframe src="{{ $currentLesson->contentable->embed_url }}" class="w-full h-full"
                                                    frameborder="0" allowfullscreen>
                                                </iframe>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Legacy Content (fallback if contentable is not set) -->
                                @if(!$currentLesson->contentable)
                                    <div class="bg-white rounded-lg shadow-sm p-8">
                                        <div class="text-center py-12">
                                            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="mt-4 text-lg text-gray-500">No content available for this
                                                lesson
                                                yet.</p>
                                            <p class="mt-2 text-sm text-gray-400">Please check back later or contact
                                                your instructor.</p>
                                        </div>
                                    </div>
                                @endif

                            </div>
                            <!-- End Content Tab -->

                            <!-- Resources Tab -->
                            @if($currentLesson->resources && $currentLesson->resources->count() > 0)
                                <div x-show="activeTab === 'resources'" x-transition
                                    class="bg-white rounded-xl shadow-sm p-8">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Downloadable Resources</h2>
                                    <div class="grid grid-cols-1 gap-4">
                                        @foreach($currentLesson->resources as $resource)
                                            <a href="{{ Storage::url($resource->file_path) }}" target="_blank" download
                                                class="group flex items-center p-6 bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg hover:from-indigo-50 hover:to-purple-50 transition-all duration-200 border border-gray-200 hover:border-indigo-300">
                                                <div
                                                    class="flex-shrink-0 w-12 h-12 bg-indigo-100 group-hover:bg-indigo-200 rounded-lg flex items-center justify-center transition-colors">
                                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </div>
                                                <div class="ml-4 flex-1">
                                                    <p
                                                        class="text-base font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">
                                                        {{ $resource->title }}
                                                    </p>
                                                    @if($resource->description)
                                                        <p class="text-sm text-gray-600 mt-1">{{ $resource->description }}
                                                        </p>
                                                    @endif
                                                </div>
                                                <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600 transition-colors"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5l7 7-7 7" />
                                                </svg>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            <!-- Comments Tab -->
                            <div x-show="activeTab === 'comments'" x-transition
                                class="bg-white rounded-xl shadow-sm p-8">
                                <h2 class="text-2xl font-bold text-gray-900 mb-6">Comments & Discussion</h2>

                                <!-- Comment Form -->
                                <div class="mb-8" x-data="{ comment: '', submitting: false }">
                                    <form @submit.prevent="submitComment" class="space-y-4">
                                        <div>
                                            <label for="comment" class="block text-sm font-semibold text-gray-900 mb-3">
                                                💬 Post a Comment
                                            </label>
                                            <textarea x-model="comment" id="comment" rows="4"
                                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none transition-all duration-200"
                                                placeholder="Ask a question or share your thoughts about this lesson..."
                                                maxlength="1000"></textarea>
                                            <div class="mt-2 flex items-center justify-between">
                                                <p class="text-sm text-gray-500">
                                                    <span x-text="comment.length">0</span>/1000 characters
                                                </p>
                                            </div>
                                        </div>
                                        <button type="submit" :disabled="!comment.trim() || submitting"
                                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-wide hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-md hover:shadow-lg">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                            </svg>
                                            <span x-show="!submitting">Post Comment</span>
                                            <span x-show="submitting">Posting...</span>
                                        </button>
                                    </form>
                                </div>

                                <!-- Comments List -->
                                <div class="space-y-6" id="comments-list">
                                    @forelse($lessonComments as $comment)
                                        @include('partials.comment-item', ['comment' => $comment, 'lesson' => $currentLesson])
                                    @empty
                                        <div
                                            class="text-center py-12 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-dashed border-gray-300">
                                            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No comments yet
                                            </h3>
                                            <p class="text-sm text-gray-500">Start a private conversation with your
                                                instructor by posting a question!</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar (1/3 width) - Sticky -->
                        <div class="lg:col-span-1">
                            <div class="sticky top-8 space-y-6">
                                <!-- Progress Card -->
                                <div class="bg-white rounded-xl shadow-sm p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Progress</h3>
                                    <div class="space-y-4">
                                        @if(in_array($currentLesson->id, $completedLessons))
                                            <div
                                                class="flex items-center p-4 bg-green-50 rounded-lg border border-green-200">
                                                <svg class="w-8 h-8 text-green-600 mr-3" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                <div>
                                                    <p class="font-semibold text-green-900">Completed!</p>
                                                    <p class="text-sm text-green-700">Great job!</p>
                                                </div>
                                            </div>
                                        @else
                                            <div>
                                                <p class="text-sm text-gray-600 mb-2">Mark this lesson as complete
                                                    when
                                                    you're done</p>
                                                <form id="completeForm" method="POST"
                                                    action="{{ route('student.courses.complete-lesson', [$course, $currentLesson->topic_id, $currentLesson]) }}">
                                                    @csrf
                                                    <button type="submit"
                                                        class="w-full inline-flex items-center justify-center px-4 py-3 bg-green-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <span class="button-text">Mark Complete</span>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Quick Info Card -->
                                <div
                                    class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl p-6 border border-indigo-100">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Info</h3>
                                    <div class="space-y-3">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-indigo-600 mr-3 mt-0.5" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-gray-700">Type</p>
                                                <p class="text-sm text-gray-600">
                                                    {{ ucfirst($currentLesson->content_type) }}
                                                </p>
                                            </div>
                                        </div>
                                        @if($currentLesson->duration_minutes)
                                            <div class="flex items-start">
                                                <svg class="w-5 h-5 text-indigo-600 mr-3 mt-0.5" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-700">Duration</p>
                                                    <p class="text-sm text-gray-600">
                                                        {{ $currentLesson->duration_minutes }} minutes
                                                    </p>
                                                </div>
                                            </div>
                                        @endif
                                        @if($currentLesson->resources && $currentLesson->resources->count() > 0)
                                            <div class="flex items-start">
                                                <svg class="w-5 h-5 text-indigo-600 mr-3 mt-0.5" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-700">Resources</p>
                                                    <p class="text-sm text-gray-600">
                                                        {{ $currentLesson->resources->count() }}
                                                        {{ Str::plural('file', $currentLesson->resources->count()) }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Need Help Card -->
                                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-gray-900">Need help?</h3>
                                            <p class="mt-1 text-sm text-gray-500">Contact your instructor if you
                                                have questions.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>     // Progress Tracking System
    (function () {
        const courseId = {{ $course->id }};
        const topicId = {{ $currentLesson->topic_id }};
        const lessonId = {{ $currentLesson->id }};
        const updateUrl = '{{ route("student.courses.update-lesson-progress", [$course, $currentLesson->topic_id, $currentLesson->id]) }}';

        let timeTracker = {
            startTime: Date.now(),
            totalTime: 0,
            interval: null,

            start() {
                this.startTime = Date.now();
                this.interval = setInterval(() => this.save(), 30000); // Save every 30 seconds
            },

            stop() {
                if (this.interval) {
                    clearInterval(this.interval);
                    this.save();
                }
            },

            save() {
                const elapsed = Math.floor((Date.now() - this.startTime) / 1000); // seconds
                if (elapsed > 0) {
                    this.totalTime += elapsed;
                    this.startTime = Date.now();

                    fetch(updateUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            time_spent: elapsed
                        })
                    }).catch(err => console.error('Failed to update time:', err));
                }
            }
        };

        // Start tracking time when page loads
        timeTracker.start();

        // Save time before leaving
        window.addEventListener('beforeunload', () => timeTracker.stop());

        // Video position tracking
        const videos = document.querySelectorAll('video');
        videos.forEach(video => {
            // Restore last position if available
            @if(isset($progress[$currentLesson->id]) && $progress[$currentLesson->id]->last_position)
                const lastPosition = parseFloat('{{ $progress[$currentLesson->id]->last_position }}');
                if (lastPosition > 0) {
                    video.currentTime = lastPosition;
                }
            @endif

            // Save position every 5 seconds while playing
            let positionSaveInterval;
            video.addEventListener('play', () => {
                positionSaveInterval = setInterval(() => {
                    if (video.currentTime > 0) {
                        fetch(updateUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                last_position: video.currentTime.toString()
                            })
                        }).catch(err => console.error('Failed to save position:', err));
                    }
                }, 5000);
            });

            video.addEventListener('pause', () => {
                if (positionSaveInterval) {
                    clearInterval(positionSaveInterval);
                }
            });

            video.addEventListener('ended', () => {
                if (positionSaveInterval) {
                    clearInterval(positionSaveInterval);
                }
            });
        });

        // Audio position tracking (similar to video)
        const audios = document.querySelectorAll('audio');
        audios.forEach(audio => {
            @if(isset($progress[$currentLesson->id]) && $progress[$currentLesson->id]->last_position)
                const lastPosition = parseFloat('{{ $progress[$currentLesson->id]->last_position }}');
                if (lastPosition > 0) {
                    audio.currentTime = lastPosition;
                }
            @endif

            let positionSaveInterval;
            audio.addEventListener('play', () => {
                positionSaveInterval = setInterval(() => {
                    if (audio.currentTime > 0) {
                        fetch(updateUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                last_position: audio.currentTime.toString()
                            })
                        }).catch(err => console.error('Failed to save position:', err));
                    }
                }, 5000);
            });

            audio.addEventListener('pause', () => {
                if (positionSaveInterval) {
                    clearInterval(positionSaveInterval);
                }
            });

            audio.addEventListener('ended', () => {
                if (positionSaveInterval) {
                    clearInterval(positionSaveInterval);
                }
            });
        });

        // Handle Mark Complete form submission
        const completeForm = document.getElementById('completeForm');
        if (completeForm) {
            completeForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const submitButton = this.querySelector('button[type="submit"]');
                const buttonText = submitButton.querySelector('.button-text');
                const originalText = buttonText.textContent;

                // Disable button and show loading state
                submitButton.disabled = true;
                buttonText.textContent = 'Marking...';

                fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            buttonText.textContent = '✓ Completed!';
                            submitButton.classList.remove('bg-green-600', 'hover:bg-green-700');
                            submitButton.classList.add('bg-green-500');

                            // Reload page after short delay to show updated UI
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            // Show error and re-enable button
                            buttonText.textContent = originalText;
                            submitButton.disabled = false;
                            alert('Failed to mark lesson as complete. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        buttonText.textContent = originalText;
                        submitButton.disabled = false;
                        alert('An error occurred. Please try again.');
                    });
            });
        }
    })();
    // Comment Functions
    window.submitComment = function () {
        const form = event.target;
        const commentData = Alpine.$data(form.closest('[x-data]'));

        if (!commentData.comment.trim()) return;

        commentData.submitting = true;

        fetch('{{ route('student.lessons.comments.store', $currentLesson) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ comment: commentData.comment })
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Comment posted successfully, reload with tab preserved
                    window.location.hash = 'comments';
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to post comment. Please try again.');
                    commentData.submitting = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                commentData.submitting = false;
            });
    };

    window.updateComment = function (commentId) {
        const commentElement = document.querySelector(`#comment-${commentId}`);
        const data = Alpine.$data(commentElement);

        if (!data.editedComment.trim()) return;

        data.submitting = true;

        fetch(`{{ route('student.lessons.comments.update', ['lesson' => $currentLesson, 'comment' => '__ID__']) }}`.replace('__ID__', commentId), {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ comment: data.editedComment })
        })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(result => {
                if (result.success) {
                    // Update the comment text in the DOM
                    const commentTextElements = commentElement.querySelectorAll('p');
                    commentTextElements.forEach(p => {
                        if (p.classList.contains('whitespace-pre-wrap') && !p.closest('[x-show="editing"]')) {
                            p.textContent = data.editedComment;
                        }
                    });
                    // Exit edit mode
                    data.editing = false;
                    data.submitting = false;
                } else {
                    alert(result.message || 'Failed to update comment. Please try again.');
                    data.submitting = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'An error occurred. Please try again.');
                data.submitting = false;
            });
    };



    window.deleteComment = function (commentId) {
        if (!confirm('Are you sure you want to delete this comment?')) return;

        fetch(`{{ route('student.lessons.comments.destroy', ['lesson' => $currentLesson, 'comment' => '__ID__']) }}`.replace('__ID__', commentId), {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Remove comment from DOM with fade animation
                    const commentElement = document.querySelector(`#comment-${commentId}`);
                    if (commentElement) {
                        commentElement.style.transition = 'opacity 0.3s ease';
                        commentElement.style.opacity = '0';
                        setTimeout(() => commentElement.remove(), 300);
                    }
                } else {
                    alert(data.message || 'Failed to delete comment. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'An error occurred. Please try again.');
            });
    };

    window.submitReply = function (parentId) {
        const commentElement = document.querySelector(`#comment-${parentId}`);
        const data = Alpine.$data(commentElement);

        if (!data.replyText.trim()) return;

        data.submitting = true;

        const route = '{{ auth()->user()->hasAnyAdminRole() ?
    route('admin.lesson-comments.store', ['lesson' => $currentLesson]) :
    (auth()->user()->isTutor() ? route('tutor.lesson-comments.store', ['lesson' => $currentLesson]) : '') }}';

        fetch(route, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                comment: data.replyText,
                parent_id: parentId
            })
        })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    window.location.hash = 'comments';
                    window.location.reload();
                } else {
                    alert('Failed to post reply. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            })
            .finally(() => {
                data.submitting = false;
            });
    };

    window.togglePin = function (commentId) {
        const route = '{{ auth()->user()->hasAnyAdminRole() ?
    route('admin.lesson-comments.toggle-pin', ['comment' => '__ID__']) :
    (auth()->user()->isTutor() ? route('tutor.lesson-comments.toggle-pin', ['comment' => '__ID__']) : '') }}'.replace('__ID__', commentId);

        fetch(route, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.hash = 'comments';
                    window.location.reload();
                } else {
                    alert('Failed to toggle pin. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
    };
</script>

<!-- Quick Note Modal -->
<div x-show="noteModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
    role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="noteModal = false"></div>

    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full" @click.stop>
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Quick Note</h3>
                <button type="button" @click="noteModal = false" class="text-gray-400 hover:text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form action="{{ route('student.notes.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="lesson_id" value="{{ $currentLesson->id }}">
                <input type="hidden" name="course_id" value="{{ $course->id }}">

                <!-- Lesson Info Banner -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm">
                            <p class="text-blue-900 font-medium">{{ $currentLesson->title }}</p>
                            <p class="text-blue-700 text-xs mt-1">This note will be linked to this lesson</p>
                        </div>
                    </div>
                </div>

                <!-- Title -->
                <div>
                    <label for="quick_note_title" class="block text-sm font-medium text-gray-700 mb-1">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="quick_note_title" required
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Enter note title...">
                </div>

                <!-- Body -->
                <div>
                    <label for="quick_note_body" class="block text-sm font-medium text-gray-700 mb-1">
                        Note Content <span class="text-red-500">*</span>
                    </label>
                    <textarea name="body" id="quick_note_body" rows="8" required
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Write your note here..."></textarea>
                    <p class="mt-1 text-xs text-gray-500">Simple text format (rich text available in full
                        editor)</p>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <a href="{{ route('student.notes.create', ['lesson_id' => $currentLesson->id, 'course_id' => $course->id]) }}"
                        class="text-sm text-indigo-600 hover:text-indigo-700">
                        Open full editor →
                    </a>
                    <div class="flex gap-3">
                        <button type="button" @click="noteModal = false"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Save Note
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@include('partials.quick-note-modal')

@endsection