@extends('layouts.tutor')

@section('title', 'Edit Topic')
@section('page-title', 'Edit Topic')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Edit Topic</h2>
            <p class="mt-1 text-sm text-gray-600">Update topic information</p>
        </div>
        <a href="{{ route('tutor.courses.show', $topic->course_id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Course
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('tutor.topics.update', $topic) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        <input type="hidden" name="course_id" value="{{ $topic->course_id }}">

        <div class="bg-white shadow rounded-lg p-6">
            <div class="space-y-6">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Topic Title *</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $topic->title) }}" required autofocus class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Give your topic a clear, descriptive name</p>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $topic->description) }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Briefly describe what students will learn in this topic</p>
                </div>

                <!-- Order -->
                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700">Display Order</label>
                    <input type="number" name="order" id="order" value="{{ old('order', $topic->order) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">Order in which this topic appears in the course (1 = first)</p>
                </div>

                <!-- Publish Status -->
                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', $topic->is_published) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Publish this topic</span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500">Students can only see published topics</p>
                </div>

                <!-- Content Summary -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Content Summary</h3>
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-2xl font-bold text-gray-900">{{ $topic->lessons->count() }}</p>
                            <p class="text-xs text-gray-600">Lessons</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-2xl font-bold text-gray-900">{{ $topic->quizzes->count() }}</p>
                            <p class="text-xs text-gray-600">Quizzes</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-2xl font-bold text-gray-900">{{ $topic->assignments->count() }}</p>
                            <p class="text-xs text-gray-600">Assignments</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-between">
            <form action="{{ route('tutor.topics.destroy', $topic) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this topic? This will also delete all lessons, quizzes, and assignments in this topic.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                    Delete Topic
                </button>
            </form>

            <div class="flex space-x-3">
                <a href="{{ route('tutor.courses.show', $topic->course_id) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Update Topic
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
