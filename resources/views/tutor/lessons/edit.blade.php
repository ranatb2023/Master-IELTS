@extends('layouts.tutor')

@section('title', 'Edit Lesson')
@section('page-title', 'Edit Lesson')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Edit Lesson</h2>
            <p class="mt-1 text-sm text-gray-600">Update lesson content and settings</p>
        </div>
        <a href="{{ route('tutor.courses.show', $lesson->topic->course_id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Course
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('tutor.lessons.update', $lesson) }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="{ contentType: '{{ old('content_type', $lesson->content_type) }}' }">
        @csrf
        @method('PUT')
        <input type="hidden" name="topic_id" value="{{ $lesson->topic_id }}">

        <!-- Basic Information -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
            <div class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Lesson Title *</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $lesson->title) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $lesson->description) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="duration_minutes" class="block text-sm font-medium text-gray-700">Duration (minutes)</label>
                        <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $lesson->duration_minutes) }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="order" class="block text-sm font-medium text-gray-700">Display Order</label>
                        <input type="number" name="order" id="order" value="{{ old('order', $lesson->order) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_preview" value="1" {{ old('is_preview', $lesson->is_preview) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Allow preview</span>
                    </label>
                    <br>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', $lesson->is_published) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Publish this lesson</span>
                    </label>
                    <br>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="requires_previous_completion" value="1" {{ old('requires_previous_completion', $lesson->requires_previous_completion) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Require previous lesson completion</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Current Content Type (Read-only display) -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Content Type</h3>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-700">Current content type: <span class="font-medium text-gray-900">{{ ucfirst($lesson->content_type) }}</span></p>
                <p class="text-xs text-gray-500 mt-1">To change the content type, you'll need to create a new lesson</p>
            </div>
            <input type="hidden" name="content_type" value="{{ $lesson->content_type }}">
        </div>

        <!-- Content Details (based on current type) -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Content</h3>

            @if($lesson->content_type === 'video')
            <div class="space-y-4">
                <div>
                    <label for="video_url" class="block text-sm font-medium text-gray-700">Video URL *</label>
                    <input type="text" name="content[url]" id="video_url" value="{{ old('content.url', $lesson->contentable->url ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="video_transcript" class="block text-sm font-medium text-gray-700">Transcript</label>
                    <textarea name="content[transcript]" id="video_transcript" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('content.transcript', $lesson->contentable->transcript ?? '') }}</textarea>
                </div>
            </div>
            @elseif($lesson->content_type === 'text')
            <div class="space-y-4">
                <div>
                    <label for="text_body" class="block text-sm font-medium text-gray-700">Content *</label>
                    <textarea name="content[body]" id="text_body" rows="12" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('content.body', $lesson->contentable->body ?? '') }}</textarea>
                </div>
                <div>
                    <label for="reading_time" class="block text-sm font-medium text-gray-700">Reading Time (minutes)</label>
                    <input type="number" name="content[reading_time]" id="reading_time" value="{{ old('content.reading_time', $lesson->contentable->reading_time ?? 5) }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            @elseif($lesson->content_type === 'document')
            <div class="space-y-4">
                @if($lesson->contentable && $lesson->contentable->file_path)
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-700">Current file: <span class="font-medium">{{ basename($lesson->contentable->file_path) }}</span></p>
                </div>
                @endif
                <div>
                    <label for="document_file" class="block text-sm font-medium text-gray-700">Upload New Document (Optional)</label>
                    <input type="file" name="content[file]" id="document_file" accept=".pdf,.doc,.docx,.ppt,.pptx" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
                <div>
                    <label for="document_pages" class="block text-sm font-medium text-gray-700">Number of Pages</label>
                    <input type="number" name="content[pages]" id="document_pages" value="{{ old('content.pages', $lesson->contentable->pages ?? '') }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            @elseif($lesson->content_type === 'embed')
            <div class="space-y-4">
                <div>
                    <label for="embed_provider" class="block text-sm font-medium text-gray-700">Provider</label>
                    <select name="content[provider]" id="embed_provider" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="youtube" {{ ($lesson->contentable->provider ?? '') == 'youtube' ? 'selected' : '' }}>YouTube</option>
                        <option value="vimeo" {{ ($lesson->contentable->provider ?? '') == 'vimeo' ? 'selected' : '' }}>Vimeo</option>
                        <option value="google_docs" {{ ($lesson->contentable->provider ?? '') == 'google_docs' ? 'selected' : '' }}>Google Docs</option>
                        <option value="other" {{ ($lesson->contentable->provider ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div>
                    <label for="embed_url" class="block text-sm font-medium text-gray-700">Embed URL *</label>
                    <input type="url" name="content[embed_url]" id="embed_url" value="{{ old('content.embed_url', $lesson->contentable->embed_url ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            @endif
        </div>

        <!-- Form Actions -->
        <div class="flex justify-between">
            <form action="{{ route('tutor.lessons.destroy', $lesson) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this lesson?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                    Delete Lesson
                </button>
            </form>

            <div class="flex space-x-3">
                <a href="{{ route('tutor.courses.show', $lesson->topic->course_id) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Update Lesson
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
