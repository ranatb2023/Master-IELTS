@extends('layouts.tutor')

@section('title', 'Student Details')
@section('page-title', 'Student Progress - ' . $enrollment->user->name)

@section('content')
    <div class="space-y-6">
        <!-- Back Button -->
        <div>
            <a href="{{ route('tutor.enrollments.index') }}" class="text-indigo-600 hover:text-indigo-900">
                ← Back to All Students
            </a>
        </div>

        <!-- Student Info Card -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    @if($enrollment->user->avatar)
                        <img class="h-16 w-16 rounded-full" src="{{ asset('storage/' . $enrollment->user->avatar) }}" alt="">
                    @else
                        <div
                            class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-bold text-xl">
                            {{ substr($enrollment->user->name, 0, 2) }}
                        </div>
                    @endif
                    <div class="ml-4">
                        <h2 class="text-2xl font-bold text-gray-900">{{ $enrollment->user->name }}</h2>
                        <p class="text-gray-500">{{ $enrollment->user->email }}</p>
                    </div>
                </div>
                <span class="px-4 py-2 text-sm font-semibold rounded-full
                    @if($enrollment->status === 'active') bg-green-100 text-green-800
                    @elseif($enrollment->status === 'completed') bg-blue-100 text-blue-800
                    @else bg-red-100 text-red-800
                    @endif">
                    {{ ucfirst($enrollment->status) }}
                </span>
            </div>
            <div class="grid grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="text-gray-500">Enrolled:</span>
                    <span class="ml-2 font-medium">{{ $enrollment->enrolled_at->format('M d, Y') }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Course:</span>
                    <span class="ml-2 font-medium">{{ $enrollment->course->title }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Last Accessed:</span>
                    <span
                        class="ml-2 font-medium">{{ $enrollment->last_accessed_at ? $enrollment->last_accessed_at->diffForHumans() : 'Never' }}</span>
                </div>
            </div>
        </div>

        <!-- Progress Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Progress</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $progressPercentage }}%</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Completed Lessons</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $completedLessons }}/{{ $totalLessons }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Quiz Average</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($quizStats['average_score'], 1) }}%
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Assignment Avg</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ number_format($assignmentStats['average_grade'], 1) }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quiz Attempts -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quiz Attempts</h3>
            @if($enrollment->quizAttempts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quiz</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($enrollment->quizAttempts as $attempt)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $attempt->quiz->title }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($attempt->score, 1) }}%</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                    @if($attempt->status === 'passed') bg-green-100 text-green-800
                                                    @else bg-red-100 text-red-800
                                                    @endif">
                                            {{ ucfirst($attempt->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $attempt->submitted_at?->format('M d, Y') ?? 'In Progress' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-sm">No quiz attempts yet.</p>
            @endif
        </div>

        <!-- Assignment Submissions -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Assignment Submissions</h3>
            @if($enrollment->assignmentSubmissions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assignment</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grade</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($enrollment->assignmentSubmissions as $submission)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $submission->assignment->title }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $submission->grade ? number_format($submission->grade, 1) . '%' : 'Not graded' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                    @if($submission->status === 'graded') bg-green-100 text-green-800
                                                    @elseif($submission->status === 'pending') bg-yellow-100 text-yellow-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                            {{ ucfirst($submission->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $submission->submitted_at?->format('M d, Y') ?? 'Not submitted' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-sm">No assignment submissions yet.</p>
            @endif
        </div>
    </div>
@endsection