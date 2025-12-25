@extends('layouts.tutor')

@section('title', 'Quiz Attempt Details')
@section('page-title', 'Quiz Attempt - ' . $quizAttempt->quiz->title)

@section('content')
    <div class="space-y-6">
        <!-- Back Button -->
        <div>
            <a href="{{ route('tutor.quiz-attempts.index') }}" class="text-indigo-600 hover:text-indigo-900">
                ← Back to All Attempts
            </a>
        </div>

        <!-- Attempt Info -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div>
                    <p class="text-sm text-gray-500">Student</p>
                    <p class="text-lg font-semibold">{{ $quizAttempt->user->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Score</p>
                    <p class="text-lg font-semibold {{ $quizAttempt->score >= 70 ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($quizAttempt->score, 1) }}%
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                        @if($quizAttempt->status === 'passed') bg-green-100 text-green-800
                        @elseif($quizAttempt->status === 'failed') bg-red-100 text-red-800
                        @else bg-yellow-100 text-yellow-800
                        @endif">
                        {{ ucfirst($quizAttempt->status) }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Submitted</p>
                    <p class="text-lg font-semibold">
                        {{ $quizAttempt->submitted_at ? $quizAttempt->submitted_at->format('M d, Y H:i') : 'In Progress' }}
                    </p>
                </div>
            </div>

            @if($quizAttempt->feedback)
                <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm font-medium text-blue-900">Tutor Feedback:</p>
                    <p class="mt-2 text-sm text-blue-800">{{ $quizAttempt->feedback }}</p>
                </div>
            @endif
        </div>

        <!-- Answers Review -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Answers</h3>
            <div class="space-y-6">
                @foreach($quizAttempt->quiz->questions as $index => $question)
                    @php
                        $answer = $quizAttempt->answers->where('question_id', $question->id)->first();
                        $isCorrect = $answer && $answer->is_correct;
                    @endphp
                    <div
                        class="border rounded-lg p-4 {{ $isCorrect ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
                        <div class="flex items-start justify-between mb-2">
                            <h4 class="text-sm font-medium text-gray-900">Question {{ $index + 1 }}</h4>
                            @if($isCorrect)
                                <span
                                    class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">Correct</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full">Incorrect</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-700 mb-3">{{ $question->question_text }}</p>

                        @if($answer)
                            <div class="mt-2">
                                <p class="text-sm text-gray-600">Student's Answer: <span
                                        class="font-medium text-gray-900">{{ $answer->answer_text ?? 'No answer' }}</span></p>
                                @if(!$isCorrect && $question->correct_answer)
                                    <p class="text-sm text-gray-600 mt-1">Correct Answer: <span
                                            class="font-medium text-green-700">{{ $question->correct_answer }}</span></p>
                                @endif
                            </div>
                        @endif

                        @if($question->explanation)
                            <div class="mt-3 p-3 bg-white bg-opacity-50 rounded">
                                <p class="text-xs text-gray-600"><strong>Explanation:</strong> {{ $question->explanation }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Manual Grading (if needed) -->
        @if($quizAttempt->status !== 'graded' && $quizAttempt->submitted_at)
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Manual Grading</h3>
                <form method="POST" action="{{ route('tutor.quiz-attempts.grade', $quizAttempt) }}">
                    @csrf
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Score (%)</label>
                            <input type="number" name="score" min="0" max="100" step="0.1" value="{{ $quizAttempt->score }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Feedback (Optional)</label>
                        <textarea name="feedback" rows="3"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $quizAttempt->feedback }}</textarea>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Update Grade
                    </button>
                </form>
            </div>
        @endif
    </div>
@endsection