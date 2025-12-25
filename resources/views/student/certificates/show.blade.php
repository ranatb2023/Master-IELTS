@extends('layouts.student')

@section('title', 'Certificate - ' . $certificate->course->title)
@section('page-title', 'Certificate Details')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('student.certificates.index') }}"
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
                <div class="flex items-center justify-center mb-4">
                    <div class="bg-white/20 rounded-full p-4">
                        <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z">
                            </path>
                        </svg>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-center">Certificate of Completion</h1>
                <p class="text-center text-indigo-100 mt-2">{{ $certificate->course->title }}</p>
            </div>

            <!-- Certificate Details -->
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Recipient</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $certificate->user->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Issue Date</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $certificate->issue_date->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Certificate Number</label>
                        <p class="text-lg font-mono font-semibold text-gray-900">{{ $certificate->certificate_number }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Template Used</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $certificate->certificateTemplate->name }}</p>
                    </div>
                </div>

                <!-- Verification Info -->
                <div class="bg-gray-50 rounded-lg p-6 mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Verification Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Verification URL</label>
                            <div class="flex items-center gap-2">
                                <input type="text" readonly value="{{ $certificate->verification_url }}"
                                    class="flex-1 text-sm bg-white border-gray-300 rounded-md" id="verification-url">
                                <button onclick="copyToClipboard('verification-url')"
                                    class="px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm">
                                    Copy
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Verification Hash</label>
                            <div class="flex items-center gap-2">
                                <input type="text" readonly value="{{ substr($certificate->verification_hash, 0, 32) }}..."
                                    class="flex-1 text-sm bg-white border-gray-300 rounded-md font-mono"
                                    id="verification-hash">
                                <button onclick="copyToClipboard('verification-hash')"
                                    class="px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm">
                                    Copy
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-wrap gap-4 justify-center">
                    <a href="{{ route('student.certificates.download', $certificate) }}"
                        class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition-colors">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10">
                            </path>
                        </svg>
                        Download PDF
                    </a>

                    <button onclick="shareOnLinkedIn()"
                        class="inline-flex items-center px-6 py-3 bg-blue-700 text-white rounded-lg hover:bg-blue-800 font-medium transition-colors">
                        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                        </svg>
                        Share on LinkedIn
                    </button>

                    <button onclick="shareOnTwitter()"
                        class="inline-flex items-center px-6 py-3 bg-sky-500 text-white rounded-lg hover:bg-sky-600 font-medium transition-colors">
                        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z">
                            </path>
                        </svg>
                        Share on Twitter
                    </button>
                </div>

                <!-- Download Stats -->
                <div class="mt-8 text-center text-sm text-gray-600">
                    <p>This certificate has been downloaded {{ $certificate->download_count }}
                        {{ Str::plural('time', $certificate->download_count) }}
                    </p>
                    @if($certificate->last_downloaded_at)
                        <p class="mt-1">Last downloaded {{ $certificate->last_downloaded_at->diffForHumans() }}</p>
                    @endif
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

            function shareOnLinkedIn() {
                const url = encodeURIComponent('{{ route('certificates.verify') }}?hash={{ $certificate->verification_hash }}');
                const title = encodeURIComponent('I just earned a certificate in {{ $certificate->course->title }}!');
                window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank');
            }

            function shareOnTwitter() {
                const url = encodeURIComponent('{{ route('certificates.verify') }}?hash={{ $certificate->verification_hash }}');
                const text = encodeURIComponent('I just earned a certificate in {{ $certificate->course->title }}! 🎓');
                window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank');
            }
        </script>
    @endpush
@endsection