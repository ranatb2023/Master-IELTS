@extends('layouts.admin')

@section('title', 'Question Details')
@section('page-title', 'Question Details')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-start">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Question Details</h2>
            <p class="mt-1 text-sm text-gray-600">
                Quiz: <a href="{{ route('admin.quizzes.show', $question->quiz) }}" class="text-indigo-600 hover:text-indigo-900">{{ $question->quiz->title }}</a>
            </p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.questions.edit', $question) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Question
            </a>
            <a href="{{ route('admin.quizzes.show', $question->quiz) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Quiz
            </a>
        </div>
    </div>

    <!-- Question Metadata -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <svg class="h-6 w-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <div>
                    <p class="text-xs text-gray-500">Type</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $question->questionType->name ?? 'Unknown' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <svg class="h-6 w-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                <div>
                    <p class="text-xs text-gray-500">Points</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $question->points }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <svg class="h-6 w-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <div>
                    <p class="text-xs text-gray-500">Difficulty</p>
                    <p class="text-sm font-semibold text-gray-900 capitalize">{{ $question->difficulty ?? 'Medium' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <svg class="h-6 w-6 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"/>
                </svg>
                <div>
                    <p class="text-xs text-gray-500">Order</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $question->order }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Question Content -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-blue-50">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Question
            </h3>
        </div>
        <div class="px-6 py-5">
            <div class="prose max-w-none">
                {!! $question->question !!}
            </div>

            @if($question->description)
                <div class="mt-4 p-4 bg-blue-50 border-l-4 border-blue-400 rounded">
                    <h4 class="text-sm font-medium text-blue-800 mb-2">Additional Context</h4>
                    <div class="prose max-w-none text-sm text-blue-700">
                        {!! $question->description !!}
                    </div>
                </div>
            @endif

            @if($question->media_type && $question->media_type !== 'none' && $question->media_url)
                <div class="mt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Media</h4>
                    @if($question->media_type === 'image')
                        <img src="{{ $question->media_url }}" alt="Question media" class="max-w-md rounded-lg border border-gray-300">
                    @elseif($question->media_type === 'audio')
                        <audio controls class="w-full max-w-md">
                            <source src="{{ $question->media_url }}" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>
                    @elseif($question->media_type === 'video')
                        <video controls class="w-full max-w-md rounded-lg border border-gray-300">
                            <source src="{{ $question->media_url }}" type="video/mp4">
                            Your browser does not support the video element.
                        </video>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Answer Options / Settings -->
    @if($question->questionType)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-green-50 to-teal-50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Answer & Settings
                </h3>
            </div>
            <div class="px-6 py-5">
                @php
                    $typeSlug = $question->questionType->slug;
                @endphp

                @if(in_array($typeSlug, ['mcq_single', 'mcq_multiple', 'true_false', 'image_choice']))
                    <!-- Multiple Choice / True-False / Image Options -->
                    <div class="space-y-3">
                        @foreach($question->options as $index => $option)
                            <div class="flex items-start p-4 rounded-lg border-2 {{ $option->is_correct ? 'border-green-500 bg-green-50' : 'border-gray-200 bg-white' }}">
                                <div class="flex-shrink-0 mt-1">
                                    @if($option->is_correct)
                                        <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="ml-3 flex-1">
                                    <span class="text-sm font-medium text-gray-700">Option {{ chr(65 + $index) }}</span>
                                    @if($typeSlug === 'image_choice')
                                        <div class="mt-2">
                                            <img src="{{ $option->option_text }}" alt="Option {{ chr(65 + $index) }}" class="max-w-xs rounded border border-gray-300">
                                        </div>
                                    @else
                                        <p class="mt-1 text-sm text-gray-900">{{ $option->option_text }}</p>
                                    @endif
                                    @if($option->is_correct)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-2">
                                            Correct Answer
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                @elseif($typeSlug === 'short_answer')
                    <!-- Short Answer -->
                    <div class="space-y-3">
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Accepted Answers:</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($question->settings['accepted_answers'] ?? [] as $answer)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-300">
                                        {{ $answer }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @if(isset($question->settings['case_sensitive']) && $question->settings['case_sensitive'])
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                Case sensitive matching enabled
                            </div>
                        @endif
                    </div>

                @elseif($typeSlug === 'fill_blanks')
                    <!-- Fill in the Blanks -->
                    <div class="space-y-4">
                        @foreach($question->settings['blanks'] ?? [] as $index => $blank)
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Blank {{ $index + 1 }} <code class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">@{{blank_{{ $index + 1 }}}}</code></h4>
                                <div class="flex flex-wrap gap-2">
                                    @php
                                        $answers = is_array($blank) ? $blank : [$blank];
                                        if (isset($blank['answers'])) {
                                            $answers = is_array($blank['answers']) ? $blank['answers'] : [$blank['answers']];
                                        }
                                    @endphp
                                    @foreach($answers as $answer)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 border border-blue-300">
                                            {{ $answer }}
                                        </span>
                                    @endforeach
                                </div>
                                @if(isset($blank['case_sensitive']) && $blank['case_sensitive'])
                                    <p class="mt-2 text-xs text-gray-600">Case sensitive</p>
                                @endif
                            </div>
                        @endforeach
                    </div>

                @elseif($typeSlug === 'matching')
                    <!-- Matching Pairs -->
                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-4 mb-2 text-xs font-medium text-gray-600">
                            <div>Left Column</div>
                            <div>Right Column (matches left)</div>
                        </div>
                        @foreach($question->settings['pairs'] ?? [] as $index => $pair)
                            <div class="grid grid-cols-2 gap-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-center">
                                    <span class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-full bg-indigo-100 text-indigo-800 font-semibold text-sm mr-3">
                                        {{ $index + 1 }}
                                    </span>
                                    <p class="text-sm text-gray-900">{{ $pair['left'] ?? '' }}</p>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                    <p class="text-sm text-gray-900">{{ $pair['right'] ?? '' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                @elseif($typeSlug === 'essay')
                    <!-- Essay Settings -->
                    <dl class="grid grid-cols-1 gap-4">
                        @if(isset($question->settings['min_words']))
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">Minimum Words</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $question->settings['min_words'] }}</dd>
                            </div>
                        @endif
                        @if(isset($question->settings['max_words']))
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">Maximum Words</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $question->settings['max_words'] }}</dd>
                            </div>
                        @endif
                        @if(isset($question->settings['sample_answer']))
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">Sample Answer</dt>
                                <dd class="mt-1 text-sm text-gray-900 prose max-w-none">{{ $question->settings['sample_answer'] }}</dd>
                            </div>
                        @endif
                        @if(isset($question->settings['grading_rubric']))
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <dt class="text-sm font-medium text-gray-500">Grading Rubric</dt>
                                <dd class="mt-1 text-sm text-gray-900 prose max-w-none">{{ $question->settings['grading_rubric'] }}</dd>
                            </div>
                        @endif
                    </dl>
                @endif

                @if($question->requiresManualGrading())
                    <div class="mt-4 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                        <div class="flex">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <p class="ml-3 text-sm text-yellow-700">
                                This question requires manual grading by an instructor.
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Explanation -->
    @if($question->explanation)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-pink-50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    Explanation
                </h3>
            </div>
            <div class="px-6 py-5">
                <div class="prose max-w-none">
                    {!! $question->explanation !!}
                </div>
            </div>
        </div>
    @endif

    <!-- Actions -->
    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
        <form action="{{ route('admin.questions.destroy', $question) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this question? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Delete Question
            </button>
        </form>

        <div class="flex space-x-3">
            <a href="{{ route('admin.questions.create', ['quiz_id' => $question->quiz_id]) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Another Question
            </a>
        </div>
    </div>
</div>
@endsection
