@extends('layouts.admin')

@section('title', 'View Quiz Attempt')
@section('page-title', 'Quiz Attempt Details')

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
        <div class="flex items-center space-x-3">
            @if($quizAttempt->requires_manual_grading && $quizAttempt->status !== 'graded')
                <a href="{{ route('admin.quiz-attempts.grade', $quizAttempt) }}" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700">
                    Grade Attempt
                </a>
            @endif
            @if(in_array($quizAttempt->status, ['submitted', 'graded']))
                <form action="{{ route('admin.quiz-attempts.reset', $quizAttempt) }}" method="POST" onsubmit="return confirm('Are you sure you want to reset this attempt? The student will be able to retake the quiz.');">
                    @csrf
                    <button type="submit" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Reset Attempt
                    </button>
                </form>
            @endif
            @if(!$quizAttempt->requires_manual_grading && $quizAttempt->status === 'graded')
                <form action="{{ route('admin.quiz-attempts.regrade', $quizAttempt) }}" method="POST" onsubmit="return confirm('Are you sure you want to regrade this attempt?');">
                    @csrf
                    <button type="submit" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Regrade
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Attempt Overview -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Attempt Overview</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Student Info -->
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

                <!-- Quiz Info -->
                <div>
                    <div class="text-sm font-medium text-gray-500 mb-1">Quiz</div>
                    <div class="text-sm text-gray-900">{{ $quizAttempt->quiz->title }}</div>
                    <div class="text-xs text-gray-500">Attempt #{{ $quizAttempt->attempt_number }}</div>
                </div>

                <!-- Status -->
                <div>
                    <div class="text-sm font-medium text-gray-500 mb-1">Status</div>
                    @php
                        $statusColors = [
                            'in_progress' => 'bg-blue-100 text-blue-800',
                            'submitted' => 'bg-yellow-100 text-yellow-800',
                            'graded' => 'bg-green-100 text-green-800',
                            'abandoned' => 'bg-gray-100 text-gray-800',
                        ];
                    @endphp
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$quizAttempt->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst(str_replace('_', ' ', $quizAttempt->status)) }}
                    </span>
                    @if($quizAttempt->requires_manual_grading && $quizAttempt->status !== 'graded')
                        <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                            Needs Grading
                        </span>
                    @endif
                </div>

                <!-- Score -->
                <div>
                    <div class="text-sm font-medium text-gray-500 mb-1">Score</div>
                    @if($quizAttempt->status === 'in_progress')
                        <div class="text-sm text-gray-500">Not submitted</div>
                    @else
                        @php
                            $earnedPoints = $quizAttempt->quizAnswers->sum('points_earned');
                        @endphp
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($quizAttempt->score, 1) }}%</div>
                        <div class="text-xs text-gray-500">
                            {{ number_format($earnedPoints, 2) }} / {{ number_format($quizAttempt->total_points, 2) }} points
                        </div>
                        @if($quizAttempt->passed)
                            <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Passed
                            </span>
                        @else
                            <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Failed
                            </span>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Additional Details -->
            <div class="mt-6 pt-6 border-t border-gray-200 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <div class="text-sm font-medium text-gray-500">Started At</div>
                    <div class="text-sm text-gray-900">{{ $quizAttempt->started_at ? $quizAttempt->started_at->format('M d, Y H:i:s') : '-' }}</div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">Submitted At</div>
                    <div class="text-sm text-gray-900">{{ $quizAttempt->submitted_at ? $quizAttempt->submitted_at->format('M d, Y H:i:s') : '-' }}</div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">Time Taken</div>
                    <div class="text-sm text-gray-900">{{ $quizAttempt->time_taken ? gmdate('H:i:s', $quizAttempt->time_taken) : '-' }}</div>
                </div>
                @if($quizAttempt->graded_at)
                    <div>
                        <div class="text-sm font-medium text-gray-500">Graded At</div>
                        <div class="text-sm text-gray-900">{{ $quizAttempt->graded_at->format('M d, Y H:i:s') }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">Graded By</div>
                        <div class="text-sm text-gray-900">{{ $quizAttempt->gradedBy?->name ?? 'System' }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Questions and Answers -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Questions and Answers</h3>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($quizAttempt->quiz->questions as $index => $question)
                @php
                    $answer = $answersGrouped[$question->id] ?? null;
                    $userAnswer = $answer['answer'] ?? null;
                    $awardedPoints = $answer['awarded_points'] ?? 0;
                    $feedback = $answer['grader_feedback'] ?? null;
                @endphp
                <div class="p-6">
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
                                @if($question->difficulty)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $question->difficulty === 'easy' ? 'bg-green-100 text-green-800' : ($question->difficulty === 'hard' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($question->difficulty) }}
                                    </span>
                                @endif
                            </div>
                            <div class="prose max-w-none">
                                {!! $question->question !!}
                            </div>
                            @if($question->description)
                                <div class="mt-2 text-sm text-gray-600">
                                    {!! $question->description !!}
                                </div>
                            @endif
                        </div>
                        <div class="ml-4 flex-shrink-0 text-right">
                            <div class="text-sm font-medium text-gray-900">
                                {{ number_format($awardedPoints, 2) }} / {{ number_format($question->points, 2) }}
                            </div>
                            <div class="text-xs text-gray-500">points</div>
                        </div>
                    </div>

                    <!-- Question Media -->
                    @if($question->media_type !== 'none' && $question->media_url)
                        <div class="mb-4">
                            @if($question->media_type === 'image')
                                <img src="{{ $question->media_url }}" alt="Question media" class="max-w-md rounded-lg border border-gray-200">
                            @elseif($question->media_type === 'audio')
                                <audio controls class="w-full max-w-md">
                                    <source src="{{ $question->media_url }}" type="audio/mpeg">
                                </audio>
                            @elseif($question->media_type === 'video')
                                <video controls class="w-full max-w-md rounded-lg border border-gray-200">
                                    <source src="{{ $question->media_url }}" type="video/mp4">
                                </video>
                            @endif
                        </div>
                    @endif

                    <!-- User Answer -->
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <div class="text-sm font-medium text-gray-700 mb-2">Student's Answer:</div>

                        @if($question->questionType->slug === 'true_false' || $question->questionType->slug === 'mcq_single')
                            @php
                                $selectedOption = $question->options->firstWhere('id', $userAnswer);
                                $correctOption = $question->options->firstWhere('is_correct', true);
                            @endphp
                            <div class="space-y-2">
                                @foreach($question->options as $option)
                                    <div class="flex items-center space-x-2">
                                        @if($option->id == $userAnswer)
                                            <span class="flex-shrink-0 w-5 h-5 rounded-full {{ $option->is_correct ? 'bg-green-500' : 'bg-red-500' }} flex items-center justify-center">
                                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </span>
                                        @elseif($option->is_correct)
                                            <span class="flex-shrink-0 w-5 h-5 rounded-full border-2 border-green-500 bg-white"></span>
                                        @else
                                            <span class="flex-shrink-0 w-5 h-5 rounded-full border-2 border-gray-300 bg-white"></span>
                                        @endif
                                        <span class="text-sm {{ $option->id == $userAnswer ? 'font-medium' : '' }} {{ $option->is_correct ? 'text-green-700' : 'text-gray-700' }}">
                                            {{ $option->option_text }}
                                        </span>
                                        @if($option->is_correct)
                                            <span class="text-xs text-green-600">(Correct)</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                        @elseif($question->questionType->slug === 'mcq_multiple')
                            @php
                                $selectedOptions = is_array($userAnswer) ? $userAnswer : [$userAnswer];
                                $correctOptionIds = $question->options->where('is_correct', true)->pluck('id')->toArray();
                            @endphp
                            <div class="space-y-2">
                                @foreach($question->options as $option)
                                    <div class="flex items-center space-x-2">
                                        @if(in_array($option->id, $selectedOptions))
                                            <span class="flex-shrink-0 w-5 h-5 rounded {{ $option->is_correct ? 'bg-green-500' : 'bg-red-500' }} flex items-center justify-center">
                                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </span>
                                        @elseif($option->is_correct)
                                            <span class="flex-shrink-0 w-5 h-5 rounded border-2 border-green-500 bg-white"></span>
                                        @else
                                            <span class="flex-shrink-0 w-5 h-5 rounded border-2 border-gray-300 bg-white"></span>
                                        @endif
                                        <span class="text-sm {{ in_array($option->id, $selectedOptions) ? 'font-medium' : '' }} {{ $option->is_correct ? 'text-green-700' : 'text-gray-700' }}">
                                            {{ $option->option_text }}
                                        </span>
                                        @if($option->is_correct)
                                            <span class="text-xs text-green-600">(Correct)</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                        @elseif($question->questionType->slug === 'essay')
                            <div class="prose max-w-none text-sm text-gray-900">
                                {!! nl2br(e($userAnswer)) !!}
                            </div>
                            @if(isset($answer['word_count']))
                                <div class="mt-2 text-xs text-gray-500">Word count: {{ $answer['word_count'] }}</div>
                            @endif

                        @elseif($question->questionType->slug === 'short_answer')
                            <div class="text-sm text-gray-900 font-medium">{{ $userAnswer }}</div>
                            @php
                                $settings = $question->settings ?? [];
                                $correctAnswers = $settings['accepted_answers'] ?? [];
                                // Ensure it's an array
                                if (is_string($correctAnswers)) {
                                    $correctAnswers = json_decode($correctAnswers, true) ?? [$correctAnswers];
                                }
                            @endphp
                            @if(!empty($correctAnswers) && is_array($correctAnswers))
                                <div class="mt-2 text-xs text-gray-600">
                                    Accepted answers: {{ implode(', ', $correctAnswers) }}
                                </div>
                            @endif

                        @else
                            <div class="text-sm text-gray-900">
                                {{ is_array($userAnswer) ? json_encode($userAnswer) : $userAnswer }}
                            </div>
                        @endif

                        @if($feedback)
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <div class="text-sm font-medium text-gray-700 mb-1">Grader Feedback:</div>
                                <div class="text-sm text-gray-600">{{ $feedback }}</div>
                            </div>
                        @endif
                    </div>

                    <!-- Explanation -->
                    @if($question->explanation && $quizAttempt->status === 'graded')
                        <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="text-sm font-medium text-blue-900 mb-1">Explanation:</div>
                            <div class="text-sm text-blue-800 prose max-w-none">
                                {!! $question->explanation !!}
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
    </div>
</div>
@endsection
