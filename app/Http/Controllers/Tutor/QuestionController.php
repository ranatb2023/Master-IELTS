<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Display a listing of questions for tutor's quizzes.
     */
    public function index(Request $request)
    {
        $query = Question::with(['quiz.lesson.topic.course'])
            ->whereHas('quiz.lesson.topic.course', function ($q) {
                $q->where('instructor_id', auth()->id());
            });

        // Apply filters
        if ($request->filled('quiz_id')) {
            $query->where('quiz_id', $request->quiz_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $query->where('question_text', 'like', "%{$request->search}%");
        }

        $questions = $query->latest()->paginate(20);

        // Get tutor's quizzes for filter
        $quizzes = Quiz::whereHas('lesson.topic.course', function ($q) {
            $q->where('instructor_id', auth()->id());
        })
            ->select('id', 'title')
            ->orderBy('title')
            ->get();

        return view('tutor.questions.index', compact('questions', 'quizzes'));
    }

    /**
     * Show the form for creating a new question.
     */
    public function create(Request $request)
    {
        // Get tutor's quizzes
        $quizzes = Quiz::whereHas('lesson.topic.course', function ($q) {
            $q->where('instructor_id', auth()->id());
        })
            ->select('id', 'title', 'lesson_id')
            ->with('lesson.topic.course:id,title')
            ->orderBy('title')
            ->get();

        $selectedQuizId = $request->get('quiz_id');

        return view('tutor.questions.create', compact('quizzes', 'selectedQuizId'));
    }

    /**
     * Store a newly created question in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'type' => 'required|in:multiple_choice,true_false,short_answer,essay',
            'question_text' => 'required|string',
            'options' => 'required_if:type,multiple_choice|array',
            'correct_answer' => 'required',
            'points' => 'required|numeric|min:0',
            'explanation' => 'nullable|string',
        ]);

        // Verify the quiz belongs to the tutor
        $quiz = Quiz::findOrFail($request->quiz_id);
        if ($quiz->lesson->topic->course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        Question::create($request->all());

        return redirect()
            ->route('tutor.questions.index')
            ->with('success', 'Question created successfully.');
    }

    /**
     * Display the specified question.
     */
    public function show(Question $question)
    {
        // Ensure question belongs to tutor's quiz
        if ($question->quiz->lesson->topic->course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this question.');
        }

        $question->load(['quiz.lesson.topic.course']);

        return view('tutor.questions.show', compact('question'));
    }

    /**
     * Show the form for editing the specified question.
     */
    public function edit(Question $question)
    {
        // Ensure question belongs to tutor's quiz
        if ($question->quiz->lesson->topic->course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Get tutor's quizzes
        $quizzes = Quiz::whereHas('lesson.topic.course', function ($q) {
            $q->where('instructor_id', auth()->id());
        })
            ->select('id', 'title', 'lesson_id')
            ->with('lesson.topic.course:id,title')
            ->orderBy('title')
            ->get();

        return view('tutor.questions.edit', compact('question', 'quizzes'));
    }

    /**
     * Update the specified question in storage.
     */
    public function update(Request $request, Question $question)
    {
        // Ensure question belongs to tutor's quiz
        if ($question->quiz->lesson->topic->course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'type' => 'required|in:multiple_choice,true_false,short_answer,essay',
            'question_text' => 'required|string',
            'options' => 'required_if:type,multiple_choice|array',
            'correct_answer' => 'required',
            'points' => 'required|numeric|min:0',
            'explanation' => 'nullable|string',
        ]);

        // Verify the new quiz also belongs to the tutor
        if ($request->quiz_id != $question->quiz_id) {
            $newQuiz = Quiz::findOrFail($request->quiz_id);
            if ($newQuiz->lesson->topic->course->instructor_id !== auth()->id()) {
                abort(403, 'Unauthorized action.');
            }
        }

        $question->update($request->all());

        return redirect()
            ->route('tutor.questions.index')
            ->with('success', 'Question updated successfully.');
    }

    /**
     * Remove the specified question from storage.
     */
    public function destroy(Question $question)
    {
        // Ensure question belongs to tutor's quiz
        if ($question->quiz->lesson->topic->course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $question->delete();

        return redirect()
            ->route('tutor.questions.index')
            ->with('success', 'Question deleted successfully.');
    }
}
