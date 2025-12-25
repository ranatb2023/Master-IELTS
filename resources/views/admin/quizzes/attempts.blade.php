@extends('layouts.admin')

@section('title', 'Quiz Attempts - ' . $quiz->title)
@section('page-title', 'Quiz Attempts: ' . $quiz->title)

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Actions -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.quizzes.show', $quiz) }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Quiz
            </a>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.quizzes.show', $quiz) }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                View Quiz Details
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500 mb-1">Total Attempts</div>
            <div class="text-3xl font-bold text-gray-900">{{ $attempts->total() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500 mb-1">Average Score</div>
            <div class="text-3xl font-bold text-blue-600">{{ number_format($stats['average_score'], 1) }}%</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500 mb-1">Pass Rate</div>
            <div class="text-3xl font-bold text-green-600">{{ number_format($stats['pass_rate'], 1) }}%</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500 mb-1">Needs Grading</div>
            <div class="text-3xl font-bold text-orange-600">{{ $stats['needs_grading'] }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Filter Attempts</h3>
        </div>
        <form method="GET" action="{{ route('admin.quizzes.attempts', $quiz) }}" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Statuses</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="graded" {{ request('status') === 'graded' ? 'selected' : '' }}>Graded</option>
                        <option value="abandoned" {{ request('status') === 'abandoned' ? 'selected' : '' }}>Abandoned</option>
                    </select>
                </div>

                <!-- Passed Filter -->
                <div>
                    <label for="passed" class="block text-sm font-medium text-gray-700 mb-1">Result</label>
                    <select name="passed" id="passed" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Results</option>
                        <option value="1" {{ request('passed') === '1' ? 'selected' : '' }}>Passed</option>
                        <option value="0" {{ request('passed') === '0' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>

                <!-- Student Search -->
                <div>
                    <label for="student" class="block text-sm font-medium text-gray-700 mb-1">Student</label>
                    <input type="text" name="student" id="student" value="{{ request('student') }}" placeholder="Search by name or email" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <!-- Manual Grading Filter -->
                <div>
                    <label for="needs_grading" class="block text-sm font-medium text-gray-700 mb-1">Grading</label>
                    <select name="needs_grading" id="needs_grading" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All</option>
                        <option value="1" {{ request('needs_grading') === '1' ? 'selected' : '' }}>Needs Manual Grading</option>
                        <option value="0" {{ request('needs_grading') === '0' ? 'selected' : '' }}>Auto-Graded</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.quizzes.attempts', $quiz) }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                    Clear Filters
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Attempts List -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">All Attempts</h3>
        </div>

        @if($attempts->isEmpty())
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No attempts found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if(request()->hasAny(['status', 'passed', 'student', 'needs_grading']))
                        Try adjusting your filters.
                    @else
                        No students have attempted this quiz yet.
                    @endif
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Student
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Attempt #
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Score
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Result
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Time Taken
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Submitted At
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($attempts as $attempt)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-600">
                                                    {{ strtoupper(substr($attempt->user->name, 0, 2)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $attempt->user->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $attempt->user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">{{ $attempt->attempt_number }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'in_progress' => 'bg-blue-100 text-blue-800',
                                            'submitted' => 'bg-yellow-100 text-yellow-800',
                                            'graded' => 'bg-green-100 text-green-800',
                                            'abandoned' => 'bg-gray-100 text-gray-800',
                                        ];
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$attempt->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                                    </span>
                                    @if($attempt->requires_manual_grading && $attempt->status !== 'graded')
                                        <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                            Needs Grading
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($attempt->status === 'in_progress')
                                        <span class="text-sm text-gray-500">-</span>
                                    @else
                                        <div class="text-sm text-gray-900">
                                            {{ number_format($attempt->score, 2) }} / {{ number_format($attempt->total_points, 2) }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ number_format($attempt->percentage, 1) }}%
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($attempt->status === 'in_progress')
                                        <span class="text-sm text-gray-500">-</span>
                                    @elseif($attempt->passed)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Passed
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Failed
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($attempt->time_taken)
                                        {{ gmdate('H:i:s', $attempt->time_taken) }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($attempt->submitted_at)
                                        {{ $attempt->submitted_at->format('M d, Y H:i') }}
                                    @else
                                        <span class="text-gray-400">Not submitted</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('admin.quiz-attempts.show', $attempt) }}" class="text-indigo-600 hover:text-indigo-900" title="View Details">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        @if($attempt->requires_manual_grading && $attempt->status !== 'graded')
                                            <a href="{{ route('admin.quiz-attempts.grade', $attempt) }}" class="text-orange-600 hover:text-orange-900" title="Grade Attempt">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $attempts->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
