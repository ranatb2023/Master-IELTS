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
            <span class="text-gray-900 font-medium">Submission Details</span>
        </div>

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Submission by {{ $submission->user->name }}</h1>
                <p class="mt-2 text-gray-600">{{ $submission->assignment->topic->course->title }} › {{ $submission->assignment->topic->title }}</p>
            </div>
            <div class="flex space-x-3">
                @if($submission->status !== 'graded')
                    <a href="{{ route('admin.assignment-submissions.grade', $submission) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        Grade Submission
                    </a>
                @else
                    <a href="{{ route('admin.assignment-submissions.grade', $submission) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Edit Grade
                    </a>
                @endif
                <a href="{{ route('admin.assignments.show', $submission->assignment) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Back to Assignment
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Submission Status</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <span class="block text-sm text-gray-600">Status</span>
                            <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($submission->status === 'graded') bg-green-100 text-green-800
                                @elseif($submission->status === 'submitted') bg-blue-100 text-blue-800
                                @elseif($submission->status === 'returned') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($submission->status) }}
                            </span>
                        </div>
                        <div>
                            <span class="block text-sm text-gray-600">Submitted</span>
                            <span class="mt-1 block font-medium">{{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y H:i') : 'Not yet' }}</span>
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
                            <span class="block text-sm text-gray-600">Attempt</span>
                            <span class="mt-1 block font-medium">#{{ $submission->attempt_number }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Score Card -->
            @if($submission->status === 'graded' || $submission->status === 'returned')
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Grading</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                        <div>
                            <span class="block text-sm text-gray-600">Score</span>
                            <span class="mt-1 block text-2xl font-bold text-gray-900">{{ $submission->score ?? 0 }}/{{ $submission->assignment->max_points }}</span>
                        </div>
                        <div>
                            <span class="block text-sm text-gray-600">Percentage</span>
                            <span class="mt-1 block text-2xl font-bold text-gray-900">{{ $submission->assignment->max_points > 0 ? round(($submission->score / $submission->assignment->max_points) * 100, 1) : 0 }}%</span>
                        </div>
                        <div>
                            <span class="block text-sm text-gray-600">Passing Score</span>
                            <span class="mt-1 block font-medium">{{ $submission->assignment->passing_points }}</span>
                        </div>
                        <div>
                            <span class="block text-sm text-gray-600">Result</span>
                            <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($submission->passed) bg-green-100 text-green-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ $submission->passed ? 'Passed' : 'Failed' }}
                            </span>
                        </div>
                    </div>

                    @if($submission->feedback)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Feedback</h3>
                        <div class="prose max-w-none text-gray-700">
                            {!! nl2br(e($submission->feedback)) !!}
                        </div>
                    </div>
                    @endif

                    @if($submission->graded_at)
                    <div class="mt-4 pt-4 border-t border-gray-200 text-sm text-gray-600">
                        Graded by {{ $submission->grader->name ?? 'System' }} on {{ $submission->graded_at->format('M d, Y H:i') }}
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Rubric Scores -->
            @if($submission->rubricScores->isNotEmpty())
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Rubric Scores</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($submission->rubricScores as $score)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-900">{{ $score->rubric->criteria }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $score->rubric->description }}</p>
                                </div>
                                <span class="ml-4 text-lg font-bold text-gray-900">{{ $score->points }}/{{ $score->rubric->max_points }}</span>
                            </div>
                            @if($score->feedback)
                            <div class="mt-2 pt-2 border-t border-gray-200">
                                <p class="text-sm text-gray-700">{{ $score->feedback }}</p>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Submission Content -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Submission Content</h2>
                </div>
                <div class="p-6">
                    @if($submission->submission_text || $submission->content)
                    <div class="prose max-w-none">
                        {!! $submission->submission_text ?? $submission->content !!}
                    </div>
                    @else
                    <p class="text-gray-500 italic">No text content provided</p>
                    @endif
                </div>
            </div>

            <!-- Submitted Files -->
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
                    <div class="space-y-4">
                        @foreach($submission->assignmentFiles as $file)
                        <div class="border border-gray-200 rounded-lg overflow-hidden" x-data="{ expanded: false }">
                            <div class="flex items-center justify-between p-4 bg-gray-50">
                                <div class="flex items-center flex-1 min-w-0">
                                    @php
                                        $icon = 'document';
                                        if (in_array($file->file_type, ['image/jpeg', 'image/png', 'image/gif'])) $icon = 'photograph';
                                        elseif ($file->file_type === 'application/pdf') $icon = 'document-text';
                                        elseif (in_array($file->file_type, ['application/zip', 'application/x-zip-compressed'])) $icon = 'archive';
                                    @endphp
                                    <svg class="w-8 h-8 text-gray-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($icon === 'photograph')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        @endif
                                    </svg>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $file->original_filename ?? $file->file_name }}</p>
                                        <p class="text-xs text-gray-500">{{ number_format($file->file_size / 1024, 2) }} KB • {{ strtoupper($file->file_type) }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    @php
                                        $previewableTypes = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'txt'];
                                        $isPreviewable = in_array(strtolower($file->file_type), $previewableTypes);
                                    @endphp

                                    @if($isPreviewable)
                                        <button @click="expanded = !expanded" class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!expanded">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="expanded" style="display: none;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            <span x-text="expanded ? 'Hide' : 'View'">View</span>
                                        </button>
                                    @endif
                                    <a href="{{ route('admin.assignment-files.download', $file) }}" class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        Download
                                    </a>
                                </div>
                            </div>

                            @if($isPreviewable)
                                <div x-show="expanded" x-transition class="p-6 bg-white border-t border-gray-200">
                                    @if(in_array(strtolower($file->file_type), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']))
                                        <div class="flex justify-center">
                                            <img src="{{ route('admin.assignment-files.view', $file) }}" alt="{{ $file->original_filename }}" class="max-w-full h-auto rounded-lg shadow-lg" style="max-height: 800px;">
                                        </div>
                                    @elseif(strtolower($file->file_type) === 'pdf')
                                        <iframe src="{{ route('admin.assignment-files.view', $file) }}" class="w-full rounded-lg border border-gray-300 shadow-inner" style="height: 85vh; min-height: 800px;"></iframe>
                                    @elseif(strtolower($file->file_type) === 'txt')
                                        <iframe src="{{ route('admin.assignment-files.view', $file) }}" class="w-full rounded-lg border border-gray-300 bg-gray-50" style="height: 600px;"></iframe>
                                    @endif
                                </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
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

                <!-- Assignment Info -->
                <div class="px-6 pb-6 space-y-3 border-t border-gray-200 pt-4">
                    <div>
                        <span class="block text-sm text-gray-600">Assignment</span>
                        <p class="mt-1 font-medium text-gray-900">{{ $submission->assignment->title }}</p>
                    </div>
                    <div>
                        <span class="block text-sm text-gray-600">Due Date</span>
                        <p class="mt-1 font-medium {{ $submission->assignment->due_date && $submission->assignment->due_date->isPast() ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $submission->assignment->due_date ? $submission->assignment->due_date->format('M d, Y H:i') : 'No deadline' }}
                        </p>
                    </div>
                    <div>
                        <span class="block text-sm text-gray-600">Max Points</span>
                        <p class="mt-1 font-medium text-gray-900">{{ $submission->assignment->max_points }}</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="px-6 pb-6 space-y-3 border-t border-gray-200 pt-4">
                    @can('delete', $submission->assignment)
                    <form action="{{ route('admin.assignment-submissions.destroy', $submission) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this submission? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete Submission
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
