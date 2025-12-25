@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center text-sm text-gray-600 mb-4">
            <a href="{{ route('admin.assignments.index') }}" class="hover:text-indigo-600">Assignments</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('admin.assignments.show', $submission->assignment) }}" class="hover:text-indigo-600">{{ $submission->assignment->title }}</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('admin.assignment-submissions.show', $submission) }}" class="hover:text-indigo-600">Submission Details</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 font-medium">Grade Submission</span>
        </div>

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Grade Submission</h1>
                <p class="mt-2 text-gray-600">Student: {{ $submission->user->name }}</p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.assignment-submissions.submit-grade', $submission) }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Submission Content (Read Only) -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Submission Content</h2>
                    </div>
                    <div class="p-6">
                        @if($submission->content)
                        <div class="prose max-w-none">
                            {!! $submission->content !!}
                        </div>
                        @else
                        <p class="text-gray-500 italic">No text content provided</p>
                        @endif
                    </div>
                </div>

                <!-- Submitted Files (Read Only) -->
                @if($submission->assignmentFiles->isNotEmpty())
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-gradient-to-r from-cyan-50 to-blue-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-semibold text-gray-900">Submitted Files ({{ $submission->assignmentFiles->count() }})</h2>
                            @if($submission->assignmentFiles->count() > 1)
                            <a href="{{ route('admin.assignment-submissions.download-all', $submission) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Download All
                            </a>
                            @endif
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($submission->assignmentFiles as $file)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $file->file_name }}</p>
                                        <p class="text-sm text-gray-500">{{ number_format($file->file_size / 1024, 2) }} KB</p>
                                    </div>
                                </div>
                                <a href="{{ route('admin.assignment-files.download', $file) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Download
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Rubric-Based Grading -->
                @if($submission->assignment->rubrics->isNotEmpty())
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Rubric Grading</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        @foreach($submission->assignment->rubrics as $index => $rubric)
                            @php
                                $existingScore = $submission->rubricScores->where('rubric_id', $rubric->id)->first();
                            @endphp
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="mb-3">
                                    <h3 class="font-medium text-gray-900">{{ $rubric->criteria }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $rubric->description }}</p>
                                    <p class="text-sm text-gray-500 mt-1">Max Points: {{ $rubric->max_points }}</p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Points Earned</label>
                                        <input type="number"
                                               name="rubric_scores[{{ $index }}][points]"
                                               min="0"
                                               max="{{ $rubric->max_points }}"
                                               step="0.5"
                                               value="{{ old('rubric_scores.' . $index . '.points', $existingScore->points ?? 0) }}"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                               required>
                                        <input type="hidden" name="rubric_scores[{{ $index }}][rubric_id]" value="{{ $rubric->id }}">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Feedback (Optional)</label>
                                        <textarea name="rubric_scores[{{ $index }}][feedback]"
                                                  rows="2"
                                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                  placeholder="Specific feedback for this criterion">{{ old('rubric_scores.' . $index . '.feedback', $existingScore->feedback ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                            <p class="text-sm text-blue-800">
                                <strong>Note:</strong> The total score will be automatically calculated based on rubric scores. You can adjust the final score below if needed.
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Overall Grading -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Overall Grade</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Score -->
                        <div>
                            <label for="score" class="block text-sm font-medium text-gray-700">Score *</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number"
                                       name="score"
                                       id="score"
                                       min="0"
                                       max="{{ $submission->assignment->max_points }}"
                                       step="0.5"
                                       value="{{ old('score', $submission->score ?? 0) }}"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('score') border-red-300 @enderror"
                                       required>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">/ {{ $submission->assignment->max_points }}</span>
                                </div>
                            </div>
                            @error('score')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Passing score: {{ $submission->assignment->passing_points }}</p>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                            <select name="status"
                                    id="status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('status') border-red-300 @enderror"
                                    required>
                                <option value="graded" {{ old('status', $submission->status) === 'graded' ? 'selected' : '' }}>Graded (Student can view)</option>
                                <option value="returned" {{ old('status', $submission->status) === 'returned' ? 'selected' : '' }}>Returned (Graded but returned for revision)</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-red-600 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Passed -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox"
                                       name="passed"
                                       id="passed"
                                       value="1"
                                       {{ old('passed', $submission->passed ?? false) ? 'checked' : '' }}
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="passed" class="font-medium text-gray-700">Mark as Passed</label>
                                <p class="text-gray-500">Override automatic pass/fail based on passing score</p>
                            </div>
                        </div>

                        <!-- Feedback -->
                        <div>
                            <label for="feedback" class="block text-sm font-medium text-gray-700">Overall Feedback</label>
                            <textarea name="feedback"
                                      id="feedback"
                                      rows="6"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('feedback') border-red-300 @enderror"
                                      placeholder="Provide detailed feedback to the student...">{{ old('feedback', $submission->feedback ?? '') }}</textarea>
                            @error('feedback')
                                <p class="mt-1 text-red-600 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.assignment-submissions.show', $submission) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-6 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Submit Grade
                    </button>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md overflow-hidden sticky top-6">
                    <!-- Student Info -->
                    <div class="bg-gradient-to-r from-gray-50 to-slate-50 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Student Information</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center space-x-3">
                            <img src="{{ $submission->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($submission->user->name) }}" alt="{{ $submission->user->name }}" class="w-12 h-12 rounded-full">
                            <div>
                                <p class="font-medium text-gray-900">{{ $submission->user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $submission->user->email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submission Info -->
                    <div class="px-6 pb-6 space-y-3 border-t border-gray-200 pt-4">
                        <div>
                            <span class="block text-sm text-gray-600">Submitted</span>
                            <p class="mt-1 font-medium text-gray-900">{{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y H:i') : 'Not yet' }}</p>
                        </div>
                        <div>
                            <span class="block text-sm text-gray-600">Late Submission</span>
                            <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($submission->is_late) bg-red-100 text-red-800
                                @else bg-green-100 text-green-800
                                @endif">
                                {{ $submission->is_late ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        <div>
                            <span class="block text-sm text-gray-600">Attempt Number</span>
                            <p class="mt-1 font-medium text-gray-900">#{{ $submission->attempt_number }}</p>
                        </div>
                        <div>
                            <span class="block text-sm text-gray-600">Files Submitted</span>
                            <p class="mt-1 font-medium text-gray-900">{{ $submission->assignmentFiles->count() }}</p>
                        </div>
                    </div>

                    <!-- Assignment Info -->
                    <div class="px-6 pb-6 space-y-3 border-t border-gray-200 pt-4">
                        <div>
                            <span class="block text-sm text-gray-600">Max Points</span>
                            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $submission->assignment->max_points }}</p>
                        </div>
                        <div>
                            <span class="block text-sm text-gray-600">Passing Score</span>
                            <p class="mt-1 font-medium text-gray-900">{{ $submission->assignment->passing_points }}</p>
                        </div>
                        <div>
                            <span class="block text-sm text-gray-600">Due Date</span>
                            <p class="mt-1 font-medium {{ $submission->assignment->due_date && $submission->assignment->due_date->isPast() ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $submission->assignment->due_date ? $submission->assignment->due_date->format('M d, Y H:i') : 'No deadline' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
