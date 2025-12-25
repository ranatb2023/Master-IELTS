@extends('layouts.tutor')

@section('title', 'Create Assignment')
@section('page-title', 'Create Assignment')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Create Assignment</h2>
            <p class="mt-1 text-sm text-gray-600">Create an assignment for students to complete</p>
        </div>
        <a href="{{ route('tutor.courses.show', request('course_id', session('course_id'))) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
            Back to Course
        </a>
    </div>

    <form action="{{ route('tutor.assignments.store') }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="topic_id" value="{{ request('topic_id') }}">

        <!-- Basic Information -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Assignment Title *</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description *</label>
                    <textarea name="description" id="description" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Provide a detailed description of what students need to do</p>
                </div>

                <div>
                    <label for="instructions" class="block text-sm font-medium text-gray-700">Instructions</label>
                    <textarea name="instructions" id="instructions" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('instructions') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Step-by-step instructions for completing the assignment</p>
                </div>

                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700">Display Order</label>
                    <input type="number" name="order" id="order" value="{{ old('order', 1) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <!-- Grading Settings -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Grading Settings</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="max_points" class="block text-sm font-medium text-gray-700">Maximum Points *</label>
                    <input type="number" name="max_points" id="max_points" value="{{ old('max_points', 100) }}" min="1" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="passing_points" class="block text-sm font-medium text-gray-700">Passing Points *</label>
                    <input type="number" name="passing_points" id="passing_points" value="{{ old('passing_points', 70) }}" min="0" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">Minimum points required to pass</p>
                </div>
            </div>

            <div class="mt-6 space-y-3">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="auto_grade" value="1" {{ old('auto_grade') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Enable automatic grading (if applicable)</span>
                </label>
                <br>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="require_passing" value="1" {{ old('require_passing') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
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
                    <input type="datetime-local" name="due_date" id="due_date" value="{{ old('due_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">Leave blank for no due date</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="allow_late_submission" value="1" {{ old('allow_late_submission', true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                            <span class="ml-2 text-sm text-gray-700">Allow late submissions</span>
                        </label>
                    </div>

                    <div>
                        <label for="late_penalty" class="block text-sm font-medium text-gray-700">Late Penalty (% per day)</label>
                        <input type="number" name="late_penalty" id="late_penalty" value="{{ old('late_penalty', 0) }}" min="0" max="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                    <input type="number" name="max_file_size" id="max_file_size" value="{{ old('max_file_size', 10) }}" min="1" max="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="max_files" class="block text-sm font-medium text-gray-700">Maximum Files Allowed</label>
                    <input type="number" name="max_files" id="max_files" value="{{ old('max_files', 5) }}" min="1" max="20" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Allowed File Types</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="allowed_file_types[]" value="pdf" {{ is_array(old('allowed_file_types')) && in_array('pdf', old('allowed_file_types')) ? 'checked' : 'checked' }} class="rounded border-gray-300 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">PDF</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="allowed_file_types[]" value="doc" {{ is_array(old('allowed_file_types')) && in_array('doc', old('allowed_file_types')) ? 'checked' : 'checked' }} class="rounded border-gray-300 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">DOC/DOCX</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="allowed_file_types[]" value="xls" {{ is_array(old('allowed_file_types')) && in_array('xls', old('allowed_file_types')) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">XLS/XLSX</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="allowed_file_types[]" value="ppt" {{ is_array(old('allowed_file_types')) && in_array('ppt', old('allowed_file_types')) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">PPT/PPTX</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="allowed_file_types[]" value="txt" {{ is_array(old('allowed_file_types')) && in_array('txt', old('allowed_file_types')) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">TXT</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="allowed_file_types[]" value="zip" {{ is_array(old('allowed_file_types')) && in_array('zip', old('allowed_file_types')) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">ZIP</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="allowed_file_types[]" value="jpg" {{ is_array(old('allowed_file_types')) && in_array('jpg', old('allowed_file_types')) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">JPG/PNG</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="allowed_file_types[]" value="mp4" {{ is_array(old('allowed_file_types')) && in_array('mp4', old('allowed_file_types')) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
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
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Publish immediately</span>
                </label>
                <br>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="show_in_feed" value="1" {{ old('show_in_feed', true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Show in course feed</span>
                </label>
                <br>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="notify_students" value="1" {{ old('notify_students', true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Notify enrolled students</span>
                </label>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('tutor.courses.show', request('course_id', session('course_id'))) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                Create Assignment
            </button>
        </div>
    </form>
</div>
@endsection
