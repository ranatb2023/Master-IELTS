@extends('layouts.student')

@section('title', 'Learning Progress - ' . $enrollment->course->title)
@section('page-title', 'Learning Progress')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('student.enrollments.show', $enrollment) }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Course Details
        </a>
    </div>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow mb-6 p-6">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Learning Progress</h1>
                <p class="text-gray-600">{{ $enrollment->course->title }}</p>
            </div>
            <div class="text-right">
                <div class="text-4xl font-bold text-indigo-600 mb-1">{{ number_format($enrollment->progress_percentage, 0) }}%</div>
                <div class="text-sm text-gray-500">Overall Progress</div>
            </div>
        </div>

        <!-- Overall Progress Bar -->
        <div class="mt-6">
            <div class="w-full bg-gray-200 rounded-full h-4">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-4 rounded-full transition-all duration-300 flex items-center justify-end pr-2"
                     style="width: {{ $enrollment->progress_percentage }}%">
                    @if($enrollment->progress_percentage > 10)
                    <span class="text-xs font-semibold text-white">{{ number_format($enrollment->progress_percentage, 0) }}%</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Lessons Progress -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['completed_lessons'] }}</p>
                    <p class="text-sm text-gray-500">of {{ $stats['total_lessons'] }}</p>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-900 mb-2">Lessons Completed</p>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full"
                     style="width: {{ $stats['total_lessons'] > 0 ? ($stats['completed_lessons'] / $stats['total_lessons'] * 100) : 0 }}%"></div>
            </div>
        </div>

        <!-- Quiz Attempts -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_quiz_attempts'] }}</p>
                    <p class="text-sm text-gray-500">attempts</p>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-900 mb-2">Quizzes Taken</p>
            @if($stats['average_quiz_score'] !== null)
            <div class="text-sm text-gray-600">
                Avg. Score: <span class="font-semibold text-green-600">{{ number_format($stats['average_quiz_score'], 1) }}%</span>
            </div>
            @else
            <div class="text-sm text-gray-500">No quizzes taken yet</div>
            @endif
        </div>

        <!-- Assignments -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['submitted_assignments'] }}</p>
                    <p class="text-sm text-gray-500">of {{ $stats['total_assignments'] }}</p>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-900 mb-2">Assignments Submitted</p>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-purple-600 h-2 rounded-full"
                     style="width: {{ $stats['total_assignments'] > 0 ? ($stats['submitted_assignments'] / $stats['total_assignments'] * 100) : 0 }}%"></div>
            </div>
        </div>

        <!-- Time Spent -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex-shrink-0 bg-orange-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_time_spent_hours'] }}</p>
                    <p class="text-sm text-gray-500">hours</p>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-900 mb-2">Time Spent Learning</p>
            <div class="text-sm text-gray-600">
                Last active: {{ $enrollment->last_accessed_at ? $enrollment->last_accessed_at->diffForHumans() : 'Never' }}
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    @if($recentActivity->count() > 0)
    <div class="bg-white rounded-lg shadow mb-6 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h2>
        <div class="flow-root">
            <ul class="-mb-8">
                @foreach($recentActivity as $activity)
                <li>
                    <div class="relative pb-8">
                        @if(!$loop->last)
                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                        @endif
                        <div class="relative flex space-x-3">
                            <div>
                                @if($activity->type == 'lesson')
                                <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </span>
                                @elseif($activity->type == 'quiz')
                                <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </span>
                                @elseif($activity->type == 'assignment')
                                <span class="h-8 w-8 rounded-full bg-purple-500 flex items-center justify-center ring-8 ring-white">
                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div>
                                    <div class="text-sm">
                                        <span class="font-medium text-gray-900">{{ $activity->title }}</span>
                                    </div>
                                    <p class="mt-0.5 text-sm text-gray-500">{{ $activity->description }}</p>
                                </div>
                                <div class="mt-1 text-sm text-gray-500">
                                    <time datetime="{{ $activity->created_at }}">{{ $activity->created_at->diffForHumans() }}</time>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <!-- Topic-wise Progress -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Progress by Topic</h2>
        <div class="space-y-6">
            @forelse($topicProgress as $topic)
            <div class="border-b border-gray-200 pb-6 last:border-b-0">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <h3 class="text-base font-medium text-gray-900">{{ $topic->title }}</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $topic->completed_items }} of {{ $topic->total_items }} items completed
                        </p>
                    </div>
                    <div class="text-right ml-4">
                        <div class="text-lg font-semibold text-indigo-600">{{ number_format($topic->progress_percentage, 0) }}%</div>
                    </div>
                </div>

                <!-- Topic Progress Bar -->
                <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                    <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300"
                         style="width: {{ $topic->progress_percentage }}%"></div>
                </div>

                <!-- Breakdown -->
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div>
                        <div class="text-gray-500">Lessons</div>
                        <div class="font-medium text-gray-900">{{ $topic->completed_lessons }} / {{ $topic->total_lessons }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Quizzes</div>
                        <div class="font-medium text-gray-900">{{ $topic->completed_quizzes }} / {{ $topic->total_quizzes }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Assignments</div>
                        <div class="font-medium text-gray-900">{{ $topic->completed_assignments }} / {{ $topic->total_assignments }}</div>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4">No topics available yet.</p>
            @endforelse
        </div>
    </div>

    <!-- Quiz Performance -->
    @if($quizAttempts->count() > 0)
    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Quiz Performance</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quiz</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attempts</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Best Score</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($quizAttempts as $attempt)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $attempt->quiz->title }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-semibold {{ $attempt->percentage >= 70 ? 'text-green-600' : ($attempt->percentage >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ number_format($attempt->percentage, 1) }}%
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $attempt->attempt_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-semibold text-indigo-600">
                                {{ number_format($attempt->best_score, 1) }}%
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $attempt->completed_at ? $attempt->completed_at->format('M d, Y') : '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Assignment Submissions -->
    @if($assignmentSubmissions->count() > 0)
    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Assignment Submissions</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assignment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Graded</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($assignmentSubmissions as $submission)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $submission->assignment->title }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($submission->status == 'graded')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Graded
                            </span>
                            @elseif($submission->status == 'submitted')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                Submitted
                            </span>
                            @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                Draft
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($submission->grade !== null)
                            <span class="text-sm font-semibold {{ $submission->grade >= 70 ? 'text-green-600' : ($submission->grade >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $submission->grade }}%
                            </span>
                            @else
                            <span class="text-sm text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $submission->graded_at ? $submission->graded_at->format('M d, Y') : '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
