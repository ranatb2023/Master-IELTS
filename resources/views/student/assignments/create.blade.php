@extends('layouts.learning')

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Error Messages -->
            @if($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <div class="font-bold mb-2">Please fix the following errors:</div>
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Assignment Info Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $assignment->title }}</h3>
                    <div class="flex items-center gap-4 text-sm text-gray-600">
                        @if($assignment->due_date)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Due: {{ \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y g:i A') }}
                                @if(now()->gt($assignment->due_date))
                                    <span class="ml-2 text-red-600 font-medium">(Overdue)</span>
                                @endif
                            </div>
                        @endif
                        @if($assignment->max_points)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                                Max Score: {{ $assignment->max_points }} points
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Submission Form -->
            <form action="{{ route('student.assignments.store', $assignment) }}" method="POST" enctype="multipart/form-data" x-data="{
                files: [],
                fileError: '',
                submitting: false,
                maxFiles: {{ $assignment->max_files ?? 10 }},
                maxFileSize: {{ $assignment->max_file_size ?? 10 }} * 1024 * 1024,

                handleFileSelect(event) {
                    this.addFiles(Array.from(event.target.files));
                },

                handleFileDrop(event) {
                    event.currentTarget.classList.remove('border-orange-500');
                    this.addFiles(Array.from(event.dataTransfer.files));
                },

                addFiles(newFiles) {
                    this.fileError = '';

                    if (this.files.length + newFiles.length > this.maxFiles) {
                        this.fileError = `Maximum ${this.maxFiles} files allowed`;
                        return;
                    }

                    for (const file of newFiles) {
                        if (file.size > this.maxFileSize) {
                            this.fileError = `File &quot;${file.name}&quot; exceeds maximum size of ${this.maxFileSize / (1024 * 1024)} MB`;
                            return;
                        }
                    }

                    this.files = [...this.files, ...newFiles];
                },

                removeFile(index) {
                    this.files.splice(index, 1);
                    this.fileError = '';
                },

                formatFileSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
                }
            }">
                @csrf

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-6">
                        <!-- Text Submission -->
                        @if($assignment->allow_text_submission)
                            <div>
                                <x-quill-editor
                                    name="submission_text"
                                    label="Your Submission"
                                    :value="old('submission_text', '')"
                                    :required="$assignment->allow_text_submission && !$assignment->allow_file_upload"
                                    height="400px"
                                    placeholder="Type your assignment submission here..."
                                />
                            </div>
                        @endif

                        <!-- File Upload -->
                        @if($assignment->allow_file_upload)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Upload Files
                                    @if($assignment->allow_file_upload && !$assignment->allow_text_submission)
                                        <span class="text-red-500">*</span>
                                    @endif
                                </label>

                                <!-- Upload Requirements -->
                                <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-800">
                                    <ul class="space-y-1">
                                        @if($assignment->max_files)
                                            <li>• Maximum {{ $assignment->max_files }} {{ Str::plural('file', $assignment->max_files) }}</li>
                                        @endif
                                        @if($assignment->max_file_size)
                                            <li>• Maximum {{ $assignment->max_file_size }} MB per file</li>
                                        @endif
                                        @if($assignment->allowed_file_types)
                                            <li>• Allowed types: {{ is_array($assignment->allowed_file_types) ? implode(', ', $assignment->allowed_file_types) : $assignment->allowed_file_types }}</li>
                                        @endif
                                    </ul>
                                </div>

                                <!-- File Input -->
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-orange-500 transition"
                                     x-on:drop.prevent="handleFileDrop($event)"
                                     x-on:dragover.prevent="$el.classList.add('border-orange-500')"
                                     x-on:dragleave.prevent="$el.classList.remove('border-orange-500')">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <div class="mt-4">
                                        <label for="files" class="cursor-pointer">
                                            <span class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                Select Files
                                            </span>
                                        </label>
                                        <input id="files"
                                               type="file"
                                               name="files[]"
                                               multiple
                                               class="hidden"
                                               @change="handleFileSelect($event)"
                                               @if($assignment->max_files) :max="{{ $assignment->max_files }}" @endif
                                               @if($assignment->allowed_file_types)
                                                   accept="{{ is_array($assignment->allowed_file_types) ? implode(',', array_map(fn($type) => '.' . trim($type), $assignment->allowed_file_types)) : implode(',', array_map(fn($type) => '.' . trim($type), explode(',', $assignment->allowed_file_types))) }}"
                                               @endif>
                                        <p class="mt-2 text-xs text-gray-500">or drag and drop files here</p>
                                    </div>
                                </div>

                                <!-- Selected Files List -->
                                <div x-show="files.length > 0" class="mt-4 space-y-2">
                                    <div class="text-sm font-medium text-gray-700 mb-2">Selected Files:</div>
                                    <template x-for="(file, index) in files" :key="index">
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center flex-1 min-w-0">
                                                <svg class="w-5 h-5 text-gray-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <span class="text-sm text-gray-700 truncate" x-text="file.name"></span>
                                                <span class="text-xs text-gray-500 ml-2" x-text="`(${formatFileSize(file.size)})`"></span>
                                            </div>
                                            <button type="button"
                                                    @click="removeFile(index)"
                                                    class="ml-3 text-red-600 hover:text-red-800">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>

                                <!-- Error Messages for Files -->
                                <div x-show="fileError" class="mt-2 text-sm text-red-600" x-text="fileError"></div>
                            </div>
                        @endif

                        <!-- Submission Notes -->
                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-yellow-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <div class="text-sm text-yellow-800">
                                    <p class="font-medium mb-1">Important Notes:</p>
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Make sure to review your work before submitting</li>
                                        <li>You can resubmit this assignment if needed</li>
                                        @if($assignment->due_date && now()->gt($assignment->due_date))
                                            <li class="text-red-600 font-medium">This submission will be marked as late</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <a href="{{ route('student.assignments.show', $assignment) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 bg-orange-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                    :disabled="submitting"
                                    x-bind:class="{'opacity-50 cursor-not-allowed': submitting}">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!submitting">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" x-show="submitting" style="display: none;">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="submitting ? 'Submitting...' : 'Submit Assignment'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
