@extends('layouts.learning')

@section('content')
    <div class="min-h-screen bg-gray-100">
        <div class="flex" x-data="{ sidebarOpen: true }">
            <!-- Sidebar -->
            <div :class="sidebarOpen ? 'w-80' : 'w-0'"
                class="bg-white shadow-lg transition-all duration-300 overflow-hidden">
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
                                <div class="bg-indigo-600 h-2 rounded-full transition-all"
                                    style="width: {{ $progressPercentage }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Course Content -->
                    <div class="p-4">
                        @foreach($course->topics as $topicIndex => $topic)
                            @php
                                // Auto-expand the topic that contains the current quiz
                                $containsCurrentQuiz = $topic->quizzes->contains('id', $quiz->id);
                            @endphp
                            <div class="mb-4" x-data="{ expanded: {{ $containsCurrentQuiz ? 'true' : 'false' }} }">
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
                                        {{ $topic->lessons->count() + $topic->quizzes->count() + $topic->assignments->count() }}
                                        items
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

                                        foreach ($topic->quizzes as $qz) {
                                            $contentItems->push([
                                                'type' => 'quiz',
                                                'order' => $qz->order ?? 0,
                                                'item' => $qz
                                            ]);
                                        }

                                        foreach ($topic->assignments as $assignment) {
                                            $contentItems->push([
                                                'type' => 'assignment',
                                                'order' => $assignment->order ?? 0,
                                                'item' => $assignment
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
                                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        @else
                                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
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
                                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @endif
                                            </a>

                                        @elseif($content['type'] === 'quiz')
                                            @php $qz = $content['item']; @endphp
                                            <a href="{{ route('student.quizzes.show', $qz) }}"
                                                class="flex items-center justify-between p-3 ml-7 rounded-lg transition
                                                               {{ isset($quiz) && $quiz->id == $qz->id ? 'bg-purple-50 border-l-4 border-purple-600' : 'hover:bg-gray-50' }}">
                                                <div class="flex items-center flex-1">
                                                    <div class="mr-3">
                                                        <svg class="w-5 h-5 {{ isset($quiz) && $quiz->id == $qz->id ? 'text-purple-600' : 'text-purple-500' }}"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1">
                                                        <p
                                                            class="text-sm {{ isset($quiz) && $quiz->id == $qz->id ? 'text-purple-900 font-medium' : 'text-gray-700' }}">
                                                            {{ $qz->title }}
                                                        </p>
                                                        <p class="text-xs text-gray-500">
                                                            {{ $qz->questions_count ?? $qz->questions->count() }} questions
                                                            @if($qz->time_limit)
                                                                • {{ $qz->time_limit }} min
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                @if(isset($completedQuizzes) && in_array($qz->id, $completedQuizzes))
                                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                @endif
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
                                                                {{ \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y') }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if(isset($completedAssignments) && in_array($assignment->id, $completedAssignments))
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                            <h1 class="text-lg font-semibold text-gray-900">{{ $quiz->title }}</h1>
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7" />
                                        </svg>
                                        Previous
                                    </a>
                                @elseif($previousItem['type'] === 'quiz')
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
                            @if($nextItem)
                                @if($nextItem['type'] === 'lesson')
                                    <a href="{{ route('student.courses.view-lesson', [$course, $nextItem['topic_id'], $nextItem['item']]) }}"
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                        Next
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                @elseif($nextItem['type'] === 'quiz')
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
                            @elseif(!$nextItem)
                                <a href="{{ route('student.dashboard') }}"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                    Complete Course
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quiz Content -->
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
                        @if(session('info'))
                            <div class="mb-6 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
                                {{ session('info') }}
                            </div>
                        @endif

                        <!-- Quiz Info Card -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                            <div class="p-8">
                                <!-- Quiz Header -->
                                <div class="flex items-start justify-between mb-6">
                                    <div class="flex-1">
                                        <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $quiz->title }}</h1>
                                        @if($quiz->description)
                                            <div class="prose max-w-none text-gray-600">
                                                {!! $quiz->description !!}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <svg class="w-16 h-16 text-purple-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Quiz Details Grid -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <div class="text-sm text-gray-600 mb-1">Questions</div>
                                        <div class="text-2xl font-bold text-gray-900">{{ $quiz->questions->count() }}</div>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <div class="text-sm text-gray-600 mb-1">Time Limit</div>
                                        <div class="text-2xl font-bold text-gray-900">
                                            @if($quiz->time_limit)
                                                {{ $quiz->time_limit }} min
                                            @else
                                                Unlimited
                                            @endif
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <div class="text-sm text-gray-600 mb-1">Passing Score</div>
                                        <div class="text-2xl font-bold text-gray-900">{{ $quiz->passing_score }}%</div>
                                    </div>
                                </div>

                                <!-- Attempts Info -->
                                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="font-semibold text-gray-900 mb-1">Attempts</h3>
                                            <p class="text-sm text-gray-600">
                                                @if($quiz->max_attempts)
                                                    You have used {{ $attempts->count() }} of {{ $quiz->max_attempts }} attempts
                                                @else
                                                    Unlimited attempts available
                                                @endif
                                            </p>
                                        </div>
                                        @if($bestScore > 0)
                                            <div class="text-right">
                                                <div class="text-sm text-gray-600">Best Score</div>
                                                <div
                                                    class="text-2xl font-bold {{ $bestScore >= $quiz->passing_score ? 'text-green-600' : 'text-yellow-600' }}">
                                                    {{ $bestScore }}%
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Instructions -->
                                @if($quiz->instructions)
                                    <div class="mb-6">
                                        <h3 class="font-semibold text-gray-900 mb-3">Instructions</h3>
                                        <div class="prose max-w-none text-gray-700">
                                            {!! $quiz->instructions !!}
                                        </div>
                                    </div>
                                @endif

                                <!-- Start Quiz Button -->
                                @if($quiz->questions->count() === 0)
                                    <div class="text-center py-6 bg-yellow-50 border border-yellow-200 rounded-lg">
                                        <svg class="w-12 h-12 text-yellow-500 mx-auto mb-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <p class="text-gray-700 font-medium">This quiz has no questions yet</p>
                                        <p class="text-sm text-gray-500 mt-1">Please check back later or contact your instructor
                                        </p>
                                    </div>
                                @elseif($canAttempt)
                                    <form action="{{ route('student.quizzes.start', $quiz) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="w-full inline-flex items-center justify-center px-6 py-3 bg-purple-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Start Quiz
                                        </button>
                                    </form>
                                @else
                                    <div class="text-center py-4 bg-gray-100 rounded-lg">
                                        <p class="text-gray-700 font-medium">Maximum attempts reached</p>
                                        <p class="text-sm text-gray-500 mt-1">You have completed all available attempts for this
                                            quiz</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Previous Attempts --}}
                        @if($attempts->count() > 0)
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Previous Attempts</h3>
                                    <div class="space-y-4">
                                        @foreach($attempts as $attempt)
                                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex-1">
                                                        <div class="flex items-center gap-4">
                                                            <div>
                                                                <div class="text-sm text-gray-600">Attempt
                                                                    #{{ $loop->count - $loop->index }}</div>
                                                                <div class="text-xs text-gray-500">
                                                                    {{ $attempt->started_at->format('M d, Y g:i A') }}</div>
                                                            </div>
                                                            <div>
                                                                <div class="text-sm text-gray-600">Score</div>
                                                                <div
                                                                    class="text-lg font-bold {{ $attempt->passed ? 'text-green-600' : 'text-yellow-600' }}">
                                                                    {{ $attempt->score }}%
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <div class="text-sm text-gray-600">Status</div>
                                                                @if($attempt->passed)
                                                                    <span
                                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                        <svg class="w-4 h-4 mr-1" fill="currentColor"
                                                                            viewBox="0 0 20 20">
                                                                            <path fill-rule="evenodd"
                                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                                clip-rule="evenodd" />
                                                                        </svg>
                                                                        Passed
                                                                    </span>
                                                                @else
                                                                    <span
                                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                        Not Passed
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        @if($attempt->status === 'in_progress')
                                                            <a href="{{ route('student.quizzes.take', [$quiz, $attempt]) }}"
                                                                class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-800 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                                Resume Quiz
                                                            </a>
                                                        @else
                                                            <a href="{{ route('student.quizzes.result', [$quiz, $attempt]) }}"
                                                                class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                                View Details
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
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