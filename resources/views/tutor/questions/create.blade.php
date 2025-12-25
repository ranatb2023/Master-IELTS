@extends('layouts.tutor')

@section('title', 'Add Question')
@section('page-title', 'Add Question')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Add Question</h2>
            <p class="mt-1 text-sm text-gray-600">Create a new question for the quiz</p>
        </div>
        <a href="{{ route('tutor.quizzes.edit', request('quiz_id')) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
            Back to Quiz
        </a>
    </div>

    <form action="{{ route('tutor.questions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="{
        questionType: 'mcq_single',
        options: [
            { text: '', is_correct: false, explanation: '' },
            { text: '', is_correct: false, explanation: '' }
        ],
        addOption() {
            this.options.push({ text: '', is_correct: false, explanation: '' });
        },
        removeOption(index) {
            if (this.options.length > 2) {
                this.options.splice(index, 1);
            }
        }
    }">
        @csrf
        <input type="hidden" name="quiz_id" value="{{ request('quiz_id') }}">

        <!-- Question Details -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Question Details</h3>
            <div class="space-y-4">
                <!-- Question Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-3">Question Type *</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none" :class="questionType === 'mcq_single' ? 'border-indigo-600 ring-2 ring-indigo-600' : 'border-gray-300'">
                            <input type="radio" name="type" value="mcq_single" class="sr-only" x-model="questionType" checked>
                            <div class="flex flex-1">
                                <div class="flex flex-col">
                                    <span class="block text-sm font-medium text-gray-900">Multiple Choice (Single)</span>
                                    <span class="mt-1 flex items-center text-xs text-gray-500">One correct answer</span>
                                </div>
                            </div>
                        </label>

                        <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none" :class="questionType === 'mcq_multiple' ? 'border-indigo-600 ring-2 ring-indigo-600' : 'border-gray-300'">
                            <input type="radio" name="type" value="mcq_multiple" class="sr-only" x-model="questionType">
                            <div class="flex flex-1">
                                <div class="flex flex-col">
                                    <span class="block text-sm font-medium text-gray-900">Multiple Choice (Multiple)</span>
                                    <span class="mt-1 flex items-center text-xs text-gray-500">Multiple correct answers</span>
                                </div>
                            </div>
                        </label>

                        <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none" :class="questionType === 'true_false' ? 'border-indigo-600 ring-2 ring-indigo-600' : 'border-gray-300'">
                            <input type="radio" name="type" value="true_false" class="sr-only" x-model="questionType">
                            <div class="flex flex-1">
                                <div class="flex flex-col">
                                    <span class="block text-sm font-medium text-gray-900">True/False</span>
                                    <span class="mt-1 flex items-center text-xs text-gray-500">Binary choice</span>
                                </div>
                            </div>
                        </label>

                        <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none" :class="questionType === 'short_answer' ? 'border-indigo-600 ring-2 ring-indigo-600' : 'border-gray-300'">
                            <input type="radio" name="type" value="short_answer" class="sr-only" x-model="questionType">
                            <div class="flex flex-1">
                                <div class="flex flex-col">
                                    <span class="block text-sm font-medium text-gray-900">Short Answer</span>
                                    <span class="mt-1 flex items-center text-xs text-gray-500">Text response</span>
                                </div>
                            </div>
                        </label>

                        <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none md:col-span-2" :class="questionType === 'passage_mcq' ? 'border-indigo-600 ring-2 ring-indigo-600' : 'border-gray-300'">
                            <input type="radio" name="type" value="passage_mcq" class="sr-only" x-model="questionType">
                            <div class="flex flex-1">
                                <div class="flex flex-col">
                                    <span class="block text-sm font-medium text-gray-900">Reading Passage with MCQ</span>
                                    <span class="mt-1 flex items-center text-xs text-gray-500">Include a reading passage followed by multiple choice questions</span>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Question Text -->
                <div>
                    <label for="question" class="block text-sm font-medium text-gray-700">Question *</label>
                    <textarea name="question" id="question" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('question') border-red-500 @enderror">{{ old('question') }}</textarea>
                    @error('question')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description/Hint -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Additional Information (Optional)</label>
                    <textarea name="description" id="description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Provide hints or context for the question</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Points -->
                    <div>
                        <label for="points" class="block text-sm font-medium text-gray-700">Points *</label>
                        <input type="number" name="points" id="points" value="{{ old('points', 1) }}" min="1" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <!-- Difficulty -->
                    <div>
                        <label for="difficulty" class="block text-sm font-medium text-gray-700">Difficulty</label>
                        <select name="difficulty" id="difficulty" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select</option>
                            <option value="easy" {{ old('difficulty') == 'easy' ? 'selected' : '' }}>Easy</option>
                            <option value="medium" {{ old('difficulty') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="hard" {{ old('difficulty') == 'hard' ? 'selected' : '' }}>Hard</option>
                        </select>
                    </div>

                    <!-- Order -->
                    <div>
                        <label for="order" class="block text-sm font-medium text-gray-700">Order</label>
                        <input type="number" name="order" id="order" value="{{ old('order', 0) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Media Upload (Optional) -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Media (Optional)</h3>
            <div class="space-y-4">
                <div>
                    <label for="media_type" class="block text-sm font-medium text-gray-700">Media Type</label>
                    <select name="media_type" id="media_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">None</option>
                        <option value="image">Image</option>
                        <option value="audio">Audio</option>
                        <option value="video">Video</option>
                    </select>
                </div>
                <div>
                    <label for="media_url" class="block text-sm font-medium text-gray-700">Media File or URL</label>
                    <input type="file" name="media_file" id="media_file" accept="image/*,audio/*,video/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="mt-1 text-xs text-gray-500">Or enter a URL</p>
                    <input type="url" name="media_url" id="media_url" value="{{ old('media_url') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <!-- Answer Options (for MCQ and True/False) -->
        <div x-show="questionType !== 'short_answer'" class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Answer Options</h3>

            <!-- True/False Options -->
            <div x-show="questionType === 'true_false'" class="space-y-3">
                <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="correct_answer" value="true" class="h-4 w-4 text-indigo-600">
                    <span class="ml-3 text-sm font-medium text-gray-900">True</span>
                </label>
                <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="correct_answer" value="false" class="h-4 w-4 text-indigo-600">
                    <span class="ml-3 text-sm font-medium text-gray-900">False</span>
                </label>
            </div>

            <!-- MCQ Options -->
            <div x-show="questionType === 'mcq_single' || questionType === 'mcq_multiple' || questionType === 'passage_mcq'" class="space-y-4">
                <template x-for="(option, index) in options" :key="index">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex items-center h-10">
                                <input
                                    :type="questionType === 'mcq_single' || questionType === 'passage_mcq' ? 'radio' : 'checkbox'"
                                    :name="questionType === 'mcq_single' || questionType === 'passage_mcq' ? 'correct_option' : 'correct_options[]'"
                                    :value="index"
                                    x-model="option.is_correct"
                                    class="h-4 w-4 text-indigo-600 border-gray-300">
                            </div>
                            <div class="flex-1 space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">Option Text *</label>
                                    <input
                                        type="text"
                                        :name="'options[' + index + '][text]'"
                                        x-model="option.text"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">Explanation (Optional)</label>
                                    <textarea
                                        :name="'options[' + index + '][explanation]'"
                                        x-model="option.explanation"
                                        rows="2"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                </div>
                                <input type="hidden" :name="'options[' + index + '][is_correct]'" :value="option.is_correct ? '1' : '0'">
                            </div>
                            <button
                                type="button"
                                @click="removeOption(index)"
                                x-show="options.length > 2"
                                class="text-red-600 hover:text-red-800 mt-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>

                <button
                    type="button"
                    @click="addOption()"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Option
                </button>
            </div>
        </div>

        <!-- General Explanation -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Explanation</h3>
            <div>
                <label for="explanation" class="block text-sm font-medium text-gray-700">Explanation (Shown after answer)</label>
                <textarea name="explanation" id="explanation" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('explanation') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Provide detailed explanation about the correct answer</p>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('tutor.quizzes.edit', request('quiz_id')) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                Add Question
            </button>
        </div>
    </form>
</div>
@endsection
