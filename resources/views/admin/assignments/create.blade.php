@extends('layouts.admin')

@section('title', 'Create Assignment')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Create New Assignment</h2>
                @if($topic)
                    <p class="mt-1 text-sm text-gray-600">
                        Course: {{ $topic->course->title }} › Topic: {{ $topic->title }}
                    </p>
                @else
                    <p class="mt-1 text-sm text-gray-600">Create a new assignment for any topic</p>
                @endif
            </div>
            <a href="{{ $topic ? route('admin.topics.show', $topic) : route('admin.assignments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
        </div>
    </div>

    <form action="{{ route('admin.assignments.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information Card -->
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-blue-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Basic Information
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Topic -->
                        <div>
                            <label for="topic_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Topic <span class="text-red-500">*</span>
                            </label>
                            <select name="topic_id" id="topic_id" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select a topic</option>
                                @foreach($topics as $topicOption)
                                    <option value="{{ $topicOption->id }}" {{ old('topic_id', $topic?->id) == $topicOption->id ? 'selected' : '' }}>
                                        {{ $topicOption->course->title }} › {{ $topicOption->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('topic_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Assignment Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Enter assignment title...">
                            @error('title')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <x-quill-editor
                                name="description"
                                :value="old('description', '')"
                                label="Description"
                                :required="true"
                                height="200px"
                                placeholder="Describe the assignment..."
                            />
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Instructions -->
                        <div>
                            <x-quill-editor
                                name="instructions"
                                :value="old('instructions', '')"
                                label="Instructions (Optional)"
                                :required="false"
                                height="200px"
                                placeholder="Provide detailed instructions for students..."
                            />
                            @error('instructions')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Grading Settings Card -->
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-pink-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                            Grading Settings
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Max Points -->
                            <div>
                                <label for="max_points" class="block text-sm font-medium text-gray-700 mb-2">
                                    Maximum Points <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="max_points" id="max_points" value="{{ old('max_points', '100') }}" min="0" step="0.01" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('max_points')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Passing Points -->
                            <div>
                                <label for="passing_points" class="block text-sm font-medium text-gray-700 mb-2">
                                    Passing Points <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="passing_points" id="passing_points" value="{{ old('passing_points', '70') }}" min="0" step="0.01" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('passing_points')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submission Settings Card -->
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-green-50 to-teal-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Submission Settings
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Due Date -->
                        <div>
                            <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Due Date (Optional)
                            </label>
                            <input type="datetime-local" name="due_date" id="due_date" value="{{ old('due_date') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('due_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Late Submission -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="allow_late_submission" id="allow_late_submission" value="1" {{ old('allow_late_submission') ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="allow_late_submission" class="font-medium text-gray-700">Allow Late Submissions</label>
                                <p class="text-gray-500">Students can submit after the due date</p>
                            </div>
                        </div>

                        <!-- Late Penalty -->
                        <div>
                            <label for="late_penalty_percentage" class="block text-sm font-medium text-gray-700 mb-2">
                                Late Penalty (%)
                            </label>
                            <input type="number" name="late_penalty_percentage" id="late_penalty_percentage" value="{{ old('late_penalty_percentage', '0') }}" min="0" max="100" step="0.01" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-xs text-gray-500">Percentage deducted from score for late submissions</p>
                            @error('late_penalty_percentage')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submission Type -->
                        <div class="space-y-4 pt-4 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-700">Submission Type</h4>

                            <!-- Allow Text Submission -->
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="allow_text_submission" id="allow_text_submission" value="1" {{ old('allow_text_submission', true) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="allow_text_submission" class="font-medium text-gray-700">Allow Text Submission</label>
                                    <p class="text-gray-500">Students can submit text using the editor</p>
                                </div>
                            </div>

                            <!-- Allow File Upload -->
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="allow_file_upload" id="allow_file_upload" value="1" {{ old('allow_file_upload', true) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="allow_file_upload" class="font-medium text-gray-700">Allow File Upload</label>
                                    <p class="text-gray-500">Students can upload files with their submission</p>
                                </div>
                            </div>
                        </div>

                        <!-- File Upload Settings -->
                        <div class="space-y-4 pt-4 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-700">File Upload Settings</h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Max File Size -->
                                <div>
                                    <label for="max_file_size_mb" class="block text-sm font-medium text-gray-700 mb-2">
                                        Max File Size (MB)
                                    </label>
                                    <input type="number" name="max_file_size_mb" id="max_file_size_mb" value="{{ old('max_file_size_mb', '10') }}" min="1" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('max_file_size_mb')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Max Files -->
                                <div>
                                    <label for="max_files" class="block text-sm font-medium text-gray-700 mb-2">
                                        Max Files
                                    </label>
                                    <input type="number" name="max_files" id="max_files" value="{{ old('max_files', '5') }}" min="1" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>

                            <!-- Allowed File Types -->
                            <div>
                                <label for="allowed_file_types" class="block text-sm font-medium text-gray-700 mb-2">
                                    Allowed File Types (comma-separated)
                                </label>
                                <input type="text" name="allowed_file_types" id="allowed_file_types" value="{{ old('allowed_file_types', 'pdf, doc, docx, txt') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="pdf, doc, docx, txt">
                                <p class="mt-1 text-xs text-gray-500">Example: pdf, doc, docx, txt, jpg, png</p>
                                @error('allowed_file_types')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Settings Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white shadow-md rounded-lg overflow-hidden sticky top-6">
                    <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-yellow-50 to-orange-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Assignment Settings
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Display Order -->
                        <div>
                            <label for="order" class="block text-sm font-medium text-gray-700 mb-2">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"/>
                                    </svg>
                                    Display Order <span class="text-red-500">*</span>
                                </div>
                            </label>
                            <input type="number" name="order" id="order" value="{{ old('order', '0') }}" min="0" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('order')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Publish Status -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_published" class="font-medium text-gray-700">Published</label>
                                <p class="text-gray-500">Make this assignment visible to students</p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-6 border-t border-gray-200 space-y-3">
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Create Assignment
                            </button>
                            <a href="{{ $topic ? route('admin.topics.show', $topic) : route('admin.assignments.index') }}" class="w-full inline-flex justify-center items-center px-4 py-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
