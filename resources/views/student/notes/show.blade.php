@extends('layouts.student')

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="flex mb-8" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li>
                        <a href="{{ route('student.notes.index') }}"
                            class="text-gray-700 hover:text-indigo-600 inline-flex items-center">
                            My Notes
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-gray-500 md:ml-2 truncate">{{ Str::limit($note->title, 50) }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            @php
                $colorClasses = [
                    'yellow' => 'bg-yellow-50 border-yellow-200',
                    'green' => 'bg-green-50 border-green-200',
                    'blue' => 'bg-blue-50 border-blue-200',
                    'red' => 'bg-red-50 border-red-200',
                    'purple' => 'bg-purple-50 border-purple-200',
                    'pink' => 'bg-pink-50 border-pink-200',
                    'orange' => 'bg-orange-50 border-orange-200',
                    'gray' => 'bg-gray-50 border-gray-200',
                ];
                $bgClass = $note->color ? ($colorClasses[$note->color] ?? 'bg-white border-gray-200') : 'bg-white border-gray-200';
            @endphp

            <!-- Note Card -->
            <div class="{{ $bgClass }} border-2 rounded-lg shadow-sm overflow-hidden mb-6">
                <!-- Header -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                @if ($note->is_pinned)
                                    <svg class="w-5 h-5 text-indigo-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L11 4.323V3a1 1 0 011-1z" />
                                    </svg>
                                @endif
                                <h1 class="text-3xl font-bold text-gray-900">{{ $note->title }}</h1>
                            </div>

                            <!-- Meta Information -->
                            <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Created {{ $note->created_at->format('M d, Y') }}
                                </div>
                                @if ($note->created_at != $note->updated_at)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Updated {{ $note->updated_at->diffForHumans() }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2 ml-4">
                            <form action="{{ route('student.notes.toggle-pin', $note) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                                    title="{{ $note->is_pinned ? 'Unpin' : 'Pin' }}">
                                    <svg class="w-5 h-5" fill="{{ $note->is_pinned ? 'currentColor' : 'none' }}"
                                        stroke="currentColor" viewBox="0 0 20 20">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L11 4.323V3a1 1 0 011-1z" />
                                    </svg>
                                </button>
                            </form>
                            <a href="{{ route('student.notes.edit', $note) }}"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit
                            </a>
                            <form action="{{ route('student.notes.destroy', $note) }}" method="POST" class="inline"
                                onsubmit="return confirm('Move this note to trash?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Tags -->
                    @if ($note->tags && count($note->tags) > 0)
                        <div class="flex flex-wrap gap-2 mt-4">
                            @foreach ($note->tags as $tag)
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                    {{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <!-- Associated Content -->
                    @if ($note->lesson || $note->course)
                        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="text-sm font-medium text-blue-900 mb-2">Associated with:</div>
                            <div class="space-y-1 text-sm text-blue-800">
                                @if ($note->lesson)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                        Lesson: {{ $note->lesson->title }}
                                    </div>
                                @endif
                                @if ($note->course)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                        Course: {{ $note->course->title }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Note Body -->
                <div class="p-6 prose max-w-none">
                    {!! $note->body !!}
                </div>

                <!-- Attachments -->
                @if ($note->attachments->count() > 0)
                    <div class="p-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Attachments ({{ $note->attachments->count() }})
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($note->attachments as $attachment)
                                <div class="flex items-center justify-between p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                    <div class="flex items-center min-w-0 flex-1">
                                        <svg class="w-8 h-8 text-gray-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $attachment->original_filename }}
                                            </p>
                                            <p class="text-xs text-gray-500">{{ $attachment->formatted_file_size }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 ml-3">
                                        <a href="{{ route('student.notes.attachments.view', [$note, $attachment]) }}"
                                            target="_blank"
                                            class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                                            View
                                        </a>
                                        <a href="{{ route('student.notes.attachments.download', [$note, $attachment]) }}"
                                            class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                                            Download
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Related Notes -->
            @if ($relatedNotes && $relatedNotes->count() > 0)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">More notes from this lesson</h3>
                    <div class="space-y-3">
                        @foreach ($relatedNotes as $relatedNote)
                            <a href="{{ route('student.notes.show', $relatedNote) }}"
                                class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">{{ $relatedNote->title }}</h4>
                                        <p class="text-sm text-gray-600 mt-1 line-clamp-2">
                                            {{ $relatedNote->getExcerpt(100) }}
                                        </p>
                                    </div>
                                    <div class="text-xs text-gray-500 ml-4">
                                        {{ $relatedNote->created_at->format('M d') }}
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection