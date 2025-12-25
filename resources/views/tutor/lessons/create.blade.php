@extends('layouts.tutor')

@section('title', 'Create Lesson')
@section('page-title', 'Create Lesson')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Create New Lesson</h2>
            <p class="mt-1 text-sm text-gray-600">Add a new lesson to your topic</p>
        </div>
        <a href="{{ route('tutor.courses.show', request('course_id', session('course_id'))) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Course
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('tutor.lessons.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="{ contentType: 'video' }">
        @csrf
        <input type="hidden" name="topic_id" value="{{ request('topic_id') }}">

        <!-- Basic Information -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
            <div class="space-y-6">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Lesson Title *</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required autofocus class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Brief overview of what this lesson covers</p>
                </div>

                <!-- Duration and Order -->
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="duration_minutes" class="block text-sm font-medium text-gray-700">Duration (minutes)</label>
                        <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', 15) }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="order" class="block text-sm font-medium text-gray-700">Display Order</label>
                        <input type="number" name="order" id="order" value="{{ old('order', 1) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <!-- Settings -->
                <div class="space-y-3">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_preview" value="1" {{ old('is_preview') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Allow preview (students can view before enrolling)</span>
                    </label>
                    <br>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Publish this lesson immediately</span>
                    </label>
                    <br>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="requires_previous_completion" value="1" {{ old('requires_previous_completion') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Require previous lesson completion</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Content Type Selection -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Content Type</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <!-- Video -->
                <label class="relative flex flex-col items-center p-4 border-2 rounded-lg cursor-pointer transition" :class="contentType === 'video' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-300 hover:border-gray-400'">
                    <input type="radio" name="content_type" value="video" x-model="contentType" class="sr-only">
                    <svg class="w-8 h-8 mb-2" :class="contentType === 'video' ? 'text-indigo-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-sm font-medium" :class="contentType === 'video' ? 'text-indigo-900' : 'text-gray-900'">Video</span>
                </label>

                <!-- Text -->
                <label class="relative flex flex-col items-center p-4 border-2 rounded-lg cursor-pointer transition" :class="contentType === 'text' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-300 hover:border-gray-400'">
                    <input type="radio" name="content_type" value="text" x-model="contentType" class="sr-only">
                    <svg class="w-8 h-8 mb-2" :class="contentType === 'text' ? 'text-indigo-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="text-sm font-medium" :class="contentType === 'text' ? 'text-indigo-900' : 'text-gray-900'">Text</span>
                </label>

                <!-- Document -->
                <label class="relative flex flex-col items-center p-4 border-2 rounded-lg cursor-pointer transition" :class="contentType === 'document' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-300 hover:border-gray-400'">
                    <input type="radio" name="content_type" value="document" x-model="contentType" class="sr-only">
                    <svg class="w-8 h-8 mb-2" :class="contentType === 'document' ? 'text-indigo-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-sm font-medium" :class="contentType === 'document' ? 'text-indigo-900' : 'text-gray-900'">Document</span>
                </label>

                <!-- Audio -->
                <label class="relative flex flex-col items-center p-4 border-2 rounded-lg cursor-pointer transition" :class="contentType === 'audio' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-300 hover:border-gray-400'">
                    <input type="radio" name="content_type" value="audio" x-model="contentType" class="sr-only">
                    <svg class="w-8 h-8 mb-2" :class="contentType === 'audio' ? 'text-indigo-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                    </svg>
                    <span class="text-sm font-medium" :class="contentType === 'audio' ? 'text-indigo-900' : 'text-gray-900'">Audio</span>
                </label>

                <!-- Presentation -->
                <label class="relative flex flex-col items-center p-4 border-2 rounded-lg cursor-pointer transition" :class="contentType === 'presentation' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-300 hover:border-gray-400'">
                    <input type="radio" name="content_type" value="presentation" x-model="contentType" class="sr-only">
                    <svg class="w-8 h-8 mb-2" :class="contentType === 'presentation' ? 'text-indigo-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                    </svg>
                    <span class="text-sm font-medium" :class="contentType === 'presentation' ? 'text-indigo-900' : 'text-gray-900'">Slides</span>
                </label>

                <!-- Embed -->
                <label class="relative flex flex-col items-center p-4 border-2 rounded-lg cursor-pointer transition" :class="contentType === 'embed' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-300 hover:border-gray-400'">
                    <input type="radio" name="content_type" value="embed" x-model="contentType" class="sr-only">
                    <svg class="w-8 h-8 mb-2" :class="contentType === 'embed' ? 'text-indigo-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                    </svg>
                    <span class="text-sm font-medium" :class="contentType === 'embed' ? 'text-indigo-900' : 'text-gray-900'">Embed</span>
                </label>
            </div>
        </div>

        <!-- Content Details -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Content Details</h3>

            <!-- Video Content -->
            <div x-show="contentType === 'video'" class="space-y-4">
                <div>
                    <label for="video_url" class="block text-sm font-medium text-gray-700">Video URL or Vimeo ID *</label>
                    <input type="text" name="content[url]" id="video_url" value="{{ old('content.url') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">Enter Vimeo video ID or full video URL</p>
                </div>
                <div>
                    <label for="video_transcript" class="block text-sm font-medium text-gray-700">Transcript (Optional)</label>
                    <textarea name="content[transcript]" id="video_transcript" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('content.transcript') }}</textarea>
                </div>
            </div>

            <!-- Text Content -->
            <div x-show="contentType === 'text'" class="space-y-4">
                <div>
                    <label for="text_body" class="block text-sm font-medium text-gray-700">Content *</label>
                    <textarea name="content[body]" id="text_body" rows="12" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('content.body') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">You can use HTML formatting</p>
                </div>
                <div>
                    <label for="reading_time" class="block text-sm font-medium text-gray-700">Reading Time (minutes)</label>
                    <input type="number" name="content[reading_time]" id="reading_time" value="{{ old('content.reading_time', 5) }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Document Content -->
            <div x-show="contentType === 'document'" class="space-y-4">
                <div>
                    <label for="document_file" class="block text-sm font-medium text-gray-700">Upload Document *</label>
                    <input type="file" name="content[file]" id="document_file" accept=".pdf,.doc,.docx,.ppt,.pptx" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="mt-1 text-xs text-gray-500">Accepted formats: PDF, DOC, DOCX, PPT, PPTX (Max 50MB)</p>
                </div>
                <div>
                    <label for="document_pages" class="block text-sm font-medium text-gray-700">Number of Pages</label>
                    <input type="number" name="content[pages]" id="document_pages" value="{{ old('content.pages') }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Audio Content -->
            <div x-show="contentType === 'audio'" class="space-y-4">
                <div>
                    <label for="audio_file" class="block text-sm font-medium text-gray-700">Upload Audio File *</label>
                    <input type="file" name="content[file]" id="audio_file" accept=".mp3,.wav,.m4a" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="mt-1 text-xs text-gray-500">Accepted formats: MP3, WAV, M4A (Max 100MB)</p>
                </div>
                <div>
                    <label for="audio_transcript" class="block text-sm font-medium text-gray-700">Transcript (Optional)</label>
                    <textarea name="content[transcript]" id="audio_transcript" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('content.transcript') }}</textarea>
                </div>
            </div>

            <!-- Presentation Content -->
            <div x-show="contentType === 'presentation'" class="space-y-4">
                <div>
                    <label for="presentation_file" class="block text-sm font-medium text-gray-700">Upload Presentation *</label>
                    <input type="file" name="content[file]" id="presentation_file" accept=".ppt,.pptx,.key" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="mt-1 text-xs text-gray-500">Accepted formats: PPT, PPTX, KEY (Max 100MB)</p>
                </div>
                <div>
                    <label for="presentation_slides" class="block text-sm font-medium text-gray-700">Number of Slides</label>
                    <input type="number" name="content[slides]" id="presentation_slides" value="{{ old('content.slides') }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Embed Content -->
            <div x-show="contentType === 'embed'" class="space-y-4">
                <div>
                    <label for="embed_provider" class="block text-sm font-medium text-gray-700">Provider</label>
                    <select name="content[provider]" id="embed_provider" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="youtube">YouTube</option>
                        <option value="vimeo">Vimeo</option>
                        <option value="google_docs">Google Docs</option>
                        <option value="codepen">CodePen</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label for="embed_url" class="block text-sm font-medium text-gray-700">Embed URL *</label>
                    <input type="url" name="content[embed_url]" id="embed_url" value="{{ old('content.embed_url') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">Paste the embed URL or iframe src</p>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('tutor.courses.show', request('course_id', session('course_id'))) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                Create Lesson
            </button>
        </div>
    </form>
</div>
@endsection
