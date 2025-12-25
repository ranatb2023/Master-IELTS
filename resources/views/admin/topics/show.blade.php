@extends('layouts.admin')

@section('title', 'Topic Details')
@section('page-title', $topic->title)

@section('content')
<div class="space-y-6" x-data="{
    showLessons: true,
    showQuizzes: false,
    showAssignments: false,
    addingLesson: false,
    editingLesson: null
}">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $topic->title }}</h2>
            <p class="mt-1 text-sm text-gray-600">
                <a href="{{ route('admin.courses.show', $topic->course) }}" class="text-indigo-600 hover:text-indigo-900">
                    {{ $topic->course->title }}
                </a>
            </p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.topics.edit', $topic) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Topic
            </a>
            <a href="{{ route('admin.topics.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Topics
            </a>
        </div>
    </div>

    <!-- Upload Progress Overlay -->
    <div id="uploadOverlay" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl p-8 max-w-md w-full mx-4">
            <div class="text-center mb-6">
                <svg class="animate-spin h-12 w-12 text-indigo-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Creating Lesson</h3>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Topic Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Topic Information</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Title</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $topic->title }}</p>
                    </div>

                    @if($topic->description)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <div class="mt-1 text-sm text-gray-900 prose max-w-none">
                            {!! $topic->description !!}
                        </div>
                    </div>
                    @endif

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Order</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $topic->order }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <p class="mt-1">
                                @if($topic->is_published)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Published</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Created</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $topic->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lessons Section -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <!-- Lessons Header -->
                <div class="bg-gradient-to-r from-green-50 to-green-100 px-6 py-4 border-b border-green-200">
                    <div class="flex justify-between items-center">
                        <button @click="showLessons = !showLessons" class="flex items-center space-x-2 text-green-900 hover:text-green-700 transition">
                            <svg :class="showLessons ? 'rotate-90' : ''" class="w-5 h-5 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            <h3 class="text-lg font-medium">Lessons ({{ $topic->lessons->count() }})</h3>
                        </button>
                        <button @click="addingLesson = true; showLessons = true" class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Lesson
                        </button>
                    </div>
                </div>

                <!-- Lessons Content -->
                <div x-show="showLessons" x-collapse class="p-6">
                    <!-- Add Lesson Form -->
                    <div x-show="addingLesson" x-collapse class="mb-6 p-4 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300" x-data="{ contentType: '' }">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-md font-medium text-gray-900">Create New Lesson</h4>
                            <button @click="addingLesson = false; contentType = ''" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <form id="lessonCreateForm" action="{{ route('admin.lessons.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="topic_id" value="{{ $topic->id }}">

                            <div class="grid grid-cols-2 gap-4">
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Lesson Title *</label>
                                    <input type="text" name="title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="col-span-2">
                                    <x-quill-editor
                                        name="description"
                                        :value="old('description')"
                                        label="Description"
                                        :required="false"
                                        height="250px"
                                        placeholder="Provide a detailed description of this lesson..."
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Content Type *</label>
                                    <select name="content_type" x-model="contentType" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Select type...</option>
                                        <option value="video">Video</option>
                                        <option value="text">Text</option>
                                        <option value="document">Document</option>
                                        <option value="audio">Audio</option>
                                        <option value="presentation">Presentation</option>
                                        <option value="embed">Embed</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Order *</label>
                                    <input type="number" name="order" value="{{ $topic->lessons->count() + 1 }}" required min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Duration (minutes) <span class="text-red-500">*</span></label>
                                    <input type="number" name="duration_minutes" min="1" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="flex items-center space-x-4 pt-6">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_published" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">Published</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_preview" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">Preview</span>
                                    </label>
                                </div>

                                <!-- Content-Specific Fields -->
                                <div class="col-span-2" x-show="contentType !== ''" x-collapse>
                                    <div class="mt-4 p-4 bg-white rounded-lg border border-gray-300">
                                        <h5 class="text-sm font-medium text-gray-900 mb-3">Content Details</h5>

                                        <!-- Video Content -->
                                        <div x-show="contentType === 'video'" x-cloak class="space-y-3" x-data="{ videoSource: 'url' }">
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
                                                    <input type="url" name="video_url" x-bind:required="contentType === 'video' && videoSource === 'url'" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="https://youtube.com/watch?v=...">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Platform</label>
                                                    <select name="video_platform" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                        <option value="youtube">YouTube</option>
                                                        <option value="vimeo">Vimeo</option>
                                                        <option value="other">Other</option>
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
                                                <textarea name="video_transcript" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Add transcript for accessibility..."></textarea>
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
                                                <input type="number" name="text_reading_time" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                                                <input type="number" name="document_file_size" step="0.1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Auto-calculated">
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
                                                <textarea name="audio_transcript" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
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
                                                <input type="number" name="presentation_slides_count" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            </div>
                                        </div>

                                        <!-- Embed Content -->
                                        <div x-show="contentType === 'embed'" x-cloak class="space-y-3">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Embed Code (iframe, script) *</label>
                                                <textarea name="embed_code" x-bind:required="contentType === 'embed'" rows="6" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-xs" placeholder="<iframe src='...'></iframe>"></textarea>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Embed URL</label>
                                                <input type="url" name="embed_url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 flex justify-end space-x-2">
                                <button type="button" @click="addingLesson = false; contentType = ''" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Create Lesson</button>
                            </div>
                        </form>
                    </div>

                    <!-- Lessons List -->
                    @if($topic->lessons->count() > 0)
                        <div class="space-y-3">
                            @foreach($topic->lessons as $lesson)
                                <div class="group relative border border-gray-200 rounded-lg hover:border-indigo-300 hover:shadow-md transition">
                                    <div class="flex items-center justify-between p-4">
                                        <div class="flex items-center space-x-4 flex-1">
                                            <span class="flex items-center justify-center w-10 h-10 bg-indigo-100 text-indigo-600 rounded-full text-sm font-bold">
                                                {{ $lesson->order }}
                                            </span>
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-2">
                                                    <h4 class="text-sm font-medium text-gray-900">{{ $lesson->title }}</h4>
                                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                                                        @if($lesson->content_type === 'video') bg-purple-100 text-purple-800
                                                        @elseif($lesson->content_type === 'text') bg-blue-100 text-blue-800
                                                        @elseif($lesson->content_type === 'document') bg-green-100 text-green-800
                                                        @elseif($lesson->content_type === 'audio') bg-yellow-100 text-yellow-800
                                                        @elseif($lesson->content_type === 'presentation') bg-red-100 text-red-800
                                                        @else bg-gray-100 text-gray-800
                                                        @endif">
                                                        {{ ucfirst($lesson->content_type) }}
                                                    </span>
                                                </div>
                                                @if($lesson->description)
                                                    <p class="text-xs text-gray-500 mt-1">{{ Str::limit(strip_tags($lesson->description), 80) }}</p>
                                                @endif
                                                <div class="flex items-center space-x-3 mt-1 text-xs text-gray-500">
                                                    @if($lesson->duration_minutes)
                                                        <span>{{ $lesson->duration_minutes }} min</span>
                                                    @endif
                                                    @if($lesson->is_preview)
                                                        <span class="text-blue-600 font-medium">Preview</span>
                                                    @endif
                                                    @if($lesson->progress_count > 0)
                                                        <span>{{ $lesson->progress_count }} students</span>
                                                    @endif
                                                    @if($lesson->comments_count > 0)
                                                        <span>{{ $lesson->comments_count }} comments</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            @if($lesson->is_published)
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Published</span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
                                            @endif
                                            <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition">
                                                <a href="{{ route('lessons.play', $lesson) }}" class="p-2 text-green-600 hover:bg-green-50 rounded-md" title="Play">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </a>
                                                <a href="{{ route('admin.lessons.show', $lesson) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-md" title="View">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                                <a href="{{ route('admin.lessons.edit', $lesson) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-md" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </a>
                                                <form action="{{ route('admin.lessons.destroy', $lesson) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this lesson?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-md" title="Delete">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div x-show="!addingLesson" class="text-center py-12 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <p class="mt-2 font-medium">No lessons added yet</p>
                            <p class="text-sm">Click "Add Lesson" to create your first lesson</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quizzes Section -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 px-6 py-4 border-b border-yellow-200">
                    <div class="flex justify-between items-center">
                        <button @click="showQuizzes = !showQuizzes" class="flex items-center space-x-2 text-yellow-900 hover:text-yellow-700 transition">
                            <svg :class="showQuizzes ? 'rotate-90' : ''" class="w-5 h-5 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            <h3 class="text-lg font-medium">Quizzes ({{ $topic->quizzes->count() }})</h3>
                        </button>
                    </div>
                </div>

                <div x-show="showQuizzes" x-collapse class="p-6">
                    @if($topic->quizzes->count() > 0)
                        <div class="space-y-3">
                            @foreach($topic->quizzes as $quiz)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:border-yellow-300 hover:shadow-md transition">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $quiz->title }}</p>
                                        <p class="text-xs text-gray-500">
                                            Passing Score: {{ $quiz->passing_score }}%
                                            @if($quiz->time_limit)
                                                • Time Limit: {{ $quiz->time_limit }} minutes
                                            @endif
                                        </p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @if($quiz->is_published)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Published</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
                                        @endif
                                        <a href="{{ route('admin.quizzes.show', $quiz) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">View</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="mt-2">No quizzes added yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Assignments Section -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-6 py-4 border-b border-purple-200">
                    <div class="flex justify-between items-center">
                        <button @click="showAssignments = !showAssignments" class="flex items-center space-x-2 text-purple-900 hover:text-purple-700 transition">
                            <svg :class="showAssignments ? 'rotate-90' : ''" class="w-5 h-5 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            <h3 class="text-lg font-medium">Assignments ({{ $topic->assignments->count() }})</h3>
                        </button>
                    </div>
                </div>

                <div x-show="showAssignments" x-collapse class="p-6">
                    @if($topic->assignments->count() > 0)
                        <div class="space-y-3">
                            @foreach($topic->assignments as $assignment)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:border-purple-300 hover:shadow-md transition">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $assignment->title }}</p>
                                        <p class="text-xs text-gray-500">
                                            Max Points: {{ $assignment->max_points }}
                                            @if($assignment->due_date)
                                                • Due: {{ $assignment->due_date->format('M d, Y') }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @if($assignment->is_published)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Published</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
                                        @endif
                                        <a href="{{ route('admin.assignments.show', $assignment) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">View</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="mt-2">No assignments added yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Stats -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Statistics</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Lessons</span>
                        <span class="text-lg font-semibold text-gray-900">{{ $topic->lessons->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Quizzes</span>
                        <span class="text-lg font-semibold text-gray-900">{{ $topic->quizzes->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Assignments</span>
                        <span class="text-lg font-semibold text-gray-900">{{ $topic->assignments->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.courses.show', $topic->course) }}" class="block w-full text-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition">
                        View Course
                    </a>
                    <a href="{{ route('admin.topics.edit', $topic) }}" class="block w-full text-center px-4 py-2 bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200 transition">
                        Edit Topic
                    </a>
                    <a href="{{ route('admin.lessons.create', ['topic_id' => $topic->id]) }}" class="block w-full text-center px-4 py-2 bg-green-100 text-green-700 rounded-md hover:bg-green-200 transition">
                        Add Lesson (Full Form)
                    </a>
                    <a href="{{ route('admin.quizzes.create', ['topic_id' => $topic->id]) }}" class="block w-full text-center px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition">
                        Add Quiz
                    </a>
                    <a href="{{ route('admin.assignments.create', ['topic_id' => $topic->id]) }}" class="block w-full text-center px-4 py-2 bg-purple-100 text-purple-700 rounded-md hover:bg-purple-200 transition">
                        Add Assignment
                    </a>
                    <button onclick="if(confirm('Are you sure you want to delete this topic? This will also delete all lessons, quizzes, and assignments.')) document.getElementById('delete-form').submit();" class="block w-full text-center px-4 py-2 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition">
                        Delete Topic
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Delete Form -->
    <form id="delete-form" action="{{ route('admin.topics.destroy', $topic) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>

@push('scripts')
<script>
// Upload progress tracking for lesson creation form
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('lessonCreateForm');
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

                // Reload the page to show the new lesson
                setTimeout(() => {
                    window.location.reload();
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
