@extends('layouts.tutor')

@section('title', 'Certificate Details')
@section('page-title', 'Certificate - ' . $certificate->certificate_number)

@section('content')
    <div class="space-y-6">
        <!-- Back Button -->
        <div>
            <a href="{{ route('tutor.certificates.index') }}" class="text-indigo-600 hover:text-indigo-900">
                ← Back to All Certificates
            </a>
        </div>

        <!-- Certificate Info -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Certificate Information</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Certificate Number</dt>
                            <dd class="text-sm text-gray-900 font-mono">{{ $certificate->certificate_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Issued To</dt>
                            <dd class="text-sm text-gray-900">{{ $certificate->user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Course</dt>
                            <dd class="text-sm text-gray-900">{{ $certificate->course->title }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Issued Date</dt>
                            <dd class="text-sm text-gray-900">{{ $certificate->issued_at->format('F d, Y') }}</dd>
                        </div>
                    </dl>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Student Information</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="text-sm text-gray-900">{{ $certificate->user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Completion Date</dt>
                            <dd class="text-sm text-gray-900">
                                {{ $certificate->completed_at ? $certificate->completed_at->format('F d, Y') : 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Certificate Preview -->
        @if($certificate->file_path)
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Certificate Preview</h3>
                <div class="flex justify-center">
                    <img src="{{ asset('storage/' . $certificate->file_path) }}" alt="Certificate"
                        class="max-w-full h-auto border-2 border-gray-200 rounded">
                </div>
            </div>
        @endif
    </div>
@endsection