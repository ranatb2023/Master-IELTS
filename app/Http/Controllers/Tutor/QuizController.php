<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /**
     * Display quizzes for tutor's courses
     */
    public function index()
    {
        $quizzes = Quiz::whereHas('course', function ($query) {
            $query->where('instructor_id', auth()->id());
        })
        ->with(['course', 'topic'])
        ->withCount('attempts')
        ->latest()
        ->paginate(20);

        return view('tutor.quizzes.index', compact('quizzes'));
    }

    /**
     * Show form to create quiz
     */
    public function create()
    {
        $courses = auth()->user()->createdCourses()
            ->where('status', '!=', 'archived')
            ->orderBy('title')
            ->get();

        return view('tutor.quizzes.create', compact('courses'));
    }

    /**
     * Store a new quiz
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'topic_id' => 'nullable|exists:topics,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:practice,graded,final_exam,mock_test',
            'passing_score' => 'required|integer|min:0|max:100',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'max_attempts' => 'nullable|integer|min:1',
            'show_correct_answers' => 'boolean',
            'randomize_questions' => 'boolean',
            'randomize_options' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Ensure tutor owns this course
        $course = Course::findOrFail($validated['course_id']);
        if ($course->instructor_id !== auth()->id()) {
            abort(403);
        }

        $quiz = Quiz::create($validated);

        return redirect()
            ->route('tutor.quizzes.show', $quiz)
            ->with('success', 'Quiz created successfully! Now add questions.');
    }

    /**
     * Display quiz details
     */
    public function show(Quiz $quiz)
    {
        // Ensure tutor owns the course
        if ($quiz->course->instructor_id !== auth()->id()) {
            abort(403);
        }

        $quiz->load(['course', 'topic', 'questions.options']);

        $stats = [
            'total_questions' => $quiz->questions()->count(),
            'total_attempts' => $quiz->attempts()->count(),
            'average_score' => round($quiz->attempts()->avg('score') ?? 0, 2),
            'pass_rate' => $quiz->attempts()->count() > 0
                ? round($quiz->attempts()->where('passed', true)->count() / $quiz->attempts()->count() * 100, 2)
                : 0,
        ];

        return view('tutor.quizzes.show', compact('quiz', 'stats'));
    }

    /**
     * Show form to edit quiz
     */
    public function edit(Quiz $quiz)
    {
        // Ensure tutor owns the course
        if ($quiz->course->instructor_id !== auth()->id()) {
            abort(403);
        }

        $courses = auth()->user()->createdCourses()
            ->where('status', '!=', 'archived')
            ->orderBy('title')
            ->get();

        return view('tutor.quizzes.edit', compact('quiz', 'courses'));
    }

    /**
     * Update quiz
     */
    public function update(Request $request, Quiz $quiz)
    {
        // Ensure tutor owns the course
        if ($quiz->course->instructor_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:practice,graded,final_exam,mock_test',
            'passing_score' => 'required|integer|min:0|max:100',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'max_attempts' => 'nullable|integer|min:1',
            'show_correct_answers' => 'boolean',
            'randomize_questions' => 'boolean',
            'randomize_options' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $quiz->update($validated);

        return redirect()
            ->route('tutor.quizzes.show', $quiz)
            ->with('success', 'Quiz updated successfully!');
    }

    /**
     * View quiz results/attempts
     */
    public function results(Quiz $quiz)
    {
        // Ensure tutor owns the course
        if ($quiz->course->instructor_id !== auth()->id()) {
            abort(403);
        }

        $attempts = $quiz->attempts()
            ->with(['user', 'enrollment'])
            ->latest()
            ->paginate(20);

        return view('tutor.quizzes.results', compact('quiz', 'attempts'));
    }
}
