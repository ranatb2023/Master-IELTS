@extends('layouts.admin')

@section('title', 'Edit Question')
@section('page-title', 'Edit Question')

@php
    // Prepare options data for all question types
    $preparedOptions = old('options', $question->options->map(function ($o) {
        return ['text' => $o->option_text, 'is_correct' => $o->is_correct];
    })->values());

    $preparedOptionsJson = json_encode($preparedOptions);
    $hasOptions = count($preparedOptions) > 0;

    // Prepare fill_blanks data - ensure it's an array of arrays
    $preparedBlanks = old('settings.blanks', $question->settings['blanks'] ?? [['']]);
    if (is_array($preparedBlanks)) {
        // Convert objects to arrays if needed
        $preparedBlanks = array_values(array_map(function ($blank) {
            // Handle old format: ['answers' => 'value'] or new format: ['value1', 'value2']
            if (is_array($blank) && isset($blank['answers'])) {
                // Old format - extract the answers value
                $answers = $blank['answers'];
                return is_array($answers) ? $answers : [$answers];
            } elseif (is_object($blank)) {
                return (array) $blank;
            }
            return is_array($blank) ? $blank : [$blank];
        }, $preparedBlanks));
    } else {
        $preparedBlanks = [['']];
    }
    $preparedBlanksJson = json_encode($preparedBlanks, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);

    // Prepare matching pairs data
    $preparedPairs = old('settings.pairs', $question->settings['pairs'] ?? [['left' => '', 'right' => '']]);
    if (is_array($preparedPairs)) {
        $preparedPairs = array_values(array_map(function ($pair) {
            if (is_object($pair)) {
                return (array) $pair;
            }
            // Ensure each pair has left and right keys
            if (is_array($pair)) {
                return [
                    'left' => $pair['left'] ?? '',
                    'right' => $pair['right'] ?? ''
                ];
            }
            return ['left' => '', 'right' => ''];
        }, $preparedPairs));
    } else {
        $preparedPairs = [['left' => '', 'right' => '']];
    }
    $preparedPairsJson = json_encode($preparedPairs, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
@endphp

@section('content')
    <div class="max-w-7xl mx-auto" x-data="questionForm()">
        <!-- Page Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Edit Question</h2>
                    <p class="mt-1 text-sm text-gray-600">Quiz: {{ $question->quiz->title }}</p>
                </div>
                <a href="{{ route('admin.quizzes.show', $question->quiz) }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Quiz
                </a>
            </div>
        </div>

        <form action="{{ route('admin.questions.update', $question) }}" method="POST" enctype="multipart/form-data"
            @submit="validateAndSubmit">
            @csrf
            @method('PUT')

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
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Basic Information
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <!-- Question Type Selection -->
                            <div>
                                <label for="question_type_id" class="block text-sm font-medium text-gray-700">Question Type
                                    <span class="text-red-500">*</span></label>
                                <select name="question_type_id" id="question_type_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    @change="loadQuestionType($event.target.value)" x-model="selectedTypeId">
                                    <option value="">Select a question type</option>
                                    @foreach($questionTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('question_type_id', $question->question_type_id) == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500" x-show="questionType"
                                    x-text="questionType?.description"></p>
                                @error('question_type_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Question Text -->
                            <div>
                                <x-quill-editor name="question" :value="old('question', $question->question)"
                                    label="Question Text" :required="true" height="200px"
                                    placeholder="Enter the question..." />
                            </div>

                            <!-- Description (Optional) -->
                            <div>
                                <x-quill-editor name="description" :value="old('description', $question->description)"
                                    label="Additional Description (Optional)" :required="false" height="300px"
                                    placeholder="Provide additional context or instructions..." />
                            </div>
                        </div>
                    </div>

                    <!-- Media Section Card -->
                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-pink-50">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Media (Optional)
                            </h3>
                        </div>
                        <div class="p-6"
                            x-data="{ mediaSource: '{{ old('media_source', $question->media_source ?? 'url') }}' }">
                            <div class="space-y-4">
                                <!-- Media Type -->
                                <div>
                                    <label for="media_type" class="block text-sm font-medium text-gray-700 mb-1">Media
                                        Type</label>
                                    <select name="media_type" id="media_type"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="none" {{ old('media_type', $question->media_type ?? 'none') == 'none' ? 'selected' : '' }}>None</option>
                                        <option value="image" {{ old('media_type', $question->media_type) == 'image' ? 'selected' : '' }}>Image</option>
                                        <option value="audio" {{ old('media_type', $question->media_type) == 'audio' ? 'selected' : '' }}>Audio</option>
                                        <option value="video" {{ old('media_type', $question->media_type) == 'video' ? 'selected' : '' }}>Video</option>
                                    </select>
                                </div>

                                <!-- Media Source Toggle -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Media Source</label>
                                    <div class="flex gap-4 mb-3">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="media_source" value="url" x-model="mediaSource"
                                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                            <span class="ml-2 text-sm text-gray-700">External URL</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="media_source" value="upload" x-model="mediaSource"
                                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                            <span class="ml-2 text-sm text-gray-700">Upload File</span>
                                        </label>
                                    </div>

                                    <!-- URL Input -->
                                    <div x-show="mediaSource === 'url'" x-transition>
                                        <label for="media_url" class="block text-sm font-medium text-gray-700 mb-1">Media
                                            URL</label>
                                        <input type="url" name="media_url" id="media_url"
                                            value="{{ old('media_url', $question->media_source === 'url' ? $question->media_url : '') }}"
                                            placeholder="https://..."
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <p class="mt-1 text-xs text-gray-500">Enter the full URL to the media file</p>
                                    </div>

                                    <!-- File Upload -->
                                    <div x-show="mediaSource === 'upload'" x-transition>
                                        <label for="media_file" class="block text-sm font-medium text-gray-700 mb-1">Upload
                                            Media File</label>
                                        <input type="file" name="media_file" id="media_file"
                                            accept="image/*,audio/*,video/*"
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                        <p class="mt-1 text-xs text-gray-500">Max size: 100MB. Supported: JPG, PNG, GIF,
                                            MP3, WAV, MP4, WEBM</p>
                                        @if($question->media_source === 'upload' && $question->media_url)
                                            <div class="mt-2 p-2 bg-gray-50 rounded border border-gray-200">
                                                <p class="text-xs text-gray-600">Current file: <span
                                                        class="font-medium">{{ basename($question->media_url) }}</span></p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @error('media_type')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('media_url')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('media_file')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Dynamic Question Type-Specific Fields Card -->
                    <div x-show="questionType" x-cloak class="bg-white shadow-md rounded-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-cyan-50 to-teal-50">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-cyan-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                                <span x-text="questionType?.name + ' Settings'"></span>
                            </h3>
                        </div>
                        <div class="p-6">

                            <!-- True/False -->
                            <template x-if="questionType?.slug === 'true_false'">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Correct Answer <span
                                            class="text-red-500">*</span></label>
                                    <div class="space-y-2"
                                        x-data="{ options: {{ $hasOptions ? $preparedOptionsJson : '[{ text: \'True\', is_correct: false }, { text: \'False\', is_correct: false }]' }} }">
                                        <template x-for="(option, index) in options" :key="index">
                                            <div class="flex items-center space-x-3">
                                                <input type="radio" name="correct_answer" :id="`option_${index}`"
                                                    :value="index" :checked="option.is_correct"
                                                    @change="options.forEach((o, i) => o.is_correct = (i === index))"
                                                    required
                                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                                <input type="hidden" :name="`options[${index}][text]`" :value="option.text">
                                                <input type="hidden" :name="`options[${index}][is_correct]`"
                                                    :value="option.is_correct ? '1' : '0'">
                                                <label :for="`option_${index}`"
                                                    class="text-sm font-medium text-gray-700 cursor-pointer"
                                                    x-text="option.text"></label>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <!-- Multiple Choice (Single) -->
                            <template x-if="questionType?.slug === 'mcq_single'">
                                <div x-data="{ options: {{ $hasOptions ? $preparedOptionsJson : '[{ text: \'\', is_correct: false }, { text: \'\', is_correct: false }]' }} }"
                                    x-init="console.log('MCQ Single options:', options)">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Answer Options <span
                                            class="text-red-500">*</span></label>
                                    <div class="space-y-2">
                                        <template x-for="(option, index) in options" :key="index">
                                            <div class="flex items-center space-x-2">
                                                <input type="radio" :name="`options_correct`" :value="index"
                                                    :checked="option.is_correct"
                                                    @change="options.forEach((o, i) => { o.is_correct = (i === index); })"
                                                    required
                                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                                <input type="hidden" :name="`options[${index}][is_correct]`"
                                                    :value="option.is_correct ? 1 : 0">
                                                <input type="text" :name="`options[${index}][text]`" x-model="option.text"
                                                    placeholder="Option text" required
                                                    class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <button type="button" @click="options.splice(index, 1)"
                                                    x-show="options.length > 2"
                                                    class="px-3 py-2 text-sm text-red-600 hover:text-red-800">Remove</button>
                                            </div>
                                        </template>
                                    </div>
                                    <button type="button" @click="options.push({ text: '', is_correct: false })"
                                        class="mt-3 px-4 py-2 text-sm font-medium text-indigo-600 hover:text-indigo-800">+
                                        Add Option</button>
                                </div>
                            </template>

                            <!-- Multiple Choice (Multiple) -->
                            <template x-if="questionType?.slug === 'mcq_multiple'">
                                <div
                                    x-data="{ options: {{ $hasOptions ? $preparedOptionsJson : '[{ text: \'\', is_correct: false }, { text: \'\', is_correct: false }]' }} }">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Answer Options <span
                                            class="text-red-500">*</span></label>
                                    <div class="space-y-2">
                                        <template x-for="(option, index) in options" :key="index">
                                            <div class="flex items-center space-x-2">
                                                <input type="checkbox" :name="`options[${index}][is_correct]`" value="1"
                                                    x-model="option.is_correct"
                                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                <input type="text" :name="`options[${index}][text]`" x-model="option.text"
                                                    placeholder="Option text" required
                                                    class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <button type="button" @click="options.splice(index, 1)"
                                                    x-show="options.length > 2"
                                                    class="px-3 py-2 text-sm text-red-600 hover:text-red-800">Remove</button>
                                            </div>
                                        </template>
                                    </div>
                                    <button type="button" @click="options.push({ text: '', is_correct: false })"
                                        class="mt-3 px-4 py-2 text-sm font-medium text-indigo-600 hover:text-indigo-800">+
                                        Add Option</button>
                                </div>
                            </template>

                            <!-- Essay -->
                            <template x-if="questionType?.slug === 'essay'">
                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="min_words" class="block text-sm font-medium text-gray-700">Minimum
                                                Words</label>
                                            <input type="number" name="settings[min_words]" id="min_words"
                                                value="{{ old('settings.min_words', $question->settings['min_words'] ?? '') }}"
                                                min="0" placeholder="e.g., 100"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>
                                        <div>
                                            <label for="max_words" class="block text-sm font-medium text-gray-700">Maximum
                                                Words</label>
                                            <input type="number" name="settings[max_words]" id="max_words"
                                                value="{{ old('settings.max_words', $question->settings['max_words'] ?? '') }}"
                                                min="0" placeholder="e.g., 500"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>
                                    </div>
                                    <div>
                                        <label for="sample_answer" class="block text-sm font-medium text-gray-700">Sample
                                            Answer (for grading reference)</label>
                                        <textarea name="settings[sample_answer]" id="sample_answer" rows="4"
                                            placeholder="Provide a sample answer for reference..."
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('settings.sample_answer', $question->settings['sample_answer'] ?? '') }}</textarea>
                                    </div>
                                    <div>
                                        <label for="grading_rubric" class="block text-sm font-medium text-gray-700">Grading
                                            Rubric</label>
                                        <textarea name="settings[grading_rubric]" id="grading_rubric" rows="4"
                                            placeholder="Describe the grading criteria..."
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('settings.grading_rubric', $question->settings['grading_rubric'] ?? '') }}</textarea>
                                    </div>
                                </div>
                            </template>

                            <!-- Short Answer -->
                            <template x-if="questionType?.slug === 'short_answer'">
                                <div class="space-y-4">
                                    <div>
                                        <label for="accepted_answers"
                                            class="block text-sm font-medium text-gray-700">Accepted Answers <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" name="settings[accepted_answers]" id="accepted_answers"
                                            value="{{ old('settings.accepted_answers', isset($question->settings['accepted_answers']) ? (is_array($question->settings['accepted_answers']) ? implode(', ', $question->settings['accepted_answers']) : $question->settings['accepted_answers']) : '') }}"
                                            placeholder="Enter answers separated by commas" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <p class="mt-1 text-xs text-gray-500">Separate multiple accepted answers with commas
                                        </p>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" name="settings[case_sensitive]" id="case_sensitive"
                                                value="1" {{ old('settings.case_sensitive', $question->settings['case_sensitive'] ?? false) ? 'checked' : '' }}
                                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="case_sensitive" class="font-medium text-gray-700">Case
                                                Sensitive</label>
                                            <p class="text-gray-500">Require exact case matching</p>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- Fill in the Blanks -->
                            <template x-if="questionType?.slug === 'fill_blanks'">
                                <div class="space-y-4">
                                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                                        <p class="text-sm text-blue-700">
                                            Use <code class="bg-blue-100 px-1 rounded">@{{blank_1}}</code>, <code
                                                class="bg-blue-100 px-1 rounded">@{{blank_2}}</code>, etc. in your question
                                            text to mark blank positions.
                                        </p>
                                    </div>
                                    <div
                                        x-data="{ blanks: {{ $preparedBlanksJson }}.map(b => ({ answers: Array.isArray(b) ? b.join(', ') : b, case_sensitive: false })) }">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Blank Fields <span
                                                class="text-red-500">*</span></label>
                                        <template x-for="(blank, index) in blanks" :key="index">
                                            <div class="mb-3 p-3 border border-gray-200 rounded-md">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="text-sm font-medium text-gray-700"
                                                        x-text="`Blank ${index + 1}`"></span>
                                                    <button type="button" @click="blanks.splice(index, 1)"
                                                        x-show="blanks.length > 1"
                                                        class="text-sm text-red-600 hover:text-red-900">Remove</button>
                                                </div>
                                                <input type="text" :name="`settings[blanks][${index}][answers]`"
                                                    x-model="blank.answers" required
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                    placeholder="Accepted answers (comma separated)">
                                                <label class="inline-flex items-center mt-2">
                                                    <input type="checkbox"
                                                        :name="`settings[blanks][${index}][case_sensitive]`" value="1"
                                                        x-model="blank.case_sensitive"
                                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                    <span class="ml-2 text-xs text-gray-700">Case Sensitive</span>
                                                </label>
                                            </div>
                                        </template>
                                        <button type="button" @click="blanks.push({ answers: '', case_sensitive: false })"
                                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            Add Blank
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <!-- Matching -->
                            <template x-if="questionType?.slug === 'matching'">
                                <div class="space-y-4">
                                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0 mt-0.5" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <div>
                                                <p class="text-sm text-blue-700 font-medium">How Matching Works:</p>
                                                <p class="text-sm text-blue-700 mt-1">
                                                    • Each left item will match with its corresponding right item (same
                                                    row)<br>
                                                    • Right items will be shuffled when shown to students<br>
                                                    • Students must correctly match all pairs
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div x-data="{ pairs: {{ $preparedPairsJson }} }">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">
                                            Matching Pairs <span class="text-red-500">*</span>
                                            <span class="text-xs font-normal text-gray-500 ml-2">(Items in the same row are
                                                correct matches)</span>
                                        </label>
                                        <div class="grid grid-cols-2 gap-4 mb-2 text-xs font-medium text-gray-600">
                                            <div class="flex items-center">
                                                <span class="bg-indigo-100 text-indigo-700 px-2 py-1 rounded">Left
                                                    Column</span>
                                            </div>
                                            <div class="flex items-center">
                                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded mr-2">Right
                                                    Column (will be shuffled)</span>
                                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                                </svg>
                                            </div>
                                        </div>
                                        <template x-for="(pair, index) in pairs" :key="index">
                                            <div
                                                class="grid grid-cols-2 gap-4 mb-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                                <div class="relative">
                                                    <input type="text" :name="`settings[pairs][${index}][left]`"
                                                        x-model="pair.left" required
                                                        class="w-full rounded-md border-indigo-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                        :placeholder="`Left item ${index + 1}`">
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                                    </svg>
                                                    <input type="text" :name="`settings[pairs][${index}][right]`"
                                                        x-model="pair.right" required
                                                        class="flex-1 rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                                        :placeholder="`Right item ${index + 1} (matches left)`">
                                                    <button type="button" @click="pairs.splice(index, 1)"
                                                        x-show="pairs.length > 1"
                                                        class="px-3 py-2 text-sm text-red-600 hover:text-red-800">×</button>
                                                </div>
                                            </div>
                                        </template>
                                        <button type="button" @click="pairs.push({ left: '', right: '' })"
                                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            Add Pair
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <!-- Image Selection -->
                            <template x-if="questionType?.slug === 'image_choice'">
                                <div class="space-y-4">
                                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0 mt-0.5" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <div>
                                                <p class="text-sm text-blue-700 font-medium">Image Selection:</p>
                                                <p class="text-sm text-blue-700 mt-1">
                                                    • Students select one correct image from multiple options<br>
                                                    • Use direct image URLs (e.g., from your storage or CDN)<br>
                                                    • Mark one image as the correct answer
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        x-data="{ options: {{ $hasOptions ? $preparedOptionsJson : '[{ text: \'\', is_correct: false }, { text: \'\', is_correct: false }]' }} }">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">
                                            Image Options <span class="text-red-500">*</span>
                                            <span class="text-xs font-normal text-gray-500 ml-2">(Select one as correct
                                                answer)</span>
                                        </label>
                                        <div class="space-y-3">
                                            <template x-for="(option, index) in options" :key="index">
                                                <div class="p-4 border border-gray-200 rounded-lg hover:border-indigo-300 transition-colors"
                                                    :class="{ 'bg-green-50 border-green-300': option.is_correct }">
                                                    <div class="flex items-start space-x-3">
                                                        <div class="flex items-center h-10">
                                                            <input type="radio" :name="`options_correct`" :value="index"
                                                                :checked="option.is_correct"
                                                                @change="options.forEach((o, i) => { o.is_correct = (i === index); })"
                                                                required
                                                                class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                                                            <input type="hidden" :name="`options[${index}][is_correct]`"
                                                                :value="option.is_correct ? 1 : 0">
                                                        </div>
                                                        <div class="flex-1">
                                                            <div class="flex items-center space-x-2 mb-2">
                                                                <span class="text-xs font-medium text-gray-500"
                                                                    x-text="`Image ${index + 1}`"></span>
                                                                <span x-show="option.is_correct"
                                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                                    <svg class="w-3 h-3 mr-1" fill="currentColor"
                                                                        viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd"
                                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                            clip-rule="evenodd" />
                                                                    </svg>
                                                                    Correct Answer
                                                                </span>
                                                            </div>
                                                            <input type="url" :name="`options[${index}][text]`"
                                                                x-model="option.text" required
                                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                                placeholder="Enter image URL (https://example.com/image.jpg)">
                                                            <div x-show="option.text" class="mt-2">
                                                                <img :src="option.text" alt="Preview"
                                                                    class="max-w-xs max-h-40 rounded border border-gray-300 object-cover"
                                                                    @@error="$el.src = 'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'100\'%3E%3Crect fill=\'%23f3f4f6\' width=\'200\' height=\'100\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'%239ca3af\'%3EInvalid URL%3C/text%3E%3C/svg%3E'">
                                                            </div>
                                                        </div>
                                                        <button type="button" @click="options.splice(index, 1)"
                                                            x-show="options.length > 2"
                                                            class="px-3 py-2 text-sm text-red-600 hover:text-red-900">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                        <button type="button" @click="options.push({ text: '', is_correct: false })"
                                            class="mt-3 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            Add Image Option
                                        </button>
                                    </div>
                                </div>
                            </template>

                        </div>
                    </div>

                    <!-- Explanation Card -->
                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-green-50 to-teal-50">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                                Explanation
                            </h3>
                        </div>
                        <div class="p-6">
                            <x-quill-editor name="explanation" :value="old('explanation', $question->explanation)"
                                label="Explanation (shown after answer)" :required="false" height="300px"
                                placeholder="Provide an explanation for the correct answer..." />
                        </div>
                    </div>
                </div>

                <!-- Right Column - Sidebar (1/3 width) -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white shadow-md rounded-lg overflow-hidden sticky top-6">
                        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-yellow-50 to-orange-50">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Question Settings
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <!-- Points -->
                            <div>
                                <label for="points" class="block text-sm font-medium text-gray-700 mb-2">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                        Points <span class="text-red-500">*</span>
                                    </div>
                                </label>
                                <input type="number" name="points" id="points"
                                    value="{{ old('points', $question->points) }}" min="0.01" step="0.01" required
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('points')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Difficulty -->
                            <div>
                                <label for="difficulty" class="block text-sm font-medium text-gray-700 mb-2">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                        Difficulty
                                    </div>
                                </label>
                                <select name="difficulty" id="difficulty"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="easy" {{ old('difficulty', $question->difficulty) == 'easy' ? 'selected' : '' }}>Easy</option>
                                    <option value="medium" {{ old('difficulty', $question->difficulty ?? 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="hard" {{ old('difficulty', $question->difficulty) == 'hard' ? 'selected' : '' }}>Hard</option>
                                </select>
                                @error('difficulty')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Order -->
                            <div>
                                <label for="order" class="block text-sm font-medium text-gray-700 mb-2">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4" />
                                        </svg>
                                        Display Order
                                    </div>
                                </label>
                                <input type="number" name="order" id="order" value="{{ old('order', $question->order) }}"
                                    min="0"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('order')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Action Buttons -->
                            <div class="pt-6 border-t border-gray-200 space-y-3">
                                <button type="submit"
                                    class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Update Question
                                </button>
                                <a href="{{ route('admin.quizzes.show', $question->quiz) }}"
                                    class="w-full inline-flex justify-center items-center px-4 py-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            function questionForm() {
                return {
                    selectedTypeId: '{{ old('question_type_id', $question->question_type_id) }}',
                    questionType: null,

                    async init() {
                        if (this.selectedTypeId) {
                            await this.loadQuestionType(this.selectedTypeId);
                        }
                    },

                    async loadQuestionType(typeId) {
                        if (!typeId) {
                            this.questionType = null;
                            return;
                        }

                        try {
                            const response = await fetch(`/admin/questions/question-types/${typeId}/schema`);
                            this.questionType = await response.json();
                        } catch (error) {
                            console.error('Failed to load question type:', error);
                        }
                    },

                    validateAndSubmit(event) {
                        // Validate MCQ Single - check if radio button is selected
                        if (this.questionType?.slug === 'mcq_single') {
                            const radioSelected = document.querySelector('input[name="options_correct"]:checked');

                            if (!radioSelected) {
                                event.preventDefault();
                                alert('Please select the correct answer for the question.');
                                return false;
                            }
                        }

                        // Validate MCQ Multiple - check if at least one checkbox is checked
                        if (this.questionType?.slug === 'mcq_multiple') {
                            const correctAnswers = Array.from(document.querySelectorAll('input[name^="options["][name$="][is_correct]"]:checked'));

                            if (correctAnswers.length === 0) {
                                event.preventDefault();
                                alert('Please select at least one correct answer for the multiple choice question.');
                                return false;
                            }
                        }

                        // Validate True/False has an answer selected
                        if (this.questionType?.slug === 'true_false') {
                            const radioSelected = document.querySelector('input[name="correct_answer"]:checked');

                            if (!radioSelected) {
                                event.preventDefault();
                                alert('Please select the correct answer (True or False).');
                                return false;
                            }
                        }

                        // Convert accepted_answers string to array for short_answer type
                        if (this.questionType?.slug === 'short_answer') {
                            const input = document.querySelector('[name="settings[accepted_answers]"]');
                            if (input && input.value) {
                                const answers = input.value.split(',').map(a => a.trim()).filter(a => a);
                                input.value = JSON.stringify(answers);
                            }
                        }

                        return true;
                    }
                }
            }
        </script>
    @endpush
@endsection