@extends('layouts.student')

@section('title', 'Verify Certificate')
@section('page-title', 'Certificate Verification')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Certificate Verification</h1>
            <p class="mt-2 text-gray-600">Enter a certificate number or verification hash to verify its authenticity</p>
        </div>

        <!-- Verification Form -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <form method="GET" action="{{ route('certificates.verify') }}" class="space-y-4">
                <div>
                    <label for="number" class="block text-sm font-medium text-gray-700 mb-2">Certificate Number</label>
                    <input type="text" name="number" id="number" value="{{ request('number') }}"
                        placeholder="CERT-XXXXXXXX-2024"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div class="text-center text-sm text-gray-500 font-medium">OR</div>

                <div>
                    <label for="hash" class="block text-sm font-medium text-gray-700 mb-2">Verification Hash</label>
                    <input type="text" name="hash" id="hash" value="{{ request('hash') }}"
                        placeholder="Enter verification hash"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <button type="submit"
                    class="w-full px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium text-lg transition-colors">
                    Verify Certificate
                </button>
            </form>
        </div>

        <!-- Verification Result -->
        @if(isset($certificate))
            @if($certificate->isRevoked())
                <!-- Revoked Certificate -->
                <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-lg font-semibold text-red-900">Certificate Revoked</h3>
                            <p class="mt-2 text-sm text-red-800">
                                This certificate has been revoked and is no longer valid.
                            </p>
                            <div class="mt-4 bg-white rounded p-4">
                                <p class="text-sm text-gray-700"><strong>Revoked on:</strong>
                                    {{ $certificate->revoked_at->format('F d, Y') }}</p>
                                <p class="text-sm text-gray-700 mt-2"><strong>Reason:</strong>
                                    {{ $certificate->revoked_reason }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Valid Certificate -->
                <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-6 mb-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-green-900">✓ Certificate Verified</h3>
                            <p class="mt-1 text-sm text-green-800">This certificate is authentic and valid.</p>
                        </div>
                    </div>
                </div>

                <!-- Certificate Details -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-8 text-white text-center">
                        <div class="flex items-center justify-center mb-4">
                            <div class="bg-white/20 rounded-full p-4">
                                <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <h2 class="text-2xl font-bold">Certificate of Completion</h2>
                    </div>

                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Recipient</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $certificate->user->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Issue Date</label>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ $certificate->issue_date->format('F d, Y') }}
                                </p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $certificate->course->title }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Certificate Number</label>
                                <p class="text-lg font-mono font-semibold text-gray-900">
                                    {{ $certificate->certificate_number }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Template</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $certificate->certificateTemplate->name }}
                                </p>
                            </div>
                            @if($certificate->expiry_date)
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                                    <p class="text-lg font-semibold text-gray-900">
                                        {{ $certificate->expiry_date->format('F d, Y') }}
                                        @if($certificate->isExpired())
                                            <span class="ml-2 text-sm text-red-600">(Expired)</span>
                                        @endif
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @elseif(isset($error))
            <!-- Error Message -->
            <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-yellow-900">Certificate Not Found</h3>
                        <p class="mt-1 text-sm text-yellow-800">{{ $error }}</p>
                        <p class="mt-2 text-sm text-yellow-700">Please check the certificate number or verification hash and
                            try again.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection