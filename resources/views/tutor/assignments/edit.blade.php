@extends('layouts.tutor')

@section('title', 'Edit Assignment')
@section('page-title', 'Edit Assignment')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Edit Assignment</h2>
            <p class="mt-1 text-sm text-gray-600">Update assignment settings and manage submissions</p>
        </div>
        <a href="{{ route('tutor.courses.show', $assignment->topic->course_id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
            Back to Course
        </a>
    </div>

    <!-- Assignment Settings Form -->
    <form action="{{ route('tutor.assignments.update', $assignment) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        <input type="hidden" name="topic_id" value="{{ $assignment->topic_id }}">

        <!-- Basic Information -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Assignment Title *</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $assignment->title) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description *</label>
                    <textarea name="description" id="description" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $assignment->description) }}</textarea>
                </div>

                <div>
                    <label for="instructions" class="block text-sm font-medium text-gray-700">Instructions</label>
                    <textarea name="instructions" id="instructions" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('instructions', $assignment->instructions) }}</textarea>
                </div>

                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700">Display Order</label>
                    <input type="number" name="order" id="order" value="{{ old('order', $assignment->order) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <!-- Grading Settings -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Grading Settings</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="max_points" class="block text-sm font-medium text-gray-700">Maximum Points *</label>
                    <input type="number" name="max_points" id="max_points" value="{{ old('max_points', $assignment->max_points) }}" min="1" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="passing_points" class="block text-sm font-medium text-gray-700">Passing Points *</label>
                    <input type="number" name="passing_points" id="passing_points" value="{{ old('passing_points', $assignment->passing_points) }}" min="0" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <div class="mt-6 space-y-3">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="auto_grade" value="1" {{ old('auto_grade', $assignment->auto_grade) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Enable automatic grading</span>
                </label>
                <br>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="require_passing" value="1" {{ old('require_passing', $assignment->require_passing) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Students must pass to continue</span>
                </label>
            </div>
        </div>

        <!-- Submission Settings -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Submission Settings</h3>
            <div class="space-y-4">
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                    <input type="datetime-local" name="due_date" id="due_date" value="{{ old('due_date', $assignment->due_date ? $assignment->due_date->format('Y-m-d\TH:i') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="allow_late_submission" value="1" {{ old('allow_late_submission', $assignment->allow_late_submission) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                            <span class="ml-2 text-sm text-gray-700">Allow late submissions</span>
                        </label>
                    </div>

                    <div>
                        <label for="late_penalty" class="block text-sm font-medium text-gray-700">Late Penalty (% per day)</label>
                        <input type="number" name="late_penalty" id="late_penalty" value="{{ old('late_penalty', $assignment->late_penalty) }}" min="0" max="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- File Upload Settings -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">File Upload Settings</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="max_file_size" class="block text-sm font-medium text-gray-700">Max File Size (MB)</label>
                    <input type="number" name="max_file_size" id="max_file_size" value="{{ old('max_file_size', $assignment->max_file_size) }}" min="1" max="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="max_files" class="block text-sm font-medium text-gray-700">Maximum Files Allowed</label>
                    <input type="number" name="max_files" id="max_files" value="{{ old('max_files', $assignment->max_files) }}" min="1" max="20" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Allowed File Types</label>
                @php
                    $allowedTypes = old('allowed_file_types', json_decode($assignment->allowed_file_types ?? '[]', true));
                @endphp
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="allowed_file_types[]" value="pdf" {{ in_array('pdf', $allowedTypes) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">PDF</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="allowed_file_types[]" value="doc" {{ in_array('doc', $allowedTypes) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">DOC/DOCX</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="allowed_file_types[]" value="xls" {{ in_array('xls', $allowedTypes) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">XLS/XLSX</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="allowed_file_types[]" value="ppt" {{ in_array('ppt', $allowedTypes) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">PPT/PPTX</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="allowed_file_types[]" value="txt" {{ in_array('txt', $allowedTypes) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">TXT</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="allowed_file_types[]" value="zip" {{ in_array('zip', $allowedTypes) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">ZIP</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="allowed_file_types[]" value="jpg" {{ in_array('jpg', $allowedTypes) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">JPG/PNG</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="allowed_file_types[]" value="mp4" {{ in_array('mp4', $allowedTypes) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">MP4/Video</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Additional Settings -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Settings</h3>
            <div class="space-y-3">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published', $assignment->is_published) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Publish immediately</span>
                </label>
                <br>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="show_in_feed" value="1" {{ old('show_in_feed', $assignment->show_in_feed) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Show in course feed</span>
                </label>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('tutor.courses.show', $assignment->topic->course_id) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                Update Assignment
            </button>
        </div>
    </form>

    <!-- Submissions Management -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900">Student Submissions</h3>
                <p class="mt-1 text-sm text-gray-600">
                    {{ $assignment->submissions->count() }} submissions |
                    {{ $assignment->submissions->where('status', 'pending')->count() }} pending |
                    {{ $assignment->submissions->where('status', 'graded')->count() }} graded
                </p>
            </div>
            <a href="{{ route('tutor.assignment-submissions.index', $assignment) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                View All Submissions
            </a>
        </div>

        @if($assignment->submissions->count() > 0)
        <div class="space-y-3">
            @foreach($assignment->submissions->take(5) as $submission)
            <div class="border border-gray-200 rounded-lg p-4 flex items-center justify-between hover:border-gray-300">
                <div class="flex items-center space-x-4">
                    <img src="{{ $submission->student->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($submission->student->name) }}" alt="{{ $submission->student->name }}" class="w-10 h-10 rounded-full">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $submission->student->name }}</p>
                        <p class="text-xs text-gray-500">Submitted {{ $submission->submitted_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @if($submission->status === 'graded')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ $submission->score }}/{{ $assignment->max_points }}
                    </span>
                    @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        Pending
                    </span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No submissions yet</h3>
            <p class="mt-1 text-sm text-gray-500">Students haven't submitted any work for this assignment.</p>
        </div>
        @endif
    </div>

    <!-- Delete Assignment -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-start justify-between">
            <div>
                <h3 class="text-lg font-medium text-red-900">Delete Assignment</h3>
                <p class="mt-1 text-sm text-gray-600">Permanently delete this assignment and all submissions. This action cannot be undone.</p>
            </div>
            <form action="{{ route('tutor.assignments.destroy', $assignment) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this assignment? All student submissions will be deleted as well.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                    Delete Assignment
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
