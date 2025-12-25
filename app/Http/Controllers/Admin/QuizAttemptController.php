<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuizAttempt;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttemptAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class QuizAttemptController extends Controller
{
    /**
     * Display a listing of all quiz attempts
     */
    public function index(Request $request)
    {
        $query = QuizAttempt::with(['user', 'quiz', 'quizAnswers']);

        // Search filter (by student name or email)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Quiz filter
        if ($request->filled('quiz_id')) {
            $query->where('quiz_id', $request->input('quiz_id'));
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Get attempts with pagination
        $attempts = $query->orderBy('started_at', 'desc')->paginate(20);

        // Get all quizzes for filter dropdown
        $quizzes = Quiz::orderBy('title')->get();

        // Calculate stats
        $stats = [
            'completed' => QuizAttempt::where('status', 'completed')->count(),
            'in_progress' => QuizAttempt::where('status', 'in_progress')->count(),
            'avg_score' => round(QuizAttempt::whereNotNull('score')->avg('score') ?? 0, 1),
        ];

        return view('admin.quiz-attempts.index', compact('attempts', 'quizzes', 'stats'));
    }

    /**
     * Display the specified quiz attempt with all details
     */
    public function show(QuizAttempt $quizAttempt)
    {
        $quizAttempt->load([
            'quiz.questions.questionType',
            'quiz.questions.options',
            'user',
            'enrollment',
            'gradedBy',
            'quizAnswers',
        ]);

        // Group answers by question from quiz_answers table
        $answersGrouped = [];
        foreach ($quizAttempt->quizAnswers as $quizAnswer) {
            // Map quiz_answers fields to expected format
            $answersGrouped[$quizAnswer->question_id] = [
                'question_id' => $quizAnswer->question_id,
                'answer' => $quizAnswer->selected_option_id ?? $quizAnswer->answer,
                'awarded_points' => $quizAnswer->points_earned ?? 0,
                'grader_feedback' => $quizAnswer->feedback,
                'is_correct' => $quizAnswer->is_correct,
            ];
        }

        return view('admin.quiz-attempts.show', compact('quizAttempt', 'answersGrouped'));
    }

    /**
     * Show the grading interface for manual grading
     */
    public function grade(QuizAttempt $quizAttempt)
    {
        // Only show grading interface if manual grading is required and not already graded
        if (!$quizAttempt->requires_manual_grading || $quizAttempt->status === 'graded') {
            return redirect()
                ->route('admin.quiz-attempts.show', $quizAttempt)
                ->with('info', 'This attempt does not require manual grading or is already graded.');
        }

        $quizAttempt->load([
            'quiz.questions.questionType',
            'quiz.questions.options',
            'user',
            'enrollment',
            'quizAnswers',
        ]);

        // Group answers by question from quiz_answers table
        $answersGrouped = [];
        foreach ($quizAttempt->quizAnswers as $quizAnswer) {
            // Map quiz_answers fields to expected format
            $answersGrouped[$quizAnswer->question_id] = [
                'question_id' => $quizAnswer->question_id,
                'answer' => $quizAnswer->selected_option_id ?? $quizAnswer->answer,
                'awarded_points' => $quizAnswer->points_earned ?? 0,
                'grader_feedback' => $quizAnswer->feedback,
                'is_correct' => $quizAnswer->is_correct,
            ];
        }

        return view('admin.quiz-attempts.grade', compact('quizAttempt', 'answersGrouped'));
    }

    /**
     * Submit manual grading scores
     */
    public function submitGrade(Request $request, QuizAttempt $quizAttempt)
    {
        // Validate that the attempt requires manual grading
        if (!$quizAttempt->requires_manual_grading) {
            return back()->with('error', 'This attempt does not require manual grading.');
        }

        // Validate grading input
        $validated = $request->validate([
            'scores' => 'required|array',
            'scores.*' => 'required|numeric|min:0',
            'feedback' => 'nullable|array',
            'feedback.*' => 'nullable|string',
            'grading_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Get all questions
            $questions = $quizAttempt->quiz->questions()->with('questionType')->get();

            // Calculate total possible points from ALL questions in the quiz
            $totalPoints = $questions->sum('points');

            // Calculate earned score
            $totalScore = 0;

            // Update quiz_answers table
            foreach ($quizAttempt->quizAnswers as $quizAnswer) {
                $question = $questions->firstWhere('id', $quizAnswer->question_id);
                if (!$question) continue;

                // Check if this question needs manual grading based on scoring_strategy
                $requiresManualGrading = $question->questionType->scoring_strategy === 'manual';

                if ($requiresManualGrading && isset($validated['scores'][$question->id])) {
                    // Manually graded question
                    $awardedPoints = min($validated['scores'][$question->id], $question->points);
                    $isCorrect = $awardedPoints >= $question->points;

                    $quizAnswer->update([
                        'points_earned' => $awardedPoints,
                        'is_correct' => $isCorrect,
                        'feedback' => $validated['feedback'][$question->id] ?? null,
                    ]);

                    $totalScore += $awardedPoints;
                } else {
                    // Auto-graded question - use already calculated points
                    $totalScore += $quizAnswer->points_earned ?? 0;
                }
            }

            // Calculate percentage and pass status
            $percentage = $totalPoints > 0 ? ($totalScore / $totalPoints) * 100 : 0;
            $passed = $percentage >= $quizAttempt->quiz->passing_score;

            // Update the attempt
            $quizAttempt->update([
                'score' => round($percentage, 2),
                'total_points' => $totalPoints,
                'percentage' => round($percentage, 2),
                'passed' => $passed,
                'status' => 'graded',
                'graded_at' => now(),
                'graded_by' => Auth::id(),
                'completed_at' => $quizAttempt->completed_at ?? now(),
            ]);

            DB::commit();

            return redirect()
                ->route('admin.quiz-attempts.show', $quizAttempt)
                ->with('success', 'Quiz attempt graded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to grade attempt: ' . $e->getMessage());
        }
    }

    /**
     * Reset a quiz attempt (allow student to retake)
     */
    public function reset(QuizAttempt $quizAttempt)
    {
        DB::beginTransaction();
        try {
            // Only reset submitted or graded attempts
            if (!in_array($quizAttempt->status, ['submitted', 'graded'])) {
                return back()->with('error', 'Only submitted or graded attempts can be reset.');
            }

            // Reset the attempt
            $quizAttempt->update([
                'status' => 'in_progress',
                'score' => null,
                'percentage' => null,
                'passed' => false,
                'answers' => null,
                'submitted_at' => null,
                'completed_at' => null,
                'graded_at' => null,
                'graded_by' => null,
                'time_taken' => null,
                'requires_manual_grading' => false,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.quizzes.attempts', $quizAttempt->quiz)
                ->with('success', 'Quiz attempt reset successfully. Student can now retake.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Failed to reset attempt: ' . $e->getMessage());
        }
    }

    /**
     * Regrade a quiz attempt (recalculate scores based on current correct answers)
     */
    public function regrade(QuizAttempt $quizAttempt)
    {
        // Only auto-gradable attempts can be regraded
        if ($quizAttempt->requires_manual_grading) {
            return back()->with('error', 'Manual attempts must be graded manually.');
        }

        DB::beginTransaction();
        try {
            // Parse answers
            $answers = is_array($quizAttempt->answers) ? $quizAttempt->answers : json_decode($quizAttempt->answers, true);

            if (!is_array($answers)) {
                return back()->with('error', 'No answers found to regrade.');
            }

            $totalScore = 0;
            $totalPoints = 0;
            $updatedAnswers = [];

            foreach ($answers as $answer) {
                $questionId = $answer['question_id'] ?? null;
                if (!$questionId) {
                    continue;
                }

                $question = Question::with(['questionType', 'options'])->find($questionId);
                if (!$question) {
                    continue;
                }

                $totalPoints += $question->points;

                // Regrade based on question type
                $awardedPoints = $this->calculateScore($question, $answer);
                $answer['awarded_points'] = $awardedPoints;
                $totalScore += $awardedPoints;

                $updatedAnswers[] = $answer;
            }

            $percentage = $totalPoints > 0 ? ($totalScore / $totalPoints) * 100 : 0;
            $passed = $percentage >= $quizAttempt->quiz->passing_score;

            $quizAttempt->update([
                'answers' => $updatedAnswers,
                'score' => $totalScore,
                'total_points' => $totalPoints,
                'percentage' => $percentage,
                'passed' => $passed,
                'status' => 'graded',
            ]);

            DB::commit();

            return back()->with('success', 'Quiz attempt regraded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Failed to regrade attempt: ' . $e->getMessage());
        }
    }

    /**
     * Calculate score for a single question
     */
    private function calculateScore(Question $question, array $answer): float
    {
        $strategy = $question->questionType->scoring_strategy;

        if ($strategy === 'manual') {
            return 0; // Manual grading required
        }

        $userAnswer = $answer['answer'] ?? null;

        // Auto-exact matching
        if ($strategy === 'auto_exact') {
            if ($question->questionType->slug === 'true_false') {
                $correctOption = $question->options()->where('is_correct', true)->first();
                return $userAnswer == $correctOption?->id ? $question->points : 0;
            }

            if ($question->questionType->slug === 'mcq_single') {
                $correctOption = $question->options()->where('is_correct', true)->first();
                return $userAnswer == $correctOption?->id ? $question->points : 0;
            }

            if ($question->questionType->slug === 'short_answer') {
                $settings = $question->settings ?? [];
                $correctAnswers = $settings['accepted_answers'] ?? [];
                $caseSensitive = $settings['case_sensitive'] ?? false;

                foreach ($correctAnswers as $correctAnswer) {
                    if ($caseSensitive) {
                        if ($userAnswer === $correctAnswer) {
                            return $question->points;
                        }
                    } else {
                        if (strtolower($userAnswer) === strtolower($correctAnswer)) {
                            return $question->points;
                        }
                    }
                }
                return 0;
            }
        }

        // Auto-partial matching (MCQ multiple, matching)
        if ($strategy === 'auto_partial') {
            if ($question->questionType->slug === 'mcq_multiple') {
                $correctOptionIds = $question->options()->where('is_correct', true)->pluck('id')->toArray();
                $userAnswers = is_array($userAnswer) ? $userAnswer : [$userAnswer];

                if (empty($correctOptionIds)) {
                    return 0;
                }

                $correctCount = count(array_intersect($userAnswers, $correctOptionIds));
                $incorrectCount = count(array_diff($userAnswers, $correctOptionIds));
                $missedCount = count(array_diff($correctOptionIds, $userAnswers));

                // Partial credit: (correct - incorrect) / total_correct
                $score = ($correctCount - $incorrectCount) / count($correctOptionIds);
                return max(0, $score * $question->points);
            }
        }

        return 0;
    }

    /**
     * Delete a quiz attempt
     */
    public function destroy(QuizAttempt $quizAttempt)
    {
        try {
            $quizAttempt->delete();

            return redirect()
                ->route('admin.quiz-attempts.index')
                ->with('success', 'Quiz attempt deleted successfully!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to delete quiz attempt: ' . $e->getMessage());
        }
    }
}
