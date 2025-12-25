@extends('layouts.student')

@section('content')
    <div class="py-12" x-data="{ viewMode: 'grid' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">My Notes</h2>
                        <p class="mt-1 text-sm text-gray-600">Organize your learning notes</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('student.notes.trashed') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Trash
                        </a>
                        <a href="{{ route('student.notes.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            New Note
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filters & Search -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
                <form method="GET" action="{{ route('student.notes.index') }}" class="space-y-4">
                    <!-- Search Bar -->
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <label for="search" class="sr-only">Search notes</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" name="search" id="search" value="{{ request('search') }}"
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="Search notes by title or content...">
                            </div>
                        </div>
                        <button type="submit"
                            class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                            Search
                        </button>
                    </div>

                    <!-- Filters -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4"
                        x-data="{ showFilters: {{ request()->hasAny(['course_id', 'lesson_id', 'color', 'tag', 'pinned']) ? 'true' : 'false' }} }">
                        <button type="button" @click="showFilters = !showFilters"
                            class="md:col-span-4 text-left text-sm font-medium text-gray-700 hover:text-indigo-600 flex items-center">
                            <svg class="w-4 h-4 mr-2 transform transition-transform" :class="{ 'rotate-90': showFilters }"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            <span x-text="showFilters ? 'Hide Filters' : 'Show Filters'"></span>
                        </button>

                        <div x-show="showFilters" x-collapse class="md:col-span-4">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pt-4">
                                <!-- Course Filter -->
                                <div>
                                    <label for="course_id"
                                        class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                                    <select name="course_id" id="course_id"
                                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">All Courses</option>
                                        @foreach ($userCourses as $course)
                                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                                {{ $course->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Color Filter -->
                                <div>
                                    <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                                    <select name="color" id="color"
                                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">All Colors</option>
                                        <option value="yellow" {{ request('color') == 'yellow' ? 'selected' : '' }}>🟡 Yellow
                                        </option>
                                        <option value="green" {{ request('color') == 'green' ? 'selected' : '' }}>🟢 Green
                                        </option>
                                        <option value="blue" {{ request('color') == 'blue' ? 'selected' : '' }}>🔵 Blue
                                        </option>
                                        <option value="red" {{ request('color') == 'red' ? 'selected' : '' }}>🔴 Red</option>
                                        <option value="purple" {{ request('color') == 'purple' ? 'selected' : '' }}>🟣 Purple
                                        </option>
                                        <option value="pink" {{ request('color') == 'pink' ? 'selected' : '' }}>🩷 Pink
                                        </option>
                                        <option value="orange" {{ request('color') == 'orange' ? 'selected' : '' }}>🟠 Orange
                                        </option>
                                        <option value="gray" {{ request('color') == 'gray' ? 'selected' : '' }}>⚪ Gray
                                        </option>
                                    </select>
                                </div>

                                <!-- Tag Filter -->
                                <div>
                                    <label for="tag" class="block text-sm font-medium text-gray-700 mb-1">Tag</label>
                                    <select name="tag" id="tag"
                                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">All Tags</option>
                                        @foreach ($allTags as $tag)
                                            <option value="{{ $tag }}" {{ request('tag') == $tag ? 'selected' : '' }}>
                                                {{ $tag }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Pinned Filter -->
                                <div>
                                    <label for="pinned" class="block text-sm font-medium text-gray-700 mb-1">Show</label>
                                    <select name="pinned" id="pinned"
                                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">All Notes</option>
                                        <option value="1" {{ request('pinned') == '1' ? 'selected' : '' }}>Pinned Only
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            @if ($notes->count() > 0)
                <!-- Notes Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($notes as $note)
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

                        <div class="relative {{ $bgClass }} border-2 rounded-lg p-6 hover:shadow-lg transition-shadow duration-200">
                            <!-- Pin Indicator -->
                            @if ($note->is_pinned)
                                <div class="absolute top-3 right-3">
                                    <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L11 4.323V3a1 1 0 011-1z" />
                                    </svg>
                                </div>
                            @endif

                            <!-- Note Title -->
                            <h3 class="text-lg font-bold text-gray-900 mb-2 pr-8">
                                <a href="{{ route('student.notes.show', $note) }}" class="hover:text-indigo-600">
                                    {{ $note->title }}
                                </a>
                            </h3>

                            <!-- Note Excerpt -->
                            <p class="text-sm text-gray-600 mb-4 line-clamp-3">
                                {{ $note->getExcerpt(120) }}
                            </p>

                            <!-- Meta Information -->
                            <div class="space-y-2 mb-4 text-xs text-gray-500">
                                @if ($note->lesson)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                        <span class="truncate">{{ $note->lesson->title }}</span>
                                    </div>
                                @endif
                                @if ($note->course)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                        <span class="truncate">{{ $note->course->title }}</span>
                                    </div>
                                @endif
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $note->created_at->diffForHumans() }}
                                </div>
                                @if ($note->attachments->count() > 0)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        {{ $note->attachments->count() }}
                                        {{ Str::plural('attachment', $note->attachments->count()) }}
                                    </div>
                                @endif
                            </div>

                            <!-- Tags -->
                            @if ($note->tags && count($note->tags) > 0)
                                <div class="flex flex-wrap gap-1 mb-4">
                                    @foreach ($note->tags as $tag)
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Actions -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <a href="{{ route('student.notes.show', $note) }}"
                                    class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                    View →
                                </a>
                                <div class="flex items-center gap-2">
                                    <form action="{{ route('student.notes.toggle-pin', $note) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-gray-400 hover:text-indigo-600" title="Pin/Unpin">
                                            <svg class="w-5 h-5" fill="{{ $note->is_pinned ? 'currentColor' : 'none' }}"
                                                stroke="currentColor" viewBox="0 0 20 20">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L11 4.323V3a1 1 0 011-1z" />
                                            </svg>
                                        </button>
                                    </form>
                                    <a href="{{ route('student.notes.edit', $note) }}" class="text-gray-400 hover:text-indigo-600"
                                        title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('student.notes.destroy', $note) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Move this note to trash?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600" title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $notes->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No notes found</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        @if (request()->hasAny(['search', 'course_id', 'color', 'tag', 'pinned']))
                            Try adjusting your search or filters
                        @else
                            Get started by creating your first note
                        @endif
                    </p>
                    <div class="mt-6">
                        @if (request()->hasAny(['search', 'course_id', 'color', 'tag', 'pinned']))
                            <a href="{{ route('student.notes.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition mr-3">
                                Clear Filters
                            </a>
                        @endif
                        <a href="{{ route('student.notes.create') }}"
                            class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Create Your First Note
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection