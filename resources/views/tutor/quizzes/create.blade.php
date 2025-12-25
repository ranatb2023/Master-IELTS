@extends('layouts.tutor')

@section('title', 'Create Quiz')
@section('page-title', 'Create Quiz')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Create New Quiz</h2>
            <p class="mt-1 text-sm text-gray-600">Create a quiz to test student knowledge</p>
        </div>
        <a href="{{ route('tutor.courses.show', request('course_id', session('course_id'))) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
            Back to Course
        </a>
    </div>

    <form action="{{ route('tutor.quizzes.store') }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="topic_id" value="{{ request('topic_id') }}">

        <!-- Basic Information -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Quiz Title *</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label for="instructions" class="block text-sm font-medium text-gray-700">Instructions for Students</label>
                    <textarea name="instructions" id="instructions" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('instructions') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Quiz Settings -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Quiz Settings</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="time_limit" class="block text-sm font-medium text-gray-700">Time Limit (minutes)</label>
                    <input type="number" name="time_limit" id="time_limit" value="{{ old('time_limit') }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">Leave blank for no time limit</p>
                </div>

                <div>
                    <label for="passing_score" class="block text-sm font-medium text-gray-700">Passing Score (%) *</label>
                    <input type="number" name="passing_score" id="passing_score" value="{{ old('passing_score', 70) }}" min="0" max="100" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="max_attempts" class="block text-sm font-medium text-gray-700">Maximum Attempts</label>
                    <input type="number" name="max_attempts" id="max_attempts" value="{{ old('max_attempts') }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">Leave blank for unlimited attempts</p>
                </div>

                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700">Display Order</label>
                    <input type="number" name="order" id="order" value="{{ old('order', 1) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <div class="mt-6 space-y-3">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="shuffle_questions" value="1" {{ old('shuffle_questions') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Shuffle questions for each attempt</span>
                </label>
                <br>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="shuffle_answers" value="1" {{ old('shuffle_answers') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Shuffle answer options</span>
                </label>
                <br>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="show_answers" value="1" {{ old('show_answers', true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Show answers after submission</span>
                </label>
                <br>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="show_correct_answers" value="1" {{ old('show_correct_answers', true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Show correct answers after submission</span>
                </label>
                <br>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="require_passing" value="1" {{ old('require_passing') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Students must pass to continue</span>
                </label>
                <br>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="certificate_eligible" value="1" {{ old('certificate_eligible') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Include in certificate eligibility</span>
                </label>
                <br>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Publish immediately</span>
                </label>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('tutor.courses.show', request('course_id', session('course_id'))) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                Create Quiz & Add Questions
            </button>
        </div>
    </form>
</div>
@endsection
