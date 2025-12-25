@extends('layouts.admin')

@section('title', 'Create Quiz')
@section('page-title', 'Create New Quiz')

@section('content')
    <div class="max-w-7xl mx-auto">
        <form action="{{ route('admin.quizzes.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Main Content (2/3 width) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information Card -->
                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-blue-50">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Basic Information
                            </h3>
                            <p class="mt-1 text-sm text-gray-600">Essential details about your quiz</p>
                        </div>

                        <div class="p-6 space-y-6">
                            <!-- Course Selection -->
                            <div>
                                <label for="course_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Course <span class="text-red-500">*</span>
                                </label>
                                <select name="course_id" id="course_id" required
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150"
                                    x-data="{}" @change="loadTopics($event.target)">
                                    <option value="">Select a course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" data-slug="{{ $course->slug }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Topic Selection -->
                            <div>
                                <label for="topic_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Topic <span class="text-red-500">*</span>
                                </label>
                                <select name="topic_id" id="topic_id" required
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150">
                                    <option value="">Select a topic</option>
                                </select>
                                @error('topic_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Select a course first to load topics</p>
                            </div>

                            <!-- Title -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                    Quiz Title <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150"
                                    placeholder="e.g., IELTS Reading Practice Test 1">
                                @error('title')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Content Card -->
                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-pink-50">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Content & Instructions
                            </h3>
                            <p class="mt-1 text-sm text-gray-600">Describe the quiz and provide instructions</p>
                        </div>

                        <div class="p-6 space-y-6">
                            <!-- Description -->
                            <div>
                                <x-quill-editor name="description" :value="old('description')" label="Description"
                                    :required="false" height="200px"
                                    placeholder="Describe the quiz content and objectives..." />
                            </div>

                            <!-- Instructions -->
                            <div>
                                <x-quill-editor name="instructions" :value="old('instructions')"
                                    label="Instructions for Students" :required="false" height="180px"
                                    placeholder="Provide specific instructions for taking this quiz..." />
                            </div>
                        </div>
                    </div>

                    <!-- Configuration Card -->
                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-green-50 to-teal-50">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Quiz Configuration
                            </h3>
                            <p class="mt-1 text-sm text-gray-600">Configure quiz parameters and limits</p>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Passing Score -->
                                <div>
                                    <label for="passing_score" class="block text-sm font-medium text-gray-700 mb-1">
                                        Passing Score (%) <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" name="passing_score" id="passing_score"
                                            value="{{ old('passing_score', 70) }}" min="0" max="100" step="0.01" required
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pr-8 transition duration-150">
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">%</span>
                                        </div>
                                    </div>
                                    @error('passing_score')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Time Limit -->
                                <div>
                                    <label for="time_limit" class="block text-sm font-medium text-gray-700 mb-1">Time
                                        Limit</label>
                                    <div class="relative">
                                        <input type="number" name="time_limit" id="time_limit"
                                            value="{{ old('time_limit') }}" min="1"
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pr-16 transition duration-150"
                                            placeholder="Unlimited">
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">minutes</span>
                                        </div>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Leave blank for no time limit</p>
                                    @error('time_limit')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Max Attempts -->
                                <div>
                                    <label for="max_attempts" class="block text-sm font-medium text-gray-700 mb-1">Maximum
                                        Attempts</label>
                                    <input type="number" name="max_attempts" id="max_attempts"
                                        value="{{ old('max_attempts') }}" min="1"
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150"
                                        placeholder="Unlimited">
                                    <p class="mt-1 text-xs text-gray-500">Leave blank for unlimited attempts</p>
                                    @error('max_attempts')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Order -->
                                <div>
                                    <label for="order" class="block text-sm font-medium text-gray-700 mb-1">Display
                                        Order</label>
                                    <input type="number" name="order" id="order" value="{{ old('order', 0) }}" min="0"
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150">
                                    @error('order')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Show Answers Setting -->
                            <div class="mt-6">
                                <label for="show_answers" class="block text-sm font-medium text-gray-700 mb-1">
                                    Show Answers <span class="text-red-500">*</span>
                                </label>
                                <select name="show_answers" id="show_answers" required
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150">
                                    <option value="never" {{ old('show_answers') == 'never' ? 'selected' : '' }}>Never
                                    </option>
                                    <option value="after_submission" {{ old('show_answers', 'after_submission') == 'after_submission' ? 'selected' : '' }}>After Submission
                                    </option>
                                    <option value="after_passing" {{ old('show_answers') == 'after_passing' ? 'selected' : '' }}>After Passing</option>
                                    <option value="always" {{ old('show_answers') == 'always' ? 'selected' : '' }}>Always
                                    </option>
                                </select>
                                @error('show_answers')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Settings Sidebar (1/3 width) -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Quiz Options Card -->
                    <div class="bg-white shadow-md rounded-lg overflow-hidden sticky top-6">
                        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-yellow-50 to-orange-50">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                                Quiz Options
                            </h3>
                            <p class="mt-1 text-sm text-gray-600">Control quiz behavior</p>
                        </div>

                        <div class="p-6 space-y-4">
                            <!-- Show Correct Answers -->
                            <div class="flex items-start p-3 rounded-lg hover:bg-gray-50 transition duration-150">
                                <div class="flex items-center h-5 mt-0.5">
                                    <input type="checkbox" name="show_correct_answers" id="show_correct_answers" value="1"
                                        {{ old('show_correct_answers', true) ? 'checked' : '' }}
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </div>
                                <div class="ml-3">
                                    <label for="show_correct_answers"
                                        class="text-sm font-medium text-gray-700 cursor-pointer">Show Correct
                                        Answers</label>
                                    <p class="text-xs text-gray-500 mt-0.5">Display which answers were correct</p>
                                </div>
                            </div>

                            <!-- Shuffle Questions -->
                            <div class="flex items-start p-3 rounded-lg hover:bg-gray-50 transition duration-150">
                                <div class="flex items-center h-5 mt-0.5">
                                    <input type="checkbox" name="shuffle_questions" id="shuffle_questions" value="1" {{ old('shuffle_questions') ? 'checked' : '' }}
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </div>
                                <div class="ml-3">
                                    <label for="shuffle_questions"
                                        class="text-sm font-medium text-gray-700 cursor-pointer">Shuffle Questions</label>
                                    <p class="text-xs text-gray-500 mt-0.5">Randomize question order</p>
                                </div>
                            </div>

                            <!-- Shuffle Answers -->
                            <div class="flex items-start p-3 rounded-lg hover:bg-gray-50 transition duration-150">
                                <div class="flex items-center h-5 mt-0.5">
                                    <input type="checkbox" name="shuffle_answers" id="shuffle_answers" value="1" {{ old('shuffle_answers') ? 'checked' : '' }}
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </div>
                                <div class="ml-3">
                                    <label for="shuffle_answers"
                                        class="text-sm font-medium text-gray-700 cursor-pointer">Shuffle Answer
                                        Options</label>
                                    <p class="text-xs text-gray-500 mt-0.5">Randomize answer choices</p>
                                </div>
                            </div>

                            <!-- Require Passing -->
                            <div class="flex items-start p-3 rounded-lg hover:bg-gray-50 transition duration-150">
                                <div class="flex items-center h-5 mt-0.5">
                                    <input type="checkbox" name="require_passing" id="require_passing" value="1" {{ old('require_passing') ? 'checked' : '' }}
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </div>
                                <div class="ml-3">
                                    <label for="require_passing"
                                        class="text-sm font-medium text-gray-700 cursor-pointer">Require Passing</label>
                                    <p class="text-xs text-gray-500 mt-0.5">Must pass to continue</p>
                                </div>
                            </div>

                            <!-- Certificate Eligible -->
                            <div class="flex items-start p-3 rounded-lg hover:bg-gray-50 transition duration-150">
                                <div class="flex items-center h-5 mt-0.5">
                                    <input type="checkbox" name="certificate_eligible" id="certificate_eligible" value="1"
                                        {{ old('certificate_eligible') ? 'checked' : '' }}
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </div>
                                <div class="ml-3">
                                    <label for="certificate_eligible"
                                        class="text-sm font-medium text-gray-700 cursor-pointer">Certificate
                                        Eligible</label>
                                    <p class="text-xs text-gray-500 mt-0.5">Counts towards certificate</p>
                                </div>
                            </div>

                            <!-- Published -->
                            <div
                                class="flex items-start p-3 rounded-lg hover:bg-gray-50 transition duration-150 border-t border-gray-200 mt-4 pt-4">
                                <div class="flex items-center h-5 mt-0.5">
                                    <input type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }}
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </div>
                                <div class="ml-3">
                                    <label for="is_published"
                                        class="text-sm font-medium text-gray-700 cursor-pointer">Published</label>
                                    <p class="text-xs text-gray-500 mt-0.5">Make quiz visible to students</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 space-y-3">
                            <button type="submit"
                                class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Create Quiz
                            </button>
                            <a href="{{ route('admin.quizzes.index') }}"
                                class="w-full inline-flex justify-center items-center px-4 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            function loadTopics(selectElement) {
                const topicSelect = document.getElementById('topic_id');
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                const courseSlug = selectedOption.dataset.slug;

                topicSelect.innerHTML = '<option value="">Loading...</option>';

                if (!courseSlug) {
                    topicSelect.innerHTML = '<option value="">Select a topic</option>';
                    return;
                }

                fetch(`/admin/courses/${courseSlug}/topics`)
                    .then(response => response.json())
                    .then(topics => {
                        topicSelect.innerHTML = '<option value="">Select a topic</option>';
                        topics.forEach(topic => {
                            const option = document.createElement('option');
                            option.value = topic.id;
                            option.textContent = topic.title;
                            topicSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error loading topics:', error);
                        topicSelect.innerHTML = '<option value="">Error loading topics</option>';
                    });
            }

            // Load topics on page load if course is already selected
            document.addEventListener('DOMContentLoaded', function () {
                const courseSelect = document.getElementById('course_id');
                if (courseSelect.value) {
                    loadTopics(courseSelect);
                }
            });
        </script>
    @endpush
@endsection