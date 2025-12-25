@extends('layouts.student')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{
            files: [],
            fileError: '',
            maxFiles: 10,
            maxFileSize: 10 * 1024 * 1024,
            attachmentsToRemove: [],
        
            handleFileSelect(event) {
                this.addFiles(Array.from(event.target.files));
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
        
            markAttachmentForRemoval(attachmentId) {
                if (!this.attachmentsToRemove.includes(attachmentId)) {
                    this.attachmentsToRemove.push(attachmentId);
                }
            },
        
            formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
            }
        }">
            <!-- Header -->
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Edit Note</h2>
                    <p class="mt-1 text-sm text-gray-600">Update your note content and settings</p>
                </div>
                <a href="{{ route('student.notes.show', $note) }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancel
                </a>
            </div>

            <form action="{{ route('student.notes.update', $note) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Hidden inputs for attachments to remove -->
                <template x-for="attachmentId in attachmentsToRemove" :key="attachmentId">
                    <input type="hidden" name="remove_attachments[]" :value="attachmentId">
                </template>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content Column (Left - 2/3 width) -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Title Card -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Note Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title', $note->title) }}" required
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-lg"
                                placeholder="Enter a descriptive title for your note...">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Body Card -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <x-quill-editor name="body" label="Note Content" :value="old('body', $note->body)" :required="true"
                                height="500px" placeholder="Write your note content here..." />
                            @error('body')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Settings Sidebar (Right - 1/3 width) -->
                    <div class="space-y-6">
                        <!-- Association Card -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                                Link to Content
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <label for="course_id" class="block text-xs font-medium text-gray-700 mb-1">
                                        Course
                                    </label>
                                    <select name="course_id" id="course_id"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        <option value="">None</option>
                                        @foreach ($userCourses as $course)
                                            <option value="{{ $course->id }}"
                                                {{ old('course_id', $note->course_id) == $course->id ? 'selected' : '' }}>
                                                {{ $course->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="lesson_id" class="block text-xs font-medium text-gray-700 mb-1">
                                        Lesson ID
                                    </label>
                                    <input type="text" name="lesson_id" id="lesson_id"
                                        value="{{ old('lesson_id', $note->lesson_id) }}"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                        placeholder="Optional">
                                    @if ($note->lesson)
                                        <p class="mt-1 text-xs text-gray-500">📌 {{ $note->lesson->title }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Organization Card -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                Organization
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <label for="color" class="block text-xs font-medium text-gray-700 mb-1">
                                        Color
                                    </label>
                                    <select name="color" id="color"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        <option value="">Default</option>
                                        <option value="yellow" {{ old('color', $note->color) == 'yellow' ? 'selected' : '' }}>🟡
                                            Yellow</option>
                                        <option value="green" {{ old('color', $note->color) == 'green' ? 'selected' : '' }}>🟢
                                            Green</option>
                                        <option value="blue" {{ old('color', $note->color) == 'blue' ? 'selected' : '' }}>🔵 Blue
                                        </option>
                                        <option value="red" {{ old('color', $note->color) == 'red' ? 'selected' : '' }}>🔴 Red
                                        </option>
                                        <option value="purple" {{ old('color', $note->color) == 'purple' ? 'selected' : '' }}>🟣
                                            Purple</option>
                                        <option value="pink" {{ old('color', $note->color) == 'pink' ? 'selected' : '' }}>🩷 Pink
                                        </option>
                                        <option value="orange" {{ old('color', $note->color) == 'orange' ? 'selected' : '' }}>🟠
                                            Orange</option>
                                        <option value="gray" {{ old('color', $note->color) == 'gray' ? 'selected' : '' }}>⚪ Gray
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label for="tags" class="block text-xs font-medium text-gray-700 mb-1">
                                        Tags
                                    </label>
                                    <input type="text" name="tags_input" id="tags"
                                        value="{{ old('tags') ? implode(', ', old('tags')) : ($note->tags ? implode(', ', $note->tags) : '') }}"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                        placeholder="comma, separated, tags">
                                    <p class="mt-1 text-xs text-gray-500">Separate with commas</p>
                                </div>

                                <div class="pt-2 border-t border-gray-200">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_pinned" id="is_pinned" value="1"
                                            {{ old('is_pinned', $note->is_pinned) ? 'checked' : '' }}
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <span class="ml-2 text-sm text-gray-700 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L11 4.323V3a1 1 0 011-1z" />
                                            </svg>
                                            Pin this note
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Existing Attachments Card -->
                        @if ($note->attachments->count() > 0)
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Current Files
                                </h3>

                                <div class="space-y-2">
                                    @foreach ($note->attachments as $attachment)
                                        <div x-show="!attachmentsToRemove.includes({{ $attachment->id }})"
                                            class="flex items-center justify-between p-2 bg-gray-50 border border-gray-200 rounded text-xs">
                                            <div class="flex items-center min-w-0 flex-1">
                                                <svg class="w-4 h-4 text-gray-400 mr-1 flex-shrink-0" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span class="truncate block">{{ $attachment->original_filename }}</span>
                                            </div>
                                            <button type="button" @click="markAttachmentForRemoval({{ $attachment->id }})"
                                                class="ml-2 text-red-600 hover:text-red-800">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- New Attachments Card -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                Add Files
                            </h3>

                            <div class="text-xs text-gray-600 mb-3 space-y-1">
                                <p>• Max 10 files total</p>
                                <p>• 10 MB per file</p>
                            </div>

                            <label for="files"
                                class="block w-full border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-indigo-400 transition cursor-pointer">
                                <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <span class="mt-2 block text-xs text-gray-600">Click to upload</span>
                            </label>
                            <input id="files" type="file" name="attachments[]" multiple class="hidden"
                                @change="handleFileSelect($event)">

                            <!-- New Files Preview -->
                            <div x-show="files.length > 0" class="mt-3 space-y-2">
                                <template x-for="(file, index) in files" :key="index">
                                    <div class="flex items-center justify-between p-2 bg-green-50 rounded text-xs border border-green-200">
                                        <div class="flex items-center min-w-0 flex-1">
                                            <svg class="w-4 h-4 text-green-600 mr-1 flex-shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            <span class="truncate text-green-900" x-text="file.name"></span>
                                        </div>
                                        <button type="button" @click="removeFile(index)" class="ml-2 text-red-600 hover:text-red-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <div x-show="fileError" class="mt-2 text-xs text-red-600" x-text="fileError"></div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Update Note
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.querySelector('form').addEventListener('submit', function(e) {
                const tagsInput = document.getElementById('tags');
                if (tagsInput && tagsInput.value) {
                    const tags = tagsInput.value.split(',').map(tag => tag.trim()).filter(tag => tag !== '');
                    tagsInput.removeAttribute('name');
                    tags.forEach((tag, index) => {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = `tags[${index}]`;
                        hiddenInput.value = tag;
                        this.appendChild(hiddenInput);
                    });
                }
            });
        </script>
    @endpush
@endsection
