<!-- Quick Note Modal Component -->
<div x-data="{ open: false }" x-show="open" @open-note-modal.window="open = true"
    @close-note-modal.window="open = false" @keydown.escape.window="open = false" style="display: none;"
    class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="open = false"></div>

    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full" @click.stop x-show="open"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Quick Note</h3>
                <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form action="{{ route('student.notes.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="lesson_id" value="{{ $currentLesson->id }}">
                <input type="hidden" name="course_id" value="{{ $course->id }}">

                <!-- Lesson Info Banner -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm">
                            <p class="text-blue-900 font-medium">{{ $currentLesson->title }}</p>
                            <p class="text-blue-700 text-xs mt-1">This note will be linked to this lesson</p>
                        </div>
                    </div>
                </div>

                <!-- Title -->
                <div>
                    <label for="quick_note_title" class="block text-sm font-medium text-gray-700 mb-1">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="quick_note_title" required
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Enter note title...">
                </div>

                <!-- Body -->
                <div>
                    <label for="quick_note_body" class="block text-sm font-medium text-gray-700 mb-1">
                        Note Content <span class="text-red-500">*</span>
                    </label>
                    <textarea name="body" id="quick_note_body" rows="8" required
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Write your note here..."></textarea>
                    <p class="mt-1 text-xs text-gray-500">Simple text format (rich text available in full editor)</p>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <a href="{{ route('student.notes.create', ['lesson_id' => $currentLesson->id, 'course_id' => $course->id]) }}"
                        class="text-sm text-indigo-600 hover:text-indigo-700">
                        Open full editor →
                    </a>
                    <div class="flex gap-3">
                        <button type="button" @click="open = false"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Save Note
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>