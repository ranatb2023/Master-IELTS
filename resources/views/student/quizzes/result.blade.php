<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Quiz Results: {{ $quiz->title }}
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('student.quizzes.show', $quiz) }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                    ← Back to Quiz
                </a>
                <a href="{{ route('student.courses.learn', $course) }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                    Back to Course →
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('info'))
                <div class="mb-6 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
                    {{ session('info') }}
                </div>
            @endif

            <!-- Results Summary Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-8">
                    <!-- Score Display -->
                    <div class="text-center mb-6">
                        @if($attempt->passed)
                            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-green-100 mb-4">
                                <svg class="w-16 h-16 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <h1 class="text-3xl font-bold text-green-600 mb-2">Congratulations!</h1>
                            <p class="text-gray-600">You passed the quiz</p>
                        @else
                            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-yellow-100 mb-4">
                                <svg class="w-16 h-16 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <h1 class="text-3xl font-bold text-yellow-600 mb-2">Keep Trying!</h1>
                            <p class="text-gray-600">You didn't pass this time, but you can try again</p>
                        @endif
                    </div>

                    <!-- Score Details -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-gray-50 p-6 rounded-lg text-center">
                            <div class="text-4xl font-bold {{ $attempt->passed ? 'text-green-600' : 'text-yellow-600' }} mb-2">
                                {{ $attempt->score }}%
                            </div>
                            <div class="text-sm text-gray-600">Your Score</div>
                        </div>
                        <div class="bg-gray-50 p-6 rounded-lg text-center">
                            <div class="text-4xl font-bold text-gray-900 mb-2">
                                {{ $quiz->passing_score }}%
                            </div>
                            <div class="text-sm text-gray-600">Passing Score</div>
                        </div>
                        <div class="bg-gray-50 p-6 rounded-lg text-center">
                            <div class="text-4xl font-bold text-gray-900 mb-2">
                                {{ $attempt->quizAnswers->where('is_correct', true)->count() }}/{{ $quiz->questions->count() }}
                            </div>
                            <div class="text-sm text-gray-600">Correct Answers</div>
                        </div>
                    </div>

                    <!-- Attempt Info -->
                    <div class="border-t border-gray-200 pt-4 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Started:</span>
                            <span class="font-medium text-gray-900">{{ $attempt->started_at->format('M d, Y g:i A') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Completed:</span>
                            <span class="font-medium text-gray-900">{{ $attempt->completed_at->format('M d, Y g:i A') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Time Taken:</span>
                            <span class="font-medium text-gray-900">{{ $attempt->started_at->diffForHumans($attempt->completed_at, true) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Status:</span>
                            @if($attempt->passed)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Passed
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Not Passed
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Question Review -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Answer Review</h2>
                    <p class="text-sm text-gray-600 mt-1">Review your answers and see the correct solutions</p>
                </div>

                <div class="divide-y divide-gray-200">
                    @foreach($quiz->questions as $index => $question)
                        @php
                            $answer = $attempt->quizAnswers->where('question_id', $question->id)->first();
                            $questionType = $question->questionType->slug ?? null;
                        @endphp
                        <div class="p-6">
                            <!-- Question Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-700 font-semibold text-sm">
                                            {{ $index + 1 }}
                                        </span>
                                        <h3 class="text-lg font-semibold text-gray-900">Question {{ $index + 1 }}</h3>
                                        @if($question->points > 0)
                                            <span class="text-sm text-gray-500">({{ $question->points }} {{ Str::plural('point', $question->points) }})</span>
                                        @endif
                                    </div>
                                    <div class="prose max-w-none">
                                        {!! $question->question !!}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    @php
                                        // Check if question type requires manual grading
                                        $requiresManualGrading = in_array($questionType, ['short_answer', 'essay']);
                                    @endphp
                                    @if($answer && $answer->is_correct === true)
                                        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Correct
                                        </div>
                                    @elseif($answer && $answer->is_correct === false)
                                        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                            Incorrect
                                        </div>
                                    @elseif($requiresManualGrading)
                                        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                            </svg>
                                            Pending Review
                                        </div>
                                    @elseif(!$answer)
                                        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-600">
                                            Not Answered
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Answer Display -->
                            <div class="mt-4 space-y-3">
                                @if($questionType === 'mcq_single' || $questionType === 'true_false' || $questionType === 'image_choice')
                                    <!-- Multiple Choice / True-False / Image Choice Answer Review -->
                                    @foreach($question->options as $option)
                                        @php
                                            $isUserAnswer = $answer && $answer->selected_option_id == $option->id;
                                            $isCorrect = $option->is_correct;
                                        @endphp
                                        <div class="p-4 rounded-lg border-2
                                            @if($isUserAnswer && $isCorrect) border-green-500 bg-green-50
                                            @elseif($isUserAnswer && !$isCorrect) border-red-500 bg-red-50
                                            @elseif($isCorrect) border-green-500 bg-green-50
                                            @else border-gray-200 bg-white
                                            @endif">
                                            <div class="flex items-center justify-between">
                                                @if($questionType === 'image_choice')
                                                    <img src="{{ $option->option_text }}" alt="Option" class="h-32 object-cover rounded">
                                                @else
                                                    <span class="text-gray-900">{{ $option->option_text }}</span>
                                                @endif
                                                <div class="flex items-center gap-2">
                                                    @if($isUserAnswer)
                                                        <span class="text-sm text-gray-600 bg-gray-200 px-2 py-1 rounded">Your answer</span>
                                                    @endif
                                                    @if($isCorrect)
                                                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                @elseif($questionType === 'short_answer' || $questionType === 'essay')
                                    <!-- Manual Grading Answer Review -->
                                    <div class="space-y-3">
                                        <div>
                                            <div class="text-sm font-medium text-gray-700 mb-2">Your Answer:</div>
                                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                                @if($answer && $answer->answer_text)
                                                    <p class="text-gray-900 whitespace-pre-wrap">{{ $answer->answer_text }}</p>
                                                @else
                                                    <p class="text-gray-500 italic">No answer provided</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                            <p class="text-sm text-yellow-800">
                                                <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                </svg>
                                                This question requires manual grading by the instructor.
                                            </p>
                                        </div>
                                    </div>

                                @elseif($questionType === 'fill_blanks')
                                    <!-- Fill in the Blanks Answer Review -->
                                    <div class="space-y-3">
                                        <div>
                                            <div class="text-sm font-medium text-gray-700 mb-2">Your Answer:</div>
                                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                                @if($answer && $answer->answer_text)
                                                    @php
                                                        $blanks = json_decode($answer->answer_text, true) ?? [];
                                                    @endphp
                                                    @if(is_array($blanks) && count($blanks) > 0)
                                                        <div class="space-y-2">
                                                            @foreach($blanks as $index => $blank)
                                                                <div>
                                                                    <span class="text-sm text-gray-600">Blank {{ $index + 1 }}:</span>
                                                                    <span class="font-medium text-gray-900">{{ $blank }}</span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <p class="text-gray-900">{{ $answer->answer_text }}</p>
                                                    @endif
                                                @else
                                                    <p class="text-gray-500 italic">No answer provided</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                @elseif($questionType === 'mcq_multiple')
                                    <!-- Multiple Answer Review -->
                                    @php
                                        $selectedOptions = $answer && $answer->answer_text ? json_decode($answer->answer_text, true) : [];
                                    @endphp
                                    @foreach($question->options as $option)
                                        @php
                                            $isUserAnswer = in_array($option->id, $selectedOptions ?? []);
                                            $isCorrect = $option->is_correct;
                                        @endphp
                                        <div class="p-4 rounded-lg border-2
                                            @if($isUserAnswer && $isCorrect) border-green-500 bg-green-50
                                            @elseif($isUserAnswer && !$isCorrect) border-red-500 bg-red-50
                                            @elseif($isCorrect) border-green-500 bg-green-50
                                            @else border-gray-200 bg-white
                                            @endif">
                                            <div class="flex items-center justify-between">
                                                <span class="text-gray-900">{{ $option->option_text }}</span>
                                                <div class="flex items-center gap-2">
                                                    @if($isUserAnswer)
                                                        <span class="text-sm text-gray-600 bg-gray-200 px-2 py-1 rounded">Selected</span>
                                                    @endif
                                                    @if($isCorrect)
                                                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                @elseif($questionType === 'matching')
                                    <!-- Matching Answer Review -->
                                    @php
                                        $pairs = $question->settings['pairs'] ?? [];
                                        $studentMatches = $answer && $answer->answer_text ? json_decode($answer->answer_text, true) : [];
                                    @endphp
                                    <div class="space-y-3">
                                        @foreach($pairs as $leftIndex => $pair)
                                            @php
                                                $studentAnswer = $studentMatches[$leftIndex] ?? null;
                                                $isCorrect = $studentAnswer !== null && $studentAnswer == $leftIndex;
                                            @endphp
                                            <div class="p-4 rounded-lg border-2 {{ $isCorrect ? 'border-green-500 bg-green-50' : 'border-red-500 bg-red-50' }}">
                                                <div class="flex items-center gap-3">
                                                    <div class="flex-1 font-medium">{{ $pair['left'] }}</div>
                                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                                    </svg>
                                                    <div class="flex-1">
                                                        <div class="text-gray-900">{{ $pair['right'] }}</div>
                                                        @if(!$isCorrect && $studentAnswer !== null)
                                                            <div class="text-sm text-red-600 mt-1">Your answer: {{ $pairs[$studentAnswer]['right'] ?? 'Unknown' }}</div>
                                                        @endif
                                                    </div>
                                                    @if($isCorrect)
                                                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                    @else
                                                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                        </svg>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                @else
                                    <!-- Fallback for other question types -->
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <p class="text-sm text-gray-600">Answer review not available for this question type.</p>
                                    </div>
                                @endif

                                <!-- Explanation -->
                                @if($question->explanation)
                                    <div class="mt-3 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                            </svg>
                                            <div>
                                                <div class="text-sm font-medium text-blue-900 mb-1">Explanation</div>
                                                <div class="text-sm text-blue-800">{!! $question->explanation !!}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex items-center justify-between">
                <a href="{{ route('student.quizzes.show', $quiz) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    ← Back to Quiz Overview
                </a>
                <a href="{{ route('student.courses.learn', $course) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Continue Learning →
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
