<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuestionType;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    /**
     * Display a listing of questions
     */
    public function index(Request $request)
    {
        $query = Question::with(['quiz', 'questionType']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Quiz filter
        if ($request->filled('quiz_id')) {
            $query->where('quiz_id', $request->input('quiz_id'));
        }

        // Question type filter
        if ($request->filled('type_id')) {
            $query->where('question_type_id', $request->input('type_id'));
        }

        // Difficulty filter
        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->input('difficulty'));
        }

        // Get questions with pagination
        $questions = $query->orderBy('quiz_id')->orderBy('order')->paginate(20);

        // Get all quizzes and question types for filters
        $quizzes = Quiz::orderBy('title')->get();
        $questionTypes = QuestionType::active()->orderBy('name')->get();

        // Calculate stats
        $stats = [
            'auto_graded' => Question::whereHas('questionType', function ($q) {
                $q->where('scoring_strategy', '!=', 'manual');
            })->count(),
            'manual_grading' => Question::whereHas('questionType', function ($q) {
                $q->where('scoring_strategy', 'manual');
            })->count(),
            'type_count' => QuestionType::active()->count(),
        ];

        return view('admin.questions.index', compact('questions', 'quizzes', 'questionTypes', 'stats'));
    }

    /**
     * Display the specified question
     */
    public function show(Question $question)
    {
        $question->load(['quiz', 'questionType', 'options']);

        return view('admin.questions.show', compact('question'));
    }

    /**
     * Show the form for creating a new question
     */
    public function create(Request $request)
    {
        $quizId = $request->query('quiz_id');
        $quiz = null;

        if ($quizId) {
            $quiz = Quiz::findOrFail($quizId);
        }

        $questionTypes = QuestionType::active()->orderBy('name')->get();

        return view('admin.questions.create', compact('quiz', 'questionTypes'));
    }

    /**
     * Store a newly created question
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'question_type_id' => 'required|exists:question_types,id',
            'question' => 'required|string',
            'description' => 'nullable|string',
            'points' => 'required|numeric|min:0.01',
            'order' => 'nullable|integer|min:0',
            'media_type' => 'nullable|in:none,image,audio,video',
            'media_source' => 'nullable|in:url,upload',
            'media_url' => 'nullable|url',
            'media_file' => 'nullable|file|mimes:jpeg,jpg,png,gif,mp3,wav,mp4,webm|max:102400',
            'explanation' => 'nullable|string',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'settings' => 'nullable|array',
            'options' => 'nullable|array',
            'options.*.text' => 'required|string',
            'options.*.is_correct' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            // Get the question type to determine if we need options
            $questionType = QuestionType::findOrFail($validated['question_type_id']);

            // Set order if not provided
            if (!isset($validated['order'])) {
                $quiz = Quiz::findOrFail($validated['quiz_id']);
                $validated['order'] = $quiz->questions()->max('order') + 1;
            }

            // Handle file upload
            if ($request->hasFile('media_file') && $request->file('media_file')->isValid()) {
                $file = $request->file('media_file');
                $path = $file->store('questions/media', 'public');
                $validated['media_url'] = $path;
                $validated['media_source'] = 'upload';
            } else {
                $validated['media_source'] = $validated['media_source'] ?? 'url';
            }

            // Create the question
            $question = Question::create([
                'quiz_id' => $validated['quiz_id'],
                'question_type_id' => $validated['question_type_id'],
                'question' => $validated['question'],
                'description' => $validated['description'] ?? null,
                'points' => $validated['points'],
                'order' => $validated['order'],
                'media_type' => $validated['media_type'] ?? 'none',
                'media_source' => $validated['media_source'] ?? 'url',
                'media_url' => $validated['media_url'] ?? null,
                'explanation' => $validated['explanation'] ?? null,
                'difficulty' => $validated['difficulty'] ?? 'medium',
                'settings' => $validated['settings'] ?? null,
            ]);

            // Create options if provided (for MCQ, True/False, etc.)
            if (!empty($validated['options'])) {
                foreach ($validated['options'] as $index => $optionData) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $optionData['text'],
                        'is_correct' => $optionData['is_correct'] ?? false,
                        'order' => $index,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.quizzes.show', $validated['quiz_id'])
                ->with('success', 'Question added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create question: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing a question
     */
    public function edit(Question $question)
    {
        $question->load(['quiz', 'questionType', 'options']);
        $questionTypes = QuestionType::active()->orderBy('name')->get();

        return view('admin.questions.edit', compact('question', 'questionTypes'));
    }

    /**
     * Update the specified question
     */
    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'question_type_id' => 'required|exists:question_types,id',
            'question' => 'required|string',
            'description' => 'nullable|string',
            'points' => 'required|numeric|min:0.01',
            'order' => 'nullable|integer|min:0',
            'media_type' => 'nullable|in:none,image,audio,video',
            'media_source' => 'nullable|in:url,upload',
            'media_url' => 'nullable|url',
            'media_file' => 'nullable|file|mimes:jpeg,jpg,png,gif,mp3,wav,mp4,webm|max:102400',
            'explanation' => 'nullable|string',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'settings' => 'nullable|array',
            'options' => 'nullable|array',
            'options.*.text' => 'required|string',
            'options.*.is_correct' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            // Handle file upload
            if ($request->hasFile('media_file') && $request->file('media_file')->isValid()) {
                // Delete old file if it was an upload
                if ($question->media_source === 'upload' && $question->media_url && \Storage::disk('public')->exists($question->media_url)) {
                    \Storage::disk('public')->delete($question->media_url);
                }

                $file = $request->file('media_file');
                $path = $file->store('questions/media', 'public');
                $validated['media_url'] = $path;
                $validated['media_source'] = 'upload';
            } else {
                $validated['media_source'] = $validated['media_source'] ?? $question->media_source ?? 'url';
            }

            // Update the question
            $question->update([
                'question_type_id' => $validated['question_type_id'],
                'question' => $validated['question'],
                'description' => $validated['description'] ?? null,
                'points' => $validated['points'],
                'order' => $validated['order'] ?? $question->order,
                'media_type' => $validated['media_type'] ?? 'none',
                'media_source' => $validated['media_source'] ?? 'url',
                'media_url' => $validated['media_url'] ?? $question->media_url,
                'explanation' => $validated['explanation'] ?? null,
                'difficulty' => $validated['difficulty'] ?? 'medium',
                'settings' => $validated['settings'] ?? null,
            ]);

            // Update options (delete old ones and create new ones)
            if (isset($validated['options'])) {
                // Delete existing options
                $question->options()->delete();

                // Create new options
                foreach ($validated['options'] as $index => $optionData) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $optionData['text'],
                        'is_correct' => $optionData['is_correct'] ?? false,
                        'order' => $index,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.quizzes.show', $question->quiz_id)
                ->with('success', 'Question updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update question: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified question
     */
    public function destroy(Question $question)
    {
        $quizId = $question->quiz_id;

        try {
            $question->delete();

            return redirect()
                ->route('admin.quizzes.show', $quizId)
                ->with('success', 'Question deleted successfully!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to delete question: ' . $e->getMessage());
        }
    }

    /**
     * Get question type schema (AJAX helper)
     */
    public function getQuestionTypeSchema(QuestionType $questionType)
    {
        return response()->json([
            'id' => $questionType->id,
            'slug' => $questionType->slug,
            'name' => $questionType->name,
            'description' => $questionType->description,
            'input_schema' => $questionType->input_schema,
            'output_schema' => $questionType->output_schema,
            'scoring_strategy' => $questionType->scoring_strategy,
            'requires_manual_grading' => $questionType->requiresManualGrading(),
        ]);
    }
}
