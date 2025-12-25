@extends('layouts.admin')

@section('title', 'Edit Lesson')
@section('page-title', 'Edit Lesson')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Edit Lesson</h2>
            <p class="mt-1 text-sm text-gray-600">Update lesson information</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.lessons.show', $lesson) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                View Lesson
            </a>
            <a href="{{ route('admin.lessons.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Lessons
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Upload Progress Overlay -->
    <div id="uploadOverlay" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl p-8 max-w-md w-full mx-4">
            <div class="text-center mb-6">
                <svg class="animate-spin h-12 w-12 text-indigo-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Updating Lesson</h3>
                <p class="text-sm text-gray-600">Please wait while we upload your files...</p>
            </div>

            <!-- Progress Bar -->
            <div class="mb-4">
                <div class="flex justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Upload Progress</span>
                    <span id="uploadPercent" class="text-sm font-medium text-indigo-600">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                    <div id="uploadProgress" class="bg-indigo-600 h-3 rounded-full transition-all duration-300 ease-out" style="width: 0%"></div>
                </div>
            </div>

            <!-- File Info -->
            <div id="uploadInfo" class="text-xs text-gray-500 text-center">
                <p id="uploadSpeed" class="mb-1">Calculating speed...</p>
                <p id="uploadSize" class="mb-1">Preparing upload...</p>
            </div>

            <p class="text-xs text-gray-500 text-center mt-4">
                <strong>Note:</strong> Large files may take several minutes. Please don't close this window.
            </p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg">
        <form id="lessonForm" action="{{ route('admin.lessons.update', $lesson) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="update_content" value="1">

            <!-- Topic Selection -->
            <div>
                <label for="topic_id" class="block text-sm font-medium text-gray-700">Topic <span class="text-red-500">*</span></label>
                <select name="topic_id" id="topic_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('topic_id') border-red-300 @enderror" required>
                    <option value="">Select a topic</option>
                    @foreach($topics as $topic)
                        <option value="{{ $topic->id }}" {{ old('topic_id', $lesson->topic_id) == $topic->id ? 'selected' : '' }}>
                            {{ $topic->course->title }} - {{ $topic->title }}
                        </option>
                    @endforeach
                </select>
                @error('topic_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Lesson Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title', $lesson->title) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-red-300 @enderror" required>
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Enter a clear, descriptive title for this lesson</p>
            </div>

            <!-- Description -->
            <x-quill-editor
                name="description"
                :value="old('description', $lesson->description)"
                label="Description"
                :required="false"
                height="300px"
                placeholder="Provide a detailed description of this lesson..."
            />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Duration -->
                <div>
                    <label for="duration_minutes" class="block text-sm font-medium text-gray-700">Duration (minutes)</label>
                    <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $lesson->duration_minutes) }}" min="0" max="9999" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('duration_minutes') border-red-300 @enderror">
                    @error('duration_minutes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Estimated time to complete this lesson</p>
                </div>

                <!-- Order -->
                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700">Display Order <span class="text-red-500">*</span></label>
                    <input type="number" name="order" id="order" value="{{ old('order', $lesson->order) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('order') border-red-300 @enderror" required>
                    @error('order')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Set the order in which this lesson should appear (0 = first)</p>
                </div>
            </div>

            <!-- Content Section -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Lesson Content</h3>

                <!-- Video Content Section -->
                @if($lesson->content_type === 'video')
                <div class="space-y-4 p-4 bg-gray-50 rounded-lg border border-gray-200" x-data="{ videoSource: '{{ old('video_source', ($lesson->contentable->source ?? 'url')) }}' }">
                    <h4 class="font-medium text-gray-900">Video Content</h4>

                    <!-- Video Source Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Video Source</label>
                        <div class="flex space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="video_source" value="url" x-model="videoSource" class="form-radio text-indigo-600">
                                <span class="ml-2 text-sm text-gray-700">URL (YouTube/Vimeo)</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="video_source" value="upload" x-model="videoSource" class="form-radio text-indigo-600">
                                <span class="ml-2 text-sm text-gray-700">Upload Video File</span>
                            </label>
                        </div>
                    </div>

                    <!-- Current Upload Info -->
                    @if($lesson->contentable && $lesson->contentable->source === 'upload' && $lesson->contentable->file_path)
                    <div class="mb-3 p-3 bg-white rounded border border-gray-200">
                        <p class="text-sm font-medium text-gray-700">Current Video:</p>
                        <p class="text-sm text-gray-600">{{ $lesson->contentable->file_name }}</p>
                        <p class="text-xs text-gray-500">
                            Type: {{ strtoupper($lesson->contentable->file_type ?? 'video') }} |
                            Size: {{ number_format(($lesson->contentable->file_size ?? 0) / 1024 / 1024, 2) }} MB
                            @if($lesson->contentable->duration_seconds)
                                | Duration: {{ gmdate('H:i:s', $lesson->contentable->duration_seconds) }}
                            @endif
                        </p>
                    </div>
                    @endif

                    <!-- URL Fields -->
                    <div x-show="videoSource === 'url'" class="space-y-4">
                        <div>
                            <label for="video_url" class="block text-sm font-medium text-gray-700">Video URL (YouTube/Vimeo) *</label>
                            <input type="url" name="video_url" id="video_url" value="{{ old('video_url', $lesson->contentable->url ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="https://youtube.com/watch?v=..." :required="videoSource === 'url'">
                            <p class="mt-1 text-sm text-gray-500">Enter a YouTube, Vimeo, or other video URL</p>
                        </div>
                        <div>
                            <label for="video_platform" class="block text-sm font-medium text-gray-700">Platform</label>
                            <select name="video_platform" id="video_platform" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="youtube" {{ old('video_platform', $lesson->contentable->platform ?? 'youtube') == 'youtube' ? 'selected' : '' }}>YouTube</option>
                                <option value="vimeo" {{ old('video_platform', $lesson->contentable->platform ?? '') == 'vimeo' ? 'selected' : '' }}>Vimeo</option>
                                <option value="other" {{ old('video_platform', $lesson->contentable->platform ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>

                    <!-- Upload Fields -->
                    <div x-show="videoSource === 'upload'" class="space-y-4">
                        <div>
                            <label for="video_file" class="block text-sm font-medium text-gray-700">
                                @if($lesson->contentable && $lesson->contentable->source === 'upload')
                                    Replace Video File
                                @else
                                    Video File <span class="text-red-500">*</span>
                                @endif
                            </label>
                            <input type="file" name="video_file" id="video_file" accept="video/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                            @if(!($lesson->contentable && $lesson->contentable->source === 'upload'))
                                :required="videoSource === 'upload'"
                            @endif>
                            <p class="mt-1 text-sm text-gray-500">Upload MP4, WebM, or OGG video (max 500MB)
                            @if($lesson->contentable && $lesson->contentable->source === 'upload')
                                - Leave empty to keep current file
                            @endif
                            </p>
                        </div>
                    </div>

                    <!-- Common Fields -->
                    <div>
                        <label for="video_transcript" class="block text-sm font-medium text-gray-700">Video Transcript</label>
                        <textarea name="video_transcript" id="video_transcript" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('video_transcript', $lesson->contentable->transcript ?? '') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Optional transcript or captions for the video</p>
                    </div>
                </div>
                @endif

                <!-- Text Content Section -->
                @if($lesson->content_type === 'text')
                <div class="space-y-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <h4 class="font-medium text-gray-900">Text Content</h4>
                    <x-quill-editor
                        name="text_body"
                        :value="old('text_body', $lesson->contentable->body ?? '')"
                        label="Text Body"
                        :required="true"
                        height="500px"
                        placeholder="Write your lesson content with rich formatting, images, videos, and links..."
                    />
                    <div>
                        <label for="text_reading_time" class="block text-sm font-medium text-gray-700">Reading Time (minutes)</label>
                        <input type="number" name="text_reading_time" id="text_reading_time" value="{{ old('text_reading_time', $lesson->contentable->reading_time ?? '') }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
                @endif

                <!-- Document Content Section -->
                @if($lesson->content_type === 'document')
                <div class="space-y-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <h4 class="font-medium text-gray-900">Document Content</h4>
                    @if($lesson->contentable && $lesson->contentable->file_path)
                    <div class="mb-3 p-3 bg-white rounded border border-gray-200">
                        <p class="text-sm font-medium text-gray-700">Current Document:</p>
                        <p class="text-sm text-gray-600">{{ $lesson->contentable->file_name }}</p>
                        <p class="text-xs text-gray-500">Type: {{ strtoupper($lesson->contentable->file_type) }}</p>
                    </div>
                    @endif
                    <div>
                        <label for="document_file" class="block text-sm font-medium text-gray-700">Replace Document</label>
                        <input type="file" name="document_file" id="document_file" accept=".pdf,.doc,.docx" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="mt-1 text-sm text-gray-500">Upload PDF, DOC, or DOCX (max 50MB) - Leave empty to keep current file</p>
                    </div>
                    <div>
                        <label for="document_file_size" class="block text-sm font-medium text-gray-700">File Size (MB)</label>
                        <input type="number" name="document_file_size" id="document_file_size" value="{{ old('document_file_size', $lesson->contentable->file_size ?? '') }}" step="0.1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Auto-calculated">
                    </div>
                </div>
                @endif

                <!-- Audio Content Section -->
                @if($lesson->content_type === 'audio')
                <div class="space-y-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <h4 class="font-medium text-gray-900">Audio Content</h4>
                    @if($lesson->contentable && $lesson->contentable->file_path)
                    <div class="mb-3 p-3 bg-white rounded border border-gray-200">
                        <p class="text-sm font-medium text-gray-700">Current Audio File:</p>
                        <p class="text-sm text-gray-600">{{ basename($lesson->contentable->file_path) }}</p>
                    </div>
                    @endif
                    <div>
                        <label for="audio_file" class="block text-sm font-medium text-gray-700">Replace Audio File</label>
                        <input type="file" name="audio_file" id="audio_file" accept=".mp3,.wav,.ogg,.m4a" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="mt-1 text-sm text-gray-500">Upload MP3, WAV, OGG, or M4A (max 100MB) - Leave empty to keep current file</p>
                    </div>
                    <div>
                        <label for="audio_transcript" class="block text-sm font-medium text-gray-700">Audio Transcript</label>
                        <textarea name="audio_transcript" id="audio_transcript" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('audio_transcript', $lesson->contentable->transcript ?? '') }}</textarea>
                    </div>
                </div>
                @endif

                <!-- Presentation Content Section -->
                @if($lesson->content_type === 'presentation')
                <div class="space-y-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <h4 class="font-medium text-gray-900">Presentation Content</h4>
                    @if($lesson->contentable && $lesson->contentable->file_path)
                    <div class="mb-3 p-3 bg-white rounded border border-gray-200">
                        <p class="text-sm font-medium text-gray-700">Current Presentation:</p>
                        <p class="text-sm text-gray-600">{{ basename($lesson->contentable->file_path) }}</p>
                    </div>
                    @endif
                    <div>
                        <label for="presentation_file" class="block text-sm font-medium text-gray-700">Replace Presentation File</label>
                        <input type="file" name="presentation_file" id="presentation_file" accept=".pdf,.ppt,.pptx" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="mt-1 text-sm text-gray-500">Upload PDF, PPT, or PPTX (max 100MB) - Leave empty to keep current file</p>
                    </div>
                    <div>
                        <label for="presentation_slides_count" class="block text-sm font-medium text-gray-700">Number of Slides</label>
                        <input type="number" name="presentation_slides_count" id="presentation_slides_count" value="{{ old('presentation_slides_count', $lesson->contentable->slides ?? '') }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
                @endif

                <!-- Embed Content Section -->
                @if($lesson->content_type === 'embed')
                <div class="space-y-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <h4 class="font-medium text-gray-900">Embed Content</h4>

                    <div>
                        <label for="embed_code" class="block text-sm font-medium text-gray-700">Embed Code (iframe, script)</label>
                        <textarea
                            name="embed_code"
                            id="embed_code"
                            rows="6"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-xs @error('embed_code') border-red-300 @enderror"
                            placeholder="<iframe src='https://...'></iframe>"
                        >{{ old('embed_code', $lesson->contentable->metadata['embed_code'] ?? '') }}</textarea>
                        @error('embed_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Paste the complete embed code from YouTube, Vimeo, or any other platform (e.g., &lt;iframe src="..."&gt;&lt;/iframe&gt;)</p>
                    </div>

                    <div class="border-t pt-4">
                        <p class="text-sm font-medium text-gray-700 mb-3">OR use a direct URL:</p>

                        <div class="space-y-3">
                            <div>
                                <label for="embed_url" class="block text-sm font-medium text-gray-700">Embed URL</label>
                                <input type="url" name="embed_url" id="embed_url" value="{{ old('embed_url', $lesson->contentable->embed_url ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('embed_url') border-red-300 @enderror">
                                @error('embed_url')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Enter a direct URL to embed (will be displayed in an iframe)</p>
                            </div>

                            <div>
                                <label for="embed_provider" class="block text-sm font-medium text-gray-700">Provider</label>
                                <input type="text" name="embed_provider" id="embed_provider" value="{{ old('embed_provider', $lesson->contentable->provider ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="mt-1 text-sm text-gray-500">Optional: Specify the provider name (e.g., YouTube, Vimeo, Google Forms)</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Checkboxes -->
            <div class="space-y-4">
                <!-- Is Preview -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="is_preview" id="is_preview" value="1" {{ old('is_preview', $lesson->is_preview) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    </div>
                    <div class="ml-3">
                        <label for="is_preview" class="text-sm font-medium text-gray-700">Preview Lesson</label>
                        <p class="text-sm text-gray-500">Allow non-enrolled users to preview this lesson</p>
                    </div>
                </div>

                <!-- Is Published -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published', $lesson->is_published) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    </div>
                    <div class="ml-3">
                        <label for="is_published" class="text-sm font-medium text-gray-700">Published</label>
                        <p class="text-sm text-gray-500">Make this lesson visible to enrolled students</p>
                    </div>
                </div>

                <!-- Requires Previous Completion -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="requires_previous_completion" id="requires_previous_completion" value="1" {{ old('requires_previous_completion', $lesson->requires_previous_completion) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    </div>
                    <div class="ml-3">
                        <label for="requires_previous_completion" class="text-sm font-medium text-gray-700">Sequential Access</label>
                        <p class="text-sm text-gray-500">Students must complete previous lessons before accessing this one</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6 border-t">
                <!-- Delete Button -->
                <div>
                    <button type="button" onclick="if(confirm('Are you sure you want to delete this lesson?')) document.getElementById('delete-form').submit();" class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete Lesson
                    </button>
                </div>

                <!-- Save/Cancel Buttons -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.lessons.show', $lesson) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Lesson
                    </button>
                </div>
            </div>
        </form>

        <!-- Hidden Delete Form -->
        <form id="delete-form" action="{{ route('admin.lessons.destroy', $lesson) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>

    <!-- Content Type Info -->
    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm">
                    <strong>Current Content Type:</strong> {{ ucfirst($lesson->content_type) }}<br>
                    <strong>Note:</strong> Content type cannot be changed after creation. To change the content type, create a new lesson.
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Upload progress tracking for edit form
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('lessonForm');
    const overlay = document.getElementById('uploadOverlay');
    const progressBar = document.getElementById('uploadProgress');
    const percentText = document.getElementById('uploadPercent');
    const speedText = document.getElementById('uploadSpeed');
    const sizeText = document.getElementById('uploadSize');

    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Check if there are any file inputs with files
        const fileInputs = form.querySelectorAll('input[type="file"]');
        let hasFiles = false;
        let totalSize = 0;

        fileInputs.forEach(input => {
            if (input.files && input.files.length > 0) {
                hasFiles = true;
                for (let file of input.files) {
                    totalSize += file.size;
                }
            }
        });

        // Show overlay if files are being uploaded
        if (hasFiles) {
            overlay.classList.remove('hidden');
            sizeText.textContent = `Total size: ${formatBytes(totalSize)}`;
        }

        // Create FormData from form
        const formData = new FormData(form);

        // Create XMLHttpRequest for progress tracking
        const xhr = new XMLHttpRequest();

        let startTime = Date.now();
        let lastLoaded = 0;
        let lastTime = startTime;

        // Track upload progress
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                progressBar.style.width = percentComplete + '%';
                percentText.textContent = Math.round(percentComplete) + '%';

                // Calculate upload speed
                const currentTime = Date.now();
                const timeDiff = (currentTime - lastTime) / 1000;
                const bytesDiff = e.loaded - lastLoaded;

                if (timeDiff > 0.5) {
                    const speed = bytesDiff / timeDiff;
                    speedText.textContent = `Speed: ${formatBytes(speed)}/s`;

                    lastLoaded = e.loaded;
                    lastTime = currentTime;
                }

                sizeText.textContent = `${formatBytes(e.loaded)} / ${formatBytes(e.total)}`;
            }
        });

        // Handle completion
        xhr.addEventListener('load', function() {
            if (xhr.status === 200 || xhr.status === 302) {
                progressBar.style.width = '100%';
                percentText.textContent = '100%';
                speedText.textContent = 'Upload complete!';

                setTimeout(() => {
                    window.location.href = "{{ route('admin.lessons.show', $lesson) }}";
                }, 500);
            } else {
                overlay.classList.add('hidden');
                alert('Upload failed. Please try again.');
            }
        });

        // Handle errors
        xhr.addEventListener('error', function() {
            overlay.classList.add('hidden');
            alert('Upload failed. Please check your connection and try again.');
        });

        xhr.addEventListener('abort', function() {
            overlay.classList.add('hidden');
            alert('Upload cancelled.');
        });

        // Send request
        xhr.open('POST', form.action);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
        xhr.send(formData);
    });

    // Helper function to format bytes
    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';

        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];

        const i = Math.floor(Math.log(bytes) / Math.log(k));

        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
});
</script>
@endpush
@endsection
