@extends('layouts.learning')

@section('content')
    <div class="min-h-screen bg-gray-100">
        <!-- Timer Display (if applicable) -->
        @if($quiz->time_limit)
            <div class="fixed top-4 right-4 z-50 flex items-center gap-2 px-4 py-2 bg-red-100 text-red-800 rounded-lg font-semibold shadow-lg"
                x-data="timer({{ $quiz->time_limit }}, '{{ $attempt->started_at->toIso8601String() }}')" x-init="startTimer()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span x-text="timeDisplay"></span>
            </div>
        @endif

        {{-- Info Banner --}}
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd" />
                </svg>
                <p class="text-sm text-blue-700">
                    <strong>💡 Tip:</strong> Your answers are auto-saved as you type. If you accidentally navigate away,
                    you can resume this quiz from the quiz list (as long as the time limit hasn't expired and the quiz is
                    still in progress).
                </p>
            </div>
        </div>

        <div class="min-h-screen bg-gray-100">
            <div class="flex" x-data="quizTaker()">
                <!-- Sidebar -->
                <div :class="sidebarOpen ? 'w-80' : 'w-0'"
                    class="bg-white shadow-lg transition-all duration-300 overflow-hidden">
                    <div class="h-screen overflow-y-auto">
                        <!-- Quiz Header -->
                        <div class="p-6 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900 line-clamp-2">{{ $quiz->title }}</h2>
                            <p class="text-sm text-gray-600 mt-1">{{ $course->title }}</p>
                            <div class="mt-4">
                                <div class="flex justify-between text-sm text-gray-600 mb-1">
                                    <span>Progress</span>
                                    <span
                                        x-text="`${Math.round((answeredCount / {{ $quiz->questions->count() }}) * 100)}%`"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-purple-600 h-2 rounded-full transition-all"
                                        :style="`width: ${(answeredCount / {{ $quiz->questions->count() }}) * 100}%`"></div>
                                </div>
                                <div class="mt-2 text-sm text-gray-600">
                                    <span x-text="answeredCount"></span> of {{ $quiz->questions->count() }} answered
                                </div>
                            </div>
                        </div>

                        <!-- Questions List -->
                        <div class="p-4">
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Questions</h3>
                            <div class="space-y-1">
                                @foreach($quiz->questions as $index => $question)
                                    <button
                                        @click="stopAllMedia(); currentQuestion = {{ $index }}; window.scrollTo({ top: 0, behavior: 'smooth' });"
                                        type="button" :class="{
                                                                    'bg-purple-50 border-purple-500': currentQuestion === {{ $index }},
                                                                    'bg-green-50 border-green-500': currentQuestion !== {{ $index }} && isQuestionAnswered({{ $question->id }}),
                                                                    'border-gray-200 hover:bg-gray-50': currentQuestion !== {{ $index }} && !isQuestionAnswered({{ $question->id }})
                                                                }"
                                        class="w-full flex items-center justify-between p-3 rounded-lg border-2 transition text-left">
                                        <div class="flex items-center flex-1">
                                            <div class="mr-3">
                                                <div :class="{
                                                                            'bg-purple-600 text-white': currentQuestion === {{ $index }},
                                                                            'bg-green-600 text-white': currentQuestion !== {{ $index }} && isQuestionAnswered({{ $question->id }}),
                                                                            'bg-gray-200 text-gray-600': currentQuestion !== {{ $index }} && !isQuestionAnswered({{ $question->id }})
                                                                        }"
                                                    class="w-8 h-8 rounded-full flex items-center justify-center font-semibold text-sm">
                                                    {{ $index + 1 }}
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p :class="{
                                                                            'text-purple-900 font-medium': currentQuestion === {{ $index }},
                                                                            'text-green-900 font-medium': currentQuestion !== {{ $index }} && isQuestionAnswered({{ $question->id }}),
                                                                            'text-gray-700': currentQuestion !== {{ $index }} && !isQuestionAnswered({{ $question->id }})
                                                                        }" class="text-sm truncate">
                                                    Question {{ $index + 1 }}
                                                </p>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span
                                                        class="text-xs text-gray-500">{{ $question->questionType->name ?? 'Question' }}</span>
                                                    @if($question->points > 0)
                                                        <span class="text-xs text-gray-500">• {{ $question->points }} pts</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ml-2">
                                            <svg x-show="isQuestionAnswered({{ $question->id }})" class="w-5 h-5 text-green-600"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Submit Section in Sidebar -->
                        <div class="p-4 border-t border-gray-200">
                            <button @click="submitQuiz()" type="button" :disabled="submitting"
                                :class="{'opacity-50 cursor-not-allowed': submitting}"
                                class="w-full inline-flex items-center justify-center px-4 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    x-show="!submitting">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" x-show="submitting"
                                    style="display: none;">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span x-text="submitting ? 'Submitting...' : 'Submit Quiz'"></span>
                            </button>
                            <p class="text-xs text-gray-500 mt-2 text-center">Make sure all questions are answered</p>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="flex-1 overflow-y-auto">
                    <!-- Toggle Sidebar Button -->
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="fixed left-0 top-20 z-10 bg-white p-2 rounded-r-lg shadow-lg hover:bg-gray-50 transition">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div class="max-w-4xl mx-auto p-6">
                        @foreach($quiz->questions as $index => $question)
                            <div x-show="currentQuestion === {{ $index }}" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform translate-x-4"
                                x-transition:enter-end="opacity-100 transform translate-x-0">

                                <!-- Question Card -->
                                <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                                    <div class="p-8">
                                        <!-- Question Header -->
                                        <div class="mb-6">
                                            <div class="flex items-start justify-between mb-4">
                                                <div class="flex items-center gap-3">
                                                    <span
                                                        class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-purple-100 text-purple-700 font-bold text-lg">
                                                        {{ $index + 1 }}
                                                    </span>
                                                    <div>
                                                        <h3 class="text-xl font-bold text-gray-900">Question {{ $index + 1 }}
                                                        </h3>
                                                        <p class="text-sm text-gray-500">of {{ $quiz->questions->count() }}
                                                            questions</p>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col items-end gap-2">
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                        {{ $question->questionType->name ?? 'Question' }}
                                                    </span>
                                                    @if($question->points > 0)
                                                        <span class="text-sm font-semibold text-purple-600">{{ $question->points }}
                                                            {{ Str::plural('point', $question->points) }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Question Text -->
                                            <div
                                                class="prose prose-lg max-w-none text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                                {!! $question->question !!}
                                            </div>

                                            {{-- Additional Description --}}
                                            @if($question->description)
                                                <div class="mt-3 p-4 bg-blue-50 border-l-4 border-blue-400 rounded-r-lg">
                                                    <div class="flex items-start">
                                                        <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0"
                                                            fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        <div class="text-sm text-gray-700">
                                                            {!! $question->description !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Media Content --}}
                                            @if($question->media_type && $question->media_type !== 'none' && $question->media_url)
                                                @php
                                                    // Determine media path based on source
                                                    if ($question->media_source === 'upload') {
                                                        $mediaPath = \Storage::url($question->media_url);
                                                    } else {
                                                        $mediaPath = $question->media_url;
                                                    }
                                                @endphp
                                                <div class="mt-4 bg-white p-4 rounded-lg border border-gray-200">
                                                    @if($question->media_type === 'image')
                                                        <img src="{{ $mediaPath }}" alt="Question media"
                                                            class="max-w-full h-auto rounded-lg shadow-md mx-auto"
                                                            onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'300\'%3E%3Crect fill=\'%23f3f4f6\' width=\'400\' height=\'300\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'%239ca3af\' font-size=\'18\'%3EImage not available%3C/text%3E%3C/svg%3E'">
                                                    @elseif($question->media_type === 'audio')
                                                        <div
                                                            class="flex items-center gap-3 bg-gradient-to-r from-purple-50 to-indigo-50 p-4 rounded-lg">
                                                            <svg class="w-8 h-8 text-purple-600 flex-shrink-0" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path
                                                                    d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z" />
                                                            </svg>
                                                            <audio controls class="flex-1">
                                                                <source src="{{ $mediaPath }}" type="audio/mpeg">
                                                                <source src="{{ $mediaPath }}" type="audio/mp3">
                                                                <source src="{{ $mediaPath }}" type="audio/wav">
                                                                <source src="{{ $mediaPath }}" type="audio/ogg">
                                                                Your browser does not support the audio element.
                                                            </audio>
                                                        </div>
                                                    @elseif($question->media_type === 'video')
                                                        <video controls class="w-full max-h-96 rounded-lg shadow-md">
                                                            <source src="{{ $mediaPath }}" type="video/mp4">
                                                            <source src="{{ $mediaPath }}" type="video/webm">
                                                            <source src="{{ $mediaPath }}" type="video/ogg">
                                                            Your browser does not support the video element.
                                                        </video>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Answer Section -->
                                        <div class="mb-6">
                                            @php
                                                $existingAnswer = $existingAnswers->get($question->id);
                                                $questionType = $question->questionType->slug ?? null;
                                            @endphp

                                            @if($questionType === 'mcq_single' || $questionType === 'true_false')
                                                <!-- Multiple Choice / True-False -->
                                                <div class="space-y-3">
                                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Select your
                                                        answer:</label>
                                                    @foreach($question->options as $option)
                                                        <label
                                                            class="flex items-start p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                                            :class="{'border-purple-500 bg-purple-50': answers[{{ $question->id }}] === {{ $option->id }}, 'border-gray-200': answers[{{ $question->id }}] !== {{ $option->id }}}">
                                                            <input type="radio" name="question_{{ $question->id }}"
                                                                value="{{ $option->id }}" class="mt-1 h-5 w-5 text-purple-600"
                                                                @change="saveAnswer({{ $question->id }}, {{ $option->id }}, null)"
                                                                :checked="answers[{{ $question->id }}] === {{ $option->id }}" {{ $existingAnswer && $existingAnswer->selected_option_id == $option->id ? 'checked' : '' }}>
                                                            <span class="ml-3 text-gray-700 flex-1">{{ $option->option_text }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>

                                            @elseif($questionType === 'mcq_multiple')
                                                <!-- Multiple Answer (Checkboxes) -->
                                                <div class="space-y-3">
                                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Select all that
                                                        apply:</label>
                                                    @foreach($question->options as $option)
                                                        <label
                                                            class="flex items-start p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition"
                                                            :class="{'border-purple-500 bg-purple-50': selectedOptions[{{ $question->id }}]?.includes({{ $option->id }}), 'border-gray-200': !selectedOptions[{{ $question->id }}]?.includes({{ $option->id }})}">
                                                            <input type="checkbox" name="question_{{ $question->id }}[]"
                                                                value="{{ $option->id }}" class="mt-1 h-5 w-5 text-purple-600 rounded"
                                                                @change="toggleMultipleAnswer({{ $question->id }}, {{ $option->id }})"
                                                                :checked="selectedOptions[{{ $question->id }}]?.includes({{ $option->id }})">
                                                            <span class="ml-3 text-gray-700 flex-1">{{ $option->option_text }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>

                                            @elseif($questionType === 'short_answer')
                                                <!-- Short Answer -->
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Your
                                                        Answer:</label>
                                                    <input type="text" name="question_{{ $question->id }}_text"
                                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-lg"
                                                        placeholder="Type your answer here..."
                                                        @input="debounceAnswer({{ $question->id }}, null, $event.target.value)"
                                                        x-model="textAnswers[{{ $question->id }}]"
                                                        value="{{ $existingAnswer?->answer_text ?? '' }}">
                                                </div>

                                            @elseif($questionType === 'essay')
                                                <!-- Essay / Long Answer -->
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Your
                                                        Answer:</label>
                                                    <textarea name="question_{{ $question->id }}_text" rows="10"
                                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-lg"
                                                        placeholder="Write your answer here..."
                                                        @input="debounceAnswer({{ $question->id }}, null, $event.target.value)"
                                                        x-model="textAnswers[{{ $question->id }}]">{{ $existingAnswer?->answer_text ?? '' }}</textarea>
                                                    <div class="mt-2 text-sm text-gray-500">
                                                        <span x-text="(textAnswers[{{ $question->id }}] || '').length"></span>
                                                        characters
                                                    </div>
                                                </div>

                                            @elseif($questionType === 'fill_blanks')
                                                <!-- Fill in the Blank -->
                                                @php
                                                    $blanks = $question->settings['blanks'] ?? [];
                                                    $existingBlanks = $existingAnswer && $existingAnswer->answer
                                                        ? (is_string($existingAnswer->answer) ? json_decode($existingAnswer->answer, true) : $existingAnswer->answer)
                                                        : [];
                                                @endphp
                                                <div class="space-y-4"
                                                    x-data="{ blankAnswers: {{ json_encode($existingBlanks ?: array_fill(0, count($blanks), '')) }} }">
                                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Fill in the
                                                        blanks:</label>
                                                    @foreach($blanks as $blankIndex => $blank)
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-600 mb-1">Blank
                                                                {{ $blankIndex + 1 }}:</label>
                                                            <input type="text"
                                                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-lg"
                                                                placeholder="Type your answer..."
                                                                x-model="blankAnswers[{{ $blankIndex }}]"
                                                                @input="debounceAnswer({{ $question->id }}, null, JSON.stringify(blankAnswers))"
                                                                value="{{ $existingBlanks[$blankIndex] ?? '' }}">
                                                        </div>
                                                    @endforeach
                                                </div>

                                            @elseif($questionType === 'matching')
                                                <!-- Matching -->
                                                @php
                                                    $pairs = $question->settings['pairs'] ?? [];
                                                    // Create a mapping of text to original index
                                                    $rightTextToIndex = [];
                                                    foreach ($pairs as $idx => $pair) {
                                                        $rightTextToIndex[$pair['right']] = $idx;
                                                    }
                                                    // Shuffle right items (use quiz shuffle_answers setting)
                                                    $rightItems = collect($pairs)->pluck('right')->values()->all();
                                                    if ($quiz->shuffle_answers) {
                                                        shuffle($rightItems);
                                                    }
                                                    $existingMatches = $existingAnswer && $existingAnswer->answer_text
                                                        ? json_decode($existingAnswer->answer_text, true)
                                                        : [];
                                                @endphp
                                                <div class="space-y-3" x-data="{ rightMap: {{ json_encode($rightTextToIndex) }} }">
                                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Match the items
                                                        from the left column with the right column:</label>
                                                    @foreach($pairs as $leftIndex => $pair)
                                                        <div class="flex items-center gap-3">
                                                            <div
                                                                class="flex-1 p-3 bg-indigo-50 rounded border-2 border-indigo-200 font-medium">
                                                                {{ $pair['left'] }}
                                                            </div>
                                                            <svg class="w-6 h-6 text-gray-400 flex-shrink-0" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                            </svg>
                                                            <select
                                                                class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                                                @change="saveMatchingAnswerByText({{ $question->id }}, '{{ $leftIndex }}', $event.target.value, rightMap)">
                                                                <option value="">Select match...</option>
                                                                @foreach($rightItems as $rightIndex => $rightItem)
                                                                    <option value="{{ $rightItem }}" {{ isset($existingMatches[$leftIndex]) && isset($rightTextToIndex[$rightItem]) && $existingMatches[$leftIndex] == $rightTextToIndex[$rightItem] ? 'selected' : '' }}>
                                                                        {{ $rightItem }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    @endforeach
                                                </div>

                                            @elseif($questionType === 'image_choice')
                                                <!-- Image Selection -->
                                                <div class="space-y-3">
                                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Select the correct
                                                        image:</label>
                                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                                        @foreach($question->options as $option)
                                                            <label class="relative cursor-pointer group"
                                                                :class="{'ring-4 ring-purple-500': answers[{{ $question->id }}] === {{ $option->id }}}">
                                                                <input type="radio" name="question_{{ $question->id }}"
                                                                    value="{{ $option->id }}" class="sr-only"
                                                                    @change="saveAnswer({{ $question->id }}, {{ $option->id }}, null)"
                                                                    :checked="answers[{{ $question->id }}] === {{ $option->id }}" {{ $existingAnswer && $existingAnswer->selected_option_id == $option->id ? 'checked' : '' }}>
                                                                <div class="relative border-2 rounded-lg overflow-hidden transition-all"
                                                                    :class="{'border-purple-500 bg-purple-50': answers[{{ $question->id }}] === {{ $option->id }}, 'border-gray-200 hover:border-gray-300': answers[{{ $question->id }}] !== {{ $option->id }}}">
                                                                    <img src="{{ $option->option_text }}"
                                                                        alt="Option {{ $loop->iteration }}"
                                                                        class="w-full h-48 object-cover"
                                                                        onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'200\'%3E%3Crect fill=\'%23f3f4f6\' width=\'200\' height=\'200\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'%239ca3af\' font-size=\'14\'%3EImage not available%3C/text%3E%3C/svg%3E'">
                                                                    <div class="absolute top-2 right-2"
                                                                        x-show="answers[{{ $question->id }}] === {{ $option->id }}">
                                                                        <div class="bg-purple-600 text-white rounded-full p-1">
                                                                            <svg class="w-5 h-5" fill="currentColor"
                                                                                viewBox="0 0 20 20">
                                                                                <path fill-rule="evenodd"
                                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                                    clip-rule="evenodd" />
                                                                            </svg>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Auto-save indicator -->
                                            <div class="mt-4 flex items-center gap-2">
                                                <div class="text-sm font-medium"
                                                    :class="saving ? 'text-blue-600' : 'text-green-600'"
                                                    x-show="saveStatus[{{ $question->id }}]">
                                                    <span x-show="saving" class="flex items-center">
                                                        <svg class="inline w-5 h-5 animate-spin mr-2" fill="none"
                                                            viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor"
                                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                            </path>
                                                        </svg>
                                                        Saving answer...
                                                    </span>
                                                    <span x-show="!saving" class="flex items-center">
                                                        <svg class="inline w-5 h-5 mr-2" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        Answer saved
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Navigation Buttons -->
                                <div class="bg-white rounded-lg shadow-sm p-6">
                                    <div class="flex items-center justify-between">
                                        <!-- Previous Button -->
                                        <button @click="previousQuestion()" x-show="currentQuestion > 0" type="button"
                                            class="inline-flex items-center px-6 py-3 bg-gray-200 border border-transparent rounded-md font-semibold text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 19l-7-7 7-7" />
                                            </svg>
                                            Previous Question
                                        </button>

                                        <div x-show="currentQuestion === 0"></div>

                                        <!-- Next Button -->
                                        <button @click="nextQuestion()"
                                            x-show="currentQuestion < {{ $quiz->questions->count() - 1 }}" type="button"
                                            class="inline-flex items-center px-6 py-3 bg-purple-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Next Question
                                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>

                                        <!-- Submit Button (last question) -->
                                        <button @click="submitQuiz()"
                                            x-show="currentQuestion === {{ $quiz->questions->count() - 1 }}" type="button"
                                            :disabled="submitting" :class="{'opacity-50 cursor-not-allowed': submitting}"
                                            class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                x-show="!submitting">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"
                                                x-show="submitting" style="display: none;">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            <span x-text="submitting ? 'Submitting...' : 'Submit Quiz'"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <script>
            function timer(timeLimit, startedAt) {
                return {
                    timeRemaining: 0,
                    timeDisplay: '',
                    interval: null,
                    startTimer() {
                        const started = new Date(startedAt);
                        const now = new Date();

                        // Debug: Check if dates are valid
                        if (isNaN(started.getTime())) {
                            console.error('Invalid start date:', startedAt);
                            this.timeDisplay = 'Error: Invalid start time';
                            return;
                        }

                        const elapsed = Math.floor((now - started) / 1000); // seconds elapsed
                        const totalSeconds = timeLimit * 60; // convert minutes to seconds
                        this.timeRemaining = totalSeconds - elapsed; // remaining seconds

                        console.log('Timer Debug:', {
                            timeLimit: timeLimit,
                            startedAt: startedAt,
                            started: started,
                            now: now,
                            elapsed: elapsed,
                            totalSeconds: totalSeconds,
                            timeRemaining: this.timeRemaining
                        });

                        if (this.timeRemaining <= 0) {
                            alert('Time is up! Your quiz will be submitted automatically.');
                            this.autoSubmit();
                            return;
                        }

                        this.updateDisplay();
                        this.interval = setInterval(() => {
                            this.timeRemaining--;
                            this.updateDisplay();

                            if (this.timeRemaining <= 0) {
                                clearInterval(this.interval);
                                alert('Time is up! Your quiz will be submitted automatically.');
                                this.autoSubmit();
                            }
                        }, 1000);
                    },
                    updateDisplay() {
                        const minutes = Math.floor(this.timeRemaining / 60);
                        const seconds = this.timeRemaining % 60;
                        this.timeDisplay = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                    },
                    autoSubmit() {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route('student.quizzes.submit', [$quiz, $attempt]) }}';
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = '{{ csrf_token() }}';
                        form.appendChild(csrfInput);
                        document.body.appendChild(form);
                        form.submit();
                    }
                };
            }

            function quizTaker() {
                return {
                    sidebarOpen: true,
                    currentQuestion: 0,
                    answers: {
                        @foreach($existingAnswers as $questionId => $answer)
                            {{ $questionId }}: {{ $answer->selected_option_id ?? 'null' }},
                        @endforeach
                            },
            textAnswers: {
                @foreach($existingAnswers as $questionId => $answer)
                    @if($answer->answer_text)
                        {{ $questionId }}: @json($answer->answer_text),
                    @endif
                @endforeach
                            },
            selectedOptions: { },
            saveStatus: { },
            saving: false,
                submitting: false,
                    saveTimeout: null,

                        init() {
                // Warn before navigating away (unless submitting)
                window.addEventListener('beforeunload', (e) => {
                    if (this.submitting) return;
                    e.preventDefault();
                    e.returnValue = ''; // Required for Chrome
                });
            },

                            get answeredCount() {
                return Object.keys(this.answers).length + Object.keys(this.textAnswers).filter(key => this.textAnswers[key]).length;
            },

            isQuestionAnswered(questionId) {
                return this.answers[questionId] !== undefined || (this.textAnswers[questionId] && this.textAnswers[questionId].length > 0);
            },

            nextQuestion() {
                if (this.currentQuestion < {{ $quiz->questions->count() - 1 }}) {
                    this.stopAllMedia();
                    this.currentQuestion++;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },

            previousQuestion() {
                if (this.currentQuestion > 0) {
                    this.stopAllMedia();
                    this.currentQuestion--;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },

            stopAllMedia() {
                // Select all audio and video elements on the page
                const mediaElements = document.querySelectorAll('audio, video');
                
                // Pause each media element and reset to start
                mediaElements.forEach(element => {
                    if (!element.paused) {
                        element.pause();
                        element.currentTime = 0;
                    }
                });
            },

            saveAnswer(questionId, optionId, answerText) {
                if (optionId !== null) {
                    this.answers[questionId] = optionId;
                }
                if (answerText !== null) {
                    this.textAnswers[questionId] = answerText;
                }

                this.saving = true;
                this.saveStatus[questionId] = true;

                fetch('{{ route('student.quizzes.save-answer', [$quiz, $attempt]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        question_id: questionId,
                        selected_option_id: optionId,
                        answer_text: answerText
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        this.saving = false;
                        setTimeout(() => {
                            this.saveStatus[questionId] = false;
                        }, 2000);
                    })
                    .catch(error => {
                        console.error('Error saving answer:', error);
                        this.saving = false;
                    });
            },

            debounceAnswer(questionId, optionId, answerText) {
                clearTimeout(this.saveTimeout);
                this.saveTimeout = setTimeout(() => {
                    this.saveAnswer(questionId, optionId, answerText);
                }, 1000);
            },

            toggleMultipleAnswer(questionId, optionId) {
                if (!this.selectedOptions[questionId]) {
                    this.selectedOptions[questionId] = [];
                }

                const index = this.selectedOptions[questionId].indexOf(optionId);
                if (index > -1) {
                    this.selectedOptions[questionId].splice(index, 1);
                } else {
                    this.selectedOptions[questionId].push(optionId);
                }

                // Save as JSON string
                this.saveAnswer(questionId, null, JSON.stringify(this.selectedOptions[questionId]));
            },

            saveMatchingAnswer(questionId, leftIndex, rightIndex) {
                // Store matching as JSON object mapping left index to right index
                if (!this.selectedOptions[questionId]) {
                    this.selectedOptions[questionId] = {};
                }
                this.selectedOptions[questionId][leftIndex] = rightIndex;
                this.saveAnswer(questionId, null, JSON.stringify(this.selectedOptions[questionId]));
            },

            saveMatchingAnswerByText(questionId, leftIndex, rightText, rightMap) {
                // Map the selected right text to its original index
                if (!this.selectedOptions[questionId]) {
                    this.selectedOptions[questionId] = {};
                }
                // Find the original index of the selected right text
                const originalRightIndex = rightMap[rightText];
                if (originalRightIndex !== undefined) {
                    this.selectedOptions[questionId][leftIndex] = originalRightIndex;
                    this.saveAnswer(questionId, null, JSON.stringify(this.selectedOptions[questionId]));
                }
            },

            submitQuiz() {
                if (this.submitting) return;

                const unansweredCount = {{ $quiz->questions->count() }} - this.answeredCount;
                let confirmMessage = 'Are you sure you want to submit your quiz?';

                if (unansweredCount > 0) {
                    confirmMessage = `You have ${unansweredCount} unanswered question(s). Are you sure you want to submit?`;
                }

                confirmMessage += '\n\nYou cannot change your answers after submission.';

                if (!confirm(confirmMessage)) {
                    return;
                }

                this.submitting = true;

                // Create a form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('student.quizzes.submit', [$quiz, $attempt]) }}';

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                document.body.appendChild(form);
                form.submit();
            }
                        };
                    }
        </script>
    </div>
    </div>
@endsection