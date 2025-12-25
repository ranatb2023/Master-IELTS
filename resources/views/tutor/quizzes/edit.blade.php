@extends('layouts.tutor')

@section('title', 'Edit Quiz')
@section('page-title', 'Edit Quiz')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Edit Quiz</h2>
            <p class="mt-1 text-sm text-gray-600">Update quiz settings and manage questions</p>
        </div>
        <a href="{{ route('tutor.courses.show', $quiz->topic->course_id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
            Back to Course
        </a>
    </div>

    <!-- Quiz Settings Form -->
    <form action="{{ route('tutor.quizzes.update', $quiz) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        <input type="hidden" name="topic_id" value="{{ $quiz->topic_id }}">

        <!-- Basic Information -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Quiz Title *</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $quiz->title) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $quiz->description) }}</textarea>
                </div>

                <div>
                    <label for="instructions" class="block text-sm font-medium text-gray-700">Instructions for Students</label>
                    <textarea name="instructions" id="instructions" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('instructions', $quiz->instructions) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Quiz Settings -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Quiz Settings</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="time_limit" class="block text-sm font-medium text-gray-700">Time Limit (minutes)</label>
                    <input type="number" name="time_limit" id="time_limit" value="{{ old('time_limit', $quiz->time_limit) }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">Leave blank for no time limit</p>
                </div>

                <div>
                    <label for="passing_score" class="block text-sm font-medium text-gray-700">Passing Score (%) *</label>
                    <input type="number" name="passing_score" id="passing_score" value="{{ old('passing_score', $quiz->passing_score) }}" min="0" max="100" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="max_attempts" class="block text-sm font-medium text-gray-700">Maximum Attempts</label>
                    <input type="number" name="max_attempts" id="max_attempts" value="{{ old('max_attempts', $quiz->max_attempts) }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">Leave blank for unlimited attempts</p>
                </div>

                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700">Display Order</label>
                    <input type="number" name="order" id="order" value="{{ old('order', $quiz->order) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <div class="mt-6 space-y-3">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="shuffle_questions" value="1" {{ old('shuffle_questions', $quiz->shuffle_questions) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Shuffle questions for each attempt</span>
                </label>
                <br>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="shuffle_answers" value="1" {{ old('shuffle_answers', $quiz->shuffle_answers) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Shuffle answer options</span>
                </label>
                <br>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="show_answers" value="1" {{ old('show_answers', $quiz->show_answers) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Show answers after submission</span>
                </label>
                <br>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="show_correct_answers" value="1" {{ old('show_correct_answers', $quiz->show_correct_answers) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Show correct answers after submission</span>
                </label>
                <br>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="require_passing" value="1" {{ old('require_passing', $quiz->require_passing) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Students must pass to continue</span>
                </label>
                <br>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="certificate_eligible" value="1" {{ old('certificate_eligible', $quiz->certificate_eligible) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Include in certificate eligibility</span>
                </label>
                <br>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published', $quiz->is_published) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Publish immediately</span>
                </label>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('tutor.courses.show', $quiz->topic->course_id) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                Update Quiz Settings
            </button>
        </div>
    </form>

    <!-- Questions Management -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900">Questions</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Total: {{ $quiz->questions->count() }} questions |
                    Total Points: {{ $quiz->questions->sum('points') }}
                </p>
            </div>
            <a href="{{ route('tutor.questions.create', ['quiz_id' => $quiz->id]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Question
            </a>
        </div>

        @if($quiz->questions->count() > 0)
        <div class="space-y-3">
            @foreach($quiz->questions->sortBy('order') as $index => $question)
            <div class="border border-gray-200 rounded-lg p-4 hover:border-gray-300 transition">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3">
                            <span class="flex items-center justify-center w-8 h-8 bg-indigo-100 text-indigo-600 rounded-full text-sm font-medium">
                                {{ $index + 1 }}
                            </span>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ Str::limit($question->question, 80) }}</p>
                                <div class="mt-1 flex items-center space-x-4 text-xs text-gray-500">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst(str_replace('_', ' ', $question->type)) }}
                                    </span>
                                    <span>{{ $question->points }} {{ Str::plural('point', $question->points) }}</span>
                                    @if($question->difficulty)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        {{ $question->difficulty === 'easy' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $question->difficulty === 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $question->difficulty === 'hard' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($question->difficulty) }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 ml-4">
                        <a href="{{ route('tutor.questions.edit', $question) }}" class="inline-flex items-center px-3 py-1 bg-white border border-gray-300 rounded-md text-xs font-medium text-gray-700 hover:bg-gray-50">
                            Edit
                        </a>
                        <form action="{{ route('tutor.questions.destroy', $question) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this question?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-50 border border-red-200 rounded-md text-xs font-medium text-red-700 hover:bg-red-100">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No questions yet</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by adding your first question.</p>
            <div class="mt-6">
                <a href="{{ route('tutor.questions.create', ['quiz_id' => $quiz->id]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Add Question
                </a>
            </div>
        </div>
        @endif
    </div>

    <!-- Delete Quiz -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-start justify-between">
            <div>
                <h3 class="text-lg font-medium text-red-900">Delete Quiz</h3>
                <p class="mt-1 text-sm text-gray-600">Permanently delete this quiz and all its questions. This action cannot be undone.</p>
            </div>
            <form action="{{ route('tutor.quizzes.destroy', $quiz) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this quiz? All questions will be deleted as well.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                    Delete Quiz
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
