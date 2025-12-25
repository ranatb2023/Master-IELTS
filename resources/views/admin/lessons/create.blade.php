@extends('layouts.admin')

@section('title', 'Create Lesson')
@section('page-title', 'Create New Lesson')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Create New Lesson</h2>
            <p class="mt-1 text-sm text-gray-600">Add a new lesson to a topic</p>
        </div>
        <a href="{{ route('admin.lessons.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Lessons
        </a>
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
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Uploading Lesson</h3>
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
        <form id="lessonForm" action="{{ route('admin.lessons.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            <!-- Topic Selection -->
            <div>
                <label for="topic_id" class="block text-sm font-medium text-gray-700">Topic <span class="text-red-500">*</span></label>
                <select name="topic_id" id="topic_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('topic_id') border-red-300 @enderror" required>
                    <option value="">Select a topic</option>
                    @foreach($topics as $topic)
                        <option value="{{ $topic->id }}" {{ old('topic_id', $selectedTopic) == $topic->id ? 'selected' : '' }}>
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
                <input type="text" name="title" id="title" value="{{ old('title') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-red-300 @enderror" required>
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Enter a clear, descriptive title for this lesson</p>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Provide a brief description of this lesson</p>
            </div>

            <!-- Content Type -->
            <div x-data="{ contentType: '{{ old('content_type', '') }}' }">
                <label for="content_type" class="block text-sm font-medium text-gray-700">Content Type <span class="text-red-500">*</span></label>
                <select name="content_type" id="content_type" x-model="contentType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('content_type') border-red-300 @enderror" required>
                    <option value="">Select content type</option>
                    <option value="video">Video</option>
                    <option value="text">Text/Article</option>
                    <option value="document">Document/PDF</option>
                    <option value="audio">Audio</option>
                    <option value="presentation">Presentation</option>
                    <option value="embed">Embed (External)</option>
                </select>
                @error('content_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Select the type of content for this lesson</p>

                <!-- Content-Specific Fields -->
                <div x-show="contentType !== ''" x-transition class="mt-4 p-4 bg-white rounded-lg border border-gray-300">
                    <h5 class="text-sm font-medium text-gray-900 mb-3">Content Details</h5>

                    <!-- Video Content -->
                    <div x-show="contentType === 'video'" x-cloak class="space-y-3" x-data="{ videoSource: '{{ old('video_source', 'url') }}' }">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Video Source *</label>
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" name="video_source" value="url" x-model="videoSource" class="rounded-full border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">External URL (YouTube/Vimeo)</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="video_source" value="upload" x-model="videoSource" class="rounded-full border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">Upload Video File</span>
                                </label>
                            </div>
                        </div>

                        <!-- URL Option -->
                        <div x-show="videoSource === 'url'" x-cloak class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Video URL (YouTube/Vimeo) *</label>
                                <input type="url" name="video_url" value="{{ old('video_url') }}" x-bind:required="contentType === 'video' && videoSource === 'url'" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="https://youtube.com/watch?v=...">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Platform</label>
                                <select name="video_platform" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="youtube" {{ old('video_platform') == 'youtube' ? 'selected' : '' }}>YouTube</option>
                                    <option value="vimeo" {{ old('video_platform') == 'vimeo' ? 'selected' : '' }}>Vimeo</option>
                                    <option value="other" {{ old('video_platform') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>

                        <!-- Upload Option -->
                        <div x-show="videoSource === 'upload'" x-cloak class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Upload Video File (MP4, MOV, AVI, WEBM) *</label>
                                <input type="file" name="video_file" x-bind:required="contentType === 'video' && videoSource === 'upload'" accept=".mp4,.mov,.avi,.webm,.mkv" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                                <p class="mt-1 text-xs text-gray-500">Max size: 500MB. Supported formats: MP4, MOV, AVI, WEBM, MKV</p>
                            </div>
                        </div>

                        <!-- Common Fields -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Video Transcript (Optional)</label>
                            <textarea name="video_transcript" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Add transcript for accessibility...">{{ old('video_transcript') }}</textarea>
                        </div>
                    </div>

                    <!-- Text Content -->
                    <div x-show="contentType === 'text'" x-cloak class="space-y-3">
                        <x-quill-editor
                            name="text_body"
                            :value="old('text_body')"
                            label="Text Content"
                            :required="false"
                            height="400px"
                            placeholder="Write your lesson content with rich formatting, images, videos, and links..."
                        />
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Reading Time (minutes)</label>
                            <input type="number" name="text_reading_time" value="{{ old('text_reading_time') }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <!-- Document Content -->
                    <div x-show="contentType === 'document'" x-cloak class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Upload Document (PDF, DOC, DOCX) *</label>
                            <input type="file" name="document_file" x-bind:required="contentType === 'document'" accept=".pdf,.doc,.docx" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">File Size (MB)</label>
                            <input type="number" name="document_file_size" value="{{ old('document_file_size') }}" step="0.1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Auto-calculated">
                        </div>
                    </div>

                    <!-- Audio Content -->
                    <div x-show="contentType === 'audio'" x-cloak class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Upload Audio (MP3, WAV) *</label>
                            <input type="file" name="audio_file" x-bind:required="contentType === 'audio'" accept=".mp3,.wav,.m4a" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Transcript (Optional)</label>
                            <textarea name="audio_transcript" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('audio_transcript') }}</textarea>
                        </div>
                    </div>

                    <!-- Presentation Content -->
                    <div x-show="contentType === 'presentation'" x-cloak class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Upload Presentation (PPT, PPTX, PDF) *</label>
                            <input type="file" name="presentation_file" x-bind:required="contentType === 'presentation'" accept=".ppt,.pptx,.pdf" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Number of Slides</label>
                            <input type="number" name="presentation_slides_count" value="{{ old('presentation_slides_count') }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <!-- Embed Content -->
                    <div x-show="contentType === 'embed'" x-cloak class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Embed Code (iframe, script) *</label>
                            <textarea name="embed_code" value="{{ old('embed_code') }}" x-bind:required="contentType === 'embed'" rows="6" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-xs" placeholder="<iframe src='...'></iframe>"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Embed URL</label>
                            <input type="url" name="embed_url" value="{{ old('embed_url') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Duration -->
                <div>
                    <label for="duration_minutes" class="block text-sm font-medium text-gray-700">Duration (minutes)</label>
                    <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', 0) }}" min="0" max="9999" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('duration_minutes') border-red-300 @enderror">
                    @error('duration_minutes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Estimated time to complete this lesson</p>
                </div>

                <!-- Order -->
                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700">Display Order <span class="text-red-500">*</span></label>
                    <input type="number" name="order" id="order" value="{{ old('order', 0) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('order') border-red-300 @enderror" required>
                    @error('order')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Set the order in which this lesson should appear (0 = first)</p>
                </div>
            </div>

            <!-- Checkboxes -->
            <div class="space-y-4">
                <!-- Is Preview -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="is_preview" id="is_preview" value="1" {{ old('is_preview') ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    </div>
                    <div class="ml-3">
                        <label for="is_preview" class="text-sm font-medium text-gray-700">Preview Lesson</label>
                        <p class="text-sm text-gray-500">Allow non-enrolled users to preview this lesson</p>
                    </div>
                </div>

                <!-- Is Published -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    </div>
                    <div class="ml-3">
                        <label for="is_published" class="text-sm font-medium text-gray-700">Published</label>
                        <p class="text-sm text-gray-500">Make this lesson visible to enrolled students</p>
                    </div>
                </div>

                <!-- Requires Previous Completion -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="requires_previous_completion" id="requires_previous_completion" value="1" {{ old('requires_previous_completion', true) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    </div>
                    <div class="ml-3">
                        <label for="requires_previous_completion" class="text-sm font-medium text-gray-700">Sequential Access</label>
                        <p class="text-sm text-gray-500">Students must complete previous lessons before accessing this one</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t">
                <a href="{{ route('admin.lessons.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Create Lesson
                </button>
            </div>
        </form>
    </div>

    <!-- Info Note -->
    <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm">
                    <strong>Note:</strong> Select a content type to reveal the content fields. All lesson information and content will be created together.
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Alpine.js handles content type toggling automatically

// Upload progress tracking
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
                const timeDiff = (currentTime - lastTime) / 1000; // seconds
                const bytesDiff = e.loaded - lastLoaded;

                if (timeDiff > 0.5) { // Update speed every 0.5 seconds
                    const speed = bytesDiff / timeDiff; // bytes per second
                    speedText.textContent = `Speed: ${formatBytes(speed)}/s`;

                    lastLoaded = e.loaded;
                    lastTime = currentTime;
                }

                // Show uploaded vs total
                sizeText.textContent = `${formatBytes(e.loaded)} / ${formatBytes(e.total)}`;
            }
        });

        // Handle completion
        xhr.addEventListener('load', function() {
            if (xhr.status === 200 || xhr.status === 302) {
                progressBar.style.width = '100%';
                percentText.textContent = '100%';
                speedText.textContent = 'Upload complete!';

                // Try to parse JSON response for redirect
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.redirect) {
                        window.location.href = response.redirect;
                        return;
                    }
                } catch (e) {
                    // If not JSON, check for Laravel redirect
                    const redirectUrl = xhr.getResponseHeader('X-Redirect') || xhr.responseURL;
                    if (redirectUrl && redirectUrl !== window.location.href) {
                        window.location.href = redirectUrl;
                        return;
                    }
                }

                // Fallback: reload or redirect to index
                setTimeout(() => {
                    window.location.href = "{{ route('admin.lessons.index') }}";
                }, 500);
            } else if (xhr.status === 422) {
                // Validation error - show specific errors
                overlay.classList.add('hidden');
                try {
                    const response = JSON.parse(xhr.responseText);
                    let errorMessage = 'Validation failed:\n\n';
                    
                    if (response.errors) {
                        // Laravel validation errors format
                        for (const [field, messages] of Object.entries(response.errors)) {
                            errorMessage += `${field}: ${messages.join(', ')}\n`;
                        }
                    } else if (response.message) {
                        errorMessage = response.message;
                    }
                    
                    alert(errorMessage);
                    console.error('Validation errors:', response);
                } catch (e) {
                    alert('Upload failed with validation error (422). Check console for details.');
                    console.error('422 Response:', xhr.responseText);
                }
            } else {
                overlay.classList.add('hidden');
                alert('Upload failed with status ' + xhr.status + '. Please try again.');
                console.error('Upload failed:', xhr.status, xhr.responseText);
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
