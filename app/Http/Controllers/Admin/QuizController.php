<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Course;
use App\Models\Topic;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /**
     * Display a listing of quizzes
     */
    public function index(Request $request)
    {
        $query = Quiz::with(['course', 'topic'])->withCount('questions');

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by course
        if ($request->filled('course')) {
            $query->where('course_id', $request->course);
        }

        // Filter by published status
        if ($request->filled('is_published')) {
            $query->where('is_published', $request->is_published === '1');
        }

        $quizzes = $query->latest()->paginate(20);
        $courses = Course::orderBy('title')->get();

        return view('admin.quizzes.index', compact('quizzes', 'courses'));
    }

    /**
     * Show the form for creating a new quiz
     */
    public function create()
    {
        $courses = Course::orderBy('title')->get();

        return view('admin.quizzes.create', compact('courses'));
    }

    /**
     * Store a newly created quiz
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'topic_id' => 'required|exists:topics,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'passing_score' => 'required|numeric|min:0|max:100',
            'time_limit' => 'nullable|integer|min:1',
            'max_attempts' => 'nullable|integer|min:1',
            'show_answers' => 'required|in:never,after_submission,after_passing,always',
            'show_correct_answers' => 'boolean',
            'shuffle_questions' => 'boolean',
            'shuffle_answers' => 'boolean',
            'require_passing' => 'boolean',
            'certificate_eligible' => 'boolean',
            'is_published' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $quiz = Quiz::create($validated);

        return redirect()
            ->route('admin.quizzes.show', $quiz)
            ->with('success', 'Quiz created successfully! Now add questions.');
    }

    /**
     * Display the specified quiz
     */
    public function show(Quiz $quiz)
    {
        $quiz->load(['course', 'topic', 'questions.questionType', 'questions.options']);

        $stats = [
            'total_questions' => $quiz->questions()->count(),
            'total_attempts' => $quiz->attempts()->count(),
            'average_score' => $quiz->attempts()->avg('percentage') ?? 0,
            'pass_rate' => $quiz->attempts()->where('passed', true)->count() / max($quiz->attempts()->count(), 1) * 100,
            'pending_manual_grading' => $quiz->attempts()->where('requires_manual_grading', true)->where('status', '!=', 'graded')->count(),
        ];

        return view('admin.quizzes.show', compact('quiz', 'stats'));
    }

    /**
     * Show the form for editing the specified quiz
     */
    public function edit(Quiz $quiz)
    {
        $courses = Course::orderBy('title')->get();
        $topics = Topic::where('course_id', $quiz->course_id)->orderBy('order')->get();

        return view('admin.quizzes.edit', compact('quiz', 'courses', 'topics'));
    }

    /**
     * Update the specified quiz
     */
    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'topic_id' => 'required|exists:topics,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'passing_score' => 'required|numeric|min:0|max:100',
            'time_limit' => 'nullable|integer|min:1',
            'max_attempts' => 'nullable|integer|min:1',
            'show_answers' => 'required|in:never,after_submission,after_passing,always',
            'show_correct_answers' => 'boolean',
            'shuffle_questions' => 'boolean',
            'shuffle_answers' => 'boolean',
            'require_passing' => 'boolean',
            'certificate_eligible' => 'boolean',
            'is_published' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $quiz->update($validated);

        return redirect()
            ->route('admin.quizzes.show', $quiz)
            ->with('success', 'Quiz updated successfully!');
    }

    /**
     * Remove the specified quiz
     */
    public function destroy(Quiz $quiz)
    {
        // Check if quiz has attempts
        if ($quiz->attempts()->count() > 0) {
            return back()
                ->with('error', 'Cannot delete quiz with existing attempts. Archive it instead.');
        }

        $quiz->delete();

        return redirect()
            ->route('admin.quizzes.index')
            ->with('success', 'Quiz deleted successfully!');
    }

    /**
     * Manage quiz questions
     */
    public function questions(Quiz $quiz)
    {
        $quiz->load(['questions.options']);

        return view('admin.quizzes.questions', compact('quiz'));
    }

    /**
     * Add question to quiz
     */
    public function addQuestion(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false,short_answer,essay,matching,ordering',
            'points' => 'required|integer|min:1',
            'explanation' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'options' => 'required_if:question_type,multiple_choice|array|min:2',
            'options.*.text' => 'required|string',
            'options.*.is_correct' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // Set order if not provided
            if (!isset($validated['order'])) {
                $validated['order'] = $quiz->questions()->max('order') + 1;
            }

            $question = $quiz->questions()->create([
                'question_text' => $validated['question_text'],
                'question_type' => $validated['question_type'],
                'points' => $validated['points'],
                'explanation' => $validated['explanation'] ?? null,
                'order' => $validated['order'],
            ]);

            // Add options for multiple choice
            if ($validated['question_type'] === 'multiple_choice' && !empty($validated['options'])) {
                foreach ($validated['options'] as $index => $optionData) {
                    $question->options()->create([
                        'option_text' => $optionData['text'],
                        'is_correct' => $optionData['is_correct'] ?? false,
                        'order' => $index,
                    ]);
                }
            }

            DB::commit();

            return back()->with('success', 'Question added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to add question: ' . $e->getMessage());
        }
    }

    /**
     * Update question
     */
    public function updateQuestion(Request $request, Quiz $quiz, Question $question)
    {
        if ($question->quiz_id !== $quiz->id) {
            abort(404);
        }

        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false,short_answer,essay,matching,ordering',
            'points' => 'required|integer|min:1',
            'explanation' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'options' => 'required_if:question_type,multiple_choice|array',
            'options.*.text' => 'required|string',
            'options.*.is_correct' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $question->update([
                'question_text' => $validated['question_text'],
                'question_type' => $validated['question_type'],
                'points' => $validated['points'],
                'explanation' => $validated['explanation'] ?? null,
                'order' => $validated['order'] ?? $question->order,
            ]);

            // Update options for multiple choice
            if ($validated['question_type'] === 'multiple_choice' && !empty($validated['options'])) {
                // Delete existing options
                $question->options()->delete();

                // Add new options
                foreach ($validated['options'] as $index => $optionData) {
                    $question->options()->create([
                        'option_text' => $optionData['text'],
                        'is_correct' => $optionData['is_correct'] ?? false,
                        'order' => $index,
                    ]);
                }
            }

            DB::commit();

            return back()->with('success', 'Question updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update question: ' . $e->getMessage());
        }
    }

    /**
     * Delete question
     */
    public function deleteQuestion(Quiz $quiz, Question $question)
    {
        if ($question->quiz_id !== $quiz->id) {
            abort(404);
        }

        $question->delete();

        return back()->with('success', 'Question deleted successfully!');
    }

    /**
     * Duplicate quiz
     */
    public function duplicate(Quiz $quiz)
    {
        DB::beginTransaction();
        try {
            $newQuiz = $quiz->replicate();
            $newQuiz->title = $quiz->title . ' (Copy)';
            $newQuiz->save();

            // Copy questions and options
            foreach ($quiz->questions as $question) {
                $newQuestion = $question->replicate();
                $newQuestion->quiz_id = $newQuiz->id;
                $newQuestion->save();

                foreach ($question->options as $option) {
                    $newOption = $option->replicate();
                    $newOption->question_id = $newQuestion->id;
                    $newOption->save();
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.quizzes.edit', $newQuiz)
                ->with('success', 'Quiz duplicated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Failed to duplicate quiz: ' . $e->getMessage());
        }
    }

    /**
     * View quiz attempts/results
     */
    public function attempts(Request $request, Quiz $quiz)
    {
        $query = $quiz->attempts()->with(['user', 'enrollment']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by passed/failed
        if ($request->filled('passed')) {
            $query->where('passed', $request->passed === '1');
        }

        // Filter by student
        if ($request->filled('student')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->student . '%')
                    ->orWhere('email', 'like', '%' . $request->student . '%');
            });
        }

        // Filter by manual grading needs
        if ($request->filled('needs_grading')) {
            if ($request->needs_grading === '1') {
                $query->where('requires_manual_grading', true)
                    ->where('status', '!=', 'graded');
            } else {
                $query->where('requires_manual_grading', false);
            }
        }

        $attempts = $query->latest('submitted_at')->paginate(20);

        // Calculate stats
        $stats = [
            'average_score' => $quiz->attempts()->where('status', 'graded')->avg('percentage') ?? 0,
            'pass_rate' => $quiz->attempts()->where('passed', true)->count() / max($quiz->attempts()->count(), 1) * 100,
            'needs_grading' => $quiz->attempts()
                ->where('requires_manual_grading', true)
                ->where('status', '!=', 'graded')
                ->count(),
        ];

        return view('admin.quizzes.attempts', compact('quiz', 'attempts', 'stats'));
    }

    /**
     * Toggle quiz published status
     */
    public function toggleStatus(Quiz $quiz)
    {
        $quiz->update(['is_published' => !$quiz->is_published]);

        return back()->with('success', 'Quiz status updated successfully!');
    }
}
