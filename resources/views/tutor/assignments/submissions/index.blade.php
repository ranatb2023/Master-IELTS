@extends('layouts.tutor')

@section('title', 'Assignment Submissions')
@section('page-title', 'Assignment Submissions')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $assignment->title }}</h2>
            <p class="mt-1 text-sm text-gray-600">Review and grade student submissions</p>
        </div>
        <a href="{{ route('tutor.assignments.edit', $assignment) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
            Back to Assignment
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Submissions</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $submissions->total() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $submissions->where('status', 'pending')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Graded</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $submissions->where('status', 'graded')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Average Score</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ $submissions->where('status', 'graded')->avg('score') ? number_format($submissions->where('status', 'graded')->avg('score'), 1) : 'N/A' }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" action="{{ route('tutor.assignment-submissions.index', $assignment) }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Submissions</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="graded" {{ request('status') === 'graded' ? 'selected' : '' }}>Graded</option>
                    <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>Late Submissions</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-sm font-medium text-gray-700">Search Student</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Student name..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                Filter
            </button>
            @if(request()->hasAny(['status', 'search']))
            <a href="{{ route('tutor.assignment-submissions.index', $assignment) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200">
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Submissions List -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if($submissions->count() > 0)
        <ul class="divide-y divide-gray-200">
            @foreach($submissions as $submission)
            <li class="hover:bg-gray-50">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 flex-1">
                            <img src="{{ $submission->student->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($submission->student->name) }}" alt="{{ $submission->student->name }}" class="w-12 h-12 rounded-full">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ $submission->student->name }}</p>
                                <p class="text-xs text-gray-500">{{ $submission->student->email }}</p>
                                <div class="mt-1 flex items-center space-x-4 text-xs text-gray-500">
                                    <span class="flex items-center">
                                        <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Submitted {{ $submission->submitted_at->diffForHumans() }}
                                    </span>
                                    @if($submission->is_late)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                        Late
                                    </span>
                                    @endif
                                    @if($submission->files->count() > 0)
                                    <span class="flex items-center">
                                        <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                        </svg>
                                        {{ $submission->files->count() }} {{ Str::plural('file', $submission->files->count()) }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            @if($submission->status === 'graded')
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ $submission->score }}/{{ $assignment->max_points }}</p>
                                <p class="text-xs text-gray-500">{{ number_format(($submission->score / $assignment->max_points) * 100, 1) }}%</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $submission->score >= $assignment->passing_points ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $submission->score >= $assignment->passing_points ? 'Pass' : 'Fail' }}
                                </span>
                            </div>
                            @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Pending Review
                            </span>
                            @endif
                            <button @click="$dispatch('open-grading-modal', { submissionId: {{ $submission->id }} })" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                {{ $submission->status === 'graded' ? 'Review' : 'Grade' }}
                            </button>
                        </div>
                    </div>

                    <!-- Files List -->
                    @if($submission->files->count() > 0)
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach($submission->files as $file)
                        <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="inline-flex items-center px-3 py-1 bg-gray-100 border border-gray-300 rounded-md text-xs font-medium text-gray-700 hover:bg-gray-200">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                            {{ basename($file->file_path) }}
                        </a>
                        @endforeach
                    </div>
                    @endif

                    <!-- Feedback Preview -->
                    @if($submission->status === 'graded' && $submission->feedback)
                    <div class="mt-4 bg-gray-50 rounded-lg p-3">
                        <p class="text-xs font-medium text-gray-700 mb-1">Feedback:</p>
                        <p class="text-xs text-gray-600">{{ Str::limit($submission->feedback, 150) }}</p>
                    </div>
                    @endif
                </div>
            </li>
            @endforeach
        </ul>

        <!-- Pagination -->
        @if($submissions->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $submissions->links() }}
        </div>
        @endif
        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No submissions found</h3>
            <p class="mt-1 text-sm text-gray-500">
                @if(request()->hasAny(['status', 'search']))
                    Try adjusting your filters.
                @else
                    Students haven't submitted any work yet.
                @endif
            </p>
        </div>
        @endif
    </div>
</div>

<!-- Grading Modal (Alpine.js) -->
<div x-data="gradingModal()" @open-grading-modal.window="openModal($event.detail.submissionId)" x-show="isOpen" class="fixed z-10 inset-0 overflow-y-auto" style="display: none;">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="isOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div x-show="isOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6">
            <div class="absolute top-0 right-0 pt-4 pr-4">
                <button @click="closeModal()" type="button" class="bg-white rounded-md text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form :action="'/tutor/assignment-submissions/' + submissionId + '/grade'" method="POST">
                @csrf
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Grade Submission</h3>

                        <div class="mt-6 space-y-4">
                            <div>
                                <label for="score" class="block text-sm font-medium text-gray-700">Score (out of {{ $assignment->max_points }}) *</label>
                                <input type="number" name="score" id="score" x-model="score" min="0" max="{{ $assignment->max_points }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label for="feedback" class="block text-sm font-medium text-gray-700">Feedback *</label>
                                <textarea name="feedback" id="feedback" rows="6" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Provide detailed feedback for the student..."></textarea>
                            </div>

                            <div>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="notify_student" value="1" checked class="rounded border-gray-300 text-indigo-600">
                                    <span class="ml-2 text-sm text-gray-700">Notify student via email</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">
                        Submit Grade
                    </button>
                    <button @click="closeModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function gradingModal() {
    return {
        isOpen: false,
        submissionId: null,
        score: 0,
        openModal(id) {
            this.submissionId = id;
            this.isOpen = true;
        },
        closeModal() {
            this.isOpen = false;
            this.submissionId = null;
            this.score = 0;
        }
    }
}
</script>
@endsection
