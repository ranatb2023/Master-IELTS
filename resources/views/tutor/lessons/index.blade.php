@extends('layouts.tutor')

@section('title', 'Lessons Management')
@section('page-title', 'All Lessons')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Lessons Management</h1>
                <p class="mt-1 text-sm text-gray-600">Manage all course lessons</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('tutor.courses.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm uppercase">
                    View Courses
                </a>
                @can('course.create')
                    <a href="{{ route('tutor.lessons.trash') }}"
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm uppercase">
                        View Trash
                    </a>
                @endcan
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white p-4 rounded-lg shadow">
            <form method="GET" action="{{ route('tutor.lessons.all') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search lessons..."
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <select name="course" class="w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">All Courses</option>
                        @foreach($courses ?? [] as $course)
                            <option value="{{ $course->id }}" {{ request('course') == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="topic" class="w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">All Topics</option>
                        @foreach($topics ?? [] as $topic)
                            <option value="{{ $topic->id }}" {{ request('topic') == $topic->id ? 'selected' : '' }}>
                                {{ $topic->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="content_type" class="w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">All Content Types</option>
                        <option value="video" {{ request('content_type') == 'video' ? 'selected' : '' }}>Video</option>
                        <option value="text" {{ request('content_type') == 'text' ? 'selected' : '' }}>Text</option>
                        <option value="document" {{ request('content_type') == 'document' ? 'selected' : '' }}>Document
                        </option>
                        <option value="audio" {{ request('content_type') == 'audio' ? 'selected' : '' }}>Audio</option>
                    </select>
                </div>

                <div class="flex space-x-2">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Filter</button>
                    <a href="{{ route('tutor.lessons.all') }}"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Reset</a>
                </div>
            </form>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Lessons</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $lessons->total() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Published</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $lessons->where('is_published', true)->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Preview Allowed</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $lessons->where('is_preview', true)->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Progress</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $lessons->sum('progress_count') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lessons Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lesson</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Topic / Course</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Content Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($lessons as $lesson)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $lesson->title }}</div>
                                        @if($lesson->description)
                                            <div class="text-sm text-gray-500 line-clamp-1">
                                                {{ Str::limit(strip_tags($lesson->description), 50) }}
                                            </div>
                                        @endif
                                        <div class="mt-1 flex items-center space-x-2">
                                            @if($lesson->is_preview)
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Preview</span>
                                            @endif
                                            @if($lesson->requires_previous_completion)
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">Sequential</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($lesson->topic)
                                        <div class="text-sm text-gray-900">{{ $lesson->topic->title }}</div>
                                        @if($lesson->topic->course)
                                            <div class="text-sm text-gray-500">{{ $lesson->topic->course->title }}</div>
                                        @endif
                                    @else
                                        <span class="text-sm text-red-600 italic">Topic Deleted</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($lesson->contentable)
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ class_basename($lesson->contentable_type) }}
                                        </span>
                                    @else
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">No
                                            Content</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $lesson->duration_minutes ? $lesson->duration_minutes . ' min' : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ $lesson->order }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($lesson->is_published)
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Published</span>
                                    @else
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-3">
                                        @can('course.view')
                                            <a href="{{ route('tutor.courses.topics.lessons.show', [$lesson->topic->course, $lesson->topic, $lesson]) }}"
                                                class="text-indigo-600 hover:text-indigo-900">View</a>
                                        @endcan
                                        @can('course.update')
                                            <a href="{{ route('tutor.courses.topics.lessons.edit', [$lesson->topic->course, $lesson->topic, $lesson]) }}"
                                                class="text-blue-600 hover:text-blue-900">Edit</a>
                                        @endcan
                                        @can('course.delete')
                                            <form
                                                action="{{ route('tutor.courses.topics.lessons.destroy', [$lesson->topic->course, $lesson->topic, $lesson]) }}"
                                                method="POST" class="inline" onsubmit="return confirm('Delete this lesson?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    No lessons found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($lessons->hasPages())
                <div class="px-4 py-3 border-t">
                    {{ $lessons->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection