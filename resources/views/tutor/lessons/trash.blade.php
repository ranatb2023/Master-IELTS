@extends('layouts.tutor')

@section('title', 'Trash - Lessons')
@section('page-title', 'Deleted Lessons')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Deleted Lessons (Trash)</h2>
                <p class="mt-1 text-sm text-gray-600">Restore or permanently delete lessons</p>
            </div>
            <a href="{{ route('tutor.lessons.all') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">
                ← Back to Lessons
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white p-4 rounded-lg shadow">
            <form method="GET" action="{{ route('tutor.lessons.trash') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search deleted lessons..."
                        class="w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <select name="topic" class="w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">All Topics</option>
                        @foreach($topics ?? [] as $topic)
                            <option value="{{ $topic->id }}" {{ request('topic') == $topic->id ? 'selected' : '' }}>
                                {{ $topic->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex space-x-2">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Filter</button>
                    <a href="{{ route('tutor.lessons.trash') }}"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Reset</a>
                </div>
            </form>
        </div>

        <!-- Lessons Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lesson</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Topic</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deleted At</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($lessons ?? [] as $lesson)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $lesson->title }}</div>
                                <div class="text-sm text-gray-500">Order: {{ $lesson->order }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $lesson->topic->title ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $lesson->topic->course->title ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $lesson->deleted_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                                <form method="POST" action="{{ route('tutor.lessons.restore', $lesson->id) }}"
                                    class="inline-block">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Restore this lesson?')"
                                        class="text-green-600 hover:text-green-900">
                                        Restore
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('tutor.lessons.force-delete', $lesson->id) }}"
                                    class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('Permanently delete this lesson? This cannot be undone!')"
                                        class="text-red-600 hover:text-red-900">
                                        Delete Forever
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                No deleted lessons found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if(isset($lessons) && $lessons->hasPages())
                <div class="px-4 py-3 border-t">
                    {{ $lessons->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection