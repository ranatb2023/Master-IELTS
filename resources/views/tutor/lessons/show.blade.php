@extends('layouts.tutor')

@section('title', 'Lesson Details')
@section('page-title', $lesson->title)

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <a href="{{ route('tutor.lessons.all') }}"
                class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Lessons
            </a>
            @can('course.update')
                <a href="{{ route('tutor.courses.topics.lessons.edit', [$lesson->topic->course, $lesson->topic, $lesson]) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">
                    Edit Lesson
                </a>
            @endcan
        </div>

        <!-- Lesson Info Card -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h2 class="text-3xl font-bold text-gray-900">{{ $lesson->title }}</h2>
                    <div class="mt-2 text-sm text-gray-600 space-y-1">
                        <p>Topic: <a href="{{ route('tutor.topics.show', $lesson->topic) }}"
                                class="text-indigo-600 hover:text-indigo-900">{{ $lesson->topic->title }}</a></p>
                        <p>Course: <a href="{{ route('tutor.courses.show', $lesson->topic->course) }}"
                                class="text-indigo-600 hover:text-indigo-900">{{ $lesson->topic->course->title }}</a></p>
                    </div>
                    @if($lesson->description)
                        <div class="mt-4 prose max-w-none">
                            {!! $lesson->description !!}
                        </div>
                    @endif
                </div>
                <div class="ml-6 space-y-2">
                    @if($lesson->is_published)
                        <span
                            class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">Published</span>
                    @else
                        <span
                            class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
                    @endif
                    @if($lesson->is_preview)
                        <span
                            class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Preview</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Lesson Details -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-600">Order</h3>
                <p class="mt-2 text-2xl font-semibold text-gray-900">#{{ $lesson->order }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-600">Duration</h3>
                <p class="mt-2 text-2xl font-semibold text-gray-900">{{ $lesson->duration_minutes ?? 0 }} min</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-600">Content Type</h3>
                <p class="mt-2 text-lg font-semibold text-gray-900">
                    {{ $lesson->contentable ? class_basename($lesson->contentable_type) : 'No Content' }}
                </p>
            </div>
        </div>

        <!-- Lesson Content -->
        @if($lesson->contentable)
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Content</h3>
                @if($lesson->contentable_type === 'App\Models\VideoContent')
                    <div class="aspect-w-16 aspect-h-9">
                        <p class="text-gray-600">Video Content: {{ $lesson->contentable->video_url }}</p>
                    </div>
                @elseif($lesson->contentable_type === 'App\Models\TextContent')
                    <div class="prose max-w-none">
                        {!! $lesson->contentable->content !!}
                    </div>
                @endif
            </div>
        @endif

        <!-- Attached Resources -->
        @if($lesson->resources && $lesson->resources->count() > 0)
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Attached Resources</h3>
                <div class="space-y-2">
                    @foreach($lesson->resources as $resource)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                            <span class="text-sm text-gray-900">{{ $resource->title }}</span>
                            <a href="{{ asset('storage/' . $resource->file_path) }}" download
                                class="text-indigo-600 hover:text-indigo-900 text-sm">
                                Download
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

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
                <div
                    class="text-center py-12 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-dashed border-gray-300">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No comments yet</h3>
                    <p class="text-sm text-gray-500">No student comments have been posted on this lesson.</p>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            window.submitReply = function (parentId) {
                const commentElement = document.querySelector(`#comment-${parentId}`);
                const data = Alpine.$data(commentElement);
                if (!data.replyText.trim()) return;
                data.submitting = true;

                fetch('{{ route("tutor.lesson-comments.store", $lesson) }}', {
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

            window.togglePin = function (commentId) {
                fetch('{{ route("tutor.lesson-comments.toggle-pin", "__ID__") }}'.replace('__ID__', commentId), {
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