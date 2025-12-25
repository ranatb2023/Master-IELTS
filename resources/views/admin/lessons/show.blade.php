@extends('layouts.admin')

@section('title', 'Lesson Details')
@section('page-title', $lesson->title)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $lesson->title }}</h2>
            <p class="mt-1 text-sm text-gray-600">
                <a href="{{ route('admin.topics.show', $lesson->topic) }}" class="text-indigo-600 hover:text-indigo-900">
                    {{ $lesson->topic->title }}
                </a>
                →
                <a href="{{ route('admin.courses.show', $lesson->topic->course) }}" class="text-indigo-600 hover:text-indigo-900">
                    {{ $lesson->topic->course->title }}
                </a>
            </p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('lessons.play', $lesson) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Play
            </a>
            <a href="{{ route('admin.lessons.edit', $lesson) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Lesson
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Lesson Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Lesson Information</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Title</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $lesson->title }}</p>
                    </div>

                    @if($lesson->description)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <div class="mt-1 text-sm text-gray-900 prose max-w-none">
                            {!! $lesson->description !!}
                        </div>
                    </div>
                    @endif

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Content Type</label>
                            <p class="mt-1">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($lesson->content_type === 'video') bg-red-100 text-red-800
                                    @elseif($lesson->content_type === 'text') bg-green-100 text-green-800
                                    @elseif($lesson->content_type === 'document') bg-blue-100 text-blue-800
                                    @elseif($lesson->content_type === 'audio') bg-purple-100 text-purple-800
                                    @elseif($lesson->content_type === 'presentation') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($lesson->content_type) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Duration</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if($lesson->duration_minutes)
                                    {{ $lesson->duration_minutes }} min
                                @else
                                    <span class="text-gray-400">Not set</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Order</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $lesson->order }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <p class="mt-1">
                                @if($lesson->is_published)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Published</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        @if($lesson->is_preview)
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <span class="text-sm text-gray-700">Preview Allowed</span>
                            </div>
                        @endif
                        @if($lesson->requires_previous_completion)
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <span class="text-sm text-gray-700">Sequential Access Required</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Content Section -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Lesson Content</h3>
                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-indigo-100 text-indigo-800">
                        {{ ucfirst($lesson->content_type) }}
                    </span>
                </div>

                @if($lesson->contentable)
                    @switch($lesson->content_type)
                        @case('video')
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-700 mb-2">Video Source</p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($lesson->contentable->source ?? 'url') === 'upload' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ($lesson->contentable->source ?? 'url') === 'upload' ? 'Uploaded Video' : 'External URL' }}
                                    </span>
                                </div>

                                @if(($lesson->contentable->source ?? 'url') === 'upload')
                                    {{-- Uploaded Video Info --}}
                                    @if($lesson->contentable->file_path)
                                        <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                                            <svg class="h-10 w-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            <div class="ml-4 flex-1">
                                                <p class="text-sm font-medium text-gray-900">{{ $lesson->contentable->file_name }}</p>
                                                <p class="text-sm text-gray-500">
                                                    Type: {{ strtoupper($lesson->contentable->file_type ?? 'video') }} |
                                                    Size: {{ number_format(($lesson->contentable->file_size ?? 0) / 1024 / 1024, 2) }} MB
                                                    @if($lesson->contentable->duration_seconds)
                                                        | Duration: {{ gmdate('H:i:s', $lesson->contentable->duration_seconds) }}
                                                    @endif
                                                </p>
                                            </div>
                                            <a href="{{ route('lessons.play', $lesson) }}" class="ml-4 inline-flex items-center px-3 py-2 border border-purple-300 shadow-sm text-sm leading-4 font-medium rounded-md text-purple-700 bg-white hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Play Video
                                            </a>
                                        </div>
                                    @endif
                                @else
                                    {{-- External Video Info --}}
                                    @if($lesson->contentable->vimeo_id)
                                        <div>
                                            <p class="text-sm font-medium text-gray-700 mb-2">Vimeo Video</p>
                                            <p class="text-sm text-gray-600">Vimeo ID: {{ $lesson->contentable->vimeo_id }}</p>
                                        </div>
                                    @endif
                                    @if($lesson->contentable->url)
                                        <div>
                                            <p class="text-sm font-medium text-gray-700 mb-2">Video URL</p>
                                            <a href="{{ $lesson->contentable->url }}" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-800 break-all">{{ $lesson->contentable->url }}</a>
                                        </div>
                                    @endif
                                @endif

                                @if($lesson->contentable->transcript)
                                    <div>
                                        <p class="text-sm font-medium text-gray-700 mb-2">Transcript</p>
                                        <div class="p-3 bg-gray-50 rounded text-sm text-gray-700 max-h-48 overflow-y-auto">
                                            {{ $lesson->contentable->transcript }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @break

                        @case('text')
                            <div class="space-y-4">
                                @if($lesson->contentable->reading_time)
                                    <p class="text-sm text-gray-600">Reading time: {{ $lesson->contentable->reading_time }} minutes</p>
                                @endif
                                <div class="prose max-w-none p-4 bg-gray-50 rounded-lg max-h-96 overflow-y-auto">
                                    {!! $lesson->contentable->body !!}
                                </div>
                            </div>
                            @break

                        @case('document')
                            <div class="space-y-3">
                                <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                                    <svg class="h-10 w-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <div class="ml-4 flex-1">
                                        <p class="text-sm font-medium text-gray-900">{{ $lesson->contentable->file_name }}</p>
                                        <p class="text-xs text-gray-500">Type: {{ strtoupper($lesson->contentable->file_type) }}</p>
                                    </div>
                                    <a href="{{ Storage::url($lesson->contentable->file_path) }}" target="_blank" class="ml-4 px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">
                                        Download
                                    </a>
                                </div>
                            </div>
                            @break

                        @case('audio')
                            <div class="space-y-4">
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Audio File</p>
                                    <audio controls class="w-full">
                                        <source src="{{ Storage::url($lesson->contentable->file_path) }}" type="audio/mpeg">
                                        Your browser does not support the audio element.
                                    </audio>
                                </div>
                                @if($lesson->contentable->transcript)
                                    <div>
                                        <p class="text-sm font-medium text-gray-700 mb-2">Transcript</p>
                                        <div class="p-3 bg-gray-50 rounded text-sm text-gray-700 max-h-48 overflow-y-auto">
                                            {{ $lesson->contentable->transcript }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @break

                        @case('presentation')
                            <div class="space-y-3">
                                <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                                    <svg class="h-10 w-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                                    </svg>
                                    <div class="ml-4 flex-1">
                                        <p class="text-sm font-medium text-gray-900">Presentation</p>
                                        <p class="text-xs text-gray-500">{{ basename($lesson->contentable->file_path) }}</p>
                                    </div>
                                    <a href="{{ Storage::url($lesson->contentable->file_path) }}" target="_blank" class="ml-4 px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">
                                        Download
                                    </a>
                                </div>
                            </div>
                            @break

                        @case('embed')
                            <div class="space-y-3">
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Embed URL</p>
                                    <a href="{{ $lesson->contentable->embed_url }}" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-800 break-all">
                                        {{ $lesson->contentable->embed_url }}
                                    </a>
                                </div>
                                @if($lesson->contentable->provider)
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <p class="text-sm font-medium text-gray-700 mb-1">Provider</p>
                                        <p class="text-sm text-gray-600">{{ $lesson->contentable->provider }}</p>
                                    </div>
                                @endif
                            </div>
                            @break
                    @endswitch
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="mt-2 text-sm">No content has been added to this lesson yet</p>
                        <p class="mt-1 text-xs text-gray-400">Edit the lesson to add content</p>
                    </div>
                @endif
            </div>

            <!-- Comments Section -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Comments ({{ $lessonComments->count() }})</h3>
                </div>

                @if($lessonComments->count() > 0)
                    <div class="space-y-6">
                        @foreach($lessonComments as $comment)
                            @include('partials.comment-item', ['comment' => $comment, 'lesson' => $lesson])
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                        <p class="mt-2">No comments yet</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Stats -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Statistics</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Progress Records</span>
                        <span class="text-lg font-semibold text-gray-900">{{ $lesson->progress->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Comments</span>
                        <span class="text-lg font-semibold text-gray-900">{{ $lesson->comments->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Resources</span>
                        <span class="text-lg font-semibold text-gray-900">{{ $lesson->resources->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Topic Info -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Topic</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.topics.show', $lesson->topic) }}" class="block text-sm font-medium text-indigo-600 hover:text-indigo-900">
                        {{ $lesson->topic->title }}
                    </a>
                    <p class="text-sm text-gray-600">Course: {{ $lesson->topic->course->title }}</p>
                    <p class="text-sm text-gray-600">
                        @if($lesson->topic->is_published)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Published</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.topics.show', $lesson->topic) }}" class="block w-full text-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition">
                        View Topic
                    </a>
                    <a href="{{ route('admin.courses.show', $lesson->topic->course) }}" class="block w-full text-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition">
                        View Course
                    </a>
                    <a href="{{ route('admin.lessons.edit', $lesson) }}" class="block w-full text-center px-4 py-2 bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200 transition">
                        Edit Lesson
                    </a>
                    <button onclick="if(confirm('Are you sure you want to delete this lesson?')) document.getElementById('delete-form').submit();" class="block w-full text-center px-4 py-2 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition">
                        Delete Lesson
                    </button>
                </div>
            </div>

            <!-- Metadata -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Metadata</h3>
                <div class="space-y-2 text-sm">
                    <div>
                        <span class="text-gray-600">Created:</span>
                        <span class="text-gray-900">{{ $lesson->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Updated:</span>
                        <span class="text-gray-900">{{ $lesson->updated_at->format('M d, Y h:i A') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Delete Form -->
    <form id="delete-form" action="{{ route('admin.lessons.destroy', $lesson) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>

@push('scripts')
    <script>
        window.submitReply = function(parentId) {
            const commentElement = document.querySelector(`#comment-${parentId}`);
            const data = Alpine.$data(commentElement);
            if (!data.replyText.trim()) return;
            data.submitting = true;

            fetch('{{ route("admin.lesson-comments.store", $lesson) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ comment: data.replyText, parent_id: parentId })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    window.location.reload();
                } else {
                    alert('Failed to post reply.');
                    data.submitting = false;
                }
            })
            .catch(error => {
                console.error(error);
                alert('An error occurred.');
                data.submitting = false;
            });
        };

        window.togglePin = function(commentId) {
            fetch('{{ route("admin.lesson-comments.toggle-pin", "__ID__") }}'.replace('__ID__', commentId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) window.location.reload();
                else alert('Failed to toggle pin.');
            })
            .catch(error => {
                console.error(error);
                alert('An error occurred.');
            });
        };
    </script>
@endpush
@endsection
