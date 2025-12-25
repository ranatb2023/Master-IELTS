@extends('layouts.admin')

@section('title', 'Issue Certificate')
@section('page-title', 'Issue Certificate Manually')

@section('content')
    <div class="max-w-4xl mx-auto">
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

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Manual Certificate Issuance</h2>
                <p class="mt-1 text-sm text-gray-600">Issue a certificate manually for a student</p>
            </div>

            <form method="POST" action="{{ route('admin.certificates.store') }}" class="space-y-6">
                @csrf

                <!-- Student Selection -->
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Student <span class="text-red-500">*</span>
                    </label>
                    <select name="user_id" id="user_id" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select a student</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Course Selection -->
                <div>
                    <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Course <span class="text-red-500">*</span>
                    </label>
                    <select name="course_id" id="course_id" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select a course</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('course_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Certificates enabled for this course</p>
                </div>

                <!-- Template Selection (Optional) -->
                <div>
                    <label for="certificate_template_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Certificate Template
                    </label>
                    <select name="certificate_template_id" id="certificate_template_id"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Use course default</option>
                        @foreach ($templates as $template)
                            <option value="{{ $template->id }}"
                                {{ old('certificate_template_id') == $template->id ? 'selected' : '' }}>
                                {{ $template->name }}
                                @if ($template->is_default)
                                    (Default)
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('certificate_template_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Leave blank to use course's default template</p>
                </div>

                <!-- Issue Date -->
                <div>
                    <label for="issue_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Issue Date
                    </label>
                    <input type="date" name="issue_date" id="issue_date" value="{{ old('issue_date', now()->toDateString()) }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('issue_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Leave blank for current date</p>
                </div>

                <!-- Expiry Date (Optional) -->
                <div>
                    <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Expiry Date (Optional)
                    </label>
                    <input type="date" name="expiry_date" id="expiry_date" value="{{ old('expiry_date') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('expiry_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Leave blank for no expiry</p>
                </div>

                <!-- Metadata (Optional) -->
                <div>
                    <label for="metadata" class="block text-sm font-medium text-gray-700 mb-2">
                        Additional Metadata (JSON)
                    </label>
                    <textarea name="metadata" id="metadata" rows="4"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder='{"note": "Manually issued by admin", "reason": "Special request"}'></textarea>
                    @error('metadata')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Optional JSON data for custom fields</p>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.certificates.index') }}"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-medium">
                        Issue Certificate
                    </button>
                </div>
            </form>
        </div>

        <!-- Information Box -->
        <div class="mt-6 bg-blue-50 rounded-lg p-6">
            <div class="flex items-start">
                <svg class="h-6 w-6 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-semibold text-blue-900">What happens when you issue a certificate?</h3>
                    <ul class="mt-2 text-sm text-blue-800 space-y-1">
                        <li>• A unique certificate number and verification hash will be generated</li>
                        <li>• The PDF certificate will be automatically created and saved</li>
                        <li>• The student will receive an email notification</li>
                        <li>• The certificate will be immediately available for download and verification</li>
                        <li>• Duplicate certificates for the same user-course combination will be prevented</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
