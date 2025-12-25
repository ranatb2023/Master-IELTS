@extends('layouts.learning')

@section('content')

<div class="min-h-screen bg-gray-100">
    <div class="flex" x-data="{ sidebarOpen: true }">
        <!-- Sidebar -->
        <div :class="sidebarOpen ? 'w-80' : 'w-0'" class="bg-white shadow-lg transition-all duration-300 overflow-hidden">
            <div class="h-screen overflow-y-auto">
                <!-- Course Header -->
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 line-clamp-2">{{ $course->title }}</h2>
                    <div class="mt-3">
                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                            <span>Course Progress</span>
                            <span>{{ $progressPercentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: {{ $progressPercentage }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Course Content -->
                <div class="p-4">
                    @foreach($course->topics as $topicIndex => $topic)
                        @php
                            // Auto-expand the topic that contains the current assignment
                            $containsCurrentAssignment = $topic->assignments->contains('id', $assignment->id);
                        @endphp
                        <div class="mb-4" x-data="{ expanded: {{ $containsCurrentAssignment ? 'true' : 'false' }} }">
                            <button @click="expanded = !expanded" class="w-full flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-gray-600 transition-transform" :class="expanded ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    <span class="font-medium text-gray-900">{{ $topic->title }}</span>
                                </div>
                                <span class="text-xs text-gray-500">
                                    {{ $topic->lessons->count() + $topic->quizzes->count() + $topic->assignments->count() }} items
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

                                    foreach ($topic->quizzes as $quiz) {
                                        $contentItems->push([
                                            'type' => 'quiz',
                                            'order' => $quiz->order ?? 0,
                                            'item' => $quiz
                                        ]);
                                    }

                                    foreach ($topic->assignments as $assn) {
                                        $contentItems->push([
                                            'type' => 'assignment',
                                            'order' => $assn->order ?? 0,
                                            'item' => $assn
                                        ]);
                                    }

                                    $contentItems = $contentItems->sortBy('order');
                                @endphp

                                @foreach($contentItems as $content)
                                    @if($content['type'] === 'lesson')
                                        @php $lesson = $content['item']; @endphp
                                        <a href="{{ route('student.courses.view-lesson', [$course, $topic, $lesson]) }}"
                                           class="flex items-center justify-between p-3 ml-7 rounded-lg transition hover:bg-gray-50">
                                            <div class="flex items-center flex-1">
                                                <div class="mr-3">
                                                    @if($lesson->content_type === 'video')
                                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    @else
                                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-sm text-gray-700">{{ $lesson->title }}</p>
                                                    @if($lesson->duration_minutes)
                                                        <p class="text-xs text-gray-500">{{ $lesson->duration_minutes }} min</p>
                                                    @endif
                                                </div>
                                            </div>
                                            @if(isset($completedLessons) && in_array($lesson->id, $completedLessons))
                                                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </a>

                                    @elseif($content['type'] === 'quiz')
                                        @php $quiz = $content['item']; @endphp
                                        <a href="{{ route('student.quizzes.show', $quiz) }}"
                                           class="flex items-center justify-between p-3 ml-7 rounded-lg transition hover:bg-gray-50">
                                            <div class="flex items-center flex-1">
                                                <div class="mr-3">
                                                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
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
                                            @if(isset($completedQuizzes) && in_array($quiz->id, $completedQuizzes))
                                                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </a>

                                    @elseif($content['type'] === 'assignment')
                                        @php $assn = $content['item']; @endphp
                                        <a href="{{ route('student.assignments.show', $assn) }}"
                                           class="flex items-center justify-between p-3 ml-7 rounded-lg transition
                                               {{ isset($assignment) && $assignment->id == $assn->id ? 'bg-orange-50 border-l-4 border-orange-600' : 'hover:bg-gray-50' }}">
                                            <div class="flex items-center flex-1">
                                                <div class="mr-3">
                                                    <svg class="w-5 h-5 {{ isset($assignment) && $assignment->id == $assn->id ? 'text-orange-600' : 'text-orange-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-sm {{ isset($assignment) && $assignment->id == $assn->id ? 'text-orange-900 font-medium' : 'text-gray-700' }}">
                                                        {{ $assn->title }}
                                                    </p>
                                                    @if($assn->due_date)
                                                        <p class="text-xs text-gray-500">Due: {{ \Carbon\Carbon::parse($assn->due_date)->format('M d, Y') }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            @if(isset($completedAssignments) && in_array($assn->id, $completedAssignments))
                                                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <h1 class="text-lg font-semibold text-gray-900">{{ $assignment->title }}</h1>
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

                        <!-- Previous Button -->
                        @if($previousItem)
                            @if($previousItem['type'] === 'lesson')
                                <a href="{{ route('student.courses.view-lesson', [$course, $previousItem['topic_id'], $previousItem['item']]) }}"
                                   class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                    Previous
                                </a>
                            @elseif($previousItem['type'] === 'quiz')
                                <a href="{{ route('student.quizzes.show', $previousItem['item']) }}"
                                   class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                    Previous Quiz
                                </a>
                            @elseif($previousItem['type'] === 'assignment')
                                <a href="{{ route('student.assignments.show', $previousItem['item']) }}"
                                   class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                    Previous Assignment
                                </a>
                            @endif
                        @endif

                        <!-- Next Button -->
                        @if($nextItem)
                            @if($nextItem['type'] === 'lesson')
                                <a href="{{ route('student.courses.view-lesson', [$course, $nextItem['topic_id'], $nextItem['item']]) }}"
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                    Next
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            @elseif($nextItem['type'] === 'quiz')
                                <a href="{{ route('student.quizzes.show', $nextItem['item']) }}"
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                    Next Quiz
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            @elseif($nextItem['type'] === 'assignment')
                                <a href="{{ route('student.assignments.show', $nextItem['item']) }}"
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                    Next Assignment
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            @endif
                        @elseif(!$nextItem)
                            <a href="{{ route('student.dashboard') }}"
                               class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                Complete Course
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Assignment Content -->
            <div class="flex-1 overflow-y-auto bg-gray-50">
                <div class="max-w-5xl mx-auto p-8">
                    <!-- Success/Error Messages -->
                    @if(session('success'))
                        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Assignment Info Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-8">
                            <!-- Assignment Header -->
                            <div class="flex items-start justify-between mb-6">
                                <div class="flex-1">
                                    <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $assignment->title }}</h1>
                                    @if($assignment->description)
                                        <div class="prose max-w-none text-gray-700 mt-4">
                                            {!! $assignment->description !!}
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <svg class="w-16 h-16 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Assignment Details Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="text-sm text-gray-600 mb-1">Due Date</div>
                                    <div class="text-lg font-bold text-gray-900">
                                        @if($assignment->due_date)
                                            {{ \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y') }}
                                            <div class="text-xs font-normal {{ now()->gt($assignment->due_date) ? 'text-red-600' : 'text-gray-500' }}">
                                                {{ \Carbon\Carbon::parse($assignment->due_date)->diffForHumans() }}
                                            </div>
                                        @else
                                            <span class="text-gray-500">No deadline</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="text-sm text-gray-600 mb-1">Max Score</div>
                                    <div class="text-2xl font-bold text-gray-900">
                                        {{ $assignment->max_points ?? 'N/A' }}
                                        @if($assignment->max_points)
                                            <span class="text-sm font-normal text-gray-500">points</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="text-sm text-gray-600 mb-1">Submission Type</div>
                                    <div class="text-lg font-semibold text-gray-900">
                                        @if($assignment->allow_file_upload && $assignment->allow_text_submission)
                                            Text & File
                                        @elseif($assignment->allow_file_upload)
                                            File Only
                                        @elseif($assignment->allow_text_submission)
                                            Text Only
                                        @else
                                            None
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Instructions -->
                            @if($assignment->instructions)
                                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                    <h3 class="font-semibold text-blue-900 mb-2 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                        Instructions
                                    </h3>
                                    <div class="prose max-w-none text-blue-800">
                                        {!! $assignment->instructions !!}
                                    </div>
                                </div>
                            @endif

                            <!-- File Upload Requirements -->
                            @if($assignment->allow_file_upload)
                                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                                    <h3 class="font-semibold text-gray-900 mb-2">File Upload Requirements</h3>
                                    <ul class="text-sm text-gray-700 space-y-1">
                                        @if($assignment->max_files)
                                            <li>• Maximum {{ $assignment->max_files }} {{ Str::plural('file', $assignment->max_files) }}</li>
                                        @endif
                                        @if($assignment->max_file_size)
                                            <li>• Maximum file size: {{ $assignment->max_file_size }} MB per file</li>
                                        @endif
                                        @if($assignment->allowed_file_types)
                                            <li>• Allowed file types: {{ is_array($assignment->allowed_file_types) ? implode(', ', $assignment->allowed_file_types) : $assignment->allowed_file_types }}</li>
                                        @endif
                                    </ul>
                                </div>
                            @endif

                            <!-- Submission Status -->
                            @if($latestSubmission)
                                <div class="mb-6 p-4 rounded-lg
                                    @if($latestSubmission->status === 'graded') bg-green-50 border border-green-200
                                    @elseif($latestSubmission->status === 'submitted') bg-blue-50 border border-blue-200
                                    @else bg-gray-50 border border-gray-200
                                    @endif">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="font-semibold text-gray-900 mb-1">Your Submission Status</h3>
                                            <div class="flex items-center gap-3">
                                                @if($latestSubmission->status === 'graded')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Graded
                                                    </span>
                                                    <span class="text-lg font-bold text-green-700">
                                                        Score: {{ $latestSubmission->score }}/{{ $assignment->max_points }}
                                                    </span>
                                                @elseif($latestSubmission->status === 'submitted')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        Awaiting Grade
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        Draft
                                                    </span>
                                                @endif
                                                @if($latestSubmission->is_late)
                                                    <span class="text-sm text-red-600 font-medium">(Late Submission)</span>
                                                @endif
                                            </div>
                                        </div>
                                        <a href="{{ route('student.assignments.view-submission', [$assignment, $latestSubmission]) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="flex items-center justify-between">
                                @php
                                    $canSubmit = true;
                                    $buttonText = 'Submit Assignment';
                                    $buttonClass = 'bg-orange-600 hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-900 focus:ring-orange-500';

                                    if ($assignment->due_date && now()->gt($assignment->due_date)) {
                                        if (!$assignment->allow_late_submission) {
                                            $canSubmit = false;
                                            $buttonText = 'Deadline Passed';
                                            $buttonClass = 'bg-gray-400 cursor-not-allowed';
                                        } else {
                                            $buttonText = 'Submit Assignment (Late)';
                                        }
                                    }

                                    if ($latestSubmission && $latestSubmission->status === 'submitted') {
                                        $buttonText = 'Resubmit Assignment';
                                    }
                                @endphp

                                <div class="text-sm text-gray-600">
                                    @if($submissions->count() > 0)
                                        {{ $submissions->count() }} {{ Str::plural('submission', $submissions->count()) }} made
                                    @else
                                        No submissions yet
                                    @endif
                                </div>

                                @if($canSubmit)
                                    <a href="{{ route('student.assignments.create', $assignment) }}" class="inline-flex items-center px-6 py-3 {{ $buttonClass }} border border-transparent rounded-md font-semibold text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        {{ $buttonText }}
                                    </a>
                                @else
                                    <button disabled class="inline-flex items-center px-6 py-3 {{ $buttonClass }} border border-transparent rounded-md font-semibold text-white uppercase tracking-widest">
                                        {{ $buttonText }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Previous Submissions -->
                    @if($submissions->count() > 0)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Submission History</h3>
                                <div class="space-y-4">
                                    @foreach($submissions as $submission)
                                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-4">
                                                        <div>
                                                            <div class="text-sm text-gray-600">Submitted</div>
                                                            <div class="text-xs text-gray-500">{{ $submission->submitted_at->format('M d, Y g:i A') }}</div>
                                                        </div>
                                                        <div>
                                                            <div class="text-sm text-gray-600">Status</div>
                                                            @if($submission->status === 'graded')
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                    Graded
                                                                </span>
                                                            @elseif($submission->status === 'submitted')
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                    Awaiting Grade
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                                    Draft
                                                                </span>
                                                            @endif
                                                        </div>
                                                        @if($submission->status === 'graded')
                                                            <div>
                                                                <div class="text-sm text-gray-600">Score</div>
                                                                <div class="text-lg font-bold text-green-600">
                                                                    {{ $submission->score }}/{{ $assignment->max_points }}
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div>
                                                    <a href="{{ route('student.assignments.view-submission', [$assignment, $submission]) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                        View Details
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Grading Rubric -->
                    @if($assignment->rubrics->count() > 0)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Grading Rubric</h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criteria</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max Points</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($assignment->rubrics as $rubric)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        {{ $rubric->criteria }}
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-700">
                                                        {{ $rubric->description }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                                        {{ $rubric->max_score }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
