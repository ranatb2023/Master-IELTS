<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\QuizAttempt;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizAttemptController extends Controller
{
    /**
     * Display a listing of quiz attempts for tutor's quizzes.
     */
    public function index(Request $request)
    {
        $query = QuizAttempt::with(['user', 'quiz', 'quiz.lesson.topic.course'])
            ->whereHas('quiz.lesson.topic.course', function ($q) {
                $q->where('instructor_id', auth()->id());
            });

        // Apply filters
        if ($request->filled('quiz_id')) {
            $query->where('quiz_id', $request->quiz_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $attempts = $query->latest()->paginate(20);

        // Get tutor's quizzes for filter
        $quizzes = Quiz::whereHas('lesson.topic.course', function ($q) {
            $q->where('instructor_id', auth()->id());
        })
            ->select('id', 'title')
            ->orderBy('title')
            ->get();

        return view('tutor.quiz-attempts.index', compact('attempts', 'quizzes'));
    }

    /**
     * Display the specified quiz attempt.
     */
    public function show(QuizAttempt $quizAttempt)
    {
        // Ensure quiz attempt belongs to tutor's quiz
        if ($quizAttempt->quiz->lesson->topic->course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this quiz attempt.');
        }

        $quizAttempt->load([
            'user',
            'quiz.questions',
            'answers'
        ]);

        return view('tutor.quiz-attempts.show', compact('quizAttempt'));
    }

    /**
     * Grade manually graded quiz attempt.
     */
    public function grade(Request $request, QuizAttempt $quizAttempt)
    {
        // Ensure quiz attempt belongs to tutor's quiz
        if ($quizAttempt->quiz->lesson->topic->course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this quiz attempt.');
        }

        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string|max:1000',
        ]);

        $quizAttempt->update([
            'score' => $request->score,
            'feedback' => $request->feedback,
            'status' => $request->score >= $quizAttempt->quiz->passing_score ? 'passed' : 'failed',
            'graded_at' => now(),
            'graded_by' => auth()->id(),
        ]);

        return redirect()
            ->route('tutor.quiz-attempts.show', $quizAttempt)
            ->with('success', 'Quiz attempt graded successfully.');
    }
}
