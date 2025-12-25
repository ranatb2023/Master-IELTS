@extends('layouts.admin')

@section('title', 'Grade Quiz Attempt')
@section('page-title', 'Grade Quiz Attempt')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.quizzes.attempts', $quizAttempt->quiz) }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Attempts
            </a>
        </div>
    </div>

    <!-- Alert -->
    <div class="bg-orange-50 border-l-4 border-orange-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-orange-700">
                    This quiz attempt requires manual grading. Review each answer and assign points accordingly.
                </p>
            </div>
        </div>
    </div>

    <!-- Student Info Card -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Student Information</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <div class="text-sm font-medium text-gray-500 mb-1">Student</div>
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-600">
                                    {{ strtoupper(substr($quizAttempt->user->name, 0, 2)) }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900">{{ $quizAttempt->user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $quizAttempt->user->email }}</div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500 mb-1">Quiz</div>
                    <div class="text-sm text-gray-900">{{ $quizAttempt->quiz->title }}</div>
                    <div class="text-xs text-gray-500">Attempt #{{ $quizAttempt->attempt_number }}</div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500 mb-1">Submitted At</div>
                    <div class="text-sm text-gray-900">{{ $quizAttempt->submitted_at ? $quizAttempt->submitted_at->format('M d, Y H:i') : '-' }}</div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500 mb-1">Time Taken</div>
                    <div class="text-sm text-gray-900">{{ $quizAttempt->time_taken ? gmdate('H:i:s', $quizAttempt->time_taken) : '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grading Form -->
    <form action="{{ route('admin.quiz-attempts.submit-grade', $quizAttempt) }}" method="POST">
        @csrf

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Grade Questions</h3>
                <p class="mt-1 text-sm text-gray-600">Assign points to each question that requires manual grading.</p>
            </div>

            <div class="divide-y divide-gray-200">
                @php
                    $autoGradedTotal = 0;
                    $manualGradedTotal = 0;
                @endphp

                @forelse($quizAttempt->quiz->questions as $index => $question)
                    @php
                        $answer = $answersGrouped[$question->id] ?? null;
                        $userAnswer = $answer['answer'] ?? null;
                        $requiresManualGrading = $question->questionType->scoring_strategy === 'manual';

                        if (!$requiresManualGrading) {
                            $autoGradedTotal += $answer['awarded_points'] ?? 0;
                        }
                    @endphp

                    <div class="p-6 {{ $requiresManualGrading ? 'bg-orange-50' : '' }}">
                        <!-- Question Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <span class="flex-shrink-0 w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-sm font-medium text-gray-700">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        {{ $question->questionType->name }}
                                    </span>
                                    @if($requiresManualGrading)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800">
                                            Manual Grading Required
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            Auto-Graded
                                        </span>
                                    @endif
                                </div>
                                <div class="prose max-w-none">
                                    {!! $question->question !!}
                                </div>
                            </div>
                            <div class="ml-4 flex-shrink-0 text-right">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ number_format($question->points, 2) }} points
                                </div>
                            </div>
                        </div>

                        <!-- User Answer -->
                        <div class="mt-4 p-4 bg-white rounded-lg border border-gray-300">
                            <div class="text-sm font-medium text-gray-700 mb-2">Student's Answer:</div>

                            @if($question->questionType->slug === 'essay')
                                <div class="prose max-w-none text-sm text-gray-900 whitespace-pre-wrap">{{ $userAnswer }}</div>
                                @if(isset($answer['word_count']))
                                    <div class="mt-2 text-xs text-gray-500">Word count: {{ $answer['word_count'] }}</div>
                                @endif

                            @elseif($question->questionType->slug === 'short_answer')
                                <div class="text-sm text-gray-900 font-medium">{{ $userAnswer }}</div>

                            @else
                                <div class="text-sm text-gray-900">
                                    {{ is_array($userAnswer) ? json_encode($userAnswer, JSON_PRETTY_PRINT) : $userAnswer }}
                                </div>
                            @endif
                        </div>

                        <!-- Sample Answer / Rubric -->
                        @if($question->settings && isset($question->settings['sample_answer']))
                            <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="text-sm font-medium text-blue-900 mb-1">Sample Answer:</div>
                                <div class="text-sm text-blue-800 whitespace-pre-wrap">{{ $question->settings['sample_answer'] }}</div>
                            </div>
                        @endif

                        @if($question->settings && isset($question->settings['grading_rubric']))
                            <div class="mt-4 p-4 bg-purple-50 rounded-lg border border-purple-200">
                                <div class="text-sm font-medium text-purple-900 mb-1">Grading Rubric:</div>
                                <div class="text-sm text-purple-800 whitespace-pre-wrap">{{ $question->settings['grading_rubric'] }}</div>
                            </div>
                        @endif

                        <!-- Grading Input -->
                        @if($requiresManualGrading)
                            <div class="mt-6 pt-6 border-t border-gray-300">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="score_{{ $question->id }}" class="block text-sm font-medium text-gray-700 mb-1">
                                            Awarded Points <span class="text-red-500">*</span>
                                        </label>
                                        <div class="flex items-center space-x-2">
                                            <input
                                                type="number"
                                                name="scores[{{ $question->id }}]"
                                                id="score_{{ $question->id }}"
                                                min="0"
                                                max="{{ $question->points }}"
                                                step="0.01"
                                                value="{{ old('scores.' . $question->id, 0) }}"
                                                required
                                                class="block w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                            <span class="text-sm text-gray-500">/ {{ number_format($question->points, 2) }}</span>
                                        </div>
                                        @error('scores.' . $question->id)
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="feedback_{{ $question->id }}" class="block text-sm font-medium text-gray-700 mb-1">
                                            Feedback (Optional)
                                        </label>
                                        <textarea
                                            name="feedback[{{ $question->id }}]"
                                            id="feedback_{{ $question->id }}"
                                            rows="3"
                                            placeholder="Provide feedback to the student..."
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        >{{ old('feedback.' . $question->id) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mt-4 p-3 bg-green-50 rounded-lg border border-green-200">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <div>
                                        <div class="text-sm font-medium text-green-800">Auto-Graded</div>
                                        <div class="text-sm text-green-700">
                                            Awarded: {{ number_format($answer['awarded_points'] ?? 0, 2) }} / {{ number_format($question->points, 2) }} points
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500">
                        No questions found in this quiz.
                    </div>
                @endforelse
            </div>

            <!-- Submit Actions -->
            <div class="p-6 bg-gray-50 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <p><strong>Auto-graded points:</strong> {{ number_format($autoGradedTotal, 2) }}</p>
                        <p class="mt-1 text-xs text-gray-500">Manual grading points will be added to this total.</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('admin.quizzes.attempts', $quizAttempt->quiz) }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Submit Grading
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
