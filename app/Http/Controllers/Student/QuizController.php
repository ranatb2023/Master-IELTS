<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /**
     * Display quiz details
     */
    public function show(Quiz $quiz)
    {
        // Check enrollment
        $course = $quiz->topic->course;
        $enrollment = auth()->user()->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->firstOrFail();

        // Check if quiz is published
        if (!$quiz->is_published) {
            abort(404, 'This quiz is not available.');
        }

        // Load course with topics, lessons, quizzes, assignments for sidebar
        $course->load([
            'topics.lessons',
            'topics.quizzes',
            'topics.assignments'
        ]);

        // Get all attempts (including in-progress)
        $attempts = auth()->user()->quizAttempts()
            ->where('quiz_id', $quiz->id)
            ->orderBy('started_at', 'desc')
            ->get();

        // Check if max attempts reached
        $canAttempt = !$quiz->max_attempts || $attempts->count() < $quiz->max_attempts;

        // Get best score
        $bestScore = $attempts->max('score') ?? 0;

        // Get completed lessons for progress tracking (for sidebar checkmarks)
        $lessonIds = $course->topics->flatMap(function ($topic) {
            return $topic->lessons->pluck('id');
        })->toArray();

        $progress = auth()->user()->progress()
            ->where('progressable_type', 'App\\Models\\Lesson')
            ->whereIn('progressable_id', $lessonIds)
            ->get()
            ->keyBy('progressable_id');

        $completedLessons = $progress->where('status', 'completed')->pluck('progressable_id')->toArray();

        // Get accurate progress percentage from CourseProgress model (includes lessons, quizzes, assignments)
        $courseProgress = \App\Models\CourseProgress::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->first();

        $progressPercentage = $courseProgress ? $courseProgress->progress_percentage : 0;

        // Get all quiz IDs for the current course
        $courseQuizIds = $course->topics->flatMap(function ($topic) {
            return $topic->quizzes->pluck('id');
        })->toArray();

        // Get all quiz attempts for the user in this course
        $userCourseQuizAttempts = auth()->user()->quizAttempts()
            ->whereIn('quiz_id', $courseQuizIds)
            ->get();

        // Get completed (passed) quizzes for sidebar checkpoint marks
        $completedQuizzes = $userCourseQuizAttempts->where('passed', true)->pluck('quiz_id')->unique()->toArray();

        // Get completed (graded) assignments for sidebar checkmarks
        $assignmentIds = $course->topics->flatMap(fn($topic) => $topic->assignments->pluck('id'))->toArray();
        $gradedSubmissions = auth()->user()->assignmentSubmissions()
            ->where('status', 'graded')
            ->whereIn('assignment_id', $assignmentIds)
            ->get();
        $completedAssignments = $gradedSubmissions->pluck('assignment_id')->unique()->toArray();

        // Build navigation (previous/next)
        $allContent = $course->topics->flatMap(function ($topic) {
            $contentItems = collect();

            foreach ($topic->lessons as $lesson) {
                $contentItems->push([
                    'type' => 'lesson',
                    'order' => $lesson->order ?? 0,
                    'item' => $lesson,
                    'topic_id' => $topic->id,
                ]);
            }

            foreach ($topic->quizzes as $qz) {
                $contentItems->push([
                    'type' => 'quiz',
                    'order' => $qz->order ?? 0,
                    'item' => $qz,
                    'topic_id' => $topic->id,
                ]);
            }

            foreach ($topic->assignments as $assignment) {
                $contentItems->push([
                    'type' => 'assignment',
                    'order' => $assignment->order ?? 0,
                    'item' => $assignment,
                    'topic_id' => $topic->id,
                ]);
            }

            return $contentItems->sortBy('order');
        });

        // Find current quiz index
        $currentIndex = $allContent->search(function ($content) use ($quiz) {
            return $content['type'] === 'quiz' && $content['item']->id === $quiz->id;
        });

        // Get previous and next content
        $previousContent = $currentIndex > 0 ? $allContent[$currentIndex - 1] : null;
        $nextContent = $currentIndex !== false && $currentIndex < $allContent->count() - 1 ? $allContent[$currentIndex + 1] : null;

        $previousItem = null;
        $nextItem = null;

        if ($previousContent) {
            $previousItem = [
                'type' => $previousContent['type'],
                'item' => $previousContent['item'],
                'topic_id' => $previousContent['topic_id'],
            ];
        }

        if ($nextContent) {
            $nextItem = [
                'type' => $nextContent['type'],
                'item' => $nextContent['item'],
                'topic_id' => $nextContent['topic_id'],
            ];
        }

        return view('student.quizzes.show', compact(
            'course',
            'quiz',
            'enrollment',
            'attempts',
            'canAttempt',
            'bestScore',
            'completedLessons',
            'completedQuizzes',
            'completedAssignments',
            'progressPercentage',
            'previousItem',
            'nextItem'
        ));
    }

    /**
     * Start a quiz attempt
     */
    public function start(Quiz $quiz)
    {
        // Check enrollment
        $course = $quiz->topic->course;
        $enrollment = auth()->user()->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->firstOrFail();

        if (!$quiz->is_published) {
            abort(404);
        }

        // Check if quiz has questions
        if ($quiz->questions()->count() === 0) {
            return back()->with('error', 'This quiz has no questions and cannot be started.');
        }

        // Check max attempts
        $attemptCount = auth()->user()->quizAttempts()
            ->where('quiz_id', $quiz->id)
            ->count();

        if ($quiz->max_attempts && $attemptCount >= $quiz->max_attempts) {
            return back()->with('error', 'Maximum attempts reached for this quiz.');
        }

        // Create new attempt
        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => auth()->id(),
            'enrollment_id' => $enrollment->id,
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        return redirect()->route('student.quizzes.take', [$quiz, $attempt]);
    }

    /**
     * Display quiz taking interface
     */
    public function take(Quiz $quiz, QuizAttempt $attempt)
    {
        // Check ownership and status
        if ($attempt->user_id !== auth()->id() || $attempt->quiz_id !== $quiz->id) {
            abort(403);
        }

        $course = $quiz->topic->course;

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('student.quizzes.result', [$quiz, $attempt]);
        }

        // Check time limit
        if ($quiz->time_limit) {
            $timeElapsed = now()->diffInMinutes($attempt->started_at);
            if ($timeElapsed > $quiz->time_limit) {
                // Auto-submit
                return $this->autoSubmit($attempt);
            }
        }

        $quiz->load([
            'questions' => function ($query) use ($quiz) {
                if ($quiz->shuffle_questions) {
                    $query->inRandomOrder();
                } else {
                    $query->orderBy('order');
                }
            },
            'questions.questionType',
            'questions.options' => function ($query) use ($quiz) {
                if ($quiz->shuffle_answers) {
                    $query->inRandomOrder();
                } else {
                    $query->orderBy('order');
                }
            }
        ]);

        // Load existing answers
        $existingAnswers = $attempt->quizAnswers()
            ->get()
            ->keyBy('question_id');

        return view('student.quizzes.take', compact('course', 'quiz', 'attempt', 'existingAnswers'));
    }

    /**
     * Save quiz answer
     */
    public function saveAnswer(Request $request, Quiz $quiz, QuizAttempt $attempt)
    {
        // Check ownership and status
        if ($attempt->user_id !== auth()->id() || $attempt->status !== 'in_progress') {
            abort(403);
        }

        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer_text' => 'nullable|string',
            'selected_option_id' => 'nullable|exists:question_options,id',
        ]);

        // Map to actual database fields
        $data = [];

        // Handle text answers (essay, short_answer, fill_blanks)
        if (isset($validated['answer_text'])) {
            $data['answer'] = $validated['answer_text'];
        }

        // Handle option selection (mcq_single, true_false, image_choice, mcq_multiple)
        if (isset($validated['selected_option_id'])) {
            // Store as array for consistency
            $data['selected_options'] = [$validated['selected_option_id']];
        }

        $attempt->quizAnswers()->updateOrCreate(
            ['question_id' => $validated['question_id']],
            $data
        );

        return response()->json(['success' => true]);
    }

    /**
     * Submit quiz
     */
    public function submit(Quiz $quiz, QuizAttempt $attempt)
    {
        // Check ownership and status
        if ($attempt->user_id !== auth()->id() || $attempt->status !== 'in_progress') {
            abort(403);
        }

        // Grade the attempt
        $this->gradeAttempt($attempt);

        // Check if quiz has questions requiring manual grading
        $hasManualGradingQuestions = $quiz->questions()->whereHas('questionType', function ($q) {
            $q->whereIn('slug', ['short_answer', 'essay']);
        })->exists();

        // Notify instructor if manual grading is required
        if ($hasManualGradingQuestions && $quiz->course->instructor) {
            $quiz->course->instructor->notify(new \App\Notifications\AssignmentSubmittedNotification(
                (object) ['title' => $quiz->title],
                (object) ['user' => $attempt->user]
            ));
        }

        return redirect()
            ->route('student.quizzes.result', [$quiz, $attempt])
            ->with('success', 'Quiz submitted successfully!');
    }

    /**
     * Display quiz results
     */
    public function result(Quiz $quiz, QuizAttempt $attempt)
    {
        // Check ownership
        if ($attempt->user_id !== auth()->id() || $attempt->quiz_id !== $quiz->id) {
            abort(403);
        }

        $course = $quiz->topic->course;

        if ($attempt->status === 'in_progress') {
            return redirect()->route('student.quizzes.take', [$quiz, $attempt]);
        }

        $attempt->load(['quizAnswers.question.options']);

        return view('student.quizzes.result', compact('course', 'quiz', 'attempt'));
    }

    /**
     * Auto-submit when time expires
     */
    private function autoSubmit(QuizAttempt $attempt)
    {
        $this->gradeAttempt($attempt);

        return redirect()
            ->route('student.quizzes.result', [
                $attempt->quiz_id,
                $attempt
            ])
            ->with('info', 'Time expired. Quiz auto-submitted.');
    }

    /**
     * Force delete quiz attempt when user abandons
     */
    public function forceComplete(Quiz $quiz, QuizAttempt $attempt)
    {
        // Check ownership and status
        if ($attempt->user_id !== auth()->id() || $attempt->status !== 'in_progress') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Delete all answers for this attempt
        $attempt->answers()->delete();

        // Delete the attempt itself
        $attempt->delete();

        return response()->json([
            'success' => true,
            'message' => 'Quiz attempt deleted'
        ]);
    }

    /**
     * Grade quiz attempt
     */
    private function gradeAttempt(QuizAttempt $attempt)
    {
        $quiz = $attempt->quiz;
        $quiz->load('questions.questionType', 'questions.options');

        $totalPoints = 0;
        $earnedPoints = 0;

        foreach ($quiz->questions as $question) {
            $totalPoints += $question->points;

            $answer = $attempt->quizAnswers()
                ->where('question_id', $question->id)
                ->first();

            if ($answer && $question->questionType) {
                $questionTypeSlug = $question->questionType->slug;
                $isCorrect = false;
                $pointsForThisQuestion = 0;

                // Check if this question type requires manual grading
                $requiresManualGradingQuestion = in_array($questionTypeSlug, ['short_answer', 'essay']);

                if (!$requiresManualGradingQuestion) {
                    // Auto-grade based on question type
                    if ($questionTypeSlug === 'mcq_single' || $questionTypeSlug === 'true_false' || $questionTypeSlug === 'image_choice') {
                        // Multiple choice single, true/false, or image choice
                        $correctOption = $question->options->where('is_correct', true)->first();
                        // Check using accessor (selected_option_id) or direct field (selected_options[0])
                        $selectedId = $answer->selected_option_id ?? ($answer->selected_options[0] ?? null);
                        if ($correctOption && $selectedId === $correctOption->id) {
                            $isCorrect = true;
                        }
                    } elseif ($questionTypeSlug === 'mcq_multiple') {
                        // Multiple choice multiple - check if all selected options are correct
                        // Answer is stored in 'answer' field as JSON string
                        $selectedOptions = is_string($answer->answer) ? json_decode($answer->answer, true) : ($answer->selected_options ?? []);
                        $correctOptions = $question->options->where('is_correct', true)->pluck('id')->toArray();
                        sort($selectedOptions);
                        sort($correctOptions);
                        if ($selectedOptions === $correctOptions) {
                            $isCorrect = true;
                        }
                    } elseif ($questionTypeSlug === 'matching') {
                        // Matching - check if all pairs match correctly
                        $studentMatches = is_string($answer->answer) ? json_decode($answer->answer, true) : [];
                        $pairs = $question->settings['pairs'] ?? [];

                        $allCorrect = true;

                        // The student answer maps leftIndex => rightIndex
                        // We need to check if each pair is correctly matched
                        foreach ($pairs as $leftIndex => $pair) {
                            if (!isset($studentMatches[$leftIndex])) {
                                $allCorrect = false;
                                break;
                            }

                            $selectedRightIndex = (int) $studentMatches[$leftIndex];

                            // For matching questions, the correct answer is when indices match
                            // because the pairs array defines the correct mappings
                            // leftIndex 0 should map to rightIndex 0, etc.
                            if ($selectedRightIndex !== $leftIndex) {
                                $allCorrect = false;
                                break;
                            }
                        }
                        $isCorrect = $allCorrect;
                    } elseif ($questionTypeSlug === 'fill_blanks') {
                        // Fill in the blanks - check if all blanks are filled correctly
                        $studentAnswer = $answer->answer;
                        $blanks = $question->settings['blanks'] ?? [];

                        $allCorrect = true;

                        // Student answer should be a JSON string with blank answers
                        $studentBlanks = is_string($studentAnswer) ? json_decode($studentAnswer, true) : [];

                        // Check each blank
                        foreach ($blanks as $index => $blank) {
                            $acceptedAnswers = is_array($blank['answers']) ? $blank['answers'] : [$blank['answers']];
                            $studentBlankAnswer = $studentBlanks[$index] ?? '';

                            // Check if student answer matches any accepted answer (case-insensitive)
                            $matched = false;
                            foreach ($acceptedAnswers as $acceptedAnswer) {
                                if (strcasecmp(trim($studentBlankAnswer), trim($acceptedAnswer)) === 0) {
                                    $matched = true;
                                    break;
                                }
                            }

                            if (!$matched) {
                                $allCorrect = false;
                                break;
                            }
                        }
                        $isCorrect = $allCorrect;
                    }

                    // Award points for correct answers
                    if ($isCorrect) {
                        $pointsForThisQuestion = $question->points;
                        $earnedPoints += $question->points;
                    }

                    $answer->update([
                        'is_correct' => $isCorrect,
                        'points_earned' => $pointsForThisQuestion
                    ]);
                } else {
                    // Manual grading questions - set to null/0 initially
                    $answer->update([
                        'is_correct' => null,
                        'points_earned' => 0
                    ]);
                }
            }
        }

        // Check if any questions require manual grading
        $requiresManualGrading = false;
        foreach ($quiz->questions as $question) {
            $questionTypeSlug = $question->questionType->slug ?? null;
            if (in_array($questionTypeSlug, ['short_answer', 'essay'])) {
                // Check if this question has scoring_strategy set to 'manual'
                $scoringStrategy = $question->questionType->scoring_strategy ?? 'auto_exact';
                if ($scoringStrategy === 'manual') {
                    $requiresManualGrading = true;
                    break;
                }
            }
        }

        $score = $totalPoints > 0 ? ($earnedPoints / $totalPoints) * 100 : 0;
        $passed = $score >= $quiz->passing_score;
        $timeTaken = now()->diffInSeconds($attempt->started_at);

        $attempt->update([
            'completed_at' => now(),
            'submitted_at' => now(),
            'status' => $requiresManualGrading ? 'submitted' : 'graded',
            'score' => round($score, 2),
            'total_points' => $totalPoints,
            'percentage' => round($score, 2),
            'passed' => $passed,
            'time_taken' => $timeTaken,
            'requires_manual_grading' => $requiresManualGrading,
        ]);

        // Update course progress to reflect quiz completion
        $course = $quiz->topic->course;
        $courseProgress = \App\Models\CourseProgress::firstOrCreate([
            'user_id' => $attempt->user_id,
            'course_id' => $course->id,
        ]);

        // Recalculate totals and completed counts
        $this->recalculateCourseProgress($courseProgress, $course);
        $courseProgress->updateProgress();
    }

    /**
     * Recalculate course progress totals and completed counts
     */
    private function recalculateCourseProgress($courseProgress, $course)
    {
        // Load all content
        $course->load(['topics.lessons', 'topics.quizzes', 'topics.assignments']);

        // Count total items
        $totalLessons = 0;
        $totalQuizzes = 0;
        $totalAssignments = 0;

        foreach ($course->topics as $topic) {
            $totalLessons += $topic->lessons->count();
            $totalQuizzes += $topic->quizzes->where('is_published', 1)->count();
            $totalAssignments += $topic->assignments->count();
        }

        // Count completed lessons
        $completedLessons = \App\Models\Progress::where('user_id', $courseProgress->user_id)
            ->where('progressable_type', 'App\\Models\\Lesson')
            ->where('status', 'completed')
            ->whereIn('progressable_id', function ($query) use ($course) {
                $query->select('lessons.id')
                    ->from('lessons')
                    ->join('topics', 'lessons.topic_id', '=', 'topics.id')
                    ->where('topics.course_id', $course->id);
            })
            ->count();

        // Count completed (passed) quizzes
        $completedQuizzes = \App\Models\QuizAttempt::where('user_id', $courseProgress->user_id)
            ->where('status', 'completed')
            ->where('passed', true)
            ->whereIn('quiz_id', function ($query) use ($course) {
                $query->select('quizzes.id')
                    ->from('quizzes')
                    ->join('topics', 'quizzes.topic_id', '=', 'topics.id')
                    ->where('topics.course_id', $course->id)
                    ->where('quizzes.is_published', 1);
            })
            ->distinct('quiz_id')
            ->count('quiz_id');

        // Count completed (graded) assignments
        $completedAssignments = \App\Models\AssignmentSubmission::where('user_id', $courseProgress->user_id)
            ->where('status', 'graded')
            ->whereIn('assignment_id', function ($query) use ($course) {
                $query->select('assignments.id')
                    ->from('assignments')
                    ->join('topics', 'assignments.topic_id', '=', 'topics.id')
                    ->where('topics.course_id', $course->id);
            })
            ->distinct('assignment_id')
            ->count('assignment_id');

        // Update the course progress record
        $courseProgress->update([
            'total_lessons' => $totalLessons,
            'total_quizzes' => $totalQuizzes,
            'total_assignments' => $totalAssignments,
            'completed_lessons' => $completedLessons,
            'completed_quizzes' => $completedQuizzes,
            'completed_assignments' => $completedAssignments,
        ]);
    }
}
