@extends('layouts.admin')

@section('title', 'Play: ' . $lesson->title)
@section('page-title', $lesson->title)

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div class="flex justify-between items-center">
        <a href="javascript:history.back()" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back
        </a>
    </div>

    <!-- Lesson Info -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="border-b pb-4 mb-4">
            <h2 class="text-2xl font-bold text-gray-900">{{ $lesson->title }}</h2>
            <p class="text-sm text-gray-600 mt-2">
                Course: <span class="font-medium">{{ $lesson->topic->course->title }}</span> |
                Topic: <span class="font-medium">{{ $lesson->topic->title }}</span>
            </p>
            @if($lesson->description)
                <div class="text-gray-700 mt-3 prose max-w-none">{!! $lesson->description !!}</div>
            @endif
        </div>

        <!-- Content Player -->
        @if($lesson->contentable)
            @switch($lesson->content_type)
                @case('video')
                    @if($lesson->contentable->source === 'upload')
                        {{-- Self-hosted Video Player --}}
                        <div class="bg-black rounded-lg overflow-hidden">
                            <video
                                controls
                                controlsList="nodownload"
                                class="w-full"
                                style="max-height: 70vh;"
                                preload="metadata"
                            >
                                <source src="{{ route('lessons.video.stream', $lesson) }}" type="video/mp4">
                                @if($lesson->contentable->transcript)
                                    <track kind="captions" label="English" srclang="en">
                                @endif
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    @elseif($lesson->contentable->vimeo_id)
                        {{-- Vimeo Player --}}
                        <div class="relative" style="padding-bottom: 56.25%; height: 0;">
                            <iframe
                                src="https://player.vimeo.com/video/{{ $lesson->contentable->vimeo_id }}?title=0&byline=0&portrait=0"
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                                frameborder="0"
                                allow="autoplay; fullscreen; picture-in-picture"
                                allowfullscreen
                                class="rounded-lg"
                            ></iframe>
                        </div>
                    @elseif($lesson->contentable->url)
                        {{-- Extract YouTube ID if it's a YouTube URL --}}
                        @php
                            $url = $lesson->contentable->url;
                            $youtubeId = null;

                            // Check various YouTube URL patterns
                            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/', $url, $matches)) {
                                $youtubeId = $matches[1];
                            }
                        @endphp

                        @if($youtubeId)
                            {{-- YouTube Player --}}
                            <div class="relative" style="padding-bottom: 56.25%; height: 0;">
                                <iframe
                                    src="https://www.youtube.com/embed/{{ $youtubeId }}"
                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen
                                    class="rounded-lg"
                                ></iframe>
                            </div>
                        @else
                            {{-- Generic Video URL --}}
                            <div class="bg-black rounded-lg overflow-hidden">
                                <video
                                    controls
                                    class="w-full"
                                    style="max-height: 70vh;"
                                    preload="metadata"
                                >
                                    <source src="{{ $lesson->contentable->url }}">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        @endif
                    @endif

                    {{-- Video Transcript --}}
                    @if($lesson->contentable->transcript)
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Transcript</h3>
                            <div class="prose max-w-none text-sm text-gray-700">
                                {!! nl2br(e($lesson->contentable->transcript)) !!}
                            </div>
                        </div>
                    @endif
                    @break

                @case('audio')
                    {{-- Audio Player --}}
                    <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-lg p-8">
                        <div class="max-w-2xl mx-auto">
                            <div class="flex items-center justify-center mb-6">
                                <svg class="w-16 h-16 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                                </svg>
                            </div>

                            @if($lesson->contentable->file_path)
                                <audio
                                    controls
                                    controlsList="nodownload"
                                    class="w-full"
                                    preload="metadata"
                                >
                                    <source src="{{ route('lessons.audio.stream', $lesson) }}">
                                    Your browser does not support the audio tag.
                                </audio>
                            @endif

                            @if($lesson->contentable->transcript)
                                <div class="mt-6 p-4 bg-white rounded-lg shadow">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Transcript</h3>
                                    <div class="prose max-w-none text-sm text-gray-700">
                                        {!! nl2br(e($lesson->contentable->transcript)) !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    @break

                @case('text')
                    {{-- Text Content --}}
                    <div class="prose max-w-none p-6 bg-white rounded-lg">
                        {!! $lesson->contentable->body !!}
                    </div>
                    @break

                @case('document')
                    {{-- Document Viewer --}}
                    @php
                        $fileExtension = strtolower($lesson->contentable->file_type);
                    @endphp

                    <div class="space-y-4">
                        <div class="flex justify-between items-center bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <svg class="h-8 w-8 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $lesson->contentable->file_name }}</h3>
                                    <p class="text-sm text-gray-600">Type: {{ strtoupper($lesson->contentable->file_type) }}</p>
                                </div>
                            </div>
                            <a href="{{ route('lessons.document.stream', $lesson) }}" download class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Download
                            </a>
                        </div>

                        @if(in_array($fileExtension, ['pdf']))
                            {{-- PDF Viewer --}}
                            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                                <iframe
                                    src="{{ route('lessons.document.stream', $lesson) }}#view=FitH"
                                    class="w-full"
                                    style="height: 85vh; min-height: 600px;"
                                    frameborder="0"
                                ></iframe>
                            </div>
                        @elseif(in_array($fileExtension, ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx']))
                            {{-- Office Documents Viewer using Google Docs Viewer --}}
                            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                                <iframe
                                    src="https://docs.google.com/viewer?url={{ urlencode(route('lessons.document.stream', $lesson)) }}&embedded=true"
                                    class="w-full"
                                    style="height: 85vh; min-height: 600px;"
                                    frameborder="0"
                                ></iframe>
                            </div>
                        @else
                            {{-- Unsupported format - show download option --}}
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-8 text-center">
                                <svg class="mx-auto h-16 w-16 text-yellow-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Preview Not Available</h3>
                                <p class="text-gray-600 mb-4">This file type cannot be previewed in the browser. Please download it to view.</p>
                            </div>
                        @endif
                    </div>
                    @break

                @case('presentation')
                    {{-- Presentation Viewer --}}
                    @php
                        $presentationExtension = pathinfo($lesson->contentable->file_path, PATHINFO_EXTENSION);
                    @endphp

                    <div class="space-y-4">
                        <div class="flex justify-between items-center bg-gradient-to-r from-green-50 to-emerald-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <svg class="h-8 w-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                                </svg>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ basename($lesson->contentable->file_path) }}</h3>
                                    <p class="text-sm text-gray-600">Presentation File</p>
                                </div>
                            </div>
                            <a href="{{ route('lessons.presentation.stream', $lesson) }}" download class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Download
                            </a>
                        </div>

                        @if(in_array(strtolower($presentationExtension), ['pdf']))
                            {{-- PDF Presentation Viewer --}}
                            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                                <iframe
                                    src="{{ route('lessons.presentation.stream', $lesson) }}#view=FitH"
                                    class="w-full"
                                    style="height: 85vh; min-height: 600px;"
                                    frameborder="0"
                                ></iframe>
                            </div>
                        @elseif(in_array(strtolower($presentationExtension), ['ppt', 'pptx']))
                            {{-- PowerPoint Viewer using Google Docs Viewer --}}
                            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                                <iframe
                                    src="https://docs.google.com/viewer?url={{ urlencode(route('lessons.presentation.stream', $lesson)) }}&embedded=true"
                                    class="w-full"
                                    style="height: 85vh; min-height: 600px;"
                                    frameborder="0"
                                ></iframe>
                            </div>
                        @else
                            {{-- Fallback for other formats --}}
                            <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                                <svg class="mx-auto h-16 w-16 text-green-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                                </svg>
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">Presentation Available for Download</h3>
                                <p class="text-gray-600 mb-6">Click the download button above to view this presentation on your device.</p>
                            </div>
                        @endif
                    </div>
                    @break

                @case('embed')
                    {{-- Embedded Content --}}
                    <div class="space-y-4">
                        @if($lesson->contentable->provider)
                            <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="h-6 w-6 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                    </svg>
                                    <p class="text-sm font-medium text-gray-700">
                                        Embedded from: <span class="text-purple-600">{{ $lesson->contentable->provider }}</span>
                                    </p>
                                </div>
                            </div>
                        @endif

                        @php
                            // Check for embed code in metadata
                            $embedCode = null;
                            if ($lesson->contentable->metadata && isset($lesson->contentable->metadata['embed_code'])) {
                                $embedCode = $lesson->contentable->metadata['embed_code'];
                            }
                        @endphp

                        @if($embedCode)
                            {{-- Custom Embed Code (iframe, script, etc.) --}}
                            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                                <div class="embed-responsive-wrapper" style="position: relative; width: 100%; min-height: 400px;">
                                    {!! $embedCode !!}
                                </div>
                            </div>
                        @elseif($lesson->contentable->embed_url)
                            {{-- Embed URL as iframe --}}
                            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                                <div class="embed-responsive" style="position: relative; padding-bottom: 56.25%; height: 0;">
                                    <iframe
                                        src="{{ $lesson->contentable->embed_url }}"
                                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                                        frameborder="0"
                                        allowfullscreen
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        class="rounded-lg"
                                    ></iframe>
                                </div>
                            </div>
                        @else
                            {{-- No embed content available --}}
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-8 text-center">
                                <svg class="mx-auto h-16 w-16 text-yellow-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Embedded Content Available</h3>
                                <p class="text-gray-600">The embedded content could not be loaded. Please contact your instructor.</p>
                            </div>
                        @endif
                    </div>
                    @break
            @endswitch
        @endif

        <!-- Additional Resources -->
        @if($lesson->resources && $lesson->resources->count() > 0)
            <div class="mt-8 border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Resources</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($lesson->resources as $resource)
                        <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <svg class="h-8 w-8 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $resource->title }}</p>
                                @if($resource->description)
                                    <p class="text-sm text-gray-600">{{ $resource->description }}</p>
                                @endif
                            </div>
                            <a href="{{ asset('storage/' . $resource->file_path) }}" download class="ml-3 text-indigo-600 hover:text-indigo-800">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
