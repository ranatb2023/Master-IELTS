@extends('layouts.admin')

@section('title', 'Certificate Details')
@section('page-title', 'Certificate Details')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('admin.certificates.index') }}"
                class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Certificates
            </a>
        </div>

        <!-- Certificate Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-8 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="bg-white/20 rounded-full p-4">
                            <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h1 class="text-2xl font-bold">Certificate of Completion</h1>
                            <p class="text-indigo-100">{{ $certificate->certificate_number }}</p>
                        </div>
                    </div>
                    @if ($certificate->is_revoked)
                        <span class="px-4 py-2 bg-red-600 text-white rounded-full text-sm font-semibold">REVOKED</span>
                    @else
                        <span class="px-4 py-2 bg-green-600 text-white rounded-full text-sm font-semibold">ACTIVE</span>
                    @endif
                </div>
            </div>

            <!-- Certificate Details -->
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Student Information</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Name</dt>
                                <dd class="text-base text-gray-900">{{ $certificate->user->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="text-base text-gray-900">{{ $certificate->user->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">User ID</dt>
                                <dd class="text-base font-mono text-gray-900">#{{ $certificate->user->id }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Certificate Information</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Course</dt>
                                <dd class="text-base text-gray-900">{{ $certificate->course->title }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Template</dt>
                                <dd class="text-base text-gray-900">{{ $certificate->certificateTemplate->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Issue Date</dt>
                                <dd class="text-base text-gray-900">{{ $certificate->issue_date->format('F d, Y') }}</dd>
                            </div>
                            @if ($certificate->expiry_date)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Expiry Date</dt>
                                    <dd class="text-base text-gray-900">
                                        {{ $certificate->expiry_date->format('F d, Y') }}
                                        @if ($certificate->isExpired())
                                            <span class="ml-2 text-sm text-red-600 font-semibold">(Expired)</span>
                                        @endif
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Download Stats -->
                <div class="border-t border-gray-200 pt-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Download Statistics</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm font-medium text-gray-500">Total Downloads</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $certificate->download_count }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm font-medium text-gray-500">Last Downloaded</p>
                            <p class="text-base text-gray-900">
                                {{ $certificate->last_downloaded_at ? $certificate->last_downloaded_at->diffForHumans() : 'Never' }}
                            </p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm font-medium text-gray-500">File Path</p>
                            <p class="text-sm font-mono text-gray-600 truncate">
                                {{ $certificate->file_path ?? 'Not generated' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Verification Info -->
                <div class="border-t border-gray-200 pt-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Verification</h3>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 mb-2">Verification URL</dt>
                            <dd class="flex items-center gap-2">
                                <input type="text" readonly value="{{ $certificate->verification_url }}"
                                    class="flex-1 text-sm bg-gray-50 border-gray-300 rounded-md" id="verification-url">
                                <button onclick="copyToClipboard('verification-url')"
                                    class="px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm">
                                    Copy
                                </button>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 mb-2">Verification Hash</dt>
                            <dd class="flex items-center gap-2">
                                <input type="text" readonly value="{{ substr($certificate->verification_hash, 0, 32) }}..."
                                    class="flex-1 text-sm bg-gray-50 border-gray-300 rounded-md font-mono"
                                    id="verification-hash">
                                <button onclick="copyToClipboard('verification-hash')"
                                    class="px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm">
                                    Copy
                                </button>
                            </dd>
                        </div>
                    </dl>
                </div>

                @if ($certificate->is_revoked)
                    <!-- Revocation Info -->
                    <div class="border-t border-gray-200 pt-6 mb-6">
                        <h3 class="text-lg font-semibold text-red-900 mb-4">Revocation Details</h3>
                        <div class="bg-red-50 rounded-lg p-4">
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-red-700">Revoked At</dt>
                                    <dd class="text-base text-red-900">{{ $certificate->revoked_at->format('F d, Y H:i') }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-red-700">Reason</dt>
                                    <dd class="text-base text-red-900">{{ $certificate->revoked_reason }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('admin.certificates.download', $certificate) }}"
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10">
                                </path>
                            </svg>
                            Download PDF
                        </a>

                        @if (!$certificate->is_revoked)
                            <button onclick="revokeCertificate()"
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                                    </path>
                                </svg>
                                Revoke Certificate
                            </button>
                        @else
                            <form method="POST" action="{{ route('admin.certificates.restore', $certificate) }}">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                        </path>
                                    </svg>
                                    Restore Certificate
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('admin.certificates.destroy', $certificate) }}"
                            onsubmit="return confirm('Are you sure? This will permanently delete the certificate and its PDF file.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function copyToClipboard(elementId) {
                const element = document.getElementById(elementId);
                element.select();
                document.execCommand('copy');
                alert('Copied to clipboard!');
            }

            function revokeCertificate() {
                const reason = prompt('Enter reason for revocation (minimum 10 characters):');
                if (reason && reason.length >= 10) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('admin.certificates.revoke', $certificate) }}';
                    form.innerHTML = `
                            @csrf
                            <input type="hidden" name="reason" value="${reason}">
                        `;
                    document.body.appendChild(form);
                    form.submit();
                } else if (reason !== null) {
                    alert('Reason must be at least 10 characters long.');
                }
            }
        </script>
    @endpush
@endsection