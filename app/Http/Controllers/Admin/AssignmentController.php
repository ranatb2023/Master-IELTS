<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Topic;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assignments = Assignment::with(['topic.course'])
            ->withCount(['submissions'])
            ->latest()
            ->paginate(20);

        return view('admin.assignments.index', compact('assignments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $topicId = $request->query('topic_id');
        $topic = null;

        if ($topicId) {
            $topic = Topic::with('course')->findOrFail($topicId);
        }

        $topics = Topic::with('course')->orderBy('title')->get();

        return view('admin.assignments.create', compact('topic', 'topics'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'topic_id' => 'required|exists:topics,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'instructions' => 'nullable|string',
            'max_points' => 'required|numeric|min:0',
            'passing_points' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'order' => 'required|integer|min:0',
            'allow_late_submission' => 'boolean',
            'allow_text_submission' => 'boolean',
            'allow_file_upload' => 'boolean',
            'late_penalty_percentage' => 'nullable|numeric|min:0|max:100',
            'max_file_size_mb' => 'nullable|integer|min:1',
            'allowed_file_types' => 'nullable|string',
            'max_files' => 'nullable|integer|min:1',
            'is_published' => 'boolean',
        ]);

        // Process allowed file types
        if (!empty($validated['allowed_file_types'])) {
            $types = array_map('trim', explode(',', $validated['allowed_file_types']));
            $validated['allowed_file_types'] = $types;
        }

        $validated['allow_late_submission'] = $request->has('allow_late_submission');
        $validated['allow_text_submission'] = $request->has('allow_text_submission');
        $validated['allow_file_upload'] = $request->has('allow_file_upload');
        $validated['is_published'] = $request->has('is_published');
        $validated['late_penalty'] = $validated['late_penalty_percentage'] ?? 0;
        $validated['max_file_size'] = $validated['max_file_size_mb'] ?? 10;

        unset($validated['late_penalty_percentage'], $validated['max_file_size_mb']);

        $assignment = Assignment::create($validated);

        return redirect()->route('admin.assignments.show', $assignment)
            ->with('success', 'Assignment created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Assignment $assignment)
    {
        $assignment->load([
            'topic.course',
            'submissions' => function($query) {
                $query->with('user')->latest();
            }
        ]);

        // Calculate statistics
        $stats = [
            'total_submissions' => $assignment->submissions->count(),
            'pending_submissions' => $assignment->submissions->where('status', 'submitted')->count(),
            'graded_submissions' => $assignment->submissions->where('status', 'graded')->count(),
            'average_score' => $assignment->submissions->where('status', 'graded')->avg('score'),
        ];

        return view('admin.assignments.show', compact('assignment', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Assignment $assignment)
    {
        $assignment->load('topic.course');
        $topics = Topic::with('course')->orderBy('title')->get();

        return view('admin.assignments.edit', compact('assignment', 'topics'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Assignment $assignment)
    {
        $validated = $request->validate([
            'topic_id' => 'required|exists:topics,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'instructions' => 'nullable|string',
            'max_points' => 'required|numeric|min:0',
            'passing_points' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'order' => 'required|integer|min:0',
            'allow_late_submission' => 'boolean',
            'allow_text_submission' => 'boolean',
            'allow_file_upload' => 'boolean',
            'late_penalty_percentage' => 'nullable|numeric|min:0|max:100',
            'max_file_size_mb' => 'nullable|integer|min:1',
            'allowed_file_types' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        // Process allowed file types
        if (!empty($validated['allowed_file_types'])) {
            $types = array_map('trim', explode(',', $validated['allowed_file_types']));
            $validated['allowed_file_types'] = $types;
        }

        $validated['allow_late_submission'] = $request->has('allow_late_submission');
        $validated['allow_text_submission'] = $request->has('allow_text_submission');
        $validated['allow_file_upload'] = $request->has('allow_file_upload');
        $validated['is_published'] = $request->has('is_published');
        $validated['late_penalty'] = $validated['late_penalty_percentage'] ?? 0;
        $validated['max_file_size'] = $validated['max_file_size_mb'] ?? 10;

        unset($validated['late_penalty_percentage'], $validated['max_file_size_mb']);

        $assignment->update($validated);

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Assignment updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assignment $assignment)
    {
        $topicTitle = $assignment->topic->title;
        $assignment->delete();

        return redirect()->route('admin.assignments.index')
            ->with('success', "Assignment deleted from {$topicTitle} successfully");
    }
}
