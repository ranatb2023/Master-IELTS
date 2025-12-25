@extends('layouts.learning')

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Submission Status Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-8">
                    <!-- Status Header -->
                    <div class="text-center mb-6">
                        @if($submission->status === 'graded')
                            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 mb-4">
                                <svg class="w-12 h-12 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <h1 class="text-2xl font-bold text-green-600 mb-2">Graded</h1>
                            <div class="text-4xl font-bold text-gray-900 mb-2">
                                {{ $submission->score }}<span
                                    class="text-2xl text-gray-500">/{{ $assignment->max_points }}</span>
                            </div>
                            <div class="text-gray-600">
                                {{ round(($submission->score / $assignment->max_points) * 100, 1) }}%
                            </div>
                        @elseif($submission->status === 'submitted')
                            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-blue-100 mb-4">
                                <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h1 class="text-2xl font-bold text-blue-600 mb-2">Awaiting Grade</h1>
                            <p class="text-gray-600">Your instructor will review your submission soon</p>
                        @else
                            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
                                <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h1 class="text-2xl font-bold text-gray-600 mb-2">Draft</h1>
                            <p class="text-gray-600">This submission is saved as a draft</p>
                        @endif
                    </div>

                    <!-- Submission Info Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-6 border-t border-gray-200">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-sm text-gray-600 mb-1">Submitted</div>
                            <div class="text-sm font-medium text-gray-900">
                                {{ $submission->submitted_at->format('M d, Y') }}
                                <div class="text-xs text-gray-500">{{ $submission->submitted_at->format('g:i A') }}</div>
                            </div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-sm text-gray-600 mb-1">Status</div>
                            <div>
                                @if($submission->status === 'graded')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Graded
                                    </span>
                                @elseif($submission->status === 'submitted')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Awaiting Grade
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Draft
                                    </span>
                                @endif
                                @if($submission->is_late)
                                    <div class="mt-1">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Late
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-sm text-gray-600 mb-1">Max Score</div>
                            <div class="text-sm font-medium text-gray-900">{{ $assignment->max_points }} points</div>
                        </div>
                    </div>

                    @if($submission->status === 'graded' && $submission->graded_at && $submission->grader)
                        <div class="mt-4 pt-4 border-t border-gray-200 text-center text-sm text-gray-600">
                            Graded by <span class="font-medium">{{ $submission->grader->name }}</span> on
                            {{ $submission->graded_at->format('M d, Y g:i A') }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Submission Content -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Your Submission</h3>
                </div>
                <div class="p-6">
                    <!-- Text Submission -->
                    @if($submission->submission_text)
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Submission Text</label>
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="prose max-w-none">
                                    {!! $submission->submission_text !!}
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Attached Files -->
                    @if($submission->assignmentFiles && $submission->assignmentFiles->count() > 0)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Attached Files</label>
                            <div class="space-y-4">
                                @foreach($submission->assignmentFiles as $file)
                                    <div class="border border-gray-200 rounded-lg overflow-hidden" x-data="{ expanded: false }">
                                        <div class="flex items-center justify-between p-4 bg-gray-50">
                                            <div class="flex items-center flex-1 min-w-0">
                                                <svg class="w-5 h-5 text-gray-400 mr-3 flex-shrink-0" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">
                                                        {{ $file->original_filename }}</p>
                                                    <p class="text-xs text-gray-500">{{ number_format($file->file_size / 1024, 2) }}
                                                        KB • {{ strtoupper($file->file_type) }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                @php
                                                    $previewableTypes = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'txt'];
                                                    $isPreviewable = in_array(strtolower($file->file_type), $previewableTypes);
                                                @endphp

                                                @if($isPreviewable)
                                                    <button @click="expanded = !expanded"
                                                        class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                            x-show="!expanded">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                            x-show="expanded" style="display: none;">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                        <span x-text="expanded ? 'Hide' : 'View'">View</span>
                                                    </button>
                                                @endif
                                                <a href="{{ route('student.assignments.download-file', [$assignment, $submission, $file]) }}"
                                                    class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                    Download
                                                </a>
                                            </div>
                                        </div>

                                        @if($isPreviewable)
                                            <div x-show="expanded" x-transition class="p-6 bg-white border-t border-gray-200">
                                                @if(in_array(strtolower($file->file_type), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']))
                                                    <div class="flex justify-center">
                                                        <img src="{{ route('student.assignments.view-file', [$assignment, $file]) }}"
                                                            alt="{{ $file->original_filename }}"
                                                            class="max-w-full h-auto rounded-lg shadow-lg" style="max-height: 800px;">
                                                    </div>
                                                @elseif(strtolower($file->file_type) === 'pdf')
                                                    <iframe src="{{ route('student.assignments.view-file', [$assignment, $file]) }}"
                                                        class="w-full rounded-lg border border-gray-300 shadow-inner"
                                                        style="height: 85vh; min-height: 800px;"></iframe>
                                                @elseif(strtolower($file->file_type) === 'txt')
                                                    <iframe src="{{ route('student.assignments.view-file', [$assignment, $file]) }}"
                                                        class="w-full rounded-lg border border-gray-300 bg-gray-50"
                                                        style="height: 600px;"></iframe>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(!$submission->submission_text && (!$submission->assignmentFiles || $submission->assignmentFiles->count() === 0))
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p>No submission content</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Grading Details -->
            @if($submission->status === 'graded')
                <!-- Instructor Feedback -->
                @if($submission->feedback)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                </svg>
                                Instructor Feedback
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="prose max-w-none">
                                {!! nl2br(e($submission->feedback)) !!}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Rubric Scores -->
                @if($submission->rubricScores && $submission->rubricScores->count() > 0)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Rubric Scores</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Criteria</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Score</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Comments</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($submission->rubricScores as $rubricScore)
                                        <tr>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                                {{ $rubricScore->rubric->criteria }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-700">
                                                {{ $rubricScore->rubric->description }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-bold text-gray-900">
                                                    {{ $rubricScore->score }}<span
                                                        class="text-gray-500">/{{ $rubricScore->rubric->max_score }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-700">
                                                {{ $rubricScore->comments ?? '—' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="bg-gray-50 font-bold">
                                        <td colspan="2" class="px-6 py-4 text-sm text-gray-900 text-right">
                                            Total Score:
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $submission->score }}<span
                                                class="text-gray-500">/{{ $assignment->max_points }}</span>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Actions -->
            <div class="flex items-center justify-between">
                <a href="{{ route('student.assignments.show', $assignment) }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    ← Back to Assignment
                </a>
                <a href="{{ route('student.courses.learn', $course) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Continue Learning →
                </a>
            </div>
        </div>
    </div>
@endsection